<?php

namespace App\Services;

use App\Models\Planning;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class PlanningPdfService
{
    /**
     * Génère un PDF du planning pour un employé
     *
     * @param Planning $planning
     * @param User $user
     * @return string Chemin du fichier PDF généré
     */
    public function generatePlanningPdf(Planning $planning, User $user)
    {
        // Récupérer tous les plannings de l'employé pour la même semaine
        $date = \Carbon\Carbon::parse($planning->date);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        
        $plannings = Planning::where('employe_id', $planning->employe_id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
        
        // Générer le HTML du PDF
        $html = View::make('pdf.planning', [
            'plannings' => $plannings,
            'employee' => $user,
            'weekStart' => $startOfWeek,
            'weekEnd' => $endOfWeek
        ])->render();
        
        // Créer le répertoire de stockage si nécessaire
        $directory = storage_path('app/public/pdf/plannings');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Générer un nom de fichier unique
        $filename = 'planning_' . $user->id . '_' . $planning->employe_id . '_' . time() . '.html';
        $filepath = $directory . '/' . $filename;
        
        // Enregistrer le HTML dans un fichier (pour le moment, nous utilisons HTML au lieu de PDF)
        file_put_contents($filepath, $html);
        
        return $filepath;
    }
}
