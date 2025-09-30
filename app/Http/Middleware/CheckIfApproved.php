<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfApproved
{

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->email !== 'oscar.romanini.jr@gmail.com' && is_null($user->approved_at)) {

                if (!$request->routeIs('login') && !$request->routeIs('logout')) {
                    Auth::logout();

                    return redirect()
                        ->route('login')
                        ->with('error', 'Sua conta está pendente de aprovação por um administrador.');
                }
            }
        }

        return $next($request);
    }
}
