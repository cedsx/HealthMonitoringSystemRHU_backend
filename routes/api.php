<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\OtpController;

Route::controller(RegisterController::class)->group(function(){
    Route::post('register/send-otp', 'sendRegistrationOtp'); 
    Route::post('register/resend-otp', 'resendRegistrationOtp'); 
    Route::post('register/verify-otp', 'verifyRegistrationOtp');
    Route::post('login/rhu', 'loginRhu'); 
    Route::post('login/barangay', 'loginBarangay');
});

// Forgot Password Routes
Route::controller(OtpController::class)->group(function(){
    Route::post('forgot-password/send-otp', 'sendOtp');
    Route::post('forgot-password/verify-otp', 'verifyOtp'); 
    Route::post('forgot-password/reset-password', 'resetPassword');
    Route::post('forgot-password/resend-otp', 'resendOtp');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [RegisterController::class, 'logout']);
});

// THIS FOR BARANGAY
Route::middleware('auth.barangay')->group(function () {
 
});

// THIS FOR RHU
Route::middleware('auth.rhu')->group(function () {

});



// SAMPLE API
// Route::get('/test-api', function () {
//     return response()->json(['working' => true]);
// });