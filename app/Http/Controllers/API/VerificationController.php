<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Barangay;
use Illuminate\Auth\Events\Verified;

class VerificationController extends BaseController
{
    public function verify(Request $request, $id, $hash)
    {
        $barangay = Barangay::findOrFail($id);
        $url = env('APP_URL') . '/login/barangay';
            
        if (!hash_equals((string) $hash, (string) sha1($barangay->email))) {
            return $this->sendError('Invalid verification link.');
        }

        // Check if the email is already verified
        if ($barangay->hasVerifiedEmail()) {
            return redirect($url)->with('status', 'Email verified successfully. You can now log in.');
        }

        // Mark the email as verified and trigger the Verified event
        $barangay->markEmailAsVerified();
        event(new Verified($barangay));

        return redirect($url)->with('status', 'Email verified successfully. You can now log in.');
    }

    
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->sendResponse([], 'Verification link sent.');
    }
}
