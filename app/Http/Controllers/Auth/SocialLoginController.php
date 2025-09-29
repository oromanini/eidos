<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    const ADMIN_EMAIL = 'oscar.romanini.jr@gmail.com';

    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::updateOrCreate(
                [
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                ],
                [
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'password' => bcrypt(Str::random(24)),
                ]
            );

            if ($user->email === self::ADMIN_EMAIL && is_null($user->approved_at)) {
                $user->update(['approved_at' => now()]);
            }

            Auth::login($user);

            return redirect()->route('home');

        } catch (\Exception $e) {
            Log::error('Socialite Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Algo deu errado durante a autenticação.');
        }
    }
}

