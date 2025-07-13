<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;


Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login/rhu', 'loginRhu'); 
    Route::post('login/barangay', 'loginBarangay');
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