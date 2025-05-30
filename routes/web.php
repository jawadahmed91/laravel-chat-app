<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sso-login', [\App\Http\Controllers\SsoController::class, 'login']);

Route::middleware(['auth'])->get('/auth/token', [TokenController::class, 'generate']);