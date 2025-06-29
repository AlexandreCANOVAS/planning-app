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
    
    public function exportComptabilite(Request $request)
    {
        $mois = $request->input('mois', now()->format('Y-m'));
        $employe_id = $request->input('employe_id');
        $societe = Auth::user()->societe;
        
        // Vérifier si l'employé existe et appartient à la société de l'utilisateur
        if ($employe_id) {
            $employe = \App\Models\Employe::where('id', $employe_id)
                ->where('societe_id', $societe->id)
                ->with('user')
                ->first();
                
            if (!$employe) {
                return back()->with('error', 'Employé non trouvé.');
            }
            
            $nomEmploye = $employe->prenom . ' ' . $employe->nom;
        } else {
            return back()->with('error', 'Veuillez sélectionner un employé.');
        }
        
        // Convertir le mois en objet Carbon pour les comparaisons
        $dateDebut = \Carbon\Carbon::createFromFormat('Y-m', $mois)->startOfMonth();
        $dateFin = \Carbon\Carbon::createFromFormat('Y-m', $mois)->endOfMonth();
        
        // Récupérer tous les plannings du mois pour l'employé
        $plannings = Planning::where('employe_id', $employe_id)
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->with('lieu')
            ->orderBy('date')
            ->get();
            
        // Initialiser les totaux du mois
        $totalHeuresMois = 0;
        $totalHeuresSup25 = 0;
        $totalHeuresSup50 = 0;
        $totalHeuresNuit = 0;
        $totalHeuresDimanche = 0;
        $totalHeuresJoursFeries = 0;
        $totalAbsences = 0;
        
        // Récupérer les plannings par semaine pour l'affichage
        $detailHeuresSupp = [];
        $date = $dateDebut->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $finMoisEtSemaine = $dateFin->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
        $semaine = 1; // Initialisation du compteur de semaines
        
        while ($date <= $finMoisEtSemaine) {
            $debutSemaine = $date->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $finSemaine = $debutSemaine->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
            
            // Calculer les heures de la semaine
            $heuresSemaine = 0;
            $heuresNuitSemaine = 0;
            $heuresDimancheSemaine = 0;
            $heuresJoursFeriesSemaine = 0;
            $absencesSemaine = 0;
            
            $planningsSemaine = $plannings->filter(function($planning) use ($debutSemaine, $finSemaine) {
                $datePlanning = \Carbon\Carbon::parse($planning->date);
                return $datePlanning >= $debutSemaine && $datePlanning <= $finSemaine;
            });
            
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
                    $heureDebut = \Carbon\Carbon::parse($planning->heure_debut);
                    $heureFin = \Carbon\Carbon::parse($planning->heure_fin);
                    
                    // Convertir en minutes depuis minuit pour faciliter les calculs
                    $debutMinutes = $heureDebut->hour * 60 + $heureDebut->minute;
                    $finMinutes = $heureFin->hour * 60 + $heureFin->minute;
                    
                    // Si l'heure de fin est avant l'heure de début, on ajoute 24h (passage à minuit)
                    if ($finMinutes < $debutMinutes) {
                        $finMinutes += 24 * 60;
                    }
                    
                    // Définir les plages de nuit en minutes (21h-06h)
                    $debutNuit = 21 * 60; // 21h00
                    $finNuit = 6 * 60;    // 06h00
                    $finNuitAjuste = $finNuit + 24 * 60; // 06h00 le lendemain
                    
                    $heuresNuit = 0;
                    
                    // Cas 1: Le shift commence avant 21h et finit après 21h mais avant 6h du matin
                    if ($debutMinutes < $debutNuit && $finMinutes > $debutNuit && $finMinutes <= $finNuitAjuste) {
                        $heuresNuit = ($finMinutes - $debutNuit) / 60;
                    }
                    // Cas 2: Le shift commence après 21h et finit avant 6h du matin
                    else if ($debutMinutes >= $debutNuit && $finMinutes <= $finNuitAjuste) {
                        $heuresNuit = ($finMinutes - $debutMinutes) / 60;
                    }
                    // Cas 3: Le shift commence après 21h et finit après 6h du matin
                    else if ($debutMinutes >= $debutNuit && $debutMinutes < 24 * 60 && $finMinutes > $finNuitAjuste) {
                        $heuresNuit = ((24 * 60) - $debutMinutes + $finNuit) / 60;
                    }
                    // Cas 4: Le shift commence avant 6h du matin et finit après 6h du matin
                    else if ($debutMinutes < $finNuit && $finMinutes > $finNuit) {
                        $heuresNuit = ($finNuit - $debutMinutes) / 60;
                    }
                    // Cas 5: Le shift commence avant 21h et finit après 6h du matin le lendemain
                    else if ($debutMinutes < $debutNuit && $finMinutes > $finNuitAjuste) {
                        $heuresNuit = ((24 * 60) - $debutNuit + $finNuit) / 60;
                    }
                    
                    // Ajouter les heures de nuit calculées
                    $heuresNuitSemaine += $heuresNuit;
                    
                    // Log pour débogage
                    \Log::info('Calcul heures de nuit', [
                        'date' => $planning->date,
                        'debut' => $planning->heure_debut,
                        'fin' => $planning->heure_fin,
                        'heures_nuit' => $heuresNuit
                    ]);
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
                    $heuresSup25 = 8; // de 36h à 43h
                    $heuresSup50 = $heuresSemaine - 43; // au-delà de 44h
                }
            }
            
            // Ajouter au total du mois si la semaine fait partie du mois
            if ($debutSemaine->month === $dateDebut->month || $finSemaine->month === $dateDebut->month) {
                $totalHeuresMois += $heuresSemaine;
                $totalHeuresSup25 += $heuresSup25;
                $totalHeuresSup50 += $heuresSup50;
                $totalHeuresNuit += $heuresNuitSemaine;
                $totalHeuresDimanche += $heuresDimancheSemaine;
                $totalAbsences += $absencesSemaine;
                
                // Log pour débogage
                \Log::info('Semaine ' . $semaine . ' - Heures de nuit: ' . $heuresNuitSemaine . ' - Total cumulé: ' . $totalHeuresNuit);
            }
            
            // Ajouter la semaine aux résultats si elle fait partie du mois
            if ($debutSemaine->month === $dateDebut->month || $finSemaine->month === $dateDebut->month) {
                $detailHeuresSupp[] = [
                    'semaine' => $debutSemaine->format('d') . ' au ' . $finSemaine->format('d/m/Y'),
                    'heures_travaillees' => $heuresSemaine,
                    'heures_25' => $heuresSup25,
                    'heures_50' => $heuresSup50,
                    'total_heures_supp' => $heuresSup25 + $heuresSup50,
                    'heures_nuit' => $heuresNuitSemaine,
                    'heures_dimanche' => $heuresDimancheSemaine,
                    'heures_jours_feries' => $heuresJoursFeriesSemaine,
                    'absences' => $absencesSemaine
                ];
                
                // Ajouter au total mensuel
                $totalHeuresJoursFeries += $heuresJoursFeriesSemaine;
            }
            
            $date->addWeek();
            $semaine++; // Incrémenter le compteur de semaines
        }
        
        // Calculer les montants si le taux horaire est disponible
        $tauxHoraire = $employe->taux_horaire ?? 0;
        $montant25 = $totalHeuresSup25 * $tauxHoraire * 1.25;
        $montant50 = $totalHeuresSup50 * $tauxHoraire * 1.5;
        $montantNuit = $totalHeuresNuit * $tauxHoraire * 1.1; // Majoration de 10% pour les heures de nuit
        $montantDimanche = $totalHeuresDimanche * $tauxHoraire * 1.5; // Majoration de 50% pour les dimanches
        $montantJoursFeries = $totalHeuresJoursFeries * $tauxHoraire * 2; // Majoration de 100% pour les jours fériés
        $montantTotal = $montant25 + $montant50 + $montantNuit + $montantDimanche + $montantJoursFeries;
        
        // Générer le PDF avec les données calculées
        $nomEmploye = $employe->prenom . ' ' . $employe->nom;
        $date = \Carbon\Carbon::createFromFormat('Y-m', $mois);
        $annee = $date->format('Y');
        $mois = $date->locale('fr')->isoFormat('MMMM');
        
        // Créer le récapitulatif mensuel pour le template
        $recapMensuel = [
            'total_heures' => $totalHeuresMois,
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
        
        // Préparer les données pour les graphiques
        $dataGraphique = [
            'labels' => ['Heures normales', 'Heures sup. 25%', 'Heures sup. 50%', 'Heures de nuit', 'Heures dimanche', 'Heures jours fériés'],
            'data' => [
                $totalHeuresMois - $totalHeuresSup25 - $totalHeuresSup50 - $totalHeuresNuit - $totalHeuresDimanche - $totalHeuresJoursFeries,
                $totalHeuresSup25,
                $totalHeuresSup50,
                $totalHeuresNuit,
                $totalHeuresDimanche,
                $totalHeuresJoursFeries
            ],
            'backgroundColor' => [
                'rgba(75, 192, 192, 0.6)',  // Heures normales - turquoise
                'rgba(255, 159, 64, 0.6)', // Heures sup 25% - orange
                'rgba(255, 99, 132, 0.6)', // Heures sup 50% - rouge
                'rgba(54, 162, 235, 0.6)', // Heures de nuit - bleu
                'rgba(153, 102, 255, 0.6)', // Heures dimanche - violet
                'rgba(255, 0, 0, 0.6)'     // Heures jours fériés - rouge vif
            ]
        ];
        
        // Convertir les heures en format HH:MM pour l'affichage
        $totalHeuresMoisFormatted = $this->convertToHHMM($totalHeuresMois);
        $totalHeuresSup25Formatted = $this->convertToHHMM($totalHeuresSup25);
        $totalHeuresSup50Formatted = $this->convertToHHMM($totalHeuresSup50);
        $totalHeuresNuitFormatted = $this->convertToHHMM($totalHeuresNuit);
        $totalHeuresDimancheFormatted = $this->convertToHHMM($totalHeuresDimanche);
        $totalHeuresJoursFeriesFormatted = $this->convertToHHMM($totalHeuresJoursFeries);
        
        // Formater les résultats par semaine pour l'affichage
        $resultatsSemaines = [];
        foreach ($detailHeuresSupp as $semaine) {
            $resultatsSemaines[] = [
                'periode' => $semaine['semaine'],
                'total_heures' => $this->convertToHHMM($semaine['heures_travaillees']),
                'heures_sup_25' => $this->convertToHHMM($semaine['heures_25']),
                'heures_sup_50' => $this->convertToHHMM($semaine['heures_50']),
                'heures_nuit' => $this->convertToHHMM($semaine['heures_nuit']),
                'heures_dimanche' => $this->convertToHHMM($semaine['heures_dimanche']),
                'heures_jours_feries' => $this->convertToHHMM($semaine['heures_jours_feries']),
                'absences' => $semaine['absences']
            ];
        }
        
        $pdf = PDF::loadView('comptabilite.pdf', [
            'employe' => $employe,
            'societe' => $societe,
            'mois' => $mois,
            'annee' => $annee,
            'nomEmploye' => $nomEmploye,
            'detailHeuresSupp' => $detailHeuresSupp,
            'resultatsSemaines' => $resultatsSemaines,
            'recapMensuel' => $recapMensuel,
            'plannings' => $plannings,
            'total_heures' => $totalHeuresMois,
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
            'total_mois' => [
                'heures' => $totalHeuresMoisFormatted,
                'heures_sup_25' => $totalHeuresSup25Formatted,
                'heures_sup_50' => $totalHeuresSup50Formatted,
                'heures_nuit' => $totalHeuresNuitFormatted,
                'heures_dimanche' => $totalHeuresDimancheFormatted,
                'heures_jours_feries' => $totalHeuresJoursFeriesFormatted,
                'absences' => $totalAbsences
            ],
            'graphique' => $dataGraphique,
        ]);
        
        $filename = 'comptabilite';
        if ($employe_id) {
            $filename .= '-' . Str::slug($employe->nom . '-' . $employe->prenom);
        }
        // Utiliser le format original pour le nom du fichier
        $moisFichier = \Carbon\Carbon::createFromFormat('Y-m', $request->input('mois'))->format('m-Y');
        $filename .= '-' . $moisFichier . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function exportComptabiliteExcel(Request $request)
    {
        $mois = $request->input('mois', now()->format('Y-m'));
        $employe_id = $request->input('employe_id');
        $societe = Auth::user()->societe;
        
        // Vérifier si l'employé existe et appartient à la société de l'utilisateur
        if ($employe_id) {
            $employe = \App\Models\Employe::where('id', $employe_id)
                ->where('societe_id', $societe->id)
                ->with('user')
                ->first();
                
            if (!$employe) {
                return back()->with('error', 'Employé non trouvé.');
            }
            
            $nomEmploye = $employe->prenom . ' ' . $employe->nom;
        } else {
            return back()->with('error', 'Veuillez sélectionner un employé.');
        }
        
        // Convertir le mois en objet Carbon pour les comparaisons
        $dateDebut = \Carbon\Carbon::createFromFormat('Y-m', $mois)->startOfMonth();
        $dateFin = \Carbon\Carbon::createFromFormat('Y-m', $mois)->endOfMonth();
        
        // Récupérer tous les plannings du mois pour l'employé
        $plannings = Planning::where('employe_id', $employe_id)
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->with('lieu')
            ->orderBy('date')
            ->get();
            
        // Initialiser les totaux du mois
        $totalHeuresMois = 0;
        $totalHeuresSup25 = 0;
        $totalHeuresSup50 = 0;
        $totalHeuresNuit = 0;
        $totalHeuresDimanche = 0;
        $totalHeuresJoursFeries = 0;
        $totalAbsences = 0;
        
        // Récupérer les plannings par semaine pour l'affichage
        $detailHeuresSupp = [];
        $date = $dateDebut->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $finMoisEtSemaine = $dateFin->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
        $semaine = 1; // Initialisation du compteur de semaines
        
        while ($date <= $finMoisEtSemaine) {
            $debutSemaine = $date->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $finSemaine = $debutSemaine->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
            
            // Calculer les heures de la semaine
            $heuresSemaine = 0;
            $heuresNuitSemaine = 0;
            $heuresDimancheSemaine = 0;
            $heuresJoursFeriesSemaine = 0;
            $absencesSemaine = 0;
            
            $planningsSemaine = $plannings->filter(function($planning) use ($debutSemaine, $finSemaine) {
                $datePlanning = \Carbon\Carbon::parse($planning->date);
                return $datePlanning->between($debutSemaine, $finSemaine);
            });
            
            foreach ($planningsSemaine as $planning) {
                $heures = $this->calculerHeures($planning);
                $heuresSemaine += $heures['total'];
                $heuresNuitSemaine += $heures['nuit'];
                
                // Vérifier si le planning est un dimanche
                $datePlanning = \Carbon\Carbon::parse($planning->date);
                if ($datePlanning->dayOfWeek === 0) { // 0 = dimanche
                    $heuresDimancheSemaine += $heures['total'];
                }
                
                // Vérifier si le planning est un jour férié
                if ($this->estJourFerie($datePlanning)) {
                    $heuresJoursFeriesSemaine += $heures['total'];
                }
            }
            
            // Calculer les absences pour cette semaine
            $absencesSemaine = $this->calculerAbsencesSemaine($employe_id, $debutSemaine, $finSemaine);
            
            // Calculer les heures supplémentaires
            $heuresSup = $this->calculerHeuresSupplementaires($heuresSemaine);
            
            // Ajouter les résultats de la semaine au tableau
            $detailHeuresSupp[] = [
                'semaine' => 'Semaine ' . $semaine . ' (' . $debutSemaine->format('d/m') . ' - ' . $finSemaine->format('d/m') . ')',
                'heures_travaillees' => $heuresSemaine,
                'heures_25' => $heuresSup['heures_25'],
                'heures_50' => $heuresSup['heures_50'],
                'heures_nuit' => $heuresNuitSemaine,
                'heures_dimanche' => $heuresDimancheSemaine,
                'heures_jours_feries' => $heuresJoursFeriesSemaine,
                'absences' => $absencesSemaine
            ];
            
            // Ajouter au total du mois
            $totalHeuresMois += $heuresSemaine;
            $totalHeuresSup25 += $heuresSup['heures_25'];
            $totalHeuresSup50 += $heuresSup['heures_50'];
            $totalHeuresNuit += $heuresNuitSemaine;
            $totalHeuresDimanche += $heuresDimancheSemaine;
            $totalHeuresJoursFeries += $heuresJoursFeriesSemaine;
            $totalAbsences += $absencesSemaine;
            
            // Passer à la semaine suivante
            $date->addWeek();
            $semaine++;
        }
        
        // Créer un nouveau classeur Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Comptabilité');
        
        // En-tête avec informations de l'employé et de la société
        $sheet->setCellValue('A1', 'Rapport comptable');
        $sheet->setCellValue('A2', 'Société: ' . $societe->nom);
        $sheet->setCellValue('A3', 'Employé: ' . $nomEmploye);
        $sheet->setCellValue('A4', 'Période: ' . \Carbon\Carbon::createFromFormat('Y-m', $mois)->locale('fr')->isoFormat('MMMM YYYY'));
        
        // Style pour les en-têtes
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '4C1D95']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        
        // En-têtes des colonnes pour le récapitulatif hebdomadaire
        $sheet->setCellValue('A6', 'Semaine');
        $sheet->setCellValue('B6', 'Total Heures');
        $sheet->setCellValue('C6', 'Heures Sup 25%');
        $sheet->setCellValue('D6', 'Heures Sup 50%');
        $sheet->setCellValue('E6', 'Heures de Nuit');
        $sheet->setCellValue('F6', 'Heures Dimanche');
        $sheet->setCellValue('G6', 'Heures Jours Fériés');
        $sheet->setCellValue('H6', 'Absences (jours)');
        
        $sheet->getStyle('A6:H6')->applyFromArray($headerStyle);
        
        // Remplir les données hebdomadaires
        $row = 7;
        foreach ($detailHeuresSupp as $semaine) {
            $sheet->setCellValue('A' . $row, $semaine['semaine']);
            $sheet->setCellValue('B' . $row, $this->convertToHHMM($semaine['heures_travaillees']));
            $sheet->setCellValue('C' . $row, $this->convertToHHMM($semaine['heures_25']));
            $sheet->setCellValue('D' . $row, $this->convertToHHMM($semaine['heures_50']));
            $sheet->setCellValue('E' . $row, $this->convertToHHMM($semaine['heures_nuit']));
            $sheet->setCellValue('F' . $row, $this->convertToHHMM($semaine['heures_dimanche']));
            $sheet->setCellValue('G' . $row, $this->convertToHHMM($semaine['heures_jours_feries']));
            $sheet->setCellValue('H' . $row, $semaine['absences']);
            $row++;
        }
        
        // Total mensuel
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'TOTAL MENSUEL');
        $sheet->setCellValue('B' . $totalRow, $this->convertToHHMM($totalHeuresMois));
        $sheet->setCellValue('C' . $totalRow, $this->convertToHHMM($totalHeuresSup25));
        $sheet->setCellValue('D' . $totalRow, $this->convertToHHMM($totalHeuresSup50));
        $sheet->setCellValue('E' . $totalRow, $this->convertToHHMM($totalHeuresNuit));
        $sheet->setCellValue('F' . $totalRow, $this->convertToHHMM($totalHeuresDimanche));
        $sheet->setCellValue('G' . $totalRow, $this->convertToHHMM($totalHeuresJoursFeries));
        $sheet->setCellValue('H' . $totalRow, $totalAbsences);
        
        $sheet->getStyle('A' . $totalRow . ':H' . $totalRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E9D5FF']]
        ]);
        
        // Ajuster la largeur des colonnes automatiquement
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Créer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'comptabilite-' . Str::slug($employe->nom . '-' . $employe->prenom) . '-' . \Carbon\Carbon::createFromFormat('Y-m', $mois)->format('m-Y') . '.xlsx';
        
        // Enregistrer le fichier temporairement et le télécharger
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
    
    /**
     * Calcule les heures normales et de nuit pour un planning
     * 
     * @param Planning $planning
     * @return array
     */
    private function calculerHeures($planning) {
        $heuresTravaillees = $planning->heures_travaillees ?? 0;
        $heuresNuit = 0;
        
        // Calculer les heures de nuit (21h-06h)
        if (!empty($planning->heure_debut) && !empty($planning->heure_fin)) {
            $heureDebut = \Carbon\Carbon::parse($planning->heure_debut);
            $heureFin = \Carbon\Carbon::parse($planning->heure_fin);
            
            // Convertir en minutes depuis minuit pour faciliter les calculs
            $debutMinutes = $heureDebut->hour * 60 + $heureDebut->minute;
            $finMinutes = $heureFin->hour * 60 + $heureFin->minute;
            
            // Si l'heure de fin est avant l'heure de début, on ajoute 24h (passage à minuit)
            if ($finMinutes < $debutMinutes) {
                $finMinutes += 24 * 60;
            }
            
            // Définir les plages de nuit en minutes (21h-06h)
            $debutNuit = 21 * 60; // 21h00
            $finNuit = 6 * 60;    // 06h00
            $finNuitAjuste = $finNuit + 24 * 60; // 06h00 le lendemain
            
            // Cas 1: Le shift commence avant 21h et finit après 21h mais avant 6h du matin
            if ($debutMinutes < $debutNuit && $finMinutes > $debutNuit && $finMinutes <= $finNuitAjuste) {
                $heuresNuit = ($finMinutes - $debutNuit) / 60;
            }
            // Cas 2: Le shift commence après 21h et finit avant 6h du matin
            else if ($debutMinutes >= $debutNuit && $finMinutes <= $finNuitAjuste) {
                $heuresNuit = ($finMinutes - $debutMinutes) / 60;
            }
            // Cas 3: Le shift commence après 21h et finit après 6h du matin
            else if ($debutMinutes >= $debutNuit && $debutMinutes < 24 * 60 && $finMinutes > $finNuitAjuste) {
                $heuresNuit = ((24 * 60) - $debutMinutes + $finNuit) / 60;
            }
            // Cas 4: Le shift commence avant 6h du matin et finit après 6h du matin
            else if ($debutMinutes < $finNuit && $finMinutes > $finNuit) {
                $heuresNuit = ($finNuit - $debutMinutes) / 60;
            }
            // Cas 5: Le shift commence avant 21h et finit après 6h du matin le lendemain
            else if ($debutMinutes < $debutNuit && $finMinutes > $finNuitAjuste) {
                $heuresNuit = ((24 * 60) - $debutNuit + $finNuit) / 60;
            }
        }
        
        return [
            'total' => $heuresTravaillees,
            'nuit' => $heuresNuit
        ];
    }
    
    /**
     * Calcule les heures supplémentaires pour une semaine
     * 
     * @param float $heuresSemaine
     * @return array
     */
    private function calculerHeuresSupplementaires($heuresSemaine) {
        $heures25 = 0;
        $heures50 = 0;
        
        // Heures sup 25% : entre 35h et 43h
        if ($heuresSemaine > 35) {
            $heures25 = min($heuresSemaine, 43) - 35;
        }
        
        // Heures sup 50% : au-delà de 43h
        if ($heuresSemaine > 43) {
            $heures50 = $heuresSemaine - 43;
        }
        
        return [
            'heures_25' => $heures25,
            'heures_50' => $heures50
        ];
    }
    
    /**
     * Calcule les absences pour une semaine
     * 
     * @param int $employe_id
     * @param Carbon $debutSemaine
     * @param Carbon $finSemaine
     * @return int
     */
    private function calculerAbsencesSemaine($employe_id, $debutSemaine, $finSemaine) {
        // Récupérer tous les plannings du mois pour cet employé
        $plannings = Planning::where('employe_id', $employe_id)
            ->whereBetween('date', [$debutSemaine->copy()->startOfMonth(), $finSemaine->copy()->endOfMonth()])
            ->get();
        
        // Pour le mois de janvier, on ne compte pas les absences
        $moisActuel = $debutSemaine->format('m');
        
        if ($moisActuel == '01') { // Janvier
            return 0;
        }
        
        // Vérifier les jours où l'employé est censé travailler
        // On se base sur les plannings pour déterminer les jours habituels de travail
        $joursHabituels = [];
        
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
        
        // Jours avec plannings dans cette semaine
        $planningsSemaine = $plannings->filter(function($planning) use ($debutSemaine, $finSemaine) {
            $datePlanning = \Carbon\Carbon::parse($planning->date);
            return $datePlanning->between($debutSemaine, $finSemaine);
        });
        
        $joursAvecPlannings = $planningsSemaine->pluck('date')->toArray();
        
        // Jours d'absence = jours où l'employé aurait dû travailler mais n'a pas de planning
        $joursAbsence = array_diff($joursSemaine, $joursAvecPlannings);
        return count($joursAbsence);
    }
    
    // La méthode convertToHHMM existe déjà plus bas dans le fichier
    
    private function estJourFerie($date) {
        // Liste des jours fériés en France
        $joursFeries = [
            // Jours fériés fixes
            '01-01', // Jour de l'an
            '01-05', // Fête du travail
            '08-05', // Victoire 1945
            '14-07', // Fête nationale
            '15-08', // Assomption
            '01-11', // Toussaint
            '11-11', // Armistice
            '25-12', // Noël
        ];
        
        // Vérifier si la date est un jour férié fixe
        $dateFormat = $date->format('d-m');
        if (in_array($dateFormat, $joursFeries)) {
            return true;
        }
        
        return false;
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
    
    private function getNomJourFerie($date)
    {
        $joursFeries = [
            '01-01' => "Jour de l'an",
            '01-05' => "Fête du travail",
            '08-05' => "Victoire 1945",
            '14-07' => "Fête nationale",
            '15-08' => "Assomption",
            '01-11' => "Toussaint",
            '11-11' => "Armistice",
            '25-12' => "Noël",
        ];
        
        $dateFormat = $date->format('d-m');
        return $joursFeries[$dateFormat] ?? "Jour férié";
    }
    
    /**
     * Génère un rapport d'activité détaillé
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportActivityReport(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'employe_id' => 'nullable|exists:employes,id',
            'mois' => 'required|string',
        ]);
        
        $employe_id = $request->input('employe_id');
        $mois = $request->input('mois');
        
        // Gérer le format "YYYY-MM"
        try {
            if (preg_match('/^\d{4}-\d{2}$/', $mois)) {
                $date = Carbon::createFromFormat('Y-m', $mois);
            } else {
                $date = Carbon::parse($mois);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur de parsing de date', [
                'mois' => $mois,
                'error' => $e->getMessage()
            ]);
            $date = now();
        }
        
        $date_debut = $date->copy()->startOfMonth();
        $date_fin = $date->copy()->endOfMonth();
        
        // Récupérer la société de l'utilisateur connecté
        $societe = Auth::user()->societe;
        
        // Récupérer les plannings pour le mois
        $query = Planning::with(['employe', 'lieu'])
            ->where('societe_id', $societe->id)
            ->whereBetween('date', [$date_debut->format('Y-m-d'), $date_fin->format('Y-m-d')]);
            
        if ($employe_id) {
            $query->where('employe_id', $employe_id);
            $employe = Employe::find($employe_id);
        }
        
        $plannings = $query->get();
        
        // Analyser les données pour le rapport
        $stats = $this->analyzeActivityData($plannings, $date_debut, $date_fin);
        
        // Générer le PDF
        $pdf = PDF::loadView('exports.activity-report', [
            'stats' => $stats,
            'societe' => $societe,
            'dateDebut' => $date_debut,
            'dateFin' => $date_fin,
            'employe' => $employe ?? null,
            'mois' => $date->format('F Y')
        ]);
        
        $filename = 'rapport-activite-' . $date->format('m-Y');
        if ($employe_id) {
            $filename .= '-' . Str::slug($employe->nom . '-' . $employe->prenom);
        }
        $filename .= '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Analyse les données d'activité pour générer des statistiques
     * 
     * @param Collection $plannings
     * @param Carbon $dateDebut
     * @param Carbon $dateFin
     * @return array
     */
    private function analyzeActivityData($plannings, $dateDebut, $dateFin)
    {
        // Structure de données pour l'analyse
        $stats = [
            'resume' => [
                'total_heures' => 0,
                'heures_standard' => 0,
                'heures_supplementaires' => 0,
                'heures_nuit' => 0,
                'heures_dimanche' => 0,
                'heures_feries' => 0,
                'nb_employes_actifs' => 0,
                'nb_lieux_utilises' => 0,
                'taux_occupation' => 0,
            ],
            'par_employe' => [],
            'par_lieu' => [],
            'par_jour_semaine' => [
                'Lundi' => 0, 'Mardi' => 0, 'Mercredi' => 0, 
                'Jeudi' => 0, 'Vendredi' => 0, 'Samedi' => 0, 'Dimanche' => 0
            ],
            'absences' => [
                'conges' => 0,
                'maladie' => 0,
                'autres' => 0,
            ],
        ];
        
        // Compteurs pour les statistiques
        $employesActifs = [];
        $lieuxUtilises = [];
        
        foreach ($plannings as $planning) {
            // Calculer les heures totales
            $heures = $this->prepareHeuresActivite($planning);
            $stats['resume']['total_heures'] += $heures['total'];
            $stats['resume']['heures_standard'] += $heures['standard'];
            $stats['resume']['heures_supplementaires'] += $heures['supplementaires'];
            $stats['resume']['heures_nuit'] += $heures['nuit'];
            
            // Ajouter l'employé aux actifs s'il n'est pas déjà compté
            if (!in_array($planning->employe_id, $employesActifs)) {
                $employesActifs[] = $planning->employe_id;
            }
            
            // Ajouter le lieu aux utilisés s'il n'est pas déjà compté
            if (!in_array($planning->lieu_id, $lieuxUtilises)) {
                $lieuxUtilises[] = $planning->lieu_id;
            }
            
            // Statistiques par jour de la semaine
            $jourSemaine = Carbon::parse($planning->date)->locale('fr_FR')->isoFormat('dddd');
            $stats['par_jour_semaine'][ucfirst($jourSemaine)] += $heures['total'];
            
            // Vérifier si c'est un dimanche
            if (Carbon::parse($planning->date)->isSunday()) {
                $stats['resume']['heures_dimanche'] += $heures['total'];
            }
            
            // Vérifier si c'est un jour férié
            if ($this->estJourFerie(Carbon::parse($planning->date))) {
                $stats['resume']['heures_feries'] += $heures['total'];
            }
            
            // Statistiques par employé
            if (!isset($stats['par_employe'][$planning->employe_id])) {
                $stats['par_employe'][$planning->employe_id] = [
                    'nom' => $planning->employe->nom . ' ' . $planning->employe->prenom,
                    'heures' => 0,
                ];
            }
            $stats['par_employe'][$planning->employe_id]['heures'] += $heures['total'];
            
            // Statistiques par lieu
            if (!isset($stats['par_lieu'][$planning->lieu_id])) {
                $stats['par_lieu'][$planning->lieu_id] = [
                    'nom' => $planning->lieu->nom,
                    'heures' => 0,
                ];
            }
            $stats['par_lieu'][$planning->lieu_id]['heures'] += $heures['total'];
        }
        
        // Finaliser les statistiques
        $stats['resume']['nb_employes_actifs'] = count($employesActifs);
        $stats['resume']['nb_lieux_utilises'] = count($lieuxUtilises);
        
        // Calculer le taux d'occupation (nombre de jours travaillés / nombre de jours ouvrables)
        $joursOuvrables = $this->calculerJoursOuvrables($dateDebut, $dateFin);
        $joursTravailles = $plannings->pluck('date')->unique()->count();
        $stats['resume']['jours_ouvrables'] = $joursOuvrables;
        $stats['resume']['taux_occupation'] = $joursOuvrables > 0 ? round(($joursTravailles / $joursOuvrables) * 100, 1) : 0;
        
        return $stats;
    }
    
    /**
     * Calcule le nombre de jours ouvrables dans une période
     * 
     * @param Carbon $debut
     * @param Carbon $fin
     * @return int
     */
    private function calculerJoursOuvrables($debut, $fin)
    {
        $joursOuvrables = 0;
        $current = $debut->copy();
        
        while ($current->lte($fin)) {
            // Si ce n'est pas un weekend et pas un jour férié
            if (!$current->isWeekend() && !$this->estJourFerie($current)) {
                $joursOuvrables++;
            }
            $current->addDay();
        }
        
        return $joursOuvrables;
    }
    
    /**
     * Prépare les données d'heures pour le rapport d'activité
     * 
     * @param Planning $planning
     * @return array
     */
    private function prepareHeuresActivite($planning)
    {
        // Utiliser la méthode existante pour les calculs de base
        $heuresBase = $this->calculerHeures($planning);
        
        // Calculer les heures totales
        $heureDebut = Carbon::parse($planning->heure_debut);
        $heureFin = Carbon::parse($planning->heure_fin);
        
        // Si l'heure de fin est avant l'heure de début, on ajoute 24h (passage à minuit)
        if ($heureFin < $heureDebut) {
            $heureFin->addDay();
        }
        
        // Calculer la durée totale en heures décimales
        $dureeMinutes = $heureFin->diffInMinutes($heureDebut);
        $dureeHeures = $dureeMinutes / 60;
        
        // Préparer le tableau de résultat pour le rapport d'activité
        $heures = [
            'total' => $dureeHeures,
            'standard' => min($dureeHeures, 7), // 7h = journée standard
            'supplementaires' => max(0, $dureeHeures - 7),
            'nuit' => $heuresBase['heures_nuit'] ?? 0,
        ];
        
        return $heures;
    }
}