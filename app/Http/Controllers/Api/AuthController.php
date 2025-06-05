<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
class AuthController extends Controller
{
    /**
     * Get SSO token from CodeIgniter CRM
     */
    public function checkSSO()
    {
        // try {
            // Request token from CodeIgniter SSO endpoint
            $response = Http::get('http://localhost:8080/api/sso/token');
            
            if ($response->failed()) {
                return response()->json(['message' => 'Not logged in on CI'], 401);
            }

            $token = $response->json('token');

            // Decode and authenticate like before
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $user = User::firstOrCreate(
                ['id' => $decoded->sub],
                [
                    'name' => $decoded->name,
                    'email' => $decoded->email,
                    'role' => $decoded->role,
                    'password' => Hash::make(Str::random(20)),
                ]
            );

            auth()->setUser($user);

            return response()->json([
                'message' => 'SSO success',
                'user' => $user
            ]);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'SSO Failed', 'error' => $e->getMessage()], 401);
        // }
    }


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