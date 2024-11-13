<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Log::info($request);
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('Quizz app')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
