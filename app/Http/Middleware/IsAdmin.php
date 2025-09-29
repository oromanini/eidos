<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    const ADMIN_EMAIL = 'oscar.romanini.jr@gmail.com';

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->email === self::ADMIN_EMAIL) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Acesso não autorizado.');
    }
}
