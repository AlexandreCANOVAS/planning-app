<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Lieu;
use App\Models\Employe;
use App\Models\ModificationPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Planning\PlanningModifie;
use App\Notifications\Planning\PlanningCreatedNotification;
use App\Notifications\Planning\PlanningUpdatedNotification;
use App\Notifications\Echange\ExchangeStatusChangedNotification;
use App\Notifications\Echange\ExchangeRequestNotification;
use App\Mail\PlanningCreated;
use App\Mail\PlanningUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PlanningController extends Controller
{
    /**
     * Affiche le planning mensuel de l'employé connecté sous forme de calendrier
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function employeCalendar(Request $request)
    {
        $employe = Auth::user()->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre profil employé n\'est pas encore configuré.');
        }
        
        // Récupérer le mois et l'année sélectionnés ou utiliser le mois et l'année actuels
        $selectedMonth = $request->input('mois', now()->month);
        $selectedYear = $request->input('annee', now()->year);
        
        // Récupérer les plannings de l'employé pour le mois et l'année sélectionnés
        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();
        
        // Récupérer les plannings de l'employé connecté
        $plannings = $employe->plannings()
            ->with('lieu')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($planning) {
                return Carbon::parse($planning->date)->format('Y-m-d');
            });
        
        // Préparer les données pour le calendrier
        $planningsData = collect();
        
        foreach ($plannings as $date => $planningsJour) {
            $planningsData[$date] = $planningsJour->map(function($planning) {
                return [
                    'id' => $planning->id,
                    'lieu' => $planning->lieu ? $planning->lieu->nom : 'Non défini',
                    'heure_debut' => $planning->heure_debut,
                    'heure_fin' => $planning->heure_fin,
                    'type' => 'planning'
                ];
            });
        }
        
        return view('plannings.employe-calendar', [
            'plannings' => $planningsData,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'firstDay' => $startDate,
            'lastDay' => $endDate,
        ]);
    }
    
    /**
     * Comparer les plannings de l'employé connecté et d'un collègue côte à côte
     * 
     * @param  \App\Models\Employe  $employe
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function comparerPlannings(Employe $employe, Request $request)
    {
        $user = auth()->user();
        $currentEmploye = $user->employe;
        
        if (!$currentEmploye) {
            return redirect()->route('employe.plannings.index')
                ->with('error', 'Vous devez être connecté en tant qu\'employé.');
        }
        
        // Vérifier que l'employé connecté n'essaie pas de comparer son propre planning
        if ($currentEmploye->id === $employe->id) {
            return redirect()->route('employe.plannings.index')
                ->with('error', 'Vous ne pouvez pas comparer votre planning avec vous-même.');
        }
        
        // Récupérer l'année et le mois sélectionnés
        $selectedYear = $request->get('annee', now()->year);
        $selectedMonth = $request->get('mois', now()->month);
        
        // Premier et dernier jour du mois
        $firstDay = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $lastDay = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();
        
        // Récupérer les plannings de l'employé connecté
        $planningsCurrent = Planning::where('employe_id', $currentEmploye->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->with('lieu')
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d');
            });
        
        // Récupérer les plannings du collègue
        $planningsCollegue = Planning::where('employe_id', $employe->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->with('lieu')
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d');
            });
        
        // Calculer le total des heures pour l'employé connecté
        $totalHeuresCurrent = 0;
        foreach ($planningsCurrent as $dayPlannings) {
            foreach ($dayPlannings as $planning) {
                $debut = Carbon::parse($planning->heure_debut);
                $fin = Carbon::parse($planning->heure_fin);
                $totalHeuresCurrent += $debut->diffInHours($fin);
            }
        }
        
        // Calculer le total des heures pour le collègue
        $totalHeuresCollegue = 0;
        foreach ($planningsCollegue as $dayPlannings) {
            foreach ($dayPlannings as $planning) {
                $debut = Carbon::parse($planning->heure_debut);
                $fin = Carbon::parse($planning->heure_fin);
                $totalHeuresCollegue += $debut->diffInHours($fin);
            }
        }
        
        return view('plannings.comparer-plannings', compact(
            'currentEmploye',
            'employe',
            'planningsCurrent',
            'planningsCollegue',
            'firstDay',
            'lastDay',
            'selectedYear',
            'selectedMonth',
            'totalHeuresCurrent',
            'totalHeuresCollegue'
        ));
    }
    
    /**
     * Proposer un échange de jours de travail entre deux employés
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function proposerEchange(Request $request)
    {
        $request->validate([
            'collegue_id' => 'required|exists:employes,id',
            'your_day' => 'required|date',
            'collegue_day' => 'required|date',
            'motif' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $demandeur = $user->employe;
        $receveur = Employe::findOrFail($request->collegue_id);
        
        // Vérifier que le demandeur a bien un planning pour le jour qu'il propose
        $planningDemandeur = Planning::where('employe_id', $demandeur->id)
            ->whereDate('date', $request->your_day)
            ->exists();
            
        if (!$planningDemandeur) {
            return redirect()->back()->with('error', 'Vous n\'avez pas de planning pour ce jour.');
        }
        
        // Vérifier que le receveur a bien un planning pour le jour demandé
        $planningReceveur = Planning::where('employe_id', $receveur->id)
            ->whereDate('date', $request->collegue_day)
            ->exists();
            
        if (!$planningReceveur) {
            return redirect()->back()->with('error', 'Votre collègue n\'a pas de planning pour ce jour.');
        }
        
        // Créer l'échange
        $echange = new \App\Models\EchangeJour([
            'demandeur_id' => $demandeur->id,
            'receveur_id' => $receveur->id,
            'jour_demandeur' => $request->your_day,
            'jour_receveur' => $request->collegue_day,
            'motif' => $request->motif,
            'statut' => 'en_attente',
        ]);
        
        $echange->save();
        
        // Notifier le receveur
        if ($receveur->user) {
            $receveur->user->notify(new \App\Notifications\Echange\ExchangeRequestNotification(
                $demandeur,
                $receveur,
                Carbon::parse($request->your_day),
                Carbon::parse($request->collegue_day),
                $echange->id
            ));
        }
        
        return redirect()->route('employe.plannings.liste-echanges')
            ->with('success', 'Votre demande d\'\u00e9change a été envoyée à ' . $receveur->prenom . ' ' . $receveur->nom);
    }
    
    /**
     * Afficher la liste des échanges de jours de travail
     *
     * @return \Illuminate\Http\Response
     */
    public function listeEchanges()
    {
        $user = auth()->user();
        $employe = $user->employe;
        
        // Récupérer les échanges où l'employé est demandeur ou receveur
        $echangesDemandes = \App\Models\EchangeJour::where('demandeur_id', $employe->id)
            ->with(['demandeur', 'receveur'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $echangesRecus = \App\Models\EchangeJour::where('receveur_id', $employe->id)
            ->with(['demandeur', 'receveur'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('plannings.echanges', compact('echangesDemandes', 'echangesRecus'));
    }
    
    /**
     * Accepter une demande d'échange de jours
     *
     * @param  \App\Models\EchangeJour  $echange
     * @return \Illuminate\Http\Response
     */
    public function accepterEchange(\App\Models\EchangeJour $echange, Request $request)
    {
        $user = auth()->user();
        $employe = $user->employe;
        
        // Vérifier que l'employé est bien le receveur de la demande
        if ($echange->receveur_id !== $employe->id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à accepter cette demande.');
        }
        
        // Vérifier que la demande est en attente
        if (!$echange->estEnAttente()) {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }
        
        // Récupérer les plannings concernés par l'échange
        $planningsDemandeur = \App\Models\Planning::where('employe_id', $echange->demandeur_id)
            ->whereDate('date', $echange->jour_demandeur)
            ->get();
            
        $planningsReceveur = \App\Models\Planning::where('employe_id', $echange->receveur_id)
            ->whereDate('date', $echange->jour_receveur)
            ->get();
            
        // Vérifier que les plannings existent toujours
        if ($planningsDemandeur->isEmpty() || $planningsReceveur->isEmpty()) {
            return redirect()->back()->with('error', 'Les plannings concernés par cet échange n\'existent plus ou ont été modifiés.');
        }
        
        // Échanger les plannings
        \DB::beginTransaction();
        try {
            // Pour chaque planning du demandeur, créer une copie pour le receveur à la date du demandeur
            foreach ($planningsDemandeur as $planning) {
                $newPlanning = $planning->replicate();
                $newPlanning->employe_id = $echange->receveur_id;
                $newPlanning->save();
                
                // Supprimer le planning original du demandeur
                $planning->delete();
            }
            
            // Pour chaque planning du receveur, créer une copie pour le demandeur à la date du receveur
            foreach ($planningsReceveur as $planning) {
                $newPlanning = $planning->replicate();
                $newPlanning->employe_id = $echange->demandeur_id;
                $newPlanning->save();
                
                // Supprimer le planning original du receveur
                $planning->delete();
            }
            
            // Mettre à jour le statut de la demande
            $echange->statut = 'accepte';
            $echange->commentaire_reponse = $request->commentaire ?? null;
            $echange->save();
            
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'échange des plannings: ' . $e->getMessage());
        }
        
        // Notifier le demandeur
        if ($echange->demandeur->user) {
            $echange->demandeur->user->notify(new ExchangeStatusChangedNotification($echange));
        }
        
        return redirect()->route('employe.plannings.liste-echanges')
            ->with('success', 'Vous avez accepté la demande d\'échange de ' . $echange->demandeur->prenom . ' ' . $echange->demandeur->nom . '. Les plannings ont été mis à jour.');
    }
    
    /**
     * Refuser une demande d'échange de jours
     *
     * @param  \App\Models\EchangeJour  $echange
     * @return \Illuminate\Http\Response
     */
    public function refuserEchange(\App\Models\EchangeJour $echange, Request $request)
    {
        $user = auth()->user();
        $employe = $user->employe;
        
        // Vérifier que l'employé est bien le receveur de la demande
        if ($echange->receveur_id !== $employe->id) {
            return redirect()->back()->with('error', 'Vous n\'\u00eates pas autorisé à refuser cette demande.');
        }
        
        // Vérifier que la demande est en attente
        if (!$echange->estEnAttente()) {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }
        
        // Mettre à jour le statut de la demande
        $echange->statut = 'refuse';
        $echange->commentaire_reponse = $request->commentaire ?? null;
        $echange->save();
        
        // Notifier le demandeur
        if ($echange->demandeur->user) {
            $echange->demandeur->user->notify(new ExchangeStatusChangedNotification($echange));
        }
        
        return redirect()->route('employe.plannings.liste-echanges')
            ->with('success', 'Vous avez refusé la demande d\'\u00e9change de ' . $echange->demandeur->prenom . ' ' . $echange->demandeur->nom);
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Si c'est un employeur, rediriger vers le calendrier mensuel
        if ($user->isEmployeur()) {
            return redirect()->route('plannings.calendar');
        }

        // Si c'est un employé
        if ($user->isEmploye()) {
            $employe = $user->employe;
            if (!$employe) {
                // Créer un nouvel employé pour l'utilisateur
                $employe = Employe::create([
                    'nom' => explode(' ', $user->name)[0] ?? $user->name,
                    'prenom' => explode(' ', $user->name)[1] ?? '',
                    'email' => $user->email,
                    'telephone' => $user->phone,
                    'societe_id' => $user->societe_id,
                    'user_id' => $user->id
                ]);
            }

            // Récupérer le mois et l'année depuis la requête ou utiliser le mois et l'année actuels
            $selectedYear = (int) $request->get('annee', now()->year);
            $selectedMonth = (int) $request->get('mois', now()->month);

            // Récupérer les plannings du mois sélectionné
            $plannings = Planning::where('employe_id', $employe->id)
                ->where('societe_id', $user->societe_id)
                ->whereYear('date', $selectedYear)
                ->whereMonth('date', $selectedMonth)
                ->with(['lieu', 'societe'])
                ->get();

            // Calculer les statistiques
            $totalHeures = $plannings->sum('heures_travaillees');
            
            // Heures supplémentaires (au-delà de 35h par semaine)
            $heuresSupplementaires = 0;
            $planningsParSemaine = $plannings->groupBy(function($planning) {
                return $planning->date->format('W'); // Numéro de semaine
            });
            
            foreach ($planningsParSemaine as $planningsSemaine) {
                $heuresSemaine = $planningsSemaine->sum('heures_travaillees');
                if ($heuresSemaine > 35) {
                    $heuresSupplementaires += ($heuresSemaine - 35);
                }
            }
            
            // Heures de nuit (entre 22h et 6h)
            $heuresNuit = $plannings->filter(function($planning) {
                $debut = Carbon::parse($planning->heure_debut);
                $fin = Carbon::parse($planning->heure_fin);
                return ($debut->hour >= 22 || $debut->hour < 6 || $fin->hour >= 22 || $fin->hour < 6);
            })->sum('heures_travaillees');
            
            // Jours fériés travaillés
            $joursFeries = $plannings->filter(function($planning) {
                // Vérifier si le jour est férié (à implémenter selon la logique de l'application)
                // Exemple simplifié : on considère que les jours fériés sont marqués dans un champ spécifique
                return $planning->lieu && $planning->lieu->nom === 'Jour Férié';
            })->count();
            
            // Dimanches travaillés
            $dimanchesTravailles = $plannings->filter(function($planning) {
                return $planning->date->dayOfWeek === Carbon::SUNDAY;
            })->count();
            
            // Récupérer les données pour le graphique d'évolution sur 6 mois
            $donneesGraphique = [];
            $moisActuel = Carbon::create($selectedYear, $selectedMonth);
            
            for ($i = 5; $i >= 0; $i--) {
                $moisPrecedent = $moisActuel->copy()->subMonths($i);
                $planningsMois = Planning::where('employe_id', $employe->id)
                    ->where('societe_id', $user->societe_id)
                    ->whereYear('date', $moisPrecedent->year)
                    ->whereMonth('date', $moisPrecedent->month)
                    ->get();
                
                $donneesGraphique[] = [
                    'mois' => $moisPrecedent->locale('fr')->isoFormat('MMM'),
                    'heures' => $planningsMois->sum('heures_travaillees')
                ];
            }
            
            // Récupérer les modifications récentes du planning (derniers 30 jours)
            $modifications = ModificationPlanning::where('employe_id', $employe->id)
                ->where('date_demande', '>=', now()->subDays(30))
                ->orderBy('date_demande', 'desc')
                ->with(['planning', 'nouveauLieu'])
                ->take(5)
                ->get();
            
            // Préparer les données du calendrier
            $debutMois = Carbon::create($selectedYear, $selectedMonth, 1);
            $finMois = $debutMois->copy()->endOfMonth();
            $planningData = [];
            
            // Initialiser le tableau avec tous les jours du mois
            for ($jour = $debutMois->copy(); $jour->lte($finMois); $jour->addDay()) {
                $date = $jour->format('Y-m-d');
                $planningData[$date] = [
                    'date' => $jour->format('d'),
                    'jour_semaine' => $jour->locale('fr')->isoFormat('ddd'),
                    'type' => 'normal',
                    'planning' => null
                ];
                
                // Marquer les week-ends
                if ($jour->isWeekend()) {
                    $planningData[$date]['type'] = 'weekend';
                }
            }
            
            // Ajouter les plannings
            foreach ($plannings as $planning) {
                $date = $planning->date->format('Y-m-d');
                if (isset($planningData[$date])) {
                    $planningData[$date]['planning'] = $planning;
                    
                    // Déterminer le type de jour
                    if ($planning->lieu) {
                        if ($planning->lieu->nom === 'CP') {
                            $planningData[$date]['type'] = 'conge';
                        } elseif ($planning->lieu->nom === 'Formation') {
                            $planningData[$date]['type'] = 'formation';
                        } elseif ($planning->lieu->nom === 'Jour Férié') {
                            $planningData[$date]['type'] = 'ferie';
                        }
                    }
                }
            }

            return view('plannings.employe', [
                'selectedYear' => $selectedYear,
                'selectedMonth' => $selectedMonth,
                'plannings' => $plannings,
                'totalHeures' => $totalHeures,
                'heuresSupplementaires' => $heuresSupplementaires,
                'heuresNuit' => $heuresNuit,
                'joursFeries' => $joursFeries,
                'dimanchesTravailles' => $dimanchesTravailles,
                'donneesGraphique' => $donneesGraphique,
                'modifications' => $modifications,
                'planningData' => $planningData
            ]);
        }

        return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
    }

    public function calendar(Request $request)
    {
        $user = auth()->user();
        $moisActuel = $request->get('mois', now()->month);
        $anneeActuelle = $request->get('annee', now()->year);
        
        $employes = Employe::where('societe_id', $user->societe_id)->get();
        $lieux = Lieu::where(function($query) use ($user) {
                $query->where('societe_id', $user->societe_id)
                      ->orWhereNull('societe_id')
                      ->orWhere('is_special', true);
            })
            ->orderBy('nom')
            ->get();
        
        \Log::info('Début récupération plannings', [
            'user_id' => $user->id,
            'societe_id' => $user->societe_id,
            'annee' => $anneeActuelle
        ]);

        // Récupérer tous les plannings pour l'année en cours
        $plannings = Planning::where('societe_id', $user->societe_id)
            ->whereYear('date', $anneeActuelle)
            ->with(['employe', 'lieu'])
            ->get();

        \Log::info('Plannings récupérés', [
            'count' => $plannings->count(),
            'first_planning' => $plannings->first()
        ]);

        // Grouper par mois
        $planningsParMois = $plannings->groupBy(function($planning) {
            return $planning->date instanceof Carbon 
                ? $planning->date->month 
                : Carbon::parse($planning->date)->month;
        });

        \Log::info('Plannings groupés par mois', [
            'mois_disponibles' => $planningsParMois->keys()->toArray()
        ]);
            
        // Préparer les données pour chaque mois
        $recapitulatifMensuel = [];
        foreach(range(1, 12) as $mois) {
            $planningsDuMois = $planningsParMois->get($mois, collect([]));
            
            $recapitulatifMensuel[$mois] = [
                'nom_mois' => Carbon::create(null, $mois, 1)->locale('fr')->monthName,
                'stats_par_employe' => []
            ];

            foreach($employes as $employe) {
                $planningsEmploye = $planningsDuMois->where('employe_id', $employe->id);
                
                if($planningsEmploye->isNotEmpty()) {
                    // Filtrer les plannings pour exclure RH et CP du calcul des heures
                    $planningsNonSpeciaux = $planningsEmploye->filter(function($planning) {
                        return !($planning->lieu && in_array($planning->lieu->nom, ['RH', 'CP']));
                    });

                    $recapitulatifMensuel[$mois]['stats_par_employe'][] = [
                        'employe' => $employe,
                        'total_heures' => $planningsNonSpeciaux->sum('heures_travaillees'),
                        'lieux' => $planningsEmploye->groupBy(function($planning) {
                            return optional($planning->lieu)->nom ?? 'Lieu inconnu';
                        })->map(function($plannings) {
                            // Pour chaque lieu, vérifier s'il est spécial
                            $isSpecial = $plannings->first()->lieu && in_array($plannings->first()->lieu->nom, ['RH', 'CP']);
                            return [
                                'count' => $plannings->count(),
                                'heures' => $isSpecial ? 0 : $plannings->sum('heures_travaillees')
                            ];
                        })
                    ];
                }
            }
        }

        return view('plannings.calendar', [
            'employes' => $employes,
            'lieux' => $lieux,
            'moisActuel' => $moisActuel,
            'anneeActuelle' => $anneeActuelle,
            'recapitulatifMensuel' => $recapitulatifMensuel
        ]);
    }

    public function calendarMensuel(Request $request)
    {
        $user = Auth::user();
        
        $employes = Employe::where('societe_id', $user->societe_id)
            ->orderBy('nom')
            ->get();

        return view('plannings.create_monthly', compact('employes'));
    }

    public function createMensuel()
    {
        $user = Auth::user();
        $employes = Employe::where('societe_id', $user->societe_id)
            ->orderBy('nom')
            ->get();
        $lieux = Lieu::where('societe_id', $user->societe_id)
            ->where('is_special', false)
            ->orderBy('nom')
            ->get();
        
        return view('plannings.create_mensuel', compact('employes', 'lieux'));
    }

    public function storeMensuel(Request $request)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'lieu_id' => 'required|string',
            'mois' => 'required|date_format:Y-m',
            'jours' => 'required|array',
            'jours.*' => 'required|integer|between:0,6',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut'
        ]);

        // Vérifier que l'employé appartient à la société de l'employeur
        $employe = Employe::findOrFail($validated['employe_id']);
        if ($employe->societe_id !== Auth::user()->societe_id) {
            return back()->with('error', 'Vous ne pouvez pas créer un planning pour cet employé.');
        }

        $mois = Carbon::createFromFormat('Y-m', $validated['mois']);
        $debut = $mois->copy()->startOfMonth();
        $fin = $mois->copy()->endOfMonth();
        $plannings = [];

        // Pour chaque jour du mois
        for ($date = $debut; $date->lte($fin); $date->addDay()) {
            // Si ce jour de la semaine est sélectionné
            if (in_array($date->dayOfWeek, $validated['jours'])) {
                $heure_debut = $date->copy()->setTimeFromTimeString($validated['heure_debut']);
                $heure_fin = $date->copy()->setTimeFromTimeString($validated['heure_fin']);
                
                // Calculer les heures travaillées
                $heures_travaillees = $validated['lieu_id'] === 'cp' ? 0 : $heure_fin->diffInMinutes($heure_debut) / 60;

                // Créer le planning
                $planning = Planning::create([
                    'employe_id' => $validated['employe_id'],
                    'lieu_id' => $validated['lieu_id'] === 'cp' ? null : $validated['lieu_id'],
                    'date' => $date->toDateString(),
                    'heure_debut' => $heure_debut,
                    'heure_fin' => $heure_fin,
                    'heures_travaillees' => $heures_travaillees,
                    'type' => $validated['lieu_id'] === 'cp' ? 'CP' : null,
                    'societe_id' => Auth::user()->societe_id
                ]);

                $plannings[] = $planning;
            }
        }

        // Notifier l'employé
        if (!empty($plannings)) {
            $employe->user->notify(new PlanningUpdatedNotification($plannings[0], ['message' => 'Votre planning a été mis à jour']));
        }

        // Rediriger vers le calendrier mensuel avec le mois et l'année sélectionnés
        return redirect()->route('plannings.calendar', [
            'mois' => $mois->month,
            'annee' => $mois->year
        ])->with('success', 'Planning mensuel créé avec succès.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plannings' => 'required|array',
            'employe_id' => 'required|exists:employes,id',
        ]);

        foreach ($data['plannings'] as $date => $planning) {
            if (empty($planning['lieu_id'])) continue;

            // Analyser les heures (format simple ou composé)
            $heures = explode('/', $planning['heures']);
            
            if (count($heures) > 1) {
                // Format composé (matin/après-midi)
                list($matin, $aprem) = $heures;
                list($debut_matin, $fin_matin) = explode('-', $matin);
                list($debut_aprem, $fin_aprem) = explode('-', $aprem);

                // Créer deux enregistrements pour la même journée
                Planning::updateOrCreate(
                    [
                        'employe_id' => $data['employe_id'],
                        'date' => $date,
                        'periode' => 'matin'
                    ],
                    [
                        'lieu_id' => $planning['lieu_id'],
                        'heure_debut' => $debut_matin,
                        'heure_fin' => $fin_matin,
                        'societe_id' => auth()->user()->societe_id
                    ]
                );

                Planning::updateOrCreate(
                    [
                        'employe_id' => $data['employe_id'],
                        'date' => $date,
                        'periode' => 'apres-midi'
                    ],
                    [
                        'lieu_id' => $planning['lieu_id'],
                        'heure_debut' => $debut_aprem,
                        'heure_fin' => $fin_aprem,
                        'societe_id' => auth()->user()->societe_id
                    ]
                );
            } else {
                // Format simple (journée complète)
                list($debut, $fin) = explode('-', $planning['heures']);
                
                Planning::updateOrCreate(
                    [
                        'employe_id' => $data['employe_id'],
                        'date' => $date,
                        'periode' => 'journee'
                    ],
                    [
                        'lieu_id' => $planning['lieu_id'],
                        'heure_debut' => $debut,
                        'heure_fin' => $fin,
                        'societe_id' => auth()->user()->societe_id
                    ]
                );
            }
        }

        return response()->json(['message' => 'Planning enregistré avec succès']);
    }

    public function storeMonthly(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'plannings' => 'required|array'
        ]);

        $employe = Employe::where('id', $data['employe_id'])
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            foreach ($data['plannings'] as $date => $planning) {
                // Supprimer les plannings existants pour cette date
                Planning::where('employe_id', $employe->id)
                    ->where('date', $date)
                    ->delete();

                // Créer le nouveau planning
                if (isset($planning['lieu_id']) && $planning['heures']) {
                    $heures = explode('-', $planning['heures']);
                    if (count($heures) >= 2) {
                        $heure_debut = trim($heures[0]);
                        $heure_fin = trim($heures[1]);

                        Planning::create([
                            'employe_id' => $employe->id,
                            'societe_id' => $user->societe_id,
                            'lieu_id' => $planning['lieu_id'],
                            'date' => $date,
                            'heure_debut' => $heure_debut,
                            'heure_fin' => $heure_fin,
                            'heures_travaillees' => 0, // À calculer
                            'periode' => 'journee'
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'Planning enregistré avec succès']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Erreur lors de l\'enregistrement du planning: ' . $e->getMessage()], 500);
        }
    }

    public function storeMonthlyCalendar(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'plannings' => 'required|array'
        ]);

        $employe = Employe::where('id', $data['employe_id'])
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            foreach ($data['plannings'] as $date => $planning) {
                // Supprimer les plannings existants pour cette date
                Planning::where('employe_id', $employe->id)
                    ->where('date', $date)
                    ->delete();

                if ($planning['type_horaire'] === 'simple') {
                    // Créer un planning simple pour la journée
                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'journee',
                        'heure_debut' => $planning['horaires']['debut'],
                        'heure_fin' => $planning['horaires']['fin'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut'], $planning['horaires']['fin'])
                    ]);
                } else {
                    // Créer deux plannings pour la journée (matin et après-midi)
                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'matin',
                        'heure_debut' => $planning['horaires']['debut_matin'],
                        'heure_fin' => $planning['horaires']['fin_matin'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_matin'], $planning['horaires']['fin_matin'])
                    ]);

                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'apres-midi',
                        'heure_debut' => $planning['horaires']['debut_aprem'],
                        'heure_fin' => $planning['horaires']['fin_aprem'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_aprem'], $planning['horaires']['fin_aprem'])
                    ]);
                }
            }

            DB::commit();
            
            // Récupérer les plannings créés pour envoyer une notification
            $createdPlannings = Planning::where('employe_id', $employe->id)
                ->whereIn('date', array_keys($data['plannings']))
                ->with('lieu') // Charger la relation lieu pour l'email
                ->orderBy('date')
                ->get();
                
            // Envoyer une notification à l'employé concerné
            if ($employe->user && !$createdPlannings->isEmpty()) {
                // Notification dans l'application
                $employe->user->notify(new PlanningCreatedNotification($createdPlannings->first()));
                
                // Envoi d'un email avec le nouveau design et pièce jointe
                if ($employe->user->email) {
                    // S'assurer que l'employé est bien chargé avec ses relations
                    $planning = $createdPlannings->first();
                    
                    Mail::to($employe->user->email)
                        ->queue(new PlanningCreated($planning, $employe->user));
                }
            }
            
            return response()->json(['message' => 'Planning enregistré avec succès']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de l\'enregistrement du planning: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de l\'enregistrement'], 500);
        }
    }

    public function viewMonthlyCalendar($employe_id, $mois, $annee)
    {
        $user = auth()->user();
        
        // Récupérer l'employé
        $employe = Employe::where('id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        // Créer les dates pour le mois
        $dateDebut = Carbon::create($annee, $mois, 1);
        $debutPeriode = $dateDebut->copy()->startOfWeek(Carbon::MONDAY);
        $finPeriode = $dateDebut->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Débogage: afficher la requête SQL
        DB::enableQueryLog();
        
        // Récupérer les plannings existants pour ce mois avec la relation lieu
        $plannings = Planning::where('employe_id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $annee)
            ->whereMonth('date', $mois)
            ->with('lieu')
            ->get();
            
        // Débogage: afficher la requête SQL exécutée
        $queries = DB::getQueryLog();
        $lastQuery = end($queries);
        
        // Stocker les informations de débogage dans la session
        session()->flash('debug_info', [
            'employe_id' => $employe_id,
            'mois' => $mois,
            'annee' => $annee,
            'query' => $lastQuery['query'] ?? 'Aucune requête',
            'count' => $plannings->count(),
            'plannings_dates' => $plannings->pluck('date')->toArray()
        ]);

        // Organiser les plannings par date et période
        $planningsByDate = [];
        foreach ($plannings as $planning) {
            $date = $planning->date instanceof Carbon 
                ? $planning->date->format('Y-m-d')
                : Carbon::parse($planning->date)->format('Y-m-d');
                
            if (!isset($planningsByDate[$date])) {
                $planningsByDate[$date] = [
                    'matin' => null,
                    'apres-midi' => null,
                    'journee' => null
                ];
            }
            
            // S'assurer que la période est définie
            $periode = $planning->periode ?? 'journee';
            $planningsByDate[$date][$periode] = $planning;
        }

        return view('plannings.view_monthly_calendar', [
            'employe' => $employe,
            'mois' => $mois,
            'annee' => $annee,
            'moisActuel' => $mois,
            'anneeActuelle' => $annee,
            'planningsByDate' => $planningsByDate,
            'debutPeriode' => $debutPeriode,
            'finPeriode' => $finPeriode,
            'debug' => true
        ]);
    }

    public function monPlanning(Request $request)
    {
        $employe = auth()->user()->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Profil employé non trouvé');
        }

        // Gérer la navigation entre les mois
        $date = $request->input('date') ? Carbon::createFromFormat('Y-m', $request->input('date')) : now();
        $previousMonth = $date->copy()->subMonth()->format('Y-m');
        $nextMonth = $date->copy()->addMonth()->format('Y-m');
        
        // Récupérer les lieux de travail pour le filtre
        $lieux = Lieu::where(function($query) use ($employe) {
                $query->where('societe_id', $employe->societe_id)
                      ->orWhereNull('societe_id')
                      ->orWhere('is_special', true);
            })
            ->get();
        $lieuId = $request->input('lieu_id');

        // Récupérer les plannings du mois
        $planningsQuery = $employe->plannings()
            ->where('societe_id', $employe->societe_id) // Ajout du filtre par société
            ->whereBetween('date', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            ])
            ->with('lieu');

        // Appliquer le filtre par lieu si nécessaire
        if ($lieuId) {
            $planningsQuery->where('lieu_id', $lieuId);
        }

        $plannings_mois = $planningsQuery
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy(function($planning) {
                return $planning->date->format('Y-m-d');
            });

        // Récupérer les plannings de la semaine
        $plannings_semaine = $employe->plannings()
            ->where('societe_id', $employe->societe_id) // Ajout du filtre par société
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->with('lieu')
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        // Statistiques du mois
        $stats = [
            'heures_mois' => $planningsQuery->sum('heures_travaillees'),
            'jours_travailles' => $plannings_mois->count(),
            'lieux_distincts' => $planningsQuery->distinct('lieu_id')->count()
        ];

        return view('plannings.calendar', compact(
            'plannings_mois',
            'plannings_semaine',
            'stats',
            'date',
            'previousMonth',
            'nextMonth',
            'lieux',
            'lieuId'
        ));
    }

    public function show(Planning $planning)
    {
        $this->authorize('view', $planning);
        return view('plannings.show', compact('planning'));
    }

    public function edit(Planning $planning)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un employeur et que le planning appartient à sa société
        if (!$user->isEmployeur() || $planning->employe->societe_id !== $user->societe_id) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        // Récupérer les employés et les lieux pour le formulaire
        $employes = Employe::where('societe_id', $user->societe_id)
            ->orderBy('nom')
            ->get();
        $lieux = Lieu::where(function($query) use ($user) {
                $query->where('societe_id', $user->societe_id)
                      ->orWhereNull('societe_id')
                      ->orWhere('is_special', true);
            })
            ->orderBy('nom')
            ->get();

        return view('plannings.edit', compact('planning', 'employes', 'lieux'));
    }

    public function update(Request $request, Planning $planning)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un employeur et que le planning appartient à sa société
        if (!$user->isEmployeur() || $planning->employe->societe_id !== $user->societe_id) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'lieu_id' => 'required|exists:lieux,id',
            'date' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
            'heures_composees' => 'nullable|numeric|min:0'
        ]);

        // Calculer les heures travaillées
        $debut = Carbon::parse($validated['date'] . ' ' . $validated['heure_debut']);
        $fin = Carbon::parse($validated['date'] . ' ' . $validated['heure_fin']);
        $heures = $fin->floatDiffInHours($debut);

        $planning->update([
            'employe_id' => $validated['employe_id'],
            'lieu_id' => $validated['lieu_id'],
            'date' => $validated['date'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'heures_travaillees' => $heures,
            'heures_composees' => $validated['heures_composees'] ?? 0
        ]);

        // Charger les relations nécessaires pour l'email
        $planning->load('lieu');
        
        // Notifier l'employé de la modification
        $planning->employe->user->notify(new PlanningUpdatedNotification($planning, [
            'message' => 'Votre planning a été modifié',
            'date' => $planning->date->format('Y-m-d')
        ]));
        
        // Envoyer un email à l'employé avec le nouveau design et pièce jointe
        if ($planning->employe->user && $planning->employe->user->email) {
            Mail::to($planning->employe->user->email)
                ->queue(new PlanningUpdated($planning, $planning->employe->user));
        }

        return redirect()->route('plannings.calendar')
            ->with('success', 'Planning modifié avec succès.');
    }

    public function destroy(Planning $planning)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un employeur et que le planning appartient à sa société
        if (!$user->isEmployeur() || $planning->employe->societe_id !== $user->societe_id) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $planning->delete();

        return redirect()->route('plannings.calendar')
            ->with('success', 'Planning supprimé avec succès.');
    }

    public function destroyMonthly($employeId, $yearMonth)
    {
        try {
            // Vérifier que l'employé appartient à la société de l'employeur
            $employe = Employe::where('id', $employeId)
                ->where('societe_id', auth()->user()->societe_id)
                ->firstOrFail();

            // Extraire l'année et le mois
            list($year, $month) = explode('-', $yearMonth);

            // Supprimer tous les plannings de l'employé pour le mois spécifié
            Planning::where('employe_id', $employeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->delete();

            return redirect()->back()->with('success', 'Les plannings ont été supprimés avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression des plannings.');
        }
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2000'
        ]);

        $employe = Employe::findOrFail($request->employe_id);
        
        // Vérifier que l'employeur a accès à cet employé
        if ($employe->societe_id !== auth()->user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cet employé');
        }

        // Créer la date à partir des paramètres
        $date = Carbon::create($request->annee, $request->mois, 1);
        
        // Récupérer les plannings du mois
        $plannings = Planning::where('employe_id', $request->employe_id)
            ->whereYear('date', $request->annee)
            ->whereMonth('date', $request->mois)
            ->with(['lieu'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        // Calculer le total des heures
        $totalHeures = $plannings->sum('heures_travaillees');

        // Générer le PDF
        $pdf = PDF::loadView('pdf.planning-mensuel', [
            'plannings' => $plannings,
            'employe' => $employe,
            'mois' => $date->locale('fr')->monthName,
            'annee' => $request->annee,
            'totalHeures' => $totalHeures
        ]);

        // Nom du fichier
        $filename = "planning_{$employe->nom}_{$employe->prenom}_{$date->format('Y_m')}.pdf";

        // Retourner le PDF pour téléchargement
        return $pdf->download($filename);
    }

    public function exportPdfEmploye(Request $request)
    {
        // Validation des paramètres
        $request->validate([
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2000'
        ]);

        $user = auth()->user();
        $employe = $user->employe;
        
        // Créer la date à partir des paramètres
        $date = Carbon::create($request->annee, $request->mois, 1);
        
        // Récupérer les plannings du mois
        $plannings = Planning::where('employe_id', $employe->id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $request->annee)
            ->whereMonth('date', $request->mois)
            ->with(['lieu'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        // Calculer le total des heures
        $totalHeures = $plannings->sum('heures_travaillees');

        // Générer le PDF
        $pdf = PDF::loadView('pdf.planning-mensuel', [
            'plannings' => $plannings,
            'employe' => $employe,
            'mois' => $date->locale('fr')->monthName,
            'annee' => $request->annee,
            'totalHeures' => $totalHeures
        ]);

        // Nom du fichier
        $filename = "planning_{$employe->nom}_{$employe->prenom}_{$date->format('Y_m')}.pdf";

        // Retourner le PDF pour téléchargement
        return $pdf->download($filename);
    }
    
    /**
     * Exporter le planning mensuel en PDF avec les modifications en rouge
     */
    public function exportPdfWithModifications(Request $request)
    {
        // Validation des paramètres
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2000',
            'modified_plannings' => 'required|string', // IDs des plannings modifiés en JSON
            'temporary_plannings' => 'nullable|string' // Plannings temporaires en JSON (jours de repos RH)
        ]);

        // Récupérer l'employé
        $employe = Employe::findOrFail($request->employe_id);
        
        // Vérifier que l'utilisateur a le droit de voir cet employé
        $this->authorize('view', $employe);
        
        // Créer la date à partir des paramètres
        $date = Carbon::create($request->annee, $request->mois, 1);
        
        // Récupérer les plannings du mois
        $planningsCollection = Planning::where('employe_id', $employe->id)
            ->whereYear('date', $request->annee)
            ->whereMonth('date', $request->mois)
            ->with(['lieu'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
            
        // Organiser les plannings par date pour le template
        $plannings = collect();
        foreach ($planningsCollection as $planning) {
            $dateStr = $planning->date->format('Y-m-d');
            $plannings->put($dateStr, $planning);
        }

        // Calculer le total des heures
        $totalHeures = $planningsCollection->sum('heures_travaillees');
        
        // Récupérer les IDs des plannings modifiés
        $modifiedPlannings = json_decode($request->modified_plannings, true);
        
        // Récupérer les plannings temporaires (jours de repos RH)
        $temporaryPlannings = [];
        if ($request->has('temporary_plannings')) {
            $temporaryPlannings = json_decode($request->temporary_plannings, true);
        }
        
        // Calculer les dates de début et de fin du mois
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // Générer le PDF
        $pdf = PDF::loadView('plannings.pdf', [
            'plannings' => $plannings,
            'employe' => $employe,
            'mois' => $date->locale('fr')->monthName,
            'annee' => $request->annee,
            'totalHeures' => $totalHeures,
            'modifiedPlannings' => $modifiedPlannings,
            'temporaryPlannings' => $temporaryPlannings,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        // Nom du fichier
        $filename = "planning_modifie_{$employe->nom}_{$employe->prenom}_{$date->format('Y_m')}.pdf";

        // Retourner le PDF pour téléchargement
        return $pdf->download($filename);
    }

    public function exportPDF(Request $request, $employe_id, $mois, $annee)
    {
        try {
            $employe = Employe::findOrFail($employe_id);
            
            // Créer la date
            $date = Carbon::create($annee, $mois, 1);
            
            // Récupérer les plannings du mois
            $plannings = Planning::where('employe_id', $employe_id)
                ->whereYear('date', $annee)
                ->whereMonth('date', $mois)
                ->with(['lieu'])
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->get();

            // Calculer le total des heures
            $totalHeures = $plannings->sum('heures_travaillees');

            // Générer le PDF
            $pdf = PDF::loadView('pdf.planning-mensuel', [
                'plannings' => $plannings,
                'employe' => $employe,
                'mois' => $date->locale('fr')->monthName,
                'annee' => $annee,
                'totalHeures' => $totalHeures
            ]);

            // Forcer le téléchargement
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="planning_' . $employe->nom . '_' . $employe->prenom . '_' . $date->format('m_Y') . '.pdf"',
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du PDF'], 500);
        }
    }

    public function exportMensuel(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $user = auth()->user();
        $employe = $user->employe;
        
        // Extraire l'année et le mois
        $date = Carbon::createFromFormat('Y-m', $request->month);
        $year = $date->year;
        $month = $date->month;

        // Récupérer les plannings du mois
        $plannings = Planning::where('employe_id', $employe->id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with(['lieu'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        // Calculer le total des heures
        $totalHeures = $plannings->sum('heures_travaillees');

        // Générer le PDF
        $pdf = PDF::loadView('pdf.planning-mensuel', [
            'plannings' => $plannings,
            'employe' => $employe,
            'mois' => $date->locale('fr')->monthName,
            'annee' => $year,
            'totalHeures' => $totalHeures
        ]);

        // Nom du fichier
        $filename = "planning_{$employe->nom}_{$employe->prenom}_{$date->format('Y_m')}.pdf";

        // Retourner le PDF pour téléchargement
        return $pdf->download($filename);
    }

    /**
     * Affiche l'index des plannings pour un employé.
     */
    public function employeIndex(Request $request)
    {
        $user = auth()->user();
        
        // Vérifier si l'utilisateur est un employé et a un employé associé
        if (!$user->isEmploye()) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $employe = $user->employe;
        if (!$employe) {
            // Créer un nouvel employé pour l'utilisateur
            $employe = Employe::create([
                'nom' => explode(' ', $user->name)[0] ?? $user->name,
                'prenom' => explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'telephone' => $user->phone,
                'societe_id' => $user->societe_id,
                'user_id' => $user->id
            ]);
        }

        $selectedYear = $request->get('annee', now()->year);
        $selectedMonth = $request->get('mois', now()->month);

        $plannings = Planning::where('employe_id', $employe->id)
            ->where('societe_id', $user->societe_id) // Ajout du filtre par société
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->with(['lieu', 'societe'])
            ->get();

        return view('plannings.employe', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'plannings' => $plannings,
            'totalHeures' => $plannings->sum('heures_travaillees')
        ]);
    }



    public function calendarIndex(Request $request)
    {
        $user = auth()->user();
        $moisActuel = $request->get('mois', now()->month);
        $anneeActuelle = $request->get('annee', now()->year);
        
        // Récupérer l'employé si c'est un employé connecté
        $employe = null;
        if ($user->isEmploye()) {
            $employe = $user->employe;
        }

        // Créer les dates pour le mois
        $dateDebut = Carbon::create($anneeActuelle, $moisActuel, 1);
        $debutPeriode = $dateDebut->copy()->startOfWeek(Carbon::MONDAY);
        $finPeriode = $dateDebut->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Récupérer les plannings pour la période
        $planningsQuery = Planning::where('societe_id', $user->societe_id)
            ->whereBetween('date', [$debutPeriode, $finPeriode])
            ->with(['employe', 'lieu']);

        // Si c'est un employé, filtrer ses plannings uniquement
        if ($employe) {
            $planningsQuery->where('employe_id', $employe->id);
        }

        $plannings = $planningsQuery->get();

        // Organiser les plannings par date
        $planningsByDate = [];
        foreach ($plannings as $planning) {
            $date = $planning->date->format('Y-m-d');
            if (!isset($planningsByDate[$date])) {
                $planningsByDate[$date] = [
                    'matin' => null,
                    'apres-midi' => null,
                    'journee' => null
                ];
            }
            $planningsByDate[$date][$planning->periode] = $planning;
        }

        return view('plannings.calendar_mensuel', [
            'employe' => $employe,
            'planningsByDate' => $planningsByDate,
            'moisActuel' => $moisActuel,
            'anneeActuelle' => $anneeActuelle,
            'debutPeriode' => $debutPeriode,
            'finPeriode' => $finPeriode
        ]);
    }

    public function createMonthlyCalendar(Request $request)
    {
        $user = auth()->user();
        $employe_id = $request->input('employe_id');
        $mois = $request->input('mois');
        $annee = $request->input('annee');

        // Récupérer l'employé
        $employe = Employe::where('id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        // Récupérer les lieux de travail (en excluant les lieux spéciaux)
        $lieux = Lieu::where('societe_id', $user->societe_id)
            ->where('is_special', false)
            ->orderBy('nom')
            ->get();

        // Créer les dates pour le mois actuel
        $dateDebut = Carbon::create($annee, $mois, 1);
        $debutPeriode = $dateDebut->copy()->startOfWeek(Carbon::MONDAY);
        $finPeriode = $dateDebut->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Récupérer les plannings existants pour ce mois
        $plannings = Planning::where('employe_id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $annee)
            ->whereMonth('date', $mois)
            ->with('lieu')
            ->get();

        // Organiser les plannings par date et période
        $planningsByDate = [];
        foreach ($plannings as $planning) {
            $date = $planning->date->format('Y-m-d');
            if (!isset($planningsByDate[$date])) {
                $planningsByDate[$date] = [
                    'matin' => null,
                    'apres-midi' => null,
                    'journee' => null
                ];
            }
            $planningsByDate[$date][$planning->periode] = $planning;
        }
        
        // Calculer le mois précédent
        $dateMoisPrecedent = $dateDebut->copy()->subMonth();
        $moisPrecedent = $dateMoisPrecedent->month;
        $anneePrecedente = $dateMoisPrecedent->year;
        
        // Créer les dates pour le mois précédent
        $dateDebutPrecedent = Carbon::create($anneePrecedente, $moisPrecedent, 1);
        $debutPeriodePrecedent = $dateDebutPrecedent->copy()->startOfWeek(Carbon::MONDAY);
        $finPeriodePrecedent = $dateDebutPrecedent->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        
        // Récupérer les plannings du mois précédent
        $planningsPrecedents = Planning::where('employe_id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $anneePrecedente)
            ->whereMonth('date', $moisPrecedent)
            ->with('lieu')
            ->get();
            
        // Organiser les plannings du mois précédent par date et période
        $planningsByDatePrecedent = [];
        foreach ($planningsPrecedents as $planning) {
            $date = $planning->date->format('Y-m-d');
            if (!isset($planningsByDatePrecedent[$date])) {
                $planningsByDatePrecedent[$date] = [
                    'matin' => null,
                    'apres-midi' => null,
                    'journee' => null
                ];
            }
            $planningsByDatePrecedent[$date][$planning->periode] = $planning;
        }

        return view('plannings.create_monthly_calendar', compact(
            'employe',
            'lieux',
            'mois',
            'annee',
            'planningsByDate',
            'debutPeriode',
            'finPeriode',
            'moisPrecedent',
            'anneePrecedente',
            'planningsByDatePrecedent',
            'debutPeriodePrecedent',
            'finPeriodePrecedent'
        ));
    }
    
    public function editMonthlyCalendar(Request $request)
    {
        $user = auth()->user();
        $employe_id = $request->input('employe_id');
        $mois = $request->input('mois');
        $annee = $request->input('annee');

        // Récupérer l'employé
        $employe = Employe::where('id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        // Récupérer les lieux de travail (en excluant les lieux spéciaux)
        $lieux = Lieu::where('societe_id', $user->societe_id)
            ->where('is_special', false)
            ->orderBy('nom')
            ->get();

        // Créer les dates pour le mois
        $dateDebut = Carbon::create($annee, $mois, 1);
        $debutPeriode = $dateDebut->copy()->startOfWeek(Carbon::MONDAY);
        $finPeriode = $dateDebut->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Récupérer les plannings existants pour ce mois
        $plannings = Planning::where('employe_id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $annee)
            ->whereMonth('date', $mois)
            ->with('lieu')
            ->get();

        // Organiser les plannings par date et période
        $planningsByDate = [];
        foreach ($plannings as $planning) {
            $date = $planning->date->format('Y-m-d');
            if (!isset($planningsByDate[$date])) {
                $planningsByDate[$date] = [
                    'matin' => null,
                    'apres-midi' => null,
                    'journee' => null
                ];
            }
            $planningsByDate[$date][$planning->periode] = $planning;
        }

        // Indiquer que nous sommes en mode modification
        $isModification = true;
        
        return view('plannings.edit_monthly_calendar', compact(
            'employe',
            'lieux',
            'mois',
            'annee',
            'planningsByDate',
            'debutPeriode',
            'finPeriode',
            'isModification'
        ));
    }
    
    public function updateMonthlyCalendar(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer',
            'plannings' => 'required|array'
        ]);

        $employe = Employe::where('id', $data['employe_id'])
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Supprimer tous les plannings existants pour ce mois et cet employé
            Planning::where('employe_id', $employe->id)
                ->where('societe_id', $user->societe_id)
                ->whereYear('date', $data['annee'])
                ->whereMonth('date', $data['mois'])
                ->delete();

            // Créer les nouveaux plannings
            foreach ($data['plannings'] as $date => $planning) {
                if ($planning['type_horaire'] === 'simple') {
                    // Créer un planning simple pour la journée
                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'journee',
                        'heure_debut' => $planning['horaires']['debut'],
                        'heure_fin' => $planning['horaires']['fin'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut'], $planning['horaires']['fin'])
                    ]);
                } else {
                    // Créer deux plannings pour la journée (matin et après-midi)
                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'matin',
                        'heure_debut' => $planning['horaires']['debut_matin'],
                        'heure_fin' => $planning['horaires']['fin_matin'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_matin'], $planning['horaires']['fin_matin'])
                    ]);

                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'apres-midi',
                        'heure_debut' => $planning['horaires']['debut_aprem'],
                        'heure_fin' => $planning['horaires']['fin_aprem'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_aprem'], $planning['horaires']['fin_aprem'])
                    ]);
                }
            }

            DB::commit();
            
            // Envoyer une notification à l'employé concerné
                        // Envoyer une notification à l'employé concerné
                        if ($employe->user) {
                            // Récupérer le premier planning mis à jour pour la notification et l'email
                            $updatedPlanning = Planning::where('employe_id', $employe->id)
                                ->whereYear('date', $data['annee'])
                                ->whereMonth('date', $data['mois'])
                                ->first();
                            
                            // Notification dans l'application
                            $employe->user->notify(new PlanningUpdatedNotification(
                                $updatedPlanning,
                                [
                                    'message' => 'Votre planning a été mis à jour',
                                    'dates' => implode(', ', array_keys($data['plannings']))
                                ]
                            ));
                            
                            // Envoi d'un email
                            if ($employe->user->email && $updatedPlanning) {
                                Mail::to($employe->user->email)
                                    ->queue(new PlanningUpdated($updatedPlanning, $employe->user));
                            }
                        }
            
            return response()->json(['message' => 'Planning modifié avec succès']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la modification du planning: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la modification'], 500);
        }
    }

    public function getMonthlyCalendar($employe_id, $annee, $mois)
    {
        try {
            $user = auth()->user();
            
            // Vérifier que l'employé appartient à la société
            $employe = Employe::where('id', $employe_id)
                ->where('societe_id', $user->societe_id)
                ->firstOrFail();

            // Récupérer tous les plannings du mois
            $plannings = Planning::with('lieu')
                ->where('employe_id', $employe_id)
                ->whereYear('date', $annee)
                ->whereMonth('date', $mois)
                ->get()
                ->map(function ($planning) {
                    // Formater la date en Y-m-d
                    $date = Carbon::parse($planning->date)->format('Y-m-d');
                    
                    // Formater les heures en H:i
                    $heure_debut = $planning->getHeureDebutFormatteeAttribute();
                    $heure_fin = $planning->getHeureFinFormatteeAttribute();
                    
                    return [
                        'date' => $date,
                        'lieu_id' => $planning->lieu_id,
                        'lieu' => optional($planning->lieu)->nom ?? 'Lieu inconnu',
                        'periode' => $planning->periode,
                        'heure_debut' => $heure_debut,
                        'heure_fin' => $heure_fin,
                        'heures_travaillees' => $planning->heures_travaillees
                    ];
                });

            \Log::info('Nombre de plannings trouvés:', ['count' => $plannings->count()]);
            return response()->json(['plannings' => $plannings]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des plannings: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des plannings'], 500);
        }
    }

    /**
     * Affiche le planning d'un collègue
     */
    public function voirPlanningCollegue(Request $request, Employe $employe)
    {
        $user = auth()->user();
        
        // Vérifier que l'employé appartient à la même société
        if ($employe->societe_id !== $user->societe_id) {
            return redirect()->route('employe.plannings.index')
                ->with('error', 'Vous ne pouvez pas voir le planning de cet employé.');
        }

        // Convertir les paramètres en entiers
        $selectedYear = (int) $request->get('annee', now()->year);
        $selectedMonth = (int) $request->get('mois', now()->month);

        $plannings = Planning::where('employe_id', $employe->id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->with(['lieu', 'societe'])
            ->get();

        return view('plannings.employe-collegue', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'plannings' => $plannings,
            'totalHeures' => $plannings->sum('heures_travaillees'),
            'employe' => $employe
        ]);
    }

    /**
     * Affiche le planning d'un collègue en format calendrier
     */
    public function voirPlanningCollegueCalendar(Request $request, Employe $employe)
    {
        $user = auth()->user();
        
        // Vérifier que l'employé appartient à la même société
        if ($employe->societe_id !== $user->societe_id) {
            return redirect()->route('employe.plannings.index')
                ->with('error', 'Vous ne pouvez pas voir le planning de cet employé.');
        }

        // Convertir les paramètres en entiers
        $selectedYear = (int) $request->get('annee', now()->year);
        $selectedMonth = (int) $request->get('mois', now()->month);

        // Récupérer tous les plannings du mois
        $plannings = Planning::where('employe_id', $employe->id)
            ->where('societe_id', $user->societe_id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->with(['lieu', 'societe'])
            ->get();

        // Créer un tableau des plannings indexé par date
        $planningsByDate = [];
        foreach ($plannings as $planning) {
            $date = Carbon::parse($planning->date)->format('Y-m-d');
            if (!isset($planningsByDate[$date])) {
                $planningsByDate[$date] = [];
            }
            $planningsByDate[$date][] = $planning;
        }

        // Obtenir le premier et dernier jour du mois
        $firstDay = Carbon::create($selectedYear, $selectedMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        return view('plannings.employe-collegue-calendar', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'plannings' => $planningsByDate,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'employe' => $employe,
            'totalHeures' => $plannings->sum('heures_travaillees')
        ]);
    }

    /**
     * Calcule le nombre d'heures travaillées entre deux heures
     */
    private function calculerHeuresTravaillees($debut, $fin)
    {
        // Si les heures sont 00:00, retourner 0
        if ($debut === '00:00' && $fin === '00:00') {
            return 0;
        }

        $heureDebut = Carbon::parse($debut);
        $heureFin = Carbon::parse($fin);
        
        // Utiliser abs() pour avoir une valeur absolue positive
        return abs($heureFin->floatDiffInHours($heureDebut));
    }

    public static function convertToHHMM($heures)
    {
        $heures_entieres = floor($heures);
        $minutes = round(($heures - $heures_entieres) * 60);
        
        // Si les minutes sont 60, on ajoute une heure
        if ($minutes == 60) {
            $heures_entieres++;
            $minutes = 0;
        }
        
        return sprintf("%02d:%02d", $heures_entieres, $minutes);
    }

    public function remplirJoursNonTravailles($employe_id, $annee, $mois)
    {
        $user = auth()->user();
        
        // Vérifier que l'employé appartient à la société
        $employe = Employe::where('id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        // Trouver le lieu RH
        $lieuRH = Lieu::where('nom', 'RH')
            ->where('is_special', true)
            ->firstOrFail();

        // Créer les dates pour le mois
        $dateDebut = Carbon::create($annee, $mois, 1);
        $finMois = $dateDebut->copy()->endOfMonth();

        // Parcourir tous les jours du mois
        $date = $dateDebut->copy();
        while ($date <= $finMois) {
            // Si c'est un samedi ou un dimanche
            if ($date->isWeekend()) {
                // Vérifier si un planning existe déjà pour cette date
                $planningExistant = Planning::where('employe_id', $employe_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();

                // Si aucun planning n'existe, créer un planning "Repos"
                if (!$planningExistant) {
                    Planning::create([
                        'employe_id' => $employe_id,
                        'societe_id' => $user->societe_id,
                        'date' => $date->format('Y-m-d'),
                        'type_horaire' => 'repos',
                        'lieu_id' => $lieuRH->id,
                        'heure_debut' => '00:00',
                        'heure_fin' => '00:00',
                        'pause_debut' => null,
                        'pause_fin' => null
                    ]);
                }
            }
            $date->addDay();
        }

        return response()->json(['success' => true]);
    }

    public function ajouterConge($employe_id, $annee, $mois)
    {
        $user = auth()->user();
        
        // Vérifier que l'employé appartient à la société
        $employe = Employe::where('id', $employe_id)
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        // Trouver le lieu "CP" (Congé Payé)
        $lieuCP = Lieu::where('nom', 'CP')
            ->where('is_special', true)
            ->firstOrFail();

        // Créer les dates pour le mois
        $dateDebut = Carbon::create($annee, $mois, 1);
        $finMois = $dateDebut->copy()->endOfMonth();

        // Parcourir tous les jours du mois
        $date = $dateDebut->copy();
        while ($date <= $finMois) {
            // Si ce n'est pas un weekend
            if (!$date->isWeekend()) {
                // Vérifier si un planning existe déjà pour cette date
                $planningExistant = Planning::where('employe_id', $employe_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();

                // Si aucun planning n'existe, créer un planning "CP"
                if (!$planningExistant) {
                    Planning::create([
                        'employe_id' => $employe_id,
                        'societe_id' => $user->societe_id,
                        'date' => $date->format('Y-m-d'),
                        'type_horaire' => 'conge',
                        'lieu_id' => $lieuCP->id,
                        'heure_debut' => '00:00',
                        'heure_fin' => '00:00',
                        'pause_debut' => null,
                        'pause_fin' => null
                    ]);
                }
            }
            $date->addDay();
        }

        return response()->json(['success' => true]);
    }
    
    /**
     * Traite une demande de modification de planning.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function demandeModification(Request $request)
    {
        $user = auth()->user();
        $employe = $user->employe;
        
        if (!$employe) {
            return response()->json(['error' => 'Profil employé non trouvé'], 404);
        }
        
        $validated = $request->validate([
            'planning_id' => 'nullable|exists:plannings,id',
            'type_modification' => 'required|in:horaires,lieu,absence,autre',
            'motif' => 'required|string|min:5',
            'details' => 'nullable|string',
            'nouvelle_date' => 'nullable|date',
            'nouveau_lieu_id' => 'nullable|exists:lieux,id',
            'nouvelle_heure_debut' => 'nullable|date_format:H:i',
            'nouvelle_heure_fin' => 'nullable|date_format:H:i|after:nouvelle_heure_debut',
        ]);
        
        // Créer la demande de modification
        $modification = ModificationPlanning::create([
            'employe_id' => $employe->id,
            'planning_id' => $validated['planning_id'] ?? null,
            'type_modification' => $validated['type_modification'],
            'date_demande' => now(),
            'statut' => 'en_attente',
            'motif' => $validated['motif'],
            'details' => $validated['details'] ?? null,
            'nouvelle_date' => $validated['nouvelle_date'] ?? null,
            'nouveau_lieu_id' => $validated['nouveau_lieu_id'] ?? null,
            'nouvelle_heure_debut' => $validated['nouvelle_heure_debut'] ?? null,
            'nouvelle_heure_fin' => $validated['nouvelle_heure_fin'] ?? null,
        ]);
        
        // Notifier l'employeur (à implémenter si nécessaire)
        // $employe->societe->employeur->notify(new DemandeModificationPlanning($modification));
        
        return response()->json([
            'success' => true,
            'message' => 'Votre demande de modification a été enregistrée et sera traitée prochainement.'
        ]);
    }
}

