<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {

            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => "Email or Password failed",
                    'data' => null
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => "Login success",
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user),
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Login failed",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        try {

            $user = Auth::user();

            return response()->json([
                'message' => "My profile",
                'data' => new UserResource($user),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed get profile",
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function logout()
    {
        try {

            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => "Logout success",
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Failed logout",
                'error' => $e->getMessage()
            ], 500);

        }
    }
}