<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComptabiliteController extends Controller
{
    public function calcul(Request $request)
    {
        try {
            $request->validate([
                'employe_id' => 'required|exists:employes,id',
                'mois' => 'required|date_format:Y-m'
            ]);

            $employe_id = $request->input('employe_id');
            $mois = $request->input('mois');

            // Convertir le mois au format date
            $date = Carbon::createFromFormat('Y-m', $mois);
            $date_debut = $date->copy()->startOfMonth();
            $date_fin = $date->copy()->endOfMonth();

            \Log::info('Calcul comptabilité', [
                'employe_id' => $employe_id,
                'mois' => $mois,
                'date_debut' => $date_debut->locale('fr')->isoFormat('D MMMM YYYY'),
                'date_fin' => $date_fin->locale('fr')->isoFormat('D MMMM YYYY')
            ]);

            // Récupérer l'employé avec ses plannings pour le mois
            $employe = Employe::with(['plannings' => function($query) use ($date_debut, $date_fin) {
                $query->whereBetween('date', [$date_debut, $date_fin])
                    ->orderBy('date', 'asc');
            }])->findOrFail($employe_id);

            // Grouper les plannings par semaine
            $planningsParSemaine = $employe->plannings->groupBy(function($planning) {
                return Carbon::parse($planning->date)->startOfWeek()->format('Y-m-d');
            });

            $semaines = [];

            // Parcourir toutes les semaines du mois
            $currentDate = $date_debut->copy()->startOfWeek();
            $lastDate = $date_fin->copy()->endOfWeek();

            while ($currentDate <= $lastDate) {
                $debutSemaine = $currentDate->copy()->format('Y-m-d');
                $planningsSemaine = $planningsParSemaine->get($debutSemaine, collect([]));
                
                // Log les plannings de la semaine
                \Log::info("Plannings semaine $debutSemaine", [
                    'plannings' => $planningsSemaine->map(function($p) {
                        return [
                            'date' => $p->date->locale('fr')->isoFormat('D MMMM YYYY'),
                            'debut' => $p->heure_debut->format('H:i'),
                            'fin' => $p->heure_fin->format('H:i'),
                            'heures' => $p->heures_travaillees
                        ];
                    })
                ]);

                // Calculer le total des heures de la semaine
                $totalHeures = 0;
                foreach ($planningsSemaine as $planning) {
                    // Créer la date de début
                    $debut = Carbon::parse($planning->date)->setTimeFromTimeString($planning->heure_debut->format('H:i:s'));
                    
                    // Créer la date de fin
                    $fin = Carbon::parse($planning->date)->setTimeFromTimeString($planning->heure_fin->format('H:i:s'));
                    
                    // Si l'heure de fin est avant l'heure de début, on ajoute 24h
                    if ($fin < $debut) {
                        $fin->addDay();
                    }
                    
                    $heures = $debut->diffInMinutes($fin) / 60;
                    $totalHeures += $heures;
                    
                    \Log::info("Heures planning", [
                        'date' => $planning->date->locale('fr')->isoFormat('D MMMM YYYY'),
                        'heures' => $heures,
                        'total_cumule' => $totalHeures
                    ]);
                }
                
                // Calculer les heures supplémentaires
                $heuresSupp25 = $totalHeures > 35 ? min($totalHeures - 35, 8) : 0;
                $heuresSupp50 = $totalHeures > 43 ? $totalHeures - 43 : 0;
                
                \Log::info("Calcul heures sup", [
                    'semaine' => $debutSemaine,
                    'total' => $totalHeures,
                    'sup25' => $heuresSupp25,
                    'sup50' => $heuresSupp50
                ]);

                $finSemaine = $currentDate->copy()->endOfWeek();
                
                // Formater les dates en français
                $debutFormate = Carbon::parse($debutSemaine)->locale('fr')->isoFormat('D MMMM YYYY');
                $finFormate = $finSemaine->locale('fr')->isoFormat('D MMMM YYYY');
                
                $semaines[] = [
                    'semaine' => Carbon::parse($debutSemaine)->weekOfYear,
                    'periode' => $debutFormate . ' au ' . $finFormate,
                    'total_heures' => number_format($totalHeures, 2),
                    'heures_sup_25' => number_format($heuresSupp25, 2),
                    'heures_sup_50' => number_format($heuresSupp50, 2)
                ];

                $currentDate->addWeek();
            }

            return response()->json([
                'success' => true,
                'semaines' => $semaines
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur calcul comptabilité: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
