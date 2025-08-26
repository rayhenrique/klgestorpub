<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('AdminMiddleware: User check', [
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'is_admin' => auth()->check() ? auth()->user()->isAdmin() : false
        ]);

        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Acesso não autorizado.');
        }

        return $next($request);
    }
} 