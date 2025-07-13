<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\Rhu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request){
        $input = $request->all();

        //this is a validation of the inputs and check if meet the requirements
        $validator = Validator::make($input, [
            'rhu_id' => 'required|exists:rhus,id',
            'name' => 'required',
            'username' => 'required|unique:barangays,username',
            'email' => 'required|email|unique:barangays,email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error. ', $validator->errors());
        }

        //check if the email in rhu table exists
        if (Rhu::where('email', $request->email)->exists()) {
            return $this->sendError('Validation Error.', ['email' => ['The email has already been taken.']]);
        }

        // Hash the password
        $input['password'] = Hash::make($input['password']);

        // Remove c_password from data
        unset($input['c_password']);

        // Store in database
        $barangay = Barangay::create($input);

        return $this->sendResponse( $barangay, 'Barangay register successfully.');
    }

    /**
     * Login for Rhu
     *
     * @return \Illuminate\Http\Response
     */
    public function loginRhu(Request $request)
    {

        $rhu = Rhu::where('username', $request->username)->first();


        if($rhu && Hash::check($request->password, $rhu->password)){
            
            $token = $rhu->createToken('Rhu')->plainTextToken;
            $rhu->remember_token = $token;
            $rhu->is_online = 1; 
            $rhu->save();
            
            $success['token'] = $token;
            $success['id'] = $rhu->id;
            $success['name'] = $rhu->name;
            $success['role'] = 'rhu';

            return $this->sendResponse($success, 'Rhu login successfully.');
        }
      
        return $this->sendError('Invalid username or password', ['error'=>'Unauthorized']);  
    }

    /**
     *  Login for Barangay
     * 
     * @return \Illuminate\Http\Response
     */
    public function loginBarangay(Request $request)
    {
        $barangay = Barangay::where('username', $request->username)->first();

        if($barangay && Hash::check($request->password, $barangay->password)){

            if (is_null($barangay->email_verified_at)) {
                return $this->sendError('Email not verified.');
            }

            $token = $barangay->createToken('Barangay')->plainTextToken;
            $barangay->remember_token = $token;
            $barangay->is_online = 1; 
            $barangay->save(); 
            
            $success['token'] = $token;
            $success['id'] = $barangay->id;
            $success['name'] = $barangay->name;
            $success['role'] = 'barangay';
     
            return $this->sendResponse($success, 'Barangay login successfully.');
        }

        return $this->sendError('Invalid username or password.', ['error'=>'Unauthorized']);
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

            $request->user()->currentAccessToken()->delete();
            $user->is_online = 0;
            $user->save();

            return $this->sendResponse([], 'User logged out successfully.');
        }

        return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
    }

}
