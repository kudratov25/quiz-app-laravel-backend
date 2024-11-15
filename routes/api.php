<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login.provider');
    Route::get('/login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('quizzes', QuizController::class);
    Route::post('quizzes/{quizId}/start', [QuizController::class, 'playQuiz']);
    Route::post('quizzes/{quizResultId}/submit-answer/{questionId}', [QuizController::class, 'submitAnswer']);
    Route::post('/media/upload', [MediaController::class, 'upload']);
});
