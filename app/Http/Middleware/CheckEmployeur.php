<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeur
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'employeur') {
            return redirect()->route('login')->with('error', 'Accès réservé aux employeurs.');
        }

        return $next($request);
    }
} 