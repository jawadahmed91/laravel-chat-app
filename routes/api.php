<?php
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\ChatController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokenController;

/*
fetch('http://laravel-chat-app.test/api/auth/check', {
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
  }
})
.then(res => res.json())
.then(data => {
  if (data.authenticated) {
    console.log('Welcome back:', data.user);
  } else {
    // Redirect to login
  }
});
*/

// Public route: login (temporary)
Route::post('/auth/token', [AuthController::class, 'login']);

// Protected routes: require valid JWT
Route::middleware(JwtMiddleware::class)->group(function () {
    Route::get('/auth/check', [AuthController::class, 'check']);
    Route::get('/auth/refresh-token', [AuthController::class, 'refreshToken']);
});

/*
How to Access Auth Data in Controllers
Inside any controller method, you can access authenticated user info like this:

    $authUser = $request->attributes->get('auth');
    dd($authUser); // will show decoded JWT user data
*/
