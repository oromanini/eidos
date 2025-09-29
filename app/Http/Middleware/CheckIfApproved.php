<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && is_null(Auth::user()->approved_at)) {
            Auth::logout();

            return redirect('/login')
                ->with('error', 'Sua conta está pendente de aprovação por um administrador.');
        }

        return $next($request);
    }
}
