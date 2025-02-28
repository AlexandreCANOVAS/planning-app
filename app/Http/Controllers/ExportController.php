<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function exportPlannings(Request $request)
    {
        $employe_id = $request->input('employe_id');
        $mois = $request->input('mois');

        \Log::info('Début export plannings', [
            'mois_recu' => $mois,
            'employe_id' => $employe_id,
            'user_id' => auth()->id(),
            'societe_id' => auth()->user()->societe_id
        ]);

        // Gérer le format "YYYY-MM"
        try {
            if (preg_match('/^\d{4}-\d{2}$/', $mois)) {
                $date = \Carbon\Carbon::createFromFormat('Y-m', $mois);
            } else {
                $date = \Carbon\Carbon::parse($mois);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur de parsing de date', [
                'mois' => $mois,
                'error' => $e->getMessage()
            ]);
            $date = now();
        }

        $date_debut = $date->copy()->startOfMonth()->format('Y-m-d');
        $date_fin = $date->copy()->endOfMonth()->format('Y-m-d');

        // Récupérer d'abord les plannings pour le mois
        $plannings = \App\Models\Planning::with(['employe', 'lieu'])
            ->where('societe_id', auth()->user()->societe_id)
            ->whereBetween('date', [$date_debut, $date_fin]);

        if ($employe_id) {
            $plannings->where('employe_id', $employe_id);
        }

        $plannings = $plannings->orderBy('date', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

        // Grouper les plannings par employé
        $planningsParEmploye = $plannings->groupBy('employe_id');
        
        // Récupérer les employés concernés
        $employes = \App\Models\Employe::whereIn('id', $planningsParEmploye->keys())
            ->where('societe_id', auth()->user()->societe_id)
            ->get()
            ->map(function($employe) use ($planningsParEmploye) {
                // Attacher les plannings à chaque employé
                $employe->setRelation('plannings', $planningsParEmploye[$employe->id]);
                return $employe;
            });

        \Log::info('Données récupérées', [
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'nb_employes' => $employes->count(),
            'nb_plannings' => $plannings->count(),
            'employes_ids' => $employes->pluck('id'),
            'plannings_dates' => $plannings->pluck('date')->unique()->sort()->values()
        ]);

        $pdf = PDF::loadView('exports.plannings', [
            'employes' => $employes,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'societe' => Auth::user()->societe,
            'mois' => $date->locale('fr')->isoFormat('MMMM YYYY')
        ]);

        $filename = 'planning';
        if ($employe_id) {
            $employe = $employes->first();
            if ($employe) {
                $filename .= '-' . Str::slug($employe->nom . '-' . $employe->prenom);
            }
        }
        $filename .= '-' . $date->format('m-Y') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportCompta(Request $request)
    {
        $mois = $request->input('mois', now()->format('Y-m'));
        $employe_id = $request->input('employe_id');
        $societe = Auth::user()->societe;
        
        // Récupérer les données pour le mois sélectionné
        $debut_mois = \Carbon\Carbon::createFromFormat('Y-m', $mois)->startOfMonth();
        $fin_mois = \Carbon\Carbon::createFromFormat('Y-m', $mois)->endOfMonth();
        
        $query = Planning::where('societe_id', $societe->id)
            ->whereBetween('date', [$debut_mois, $fin_mois]);

        // Filtrer par employé si spécifié
        if ($employe_id) {
            $query->where('employe_id', $employe_id);
        }

        $plannings = $query->with(['employe', 'lieu'])->get();

        $pdf = PDF::loadView('exports.compta', [
            'plannings' => $plannings,
            'societe' => $societe,
            'mois' => $mois,
            'employe_id' => $employe_id
        ]);

        $filename = 'comptabilite';
        if ($employe_id) {
            $employe = $plannings->first()->employe;
            $filename .= '-' . Str::slug($employe->nom . '-' . $employe->prenom);
        }
        $filename .= '-' . $mois . '.pdf';

        return $pdf->download($filename);
    }
} 