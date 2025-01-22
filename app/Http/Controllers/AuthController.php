<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{

    // Register a new user (optional)
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email','unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Authenticate user
        $token = JWTAuth::fromUser($user);

        return response()->json([
                'access_token' => $token,
                'type' => 'bearer'
        ], 200);
    }


    //login function 
    public function login(Request $request)
    {
        // validate the request
        $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "string", 'min:8'],
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();

            $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);
            $type = 'bearer';
            return response()->json(compact('token','type'));
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }



    // Logout and invalidate the token
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not invalidate token'], 500);
        }
    }

    // refresh token
    public function refresh()
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 400);
        }

        try {
            $refreshedToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }

        return response()->json(compact('refreshedToken'));

    }

}
