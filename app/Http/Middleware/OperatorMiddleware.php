<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isOperator()) {
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
