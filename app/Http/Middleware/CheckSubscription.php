<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de login
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        try {
            // Vérifier si l'utilisateur a un abonnement actif
            // On utilise une vérification plus robuste avec try/catch
            if (!$request->user()->subscribed('default')) {
                // Rediriger vers la page d'abonnement
                return redirect()->route('subscription.show')
                    ->with('warning', 'Un abonnement est nécessaire pour accéder à cette fonctionnalité.');
            }
        } catch (\Exception $e) {
            // En cas d'erreur (par exemple si les tables d'abonnement n'existent pas encore)
            // On log l'erreur et on redirige vers la page d'abonnement
            \Illuminate\Support\Facades\Log::error('Erreur de vérification d\'abonnement: ' . $e->getMessage());
            return redirect()->route('subscription.show')
                ->with('warning', 'Un abonnement est nécessaire pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
