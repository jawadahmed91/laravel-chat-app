<?php

use App\Events\UserTyping;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\ChatController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Auth\SsoLoginController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
Route::get('/check-sso', [AuthController::class, 'checkSSO']);
Route::post('/auth/token', [AuthController::class, 'login']);

// Protected routes: require valid JWT
Route::middleware(JwtMiddleware::class)->group(function () {
    Route::get('/auth/check', [AuthController::class, 'check']);
    Route::get('/auth/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::middleware('verify.sso.jwt')->group(function () {
    Route::get('/user', fn () => auth()->user());
});

// Route::middleware('auth:sanctum')->group(function () {
  Route::post('/chat/room', [ChatRoomController::class, 'store']);
  Route::post('/chat/message/send', [MessageController::class, 'send']);
  Route::get('/chat/messages', [MessageController::class, 'fetch']);
// });

Route::post('/chat/typing', function (Request $request) {
    $request->validate([
        'room_id' => 'required|integer',
    ]);

    broadcast(new UserTyping($request->room_id, auth()->id()))->toOthers();

    return response()->json(['status' => 'typing broadcasted']);
});
// ->middleware('auth:sanctum');

Route::post('/chat/seen', function (Request $request) {
    $request->validate([
        'room_id' => 'required|integer',
        'message_id' => 'required|integer',
    ]);

    $table = 'chat_room_' . $request->room_id;
    if (!Schema::hasTable($table)) {
        return response()->json(['error' => 'Room not found'], 404);
    }

    $message = DB::table($table)->where('id', $request->message_id)->first();
    if (!$message) {
        return response()->json(['error' => 'Message not found'], 404);
    }

    $seenBy = json_decode($message->seen_by ?? '[]', true);
    if (!in_array(auth()->id(), $seenBy)) {
        $seenBy[] = auth()->id();
        DB::table($table)->where('id', $request->message_id)->update([
            'seen_by' => json_encode($seenBy)
        ]);
    }

    return response()->json(['status' => 'marked as seen']);
});
// ->middleware('auth:sanctum');

/*
How to Access Auth Data in Controllers
Inside any controller method, you can access authenticated user info like this:

    $authUser = $request->attributes->get('auth');
    dd($authUser); // will show decoded JWT user data
*/
