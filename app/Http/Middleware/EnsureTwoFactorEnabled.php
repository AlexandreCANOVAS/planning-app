<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class EnsureTwoFactorEnabled
{
    /**
     * Liste des routes qui sont exclues de la vérification 2FA
     *
     * @var array
     */
    protected $except = [
        'two-factor.verify',
        'two-factor.verify-recovery',
        'logout'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Si l'utilisateur est connecté, a activé la 2FA et n'a pas encore vérifié son code
        if ($user && $user->two_factor_secret && !Session::has('two_factor_verified')) {
            // Vérifier si la route actuelle est exclue de la vérification 2FA
            $currentRoute = $request->route();
            if ($currentRoute) {
                $routeName = $currentRoute->getName();
                
                // Si la route actuelle est dans la liste des exceptions, on la laisse passer
                if ($routeName && in_array($routeName, $this->except)) {
                    return $next($request);
                }
            }
            
            // Si la route actuelle n'est pas exclue, on stocke l'URL pour y revenir après la vérification
            // et on redirige vers la page de vérification 2FA
            Session::put('url.intended', $request->fullUrl());
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }
}
