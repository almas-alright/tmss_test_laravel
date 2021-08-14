<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JWTAuth;

class Password extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['resetPassword']]);
    }

    /**
     * Reset Password (if password forgot).
     * @group  Password
     *
     * @bodyParam email email required user email
     * @bodyParam password string required user password
     * @bodyParam password_confirmation string required user password
     *
     * @response  201 {
     *  "success": "boolean",
     *  "message": "status message"
     * }
     *
     * @response  422 {
     *  "success": "boolean",
     *  "errors": "validation error"
     * }
     * @return \Illuminate\Http\JsonResponse
     */

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors()
                ],
                422
            );
        }

        $nm = Validator::make([], []); // Empty data and rules fields
        $nm->errors()->add('abc', 'Invalid email');
        $invalid = new ValidationException($nm);

        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            $updateUser = User::find($user->id);
            $updateUser->password = bcrypt($request->password);
            $updateUser->save();
            //Mail
            return response()->json([
                'success' => true,
                'message' => 'Your Password has been reset please check inbox of '.$request->email.' for details',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'errors' => $invalid->errors()
            ], 422);
        }

    }

    /**
     * Change Password
     * @authenticated
     * @group  Password
     *
     * @bodyParam email email required user email
     * @bodyParam password string required user password
     * @bodyParam password_confirmation string required user password
     *
     * @response  201 {
     *  "success": "boolean",
     *  "message": "status message"
     * }
     *
     * @response  422 {
     *  "success": "boolean",
     *  "errors": "validation error"
     * }
     * @return \Illuminate\Http\JsonResponse
     */

    public function changePassword(Request $request){
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'password_current' => 'required|string|between:2,100',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors()
                ],
                422
            );
        }
        $nm = Validator::make([], []); // Empty data and rules fields
        $nm->errors()->add('abc', 'Current Password Not Matched');
        $invalid = new ValidationException($nm);
        $user = User::find($user->id);
        $matched = Hash::check($request->input('password_current'), $user->password);
        if($matched){
            $user->password = bcrypt($request->input('password'));
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Your Password has been changed successfully. please login again',
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'errors' => $invalid->errors()
            ], 422);
        }
    }
}
