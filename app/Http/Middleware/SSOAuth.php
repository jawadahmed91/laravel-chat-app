<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SSOAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Missing token'], 401);
        }

        // try {
            // Decode payload without verifying
            $publicKey = <<<EOD
-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEAmDx88wFfnn6qAl7vyE13G9esPQ0h8UG/yJ3TNCatt3NuctFIwTUc
en/pwDO2F0hEs8OcdH49vp3xge4mThXLoEsZncojl8TnxVM829lnfou2nVgClgPW
uf0ThgOenB0bgZOxU63Hw0cPofGB8O40BDgCntiVGM349RfEY6Y0XfVNTKsbLsF+
G1F2KQY/qMSUcE4+0wIHAR6ScUtjZhLvZau5z1QgK4VUSbFrI2d0naPFM9K5WxtI
7HD0O40JuWf2tcTwEHZTakNfx2wkocGB10iIQ1YzZRRnpTzyYc+CiuHkTrcMpCYA
A/vz2e/Bes//DyRAucqYHtPhkelA2Cm+MQIDAQAB
-----END RSA PUBLIC KEY-----
EOD;
        $keys = DB::table('oauth_public_keys')->where('client_id', 'user_1')->first();

// dd($keys->public_key);

            $payload = JWT::decode($token, new Key($keys->public_key, 'RS256')); // Dummy for decode only
        dd($payload);

            $userId = $payload->user_id ?? null;

            if (!$userId) {
                throw new \Exception('Invalid token');
            }

            // Fetch public key from CI API
            $res = Http::get("http://ci-app.local/oauth/public-key/{$userId}");
            if (!$res->ok()) {
                throw new \Exception('Public key fetch failed');
            }

            $key = $res->json();
            $decoded = JWT::decode($token, new Key($key['public_key'], $key['algorithm']));

            // Now find or create user in Laravel
            $user = User::firstOrCreate(
                ['id' => $userId],
                ['email' => $decoded->email]
            );

            Auth::login($user);

        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Token invalid: ' . $e->getMessage()], 401);
        // }

        return $next($request);
    }
}
