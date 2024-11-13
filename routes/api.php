<?php

use App\Http\Controllers\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('login/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login.provider');
Route::get('login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

