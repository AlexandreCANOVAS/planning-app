<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LieuController extends Controller
{
    /**
     * Récupérer tous les lieux avec leurs coordonnées pour la carte
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $societeId = $user->societe_id;
        
        $lieux = Lieu::deSociete($societeId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'nom', 'adresse', 'ville', 'code_postal', 'couleur', 'latitude', 'longitude']);
        
        return response()->json($lieux);
    }
    
    /**
     * Récupérer les détails d'un lieu spécifique
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $lieu = Lieu::findOrFail($id);
        
        // Vérifier si l'utilisateur a accès à ce lieu
        $user = Auth::user();
        if ($lieu->societe_id !== null && $lieu->societe_id !== $user->societe_id && !$lieu->is_special) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Ajouter les statistiques
        $lieu->employes_aujourdhui = $lieu->getEmployesCountAttribute();
        $lieu->heures_mois = $lieu->plannings()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('heures');
        
        return response()->json($lieu);
    }
}
