<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{
    /**
     * Récupère les soldes de congés de l'employé connecté
     */
    public function getSoldesConges()
    {
        try {
            $user = Auth::user();
            $employe = $user->employe;
            
            if (!$employe) {
                return response()->json([
                    'error' => 'Aucun employé associé à cet utilisateur'
                ], 404);
            }
            
            return response()->json([
                'solde_conges' => $employe->solde_conges,
                'solde_rtt' => $employe->solde_rtt,
                'solde_conges_exceptionnels' => $employe->solde_conges_exceptionnels,
                'updated_at' => $employe->updated_at->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération des soldes de congés',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
