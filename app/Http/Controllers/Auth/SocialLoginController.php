<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialLoginController extends Controller
{
    const ADMIN_EMAIL = 'oscar.romanini.jr@gmail.com';

    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $socialUser = null;

        try {
            $socialUser = Socialite::driver($provider)->user();

            if (! $socialUser->getId()) {
                throw new \RuntimeException('Social provider did not return a valid user id.');
            }

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

            return redirect()->route('dashboard');

        } catch (Throwable $e) {
            Log::error('Socialite callback failed.', [
                'provider' => $provider,
                'social_user_id' => $socialUser?->getId(),
                'social_user_email' => $socialUser?->getEmail(),
                'user_model' => User::class,
                'user_connection' => (new User())->getConnectionName(),
                'database_default' => config('database.default'),
                'database_debug' => $this->databaseDebugInfo(),
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Algo deu errado durante a autenticação.');
        }
    }

    private function databaseDebugInfo(): array
    {
        try {
            $connection = DB::connection();

            return [
                'connection_name' => $connection->getName(),
                'driver' => $connection->getDriverName(),
                'database' => method_exists($connection, 'getDatabaseName') ? $connection->getDatabaseName() : null,
                'has_pdo' => method_exists($connection, 'getPdo') ? (bool) $connection->getPdo() : null,
            ];
        } catch (Throwable $e) {
            return [
                'connection_error' => $e->getMessage(),
            ];
        }
    }
}
