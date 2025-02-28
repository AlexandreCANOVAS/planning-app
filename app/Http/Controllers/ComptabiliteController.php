<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComptabiliteController extends Controller
{
    public function index()
    {
        // Récupérer les employés de la société de l'utilisateur connecté
        $employes = Employe::where('societe_id', Auth::user()->societe_id)
            ->with('user')
            ->get();
            
        $moisActuel = Carbon::now()->format('Y-m');
        return view('comptabilite.index', compact('employes', 'moisActuel'));
    }

    public function calculerHeures(Request $request)
    {
        try {
            $request->validate([
                'employe_id' => 'required|exists:employes,id',
                'mois' => 'required|date_format:Y-m'
            ]);

            $employeId = $request->employe_id;
            $mois = $request->mois;

            // Convertir le mois en objet Carbon pour les comparaisons
            $dateDebut = Carbon::createFromFormat('Y-m', $mois)->startOfMonth();
            $dateFin = Carbon::createFromFormat('Y-m', $mois)->endOfMonth();

            // Récupérer tous les plannings du mois
            $plannings = Planning::where('employe_id', $employeId)
                ->whereBetween('date', [$dateDebut, $dateFin])
                ->orderBy('date')
                ->get();

            // Initialiser les totaux du mois
            $totalHeuresMois = 0;
            $totalHeuresSup25 = 0;
            $totalHeuresSup50 = 0;

            // Récupérer les plannings par semaine pour l'affichage
            $resultatsSemaines = [];
            $date = $dateDebut->copy()->startOfWeek(Carbon::MONDAY);
            $finMoisEtSemaine = $dateFin->copy()->endOfWeek(Carbon::SUNDAY);

            while ($date <= $finMoisEtSemaine) {
                $debutSemaine = $date->copy()->startOfWeek(Carbon::MONDAY);
                $finSemaine = $debutSemaine->copy()->endOfWeek(Carbon::SUNDAY);

                // Calculer les heures de la semaine
                $heuresSemaine = 0;
                $planningsSemaine = $plannings->filter(function($planning) use ($debutSemaine, $finSemaine) {
                    $datePlanning = Carbon::parse($planning->date);
                    return $datePlanning >= $debutSemaine && $datePlanning <= $finSemaine;
                });

                foreach ($planningsSemaine as $planning) {
                    $heuresSemaine += abs($this->convertHHMMToFloat($planning->heures_travaillees));
                }

                // Calculer les heures supplémentaires de la semaine
                $heuresSup25 = 0;
                $heuresSup50 = 0;

                if ($heuresSemaine > 35) {
                    if ($heuresSemaine <= 43) {
                        $heuresSup25 = $heuresSemaine - 35;
                    } else {
                        $heuresSup25 = 8; // de 36h à 43h
                        $heuresSup50 = $heuresSemaine - 43; // au-delà de 44h
                    }
                }

                // Ajouter au total du mois
                if ($debutSemaine->month === $dateDebut->month || $finSemaine->month === $dateDebut->month) {
                    $totalHeuresMois += $heuresSemaine;
                    $totalHeuresSup25 += $heuresSup25;
                    $totalHeuresSup50 += $heuresSup50;
                }

                // Ajouter la semaine aux résultats si elle fait partie du mois
                if ($debutSemaine->month === $dateDebut->month || $finSemaine->month === $dateDebut->month) {
                    $resultatsSemaines[] = [
                        'periode' => $debutSemaine->format('d') . ' au ' . $finSemaine->format('d/m/Y'),
                        'total_heures' => $this->convertToHHMM($heuresSemaine),
                        'heures_sup_25' => $this->convertToHHMM($heuresSup25),
                        'heures_sup_50' => $this->convertToHHMM($heuresSup50)
                    ];
                }

                $date->addWeek();
            }

            return response()->json([
                'semaines' => $resultatsSemaines,
                'total_mois' => [
                    'heures' => $this->convertToHHMM($totalHeuresMois),
                    'heures_sup_25' => $this->convertToHHMM($totalHeuresSup25),
                    'heures_sup_50' => $this->convertToHHMM($totalHeuresSup50)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors du calcul des heures : ' . $e->getMessage()
            ], 500);
        }
    }

    private function convertToHHMM($decimal)
    {
        // Enlever le signe négatif pour l'affichage
        $decimal = abs($decimal);
        
        $hours = floor($decimal);
        $minutes = round(($decimal - $hours) * 60);
        
        // Si les minutes sont 60, ajuster les heures
        if ($minutes == 60) {
            $hours++;
            $minutes = 0;
        }
        
        return sprintf("%02d:%02d", $hours, $minutes);
    }

    private function convertHHMMToFloat($heures)
    {
        if (strpos($heures, ':') === false) {
            return floatval($heures);
        }
        
        list($h, $m) = explode(':', $heures);
        return intval($h) + (intval($m) / 60);
    }

    private function calculerHeuresPourPlanning($planning)
    {
        $heuresNormales = $planning->heures_travaillees;
        $heuresDimanche = 0;
        $heuresJoursFeries = 0;
        $heuresNuit = 0;

        $date = Carbon::parse($planning->date);

        // Vérifier si c'est un dimanche
        if ($date->isDayOfWeek(Carbon::SUNDAY)) {
            $heuresDimanche = $heuresNormales;
            $heuresNormales = 0;
        }
        // Vérifier si c'est un jour férié
        else if ($this->estJourFerie($date)) {
            $heuresJoursFeries = $heuresNormales;
            $heuresNormales = 0;
        }

        return [
            'normales' => $heuresNormales,
            'dimanche' => $heuresDimanche,
            'joursFeries' => $heuresJoursFeries,
            'nuit' => $heuresNuit
        ];
    }

    private function calculerHeuresDeNuit($heureDebut, $heureFin)
    {
        $debutNuit = Carbon::parse('21:00');
        $finNuit = Carbon::parse('06:00')->addDay();
        
        $heuresNuit = 0;
        
        // Si le planning chevauche la période de nuit
        if ($heureDebut->lt($finNuit) || $heureFin->gt($debutNuit)) {
            // Calculer l'intersection avec la période de nuit
            $debutPeriode = max($heureDebut, $debutNuit);
            $finPeriode = min($heureFin, $finNuit);
            
            if ($debutPeriode->lt($finPeriode)) {
                $heuresNuit = $debutPeriode->floatDiffInHours($finPeriode);
            }
        }
        
        return $heuresNuit;
    }

    private function estJourFerie($date)
    {
        // Liste des jours fériés en France pour l'année en cours
        $joursFeries = [
            '01-01', // Jour de l'an
            '05-01', // Fête du travail
            '05-08', // Victoire 1945
            '07-14', // Fête nationale
            '08-15', // Assomption
            '11-01', // Toussaint
            '11-11', // Armistice
            '12-25', // Noël,
        ];

        return in_array($date->format('m-d'), $joursFeries);
    }
}
