<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);

        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(40),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);

    }

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {

            return response()->json(['error' => 'Unauthorized'], 401);

        }

        return $this->respondWithToken($token);

    }

    public function me()
    {

        return response()->json(auth()->user());

    }

    public function logout()
    {

        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);

    }

    public function refresh()
    {

        return $this->respondWithToken(auth()->refresh());

    }

    public function forgotPassword(Request $request)
    {

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response()->json(['message' => 'If the email exists, a reset link has been sent.'], 200);

        }

        $token = Str::random(60);

        $user->forceFill([
            'reset_token' => Hash::make($token),
            'reset_token_created_at' => now(),
        ])->save();

        return response()->json(['reset_token' => $token], 200);

    }

    public function resetPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->token, $user->reset_token)) {

            return response()->json(['message' => 'Invalid token'], 400);

        }

        if ($user->reset_token_created_at->diffInHours(now()) > 1) {

            return response()->json(['message' => 'Token expired'], 400);

        }

        $user->password = Hash::make($request->password);

        $user->reset_token = null;

        $user->reset_token_created_at = null;

        $user->save();

        return response()->json(['message' => 'Password reset successfully']);

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

}
