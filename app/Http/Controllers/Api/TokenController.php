<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Http\Controllers\Controller;

class TokenController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        // Get authenticated user from session or request
        $user = $request->user() ?: $this->getUserFromSession($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Generate JWT
        $key = env('JWT_SECRET');
        $payload = [
            'iss' => 'laravel-chat-app',
            'exp' => time() + 3600,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return response()->json([
            'token' => $token,
            'user' => $user->only(['id', 'name', 'email'])
        ]);
    }

    protected function getUserFromSession(Request $request)
    {
        // Example: get user from session if using Laravel session auth
        if ($request->session()->has('loginId')) {
            $userId = $request->session()->get('loginId');
            return User::find($userId);
        }

        return null;
    }
}