<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\Rhu;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function sendRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rhu_id' => 'required|exists:rhus,id',
            'name' => 'required',
            'username' => 'required|unique:barangays,username',
            'email' => 'required|email|unique:barangays,email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $email = $request->input('email');
        $username = $request->input('username');

        // Generate OTP
        $otpCode = rand(100000, 999999);

        // Delete any existing OTP for this email
        Otp::where('email', $email)->delete();

        // Create new OTP
        $otp = Otp::create([
            'email' => $email,
            'otp_code' => $otpCode,
        ]);

        // Send OTP email
        Mail::send('otp', ['otp' => $otpCode, 'username' => $username], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Verify Your Registration - Health Monitoring System');
        });

        $otpLifetime = 300;

        return $this->sendResponse([
        'expires_in' => $otpLifetime,
        'created_at' => $otp->created_at->toDateTimeString(),], 'OTP sent successfully.');
    }

    public function resendRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'username' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $email = $request->input('email');
        $username = $request->input('username');

        // Generate new OTP
        $otpCode = rand(100000, 999999);

        // Delete any existing OTP for this email
        Otp::where('email', $email)->delete();

        // Create new OTP
        $otp = Otp::create([
            'email' => $email,
            'otp_code' => $otpCode,
        ]);

        // Send OTP email
        try {
            Mail::send('otp', ['otp' => $otpCode, 'username' => $username], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Resend: Verify Your Registration - Health Monitoring System');
            });

            $otpLifetime = 300;

            return $this->sendResponse([
                'expires_in' => $otpLifetime,
                'created_at' => $otp->created_at->toDateTimeString(),], 'OTP resent successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send OTP.', ['error' => $e->getMessage()]);
        }
    }


    public function verifyRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'rhu_id' => 'required|exists:rhus,id',
            'name' => 'required',
            'username' => 'required|unique:barangays,username',
            'email' => 'required|email|unique:barangays,email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
            'otp_code' => 'required|string|size:6', 
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

        // Check if OTP has expired (5 minutes)
        if ($otp->created_at->diffInSeconds(now()) > 400) {
            $otp->delete(); 
            return $this->sendError('OTP has expired.');
        }

        // OTP is valid, proceed with registration
        try {
            $barangay = Barangay::create([
                'rhu_id' => $request->input('rhu_id'),  // Add rhu_id
                'name' => $request->input('name'),       // Use 'name' instead of 'barangay'
                'username' => $request->input('username'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'email_verified_at' => now(),
            ]);

            // Delete the OTP after successful registration
            $otp->delete();

            // Create token for the user
            $token = $barangay->createToken('MyApp')->plainTextToken;

            $success['token'] = $token;
            $success['user'] = $barangay;

            return $this->sendResponse($success, 'User registered successfully.');

        } catch (\Exception $e) {
            return $this->sendError('Registration failed.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Login for Rhu
     *
     * @return \Illuminate\Http\Response
     */
    public function loginRhu(Request $request)
    {

        // Validate the request
        $request->validate([
            'username' => 'required|string', // This can be either email or username
            'password' => 'required|string',
        ]);

        $loginInput = $request->username;
        $password = $request->password;

        // Try to find rhu by email first, then by username
        $rhu = Rhu::where('email', $loginInput)
                            ->orWhere('username', $loginInput)
                            ->first();

        if (!$rhu) {
            return $this->sendError('Invalid email/username or password.', ['error'=>'Unauthorized']);
        }

        if (!Hash::check($password, $rhu->password)) {
            return $this->sendError('Invalid email/username or password.', ['error'=>'Unauthorized']);
        }

        // Create token and update user status
        $token = $rhu->createToken('Rhu')->plainTextToken;
        $rhu->remember_token = $token;
        $rhu->is_online = 1; 
        $rhu->save(); 
        
        $success = [
            'token' => $token,
            'id' => $rhu->id,
            'name' => $rhu->name,
            'role' => 'rhu'
        ];

        return $this->sendResponse($success, 'Rhu login successfully.');
    }
    

    /**
     *  Login for Barangay
     * 
     * @return \Illuminate\Http\Response
     */
    public function loginBarangay(Request $request)
    {
        // Validate the request
        $request->validate([
            'username' => 'required|string', // This can be either email or username
            'password' => 'required|string',
        ]);

        $loginInput = $request->username;
        $password = $request->password;

        // Try to find barangay by email first, then by username
        $barangay = Barangay::where('email', $loginInput)
                            ->orWhere('username', $loginInput)
                            ->first();

        if (!$barangay) {
            return $this->sendError('Invalid email/username or password.', ['error'=>'Unauthorized']);
        }

        if (!Hash::check($password, $barangay->password)) {
            return $this->sendError('Invalid email/username or password.', ['error'=>'Unauthorized']);
        }

        if (is_null($barangay->email_verified_at)) {
            return $this->sendError('Email not verified.');
        }

        // Create token and update user status
        $token = $barangay->createToken('Barangay')->plainTextToken;
        $barangay->remember_token = $token;
        $barangay->is_online = 1; 
        $barangay->save(); 
        
        $success = [
            'token' => $token,
            'id' => $barangay->id,
            'name' => $barangay->name,
            'role' => 'barangay'
        ];

        return $this->sendResponse($success, 'Barangay login successfully.');
    }


    /**
     * Logout for RHU and Barangay
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        /**
         * @var \App\Models\Barangay|\App\Models\Rhu $user
         */
        $user = Auth::user(); // Get the currently authenticated user

        if ($user) {
            // Revoke current token
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            $user->remember_token = null;
            $user->is_online = 0;
            $user->save();

            return $this->sendResponse([], 'User logged out successfully.');
        }

        return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
    }

}
