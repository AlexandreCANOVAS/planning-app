<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Planning;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    // Méthode pour calculer les heures mensuelles
    private function calculerHeuresMensuel($plannings, $employe, $mois, $annee)
    {
        // Initialiser les totaux
        $totalHeures = 0;
        $totalHeuresSup25 = 0;
        $totalHeuresSup50 = 0;
        $totalHeuresNuit = 0;
        $totalHeuresDimanche = 0;
        $totalHeuresJoursFeries = 0;
        $totalAbsences = 0;
        
        // Log pour débogage
        \Log::info('Début du calcul des heures mensuelles pour ' . $employe->nom . ' ' . $employe->prenom);
        \Log::info('Nombre de plannings: ' . $plannings->count());
        
        // Regrouper les plannings par semaine
        $planningsParSemaine = $plannings->groupBy(function ($planning) {
            return \Carbon\Carbon::parse($planning->date)->weekOfYear;
        });
        
        // Calculer les heures pour chaque semaine
        foreach ($planningsParSemaine as $semaine => $planningsSemaine) {
            $heuresSemaine = 0;
            $heuresNuitSemaine = 0;
            $heuresDimancheSemaine = 0;
            $heuresJoursFeriesSemaine = 0;
            $absencesSemaine = 0;
            
            // Déterminer le premier et dernier jour de la semaine
            $premierPlanning = $planningsSemaine->sortBy('date')->first();
            $debutSemaine = \Carbon\Carbon::parse($premierPlanning->date)->startOfWeek();
            $finSemaine = $debutSemaine->copy()->endOfWeek();
            
            // Pour le mois de janvier, on ne compte pas les absences
            // Pour les autres mois, on utilise une logique plus précise
            $moisActuel = $debutSemaine->format('m');
            
            if ($moisActuel == '01') { // Janvier
                $absencesSemaine = 0;
            } else {
                // Vérifier les jours où l'employé est censé travailler
                // On se base sur les plannings des semaines précédentes pour déterminer les jours habituels de travail
                
                // Pour simplifier, on considère comme jours de travail habituels les jours
                // où l'employé a travaillé au moins une fois dans le mois
                $joursHabituels = [];
                
                // Récupérer tous les plannings du mois pour cet employé
                foreach ($plannings as $planning) {
                    $jourSemaine = \Carbon\Carbon::parse($planning->date)->dayOfWeek;
                    $joursHabituels[$jourSemaine] = true;
                }
                
                // Calculer les jours où l'employé aurait dû travailler cette semaine
                $joursSemaine = [];
                $currentDay = $debutSemaine->copy();
                while ($currentDay <= $finSemaine) {
                    $jourSemaine = $currentDay->dayOfWeek;
                    // On ne compte que les jours où l'employé travaille habituellement
                    if (isset($joursHabituels[$jourSemaine])) {
                        $joursSemaine[] = $currentDay->format('Y-m-d');
                    }
                    $currentDay->addDay();
                }
                
                // Jours avec plannings
                $joursAvecPlannings = $planningsSemaine->pluck('date')->toArray();
                
                // Jours d'absence = jours où l'employé aurait dû travailler mais n'a pas de planning
                $joursAbsence = array_diff($joursSemaine, $joursAvecPlannings);
                $absencesSemaine = count($joursAbsence);
            }
            
            foreach ($planningsSemaine as $planning) {
                $heuresTravaillees = $planning->heures_travaillees;
                $heuresSemaine += $heuresTravaillees;
                
                $datePlanning = \Carbon\Carbon::parse($planning->date);
                
                // Calculer les heures de nuit (21h-06h)
                if (!empty($planning->heure_debut) && !empty($planning->heure_fin)) {
                    // Vérifier si le lieu existe et n'est pas RH ou CP
                    $lieuExclu = false;
                    if ($planning->lieu && in_array($planning->lieu->nom, ['RH', 'CP'])) {
                        $lieuExclu = true;
                    }
                    
                    if (!$lieuExclu) {
                        // Approche simplifiée pour le calcul des heures de nuit
                        $heureDebut = (int)\Carbon\Carbon::parse($planning->heure_debut)->format('H');
                        $heureFin = (int)\Carbon\Carbon::parse($planning->heure_fin)->format('H');
                        
                        // Si l'heure de fin est inférieure à l'heure de début, cela signifie que le service se termine le lendemain
                        if ($heureFin < $heureDebut) {
                            $heureFin += 24;
                        }
                        
                        // Calculer les heures de nuit
                        $heuresNuit = 0;
                        
                        // Cas 1: Service commence avant 21h et finit après 21h
                        if ($heureDebut < 21 && $heureFin > 21) {
                            // Heures de nuit = de 21h jusqu'à la fin du service (ou 6h max)
                            $heuresNuit += min($heureFin, 30) - 21; // 30 = 6h + 24h
                        }
                        
                        // Cas 2: Service commence après 21h ou avant 6h
                        if ($heureDebut >= 21 || $heureDebut < 6) {
                            $debutCalcul = $heureDebut;
                            $finCalcul = min($heureFin, 30); // 30 = 6h + 24h
                            
                            if ($debutCalcul >= 21) {
                                // Service de nuit qui commence après 21h
                                $heuresNuit += $finCalcul - $debutCalcul;
                            } else {
                                // Service de nuit qui commence avant 6h
                                $heuresNuit += min($finCalcul, 6) - $debutCalcul;
                            }
                        }
                        
                        // Log pour débogage
                        \Log::info('Planning du ' . $planning->date . ' - Heures de nuit: ' . $heuresNuit . ' (début: ' . $heureDebut . 'h, fin: ' . $heureFin . 'h)');
                        
                        $heuresNuitSemaine += $heuresNuit;
                    }
                }
                
                // Vérifier si c'est un dimanche
                if ($datePlanning->isDayOfWeek(0)) {
                    $heuresDimancheSemaine += $heuresTravaillees;
                }
                
                // Vérifier si c'est un jour férié
                if ($this->estJourFerie($datePlanning)) {
                    $heuresJoursFeriesSemaine += $heuresTravaillees;
                }
            }
            
            // Calculer les heures supplémentaires de la semaine
            $heuresSup25 = 0;
            $heuresSup50 = 0;
            
            if ($heuresSemaine > 35) {
                if ($heuresSemaine <= 43) {
                    $heuresSup25 = $heuresSemaine - 35;
                } else {
                    $heuresSup25 = 8; // 43 - 35 = 8 heures à 25%
                    $heuresSup50 = $heuresSemaine - 43;
                }
            }
            
            // Ajouter au total du mois
            $totalHeures += $heuresSemaine;
            $totalHeuresSup25 += $heuresSup25;
            $totalHeuresSup50 += $heuresSup50;
            $totalHeuresNuit += $heuresNuitSemaine;
            $totalHeuresDimanche += $heuresDimancheSemaine;
            $totalHeuresJoursFeries += $heuresJoursFeriesSemaine;
            $totalAbsences += $absencesSemaine;
            
            // Log pour débogage
            \Log::info('Semaine ' . $semaine . ' - Heures de nuit: ' . $heuresNuitSemaine . ' - Total cumulé: ' . $totalHeuresNuit);
        }
        
        // Log pour débogage final
        \Log::info('Récapitulatif mensuel:');
        \Log::info('- Total heures: ' . $totalHeures);
        \Log::info('- Heures sup 25%: ' . $totalHeuresSup25);
        \Log::info('- Heures sup 50%: ' . $totalHeuresSup50);
        \Log::info('- Heures de nuit: ' . $totalHeuresNuit);
        \Log::info('- Heures dimanche: ' . $totalHeuresDimanche);
        \Log::info('- Heures jours fériés: ' . $totalHeuresJoursFeries);
        \Log::info('- Absences: ' . $totalAbsences);
        
        // Calculer les montants si le taux horaire est disponible
        $tauxHoraire = $employe->taux_horaire ?? 0;
        $montant25 = $totalHeuresSup25 * $tauxHoraire * 1.25;
        $montant50 = $totalHeuresSup50 * $tauxHoraire * 1.5;
        $montantNuit = $totalHeuresNuit * $tauxHoraire * 1.1; // Majoration de 10% pour les heures de nuit
        $montantDimanche = $totalHeuresDimanche * $tauxHoraire * 1.5; // Majoration de 50% pour les dimanches
        $montantJoursFeries = $totalHeuresJoursFeries * $tauxHoraire * 2; // Majoration de 100% pour les jours fériés
        $montantTotal = $montant25 + $montant50 + $montantNuit + $montantDimanche + $montantJoursFeries;
        
        return [
            'total_heures' => $totalHeures,
            'heures_25' => $totalHeuresSup25,
            'heures_50' => $totalHeuresSup50,
            'heures_nuit' => $totalHeuresNuit,
            'heures_dimanche' => $totalHeuresDimanche,
            'heures_jours_feries' => $totalHeuresJoursFeries,
            'absences' => $totalAbsences,
            'taux_horaire' => $tauxHoraire,
            'montant_25' => $montant25,
            'montant_50' => $montant50,
            'montant_nuit' => $montantNuit,
            'montant_dimanche' => $montantDimanche,
            'montant_jours_feries' => $montantJoursFeries,
            'montant_total' => $montantTotal,
        ];
    }
}
