<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class VerifySSOJwt
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Missing token'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('SSO_JWT_SECRET'), 'HS256'));

            $user = User::find($decoded->sub); // assuming 'sub' = user ID
            if (!$user) {
                return response()->json(['message' => 'Invalid user'], 403);
            }

            auth()->setUser($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}

