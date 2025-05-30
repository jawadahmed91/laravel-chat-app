<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class AuthController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        // Get decoded user data from middleware
        $authUser = $request->attributes->get('auth');

        return response()->json([
            'authenticated' => true,
            'user' => $authUser
        ]);
    }

    // Temporary login endpoint (for demo/testing)
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !password_verify($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->generateToken($user);
    }

    // Returns a fresh JWT token for authenticated user
    public function refreshToken(Request $request): JsonResponse
    {
        $authUser = $request->attributes->get('auth');

        $user = User::find($authUser['id']);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return $this->generateToken($user);
    }

    protected function generateToken($user): JsonResponse
    {
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
}

// GET /api/auth/check
// Authorization: Bearer YOUR_JWT_TOKEN