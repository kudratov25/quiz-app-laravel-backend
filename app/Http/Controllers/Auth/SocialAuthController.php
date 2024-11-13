<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        try {
            $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Provider not supported'], 400);
        }
    }
    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();

            $existingUser = User::where('email', $user->getEmail())->first();

            if ($existingUser) {
                $socialLogin = SocialLogin::where('user_id', $existingUser->id)
                    ->where('provider', $provider)
                    ->first();

                if (!$socialLogin) {
                    SocialLogin::create([
                        'user_id' => $existingUser->id,
                        'provider' => $provider,
                        'provider_id' => $user->getId(),
                    ]);
                }

                $token = $existingUser->createToken('Quizz app')->plainTextToken;

                return response()->json([
                    'user' => $existingUser,
                    'token' => $token
                ]);
            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'avatar' => $user->getAvatar(),
                    'password' => Hash::make(Str::random(32))
                ]);

                SocialLogin::create([
                    'user_id' => $newUser->id,
                    'provider' => $provider,
                    'provider_id' => $user->getId(),
                ]);

                $token = $newUser->createToken('Quizz app')->plainTextToken;

                return response()->json([
                    'user' => $newUser,
                    'token' => $token
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to authenticate using ' . ucfirst($provider),
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
