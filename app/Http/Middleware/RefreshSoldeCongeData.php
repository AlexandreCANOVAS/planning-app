<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RefreshSoldeCongeData
{
    /**
     * Middleware pour s'assurer que les données des soldes de congés sont à jour
     * après une modification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Vérifier si nous sommes dans une route de mise à jour des soldes de congés
        if ($request->is('conges/solde/*') && $request->isMethod('PUT')) {
            Log::info('RefreshSoldeCongeData middleware: Mise à jour des soldes de congés détectée');
            
            // Si la réponse est une redirection, nous ajoutons un paramètre pour forcer le rafraîchissement
            if ($response->isRedirection()) {
                // Effacer tout message d'erreur existant dans la session si un message de succès est présent
                if (session()->has('success') && session()->has('error')) {
                    session()->forget('error');
                    Log::info('RefreshSoldeCongeData middleware: Message d\'erreur supprimé car succès détecté');
                }
                
                $targetUrl = $response->getTargetUrl();
                if (strpos($targetUrl, '?') !== false) {
                    $targetUrl .= '&refresh_soldes=1';
                } else {
                    $targetUrl .= '?refresh_soldes=1';
                }
                $response->setTargetUrl($targetUrl);
                Log::info('RefreshSoldeCongeData middleware: URL de redirection modifiée', ['url' => $targetUrl]);
            }
        }

        return $response;
    }
}
