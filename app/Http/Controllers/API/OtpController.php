<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Otp;
use App\Models\Barangay; 
use App\Models\Rhu; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class OtpController extends BaseController
{
    public function sendOtp(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $email = $request->input('email');

        $barangay = Barangay::where('email', $email)->first();
        $rhu = Rhu::where('email', $email)->first();
        
        if (!$barangay && !$rhu) {
            return $this->sendError('Email not found in our records.');
        }

        // Delete any existing OTP for this email
        Otp::where('email', $email)->delete();

        $otpCode = rand(100000, 999999);

        Otp::create([
            'email' => $email,
            'otp_code' => $otpCode,
        ]);

        // Send OTP via email
        try {
            Mail::send('forgot-otp', ['otp' => $otpCode], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Password Reset - Your OTP Code');
            });

            return $this->sendResponse([], 'Verification code sent to your email.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send OTP. Please try again.');
        }
    }


     public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $email = $request->input('email');

        // Check if email exists
        $barangay = Barangay::where('email', $email)->first();
        $rhu = Rhu::where('email', $email)->first();
        
        if (!$barangay && !$rhu) {
            return $this->sendError('Email not found in our records.');
        }

        // Delete existing OTP
        Otp::where('email', $email)->delete();

        // Generate new OTP
        $otpCode = rand(100000, 999999);

        // Save new OTP
        Otp::create([
            'email' => $email,
            'otp_code' => $otpCode,
        ]);

        // Send new OTP via email
        try {
            Mail::send('otp', ['otp' => $otpCode], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Resend: Password Reset - Your OTP Code');
            });

            return $this->sendResponse([], 'New verification code sent to your email.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send OTP. Please try again.');
        }
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $email = $request->input('email');
        $otpCode = $request->input('otp_code');

        // Check if the OTP exists and matches
        $otp = Otp::where('email', $email)
                    ->where('otp_code', $otpCode)
                    ->first();

        if (!$otp) {
            return $this->sendError('Invalid OTP.');
        }

        if ($otp->created_at->diffInSeconds(now()) > 300) {
            $otp->delete(); 
            return $this->sendError('OTP has expired.');
        }


        return $this->sendResponse([], 'OTP verified successfully.');
    }




    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|digits:6', 
            'password' => 'required|string|confirmed', 
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $email = $request->input('email');
        $otpCode = $request->input('otp_code');
        $password = $request->input('password');
        
        // Verify OTP one more time
        $otp = Otp::where('email', $email)
                    ->where('otp_code', $otpCode)
                    ->first();

        if (!$otp) {
            return $this->sendError('Invalid verification code.');
        }

        // Check if OTP has expired
        if ($otp->created_at->diffInSeconds(now()) > 300) {
            $otp->delete();
            return $this->sendError('Verification code has expired.');
        }

        // Check if the email belongs to a barangay or an rhu
        $barangay = Barangay::where('email', $email)->first();
        $rhu = Rhu::where('email', $email)->first();

        
        if ($barangay) {
            $barangay->password = Hash::make($password);
            $barangay->save();
        } elseif ($rhu) {
            $rhu->password = Hash::make($request->input('password'));
            $rhu->save();
        } else {
            return $this->sendError('User not found.');
        }

        // Delete the OTP after successful password reset
        $otp->delete();

        return $this->sendResponse([], 'Password reset successfully.');

    }


}
