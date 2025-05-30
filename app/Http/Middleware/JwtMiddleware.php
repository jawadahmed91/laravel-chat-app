<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $key = new Key(env('JWT_SECRET'), 'HS256');
            $decoded = JWT::decode($token, $key);

            // Attach user to request
            // $request->auth = (array) $decoded->user;
            $request->attributes->add(['auth' => (array) $decoded->user]);

            return $next($request);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token: ' . $e->getMessage()], 401);
        }
    }
}
