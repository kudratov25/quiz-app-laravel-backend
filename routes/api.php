<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::get('login/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login.provider');
Route::get('login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
