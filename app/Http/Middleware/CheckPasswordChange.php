<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class CheckPasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $currentRoute = $request->path();

        Log::info('CheckPasswordChange middleware', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'password_changed' => $user->password_changed,
            'current_route' => $currentRoute,
            'session_id' => Session::getId(),
            'request_path' => $request->path(),
            'request_method' => $request->method()
        ]);

        // Liste des routes à exclure
        $excludedRoutes = [
            'change-password',
            'logout',
            'login'
        ];

        // Vérifier si l'URL actuelle commence par l'une des routes exclues
        $shouldExclude = collect($excludedRoutes)->some(function ($route) use ($currentRoute) {
            return str_starts_with($currentRoute, $route);
        });

        if ($user->password_changed === false && !$shouldExclude) {
            Log::info('Redirecting to change password', [
                'from_route' => $currentRoute,
                'user_id' => $user->id,
                'session_id' => Session::getId()
            ]);

            return redirect('/change-password');
        }

        return $next($request);
    }
}
