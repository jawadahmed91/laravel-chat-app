<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class SsoController extends Controller
{
    public function login(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            return response('Missing token', 400);
        }

        try {
            $key = new Key(env('JWT_SECRET'), 'HS256');
            $decoded = JWT::decode($token, $key);

            // Find user in Laravel
            $user = User::find($decoded->user->id);

            if (!$user) {
                return response('User not found', 404);
            }

            Auth::login($user);

            return redirect('/chat'); // Redirect to chat dashboard

        } catch (\Exception $e) {
            return response('Invalid token: ' . $e->getMessage(), 401);
        }
    }
}