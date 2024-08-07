<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authentication extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        //if validation fails
        if ($validator->fails()) {
            $responseValidator = [
                'success' => false,
                'variable' => 'INVALID_ENTRY',
                'message' => $validator->errors()
            ];

            return response()->json($responseValidator, 422);
        }

        $credentials = $request->only('email', 'password');
        $date = Carbon::now()->addDays(7)->timestamp;

        try {
            if (!$token = JWTAuth::attempt($credentials, ['exp' => $date])) {
                return response()->json(
                    [
                        'success' => false,
                        'variable' => 'INVALID_LOGIN_CREDENTIALS',
                        'message' => 'Invalid credentials'
                    ],
                    400
                );
            }
        } catch (JWTException $e) {
            Log::error($e->getMessage(), ['stack' => $e->getTraceAsString()]);

            return response()->json(
                [
                    'success' => false,
                    'variable' => 'FAILED_CREATE_TOKEN',
                    'message' => 'Could not create token'
                ],
                500
            );
        }

        if (!$token) {
            return response()->json(
                [
                    "success" => false,
                    'variable' => 'FAILED_LOGIN',
                    'message' => 'Unauthorized',
                ],
                401
            );
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            "success" => true,
            'variable' => 'SUCCESS_CREATE_TOKEN',
            "message" => "User logged in successfully",
            'data' => [
                "id" => $user->id,
                "name" => $user->name,
                "username" => $user->username,
                "email" => $user->email,
                "email_verified_at" => $user->email_verified_at,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at
            ],
            'token' => $token,
            'expires' => auth()->factory()->getTTL() * 60,
            'type' => 'bearer'
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'username' => 'required|string|max:80|unique:users',
            'email' => 'required|string|email|max:80|unique:users',
            'password' => 'required|string|min:6',
        ]);

        //if validation fails
        if ($validator->fails()) {
            $responseValidator = [
                'success' => false,
                'variable' => 'INVALID_ENTRY',
                'message' => $validator->errors()
            ];

            return response()->json($responseValidator, 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            "success" => true,
            'variable' => 'SUCCESS_CREATE_ACCOUNT',
            'message' => 'User created successfully',
            'data' => [
                "id" => $user->id,
                "name" => $user->name,
                "username" => $user->username,
                "email" => $user->email,
                "email_verified_at" => $user->email_verified_at,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at
            ],
            'token' => $token,
            'expires' => auth()->factory()->getTTL() * 60,
            'type' => 'bearer'
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            "success" => true,
            'variable' => 'ACCOUNT_IS_LOGOUT',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => true,
            'variable' => 'SUCCESS_REFRESH_TOKEN',
            'user' => Auth::guard('api')->user(),
            'authorisation' => [
                'token' => Auth::guard('api')->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
