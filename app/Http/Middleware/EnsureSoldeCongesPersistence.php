<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureSoldeCongesPersistence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si c'est une requête AJAX pour les soldes de congés
        $isAjaxSoldeRequest = $request->ajax() && $request->is('conges/solde/*') && 
                             ($request->isMethod('PUT') || $request->isMethod('PATCH'));
        
        // Traiter la requête normalement
        $response = $next($request);
        
        // Si c'est une requête AJAX pour les soldes, s'assurer que la réponse est propre
        if ($isAjaxSoldeRequest && $response->headers->get('Content-Type') === 'application/json') {
            // S'assurer qu'il n'y a pas de contenu supplémentaire avant ou après le JSON
            $content = $response->getContent();
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}');
            
            if ($jsonStart !== 0 || $jsonEnd !== strlen($content) - 1) {
                // Il y a du contenu supplémentaire, extraire uniquement le JSON valide
                if ($jsonStart !== false && $jsonEnd !== false) {
                    $cleanJson = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
                    try {
                        // Vérifier que c'est un JSON valide
                        $jsonData = json_decode($cleanJson, true, 512, JSON_THROW_ON_ERROR);
                        // Remplacer le contenu de la réponse par le JSON propre
                        $response->setContent($cleanJson);
                        Log::info('EnsureSoldeCongesPersistence: Nettoyage de la réponse JSON effectué');
                    } catch (\Exception $e) {
                        Log::error('EnsureSoldeCongesPersistence: Erreur lors du nettoyage JSON', [
                            'error' => $e->getMessage(),
                            'content' => substr($content, 0, 500) . '...'
                        ]);
                    }
                }
            }
        }

        // Vérifier si nous sommes dans le contexte d'une mise à jour de solde de congés
        if ($request->is('conges/solde/*') && ($request->isMethod('PUT') || $request->isMethod('PATCH'))) {
            $employeId = intval(basename($request->path()));
            
            // Vérifier si l'employé existe
            if ($employeId > 0) {
                // Vérifier si des soldes ont été soumis dans la requête
                $soldeConges = $request->input('solde_conges');
                $soldeRtt = $request->input('solde_rtt');
                $soldeCongesExceptionnels = $request->input('solde_conges_exceptionnels');
                
                if ($soldeConges !== null) {
                    // Formater correctement les valeurs décimales
                    $soldeConges = number_format((float)$soldeConges, 1, '.', '');
                    $soldeRtt = number_format((float)$soldeRtt, 1, '.', '');
                    $soldeCongesExceptionnels = number_format((float)$soldeCongesExceptionnels, 1, '.', '');
                    
                    Log::info('EnsureSoldeCongesPersistence: Vérification des soldes après traitement', [
                        'employe_id' => $employeId,
                        'solde_conges_soumis' => $soldeConges,
                        'solde_rtt_soumis' => $soldeRtt,
                        'solde_conges_exceptionnels_soumis' => $soldeCongesExceptionnels
                    ]);
                    
                    // Vérifier les valeurs actuelles en base de données
                    $currentValues = DB::select("SELECT solde_conges, solde_rtt, solde_conges_exceptionnels FROM employes WHERE id = ?", [$employeId]);
                    
                    if (!empty($currentValues)) {
                        $currentSoldeConges = $currentValues[0]->solde_conges;
                        $currentSoldeRtt = $currentValues[0]->solde_rtt;
                        $currentSoldeCongesExceptionnels = $currentValues[0]->solde_conges_exceptionnels;
                        
                        Log::info('EnsureSoldeCongesPersistence: Valeurs actuelles en base de données', [
                            'employe_id' => $employeId,
                            'solde_conges_actuel' => $currentSoldeConges,
                            'solde_rtt_actuel' => $currentSoldeRtt,
                            'solde_conges_exceptionnels_actuel' => $currentSoldeCongesExceptionnels
                        ]);
                        
                        // Vérifier si les valeurs sont différentes
                        if ($currentSoldeConges != $soldeConges || $currentSoldeRtt != $soldeRtt || $currentSoldeCongesExceptionnels != $soldeCongesExceptionnels) {
                            Log::warning('EnsureSoldeCongesPersistence: Différence détectée, forçage de la mise à jour', [
                                'employe_id' => $employeId,
                                'solde_conges_diff' => "$currentSoldeConges != $soldeConges",
                                'solde_rtt_diff' => "$currentSoldeRtt != $soldeRtt",
                                'solde_conges_exceptionnels_diff' => "$currentSoldeCongesExceptionnels != $soldeCongesExceptionnels"
                            ]);
                            
                            // Forcer la mise à jour directe en SQL avec des valeurs explicitement typées
                            try {
                                // S'assurer que les valeurs sont bien des décimaux avec 1 chiffre après la virgule
                                $soldeCongesFloat = (float)$soldeConges;
                                $soldeRttFloat = (float)$soldeRtt;
                                $soldeExceptionnelsFloat = (float)$soldeCongesExceptionnels;
                                
                                Log::info('EnsureSoldeCongesPersistence: Valeurs converties pour mise à jour forcée', [
                                    'solde_conges' => $soldeCongesFloat,
                                    'solde_rtt' => $soldeRttFloat,
                                    'solde_conges_exceptionnels' => $soldeExceptionnelsFloat
                                ]);
                                
                                // Utiliser CAST pour s'assurer que MySQL traite les valeurs comme des DECIMAL(5,1)
                                DB::statement(
                                    "UPDATE employes SET solde_conges = CAST(? AS DECIMAL(5,1)), solde_rtt = CAST(? AS DECIMAL(5,1)), solde_conges_exceptionnels = CAST(? AS DECIMAL(5,1)), updated_at = ? WHERE id = ?",
                                    [$soldeCongesFloat, $soldeRttFloat, $soldeExceptionnelsFloat, now(), $employeId]
                                );
                                
                                Log::info('EnsureSoldeCongesPersistence: Mise à jour forcée effectuée');
                            } catch (\Exception $e) {
                                Log::error('EnsureSoldeCongesPersistence: Erreur lors de la mise à jour forcée', [
                                    'message' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                            }
                            
                            // Vérifier que la mise à jour a bien été effectuée
                            $updatedValues = DB::select("SELECT solde_conges, solde_rtt, solde_conges_exceptionnels FROM employes WHERE id = ?", [$employeId]);
                            
                            Log::info('EnsureSoldeCongesPersistence: Valeurs après mise à jour forcée', [
                                'employe_id' => $employeId,
                                'solde_conges_nouveau' => $updatedValues[0]->solde_conges,
                                'solde_rtt_nouveau' => $updatedValues[0]->solde_rtt,
                                'solde_conges_exceptionnels_nouveau' => $updatedValues[0]->solde_conges_exceptionnels
                            ]);
                            
                            // Effacer tout message d'erreur existant dans la session
                            if (session()->has('error')) {
                                session()->forget('error');
                                Log::info('EnsureSoldeCongesPersistence: Message d\'erreur supprimé de la session');
                            }
                        }
                    }
                }
            }
        }

        return $response;
    }
}
