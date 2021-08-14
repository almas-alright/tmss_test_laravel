<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     * @group  Authentication
     * @bodyParam email email required user email
     * @bodyParam password string required user password
     *
     * @response  {
     *  "token": "JWT token"
     * }
     *
     * @response  422{
     *  "success": "boolean",
     *  "message": "error message"
     * }
     *
     * @response  422{
     *  "success": "boolean",
     *  "message": "validation error [array]"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ],['active.exists' => 'user not active']);

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
        $nm->errors()->add('abc', 'Invalid email or password');
        $invalid = new ValidationException($nm);

        if (!$token = auth('api')->attempt($validator->validated())) {
            return response()->json([
                'success' => false,
                'errors' => $invalid->errors()
            ], 422);
        } else {
            return $this->createNewToken($token);
        }
    }

    /**
     * Register a User.
     * @group  Authentication
     *
     * @bodyParam name string required username
     * @bodyParam email email required user email
     * @bodyParam password string required user password
     *
     * @response  201 {
     *  "success": "boolean",
     *  "message": "status message"
     * }
     *
     * @response  422 {
     *  "success": "boolean",
     *  "message": "validation error"
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
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
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'User successfully registered',
                'user' => 'fd'//$user
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Opps Something Happened Wrong',
                'user' => 'fd'//$user
            ], 201);
        }
    }


    /**
     * Refresh a token.
     * @authenticated
     * @group  Authentication
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('api')->refresh());
    }

    /**
     * Get the authenticated User.
     * @authenticated
     * @group  Authentication
     * @response 200 {
     * "user": {
     *   "id": 1,
     *   "name": "abcd",
     *   "email": "abcd@email.com",
     *   "email_verified_at": "2020-08-26T06:00:00.000000Z",
     *   "active": 1,
     *   "deleted_at": null,
     *   "created_at": "2020-08-26T15:39:24.000000Z",
     *   "updated_at": "2020-08-26T15:39:24.000000Z"
     *   }
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(['user' => auth('api')->user()]);
    }

    /**
     * Log the user out (Invalidate the token).
     * @authenticated
     * @group  Authentication
     *
     * @response  200 {
     *  "message": "User successfully signed out"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }
}
