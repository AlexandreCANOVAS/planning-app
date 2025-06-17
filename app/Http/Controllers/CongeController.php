<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Employe;
use App\Models\CongeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Notifications\Conge\CongeStatusModifie;
use App\Notifications\Conge\CongeCreatedNotification;
use App\Notifications\Conge\CongeStatusChangedNotification;
use Carbon\Carbon;
use App\Events\CongeRequested;
use App\Events\CongeStatusUpdated;

class CongeController extends Controller
{
    public function index(Request $request)
    {
        $societe = Auth::user()->societe;
        
        // Définir l'année et le mois en cours pour les filtres
        $maintenant = Carbon::now();
        $anneeEnCours = $maintenant->year;
        $moisEnCours = $maintenant->month;
        
        // Récupérer les paramètres de filtrage
        $statut = $request->input('statut');
        $periode = $request->input('periode');
        $employe = $request->input('employe');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        
        // Construire la requête de base
        $query = Conge::query()
            ->select('conges.*')
            ->join('employes', 'conges.employe_id', '=', 'employes.id')
            ->where('employes.societe_id', $societe->id)
            ->with('employe');
            
        // Filtrer par statut
        if ($statut && in_array($statut, ['accepte', 'refuse', 'en_attente'])) {
            $query->where('conges.statut', $statut);
        }
        
        // Filtrer par employé
        if ($employe) {
            $query->whereHas('employe', function($q) use ($employe) {
                $q->where('nom', 'like', "%{$employe}%")
                  ->orWhere('prenom', 'like', "%{$employe}%");
            });
        }
        
        // Filtrer par période
        $anneeEnCours = Carbon::now()->year;
        $moisEnCours = Carbon::now()->month;
        
        if ($dateDebut && $dateFin) {
            // Si des dates spécifiques sont fournies
            // Convertir les dates au format Y-m-d
            $debutFormatted = Carbon::parse($dateDebut)->format('Y-m-d');
            $finFormatted = Carbon::parse($dateFin)->format('Y-m-d');
            
            $query->where(function($q) use ($debutFormatted, $finFormatted) {
                $q->whereBetween('date_debut', [$debutFormatted, $finFormatted])
                  ->orWhereBetween('date_fin', [$debutFormatted, $finFormatted])
                  ->orWhere(function($subq) use ($debutFormatted, $finFormatted) {
                      $subq->where('date_debut', '<=', $debutFormatted)
                           ->where('date_fin', '>=', $finFormatted);
                  });
            });
        } else if ($periode) {
            // Filtrer par période prédéfinie
            switch ($periode) {
                case 'mois_courant':
                    $debut = Carbon::create($anneeEnCours, $moisEnCours, 1)->startOfDay();
                    $fin = Carbon::create($anneeEnCours, $moisEnCours, 1)->endOfMonth()->endOfDay();
                    break;
                case 'trimestre_courant':
                    $trimestre = ceil($moisEnCours / 3);
                    $debutTrimestre = ($trimestre - 1) * 3 + 1;
                    $debut = Carbon::create($anneeEnCours, $debutTrimestre, 1)->startOfDay();
                    $fin = Carbon::create($anneeEnCours, $debutTrimestre + 2, 1)->endOfMonth()->endOfDay();
                    break;
                case 'annee_courante':
                    $debut = Carbon::create($anneeEnCours, 1, 1)->startOfDay();
                    $fin = Carbon::create($anneeEnCours, 12, 31)->endOfDay();
                    break;
                default:
                    $debut = null;
                    $fin = null;
            }
            
            if ($debut && $fin) {
                $query->where(function($q) use ($debut, $fin) {
                    $q->whereBetween('date_debut', [$debut, $fin])
                      ->orWhereBetween('date_fin', [$debut, $fin])
                      ->orWhere(function($subq) use ($debut, $fin) {
                          $subq->where('date_debut', '<=', $debut)
                               ->where('date_fin', '>=', $fin);
                      });
                });
            }
        }
        
        // Exécuter la requête
        $conges = $query->orderBy('conges.date_debut', 'desc')->get();
        
        // Préparer les données pour le graphique
        $congesMensuels = [
            'accepte' => array_fill(0, 12, 0),
            'en_attente' => array_fill(0, 12, 0),
            'refuse' => array_fill(0, 12, 0)
        ];
        
        foreach ($conges as $conge) {
            $dateDebut = Carbon::parse($conge->date_debut);
            if ($dateDebut->year == $anneeEnCours) {
                $mois = $dateDebut->month - 1; // Les tableaux commencent à 0
                $congesMensuels[$conge->statut][$mois]++;
            }
        }
        
        // Récupérer la liste des employés pour le filtre
        $employes = Employe::where('societe_id', $societe->id)
                          ->orderBy('nom')
                          ->get();
        
        return view('conges.index', compact(
            'conges', 
            'congesMensuels', 
            'employes', 
            'statut', 
            'periode', 
            'employe',
            'dateDebut',
            'dateFin'
        ));
    }

    public function create()
    {
        $employes = Auth::user()->societe->employes()->orderBy('nom')->get();
        return view('conges.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string',
        ]);

        $employe = auth()->user()->employe;
        $conge = $employe->conges()->create([
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'motif' => $validated['motif'],
            'statut' => 'en_attente',
        ]);

        try {
            broadcast(new CongeRequested($conge))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Erreur de broadcast: ' . $e->getMessage());
        }

        return redirect()->route('conges.index')
            ->with('success', 'Votre demande de congé a été envoyée.');
    }

    public function edit(Conge $conge)
    {
        $this->authorize('update', $conge);
        
        $employes = Auth::user()->societe->employes()->orderBy('nom')->get();
        return view('conges.edit', compact('conge', 'employes'));
    }

    public function update(Request $request, Conge $conge)
    {
        $this->authorize('update', $conge);

        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,accepte,refuse'
        ]);

        $conge->update($validated);

        return redirect()->route('conges.index')
            ->with('success', 'Demande de congé mise à jour avec succès');
    }

    public function destroy(Conge $conge)
    {
        $this->authorize('delete', $conge);
        
        $conge->delete();

        return redirect()->route('conges.index')
            ->with('success', 'Demande de congé supprimée avec succès');
    }

    public function updateStatus(Request $request, Conge $conge)
    {
        // Vérifier que l'utilisateur est autorisé à modifier ce congé
        if ($conge->employe->societe_id !== Auth::user()->societe->id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette demande de congé.');
        }

        $validated = $request->validate([
            'statut' => 'required|in:accepte,refuse',
            'commentaire' => 'nullable|string'
        ]);
        
        // Enregistrer l'ancien statut avant modification
        $ancienStatut = $conge->statut;
        $nouveauStatut = $validated['statut'];
        $commentaire = $validated['commentaire'] ?? null;

        // Mettre à jour le statut du congé
        $conge->update([
            'statut' => $nouveauStatut,
            'commentaire' => $commentaire
        ]);
        
        // Enregistrer l'historique de la modification
        CongeHistory::create([
            'conge_id' => $conge->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $commentaire
        ]);
        
        // Envoyer une notification à l'employé concerné
        $employe = $conge->employe;
        $employe->user->notify(new CongeStatusChangedNotification($conge, $ancienStatut, $nouveauStatut));

        try {
            broadcast(new CongeStatusUpdated($conge))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Erreur de broadcast: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Le statut de la demande de congé a été mis à jour.');
    }

    public function show(Conge $conge)
    {
        // Vérifier que l'utilisateur a le droit de voir ce congé
        $societe = Auth::user()->societe;
        if ($conge->employe->societe_id !== $societe->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette demande de congé.');
        }

        return view('conges.show', compact('conge'));
    }

    public function mesConges()
    {
        $employe = Auth::user()->employe;
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil employé pour accéder à cette page.');
        }

        $conges = $employe->conges()
            ->orderBy('date_debut', 'desc')
            ->get();

        // Récupérer les congés des autres employés de la même société
        $autresConges = Conge::query()
            ->with('employe')
            ->whereHas('employe', function($query) use ($employe) {
                $query->where('societe_id', $employe->societe_id)
                      ->where('id', '!=', $employe->id);
            })
            ->where('statut', 'accepte')
            ->where(function($query) {
                $query->where('date_debut', '>=', now()->startOfMonth())
                      ->orWhere('date_fin', '>=', now());
            })
            ->orderBy('date_debut')
            ->get();

        return view('conges.mes-conges', compact('conges', 'autresConges'));
    }

    public function demandeConge(Request $request)
    {
        $employe = Auth::user()->employe;
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre profil employé n\'est pas encore configuré.');
        }

        $validated = $request->validate([
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string|max:255'
        ]);

        // Calcul de la durée en jours ouvrés
        $debut = Carbon::parse($validated['date_debut']);
        $fin = Carbon::parse($validated['date_fin']);
        $duree = 0;
        for ($date = $debut; $date->lte($fin); $date->addDay()) {
            if (!$date->isWeekend()) {
                $duree++;
            }
        }

        $conge = $employe->conges()->create([
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'motif' => $validated['motif'],
            'duree' => $duree,
            'statut' => 'en_attente'
        ]);
        
        // Envoyer une notification aux administrateurs
        $admins = \App\Models\User::where('role', 'employeur')
            ->whereHas('societe', function($query) use ($employe) {
                $query->where('id', $employe->societe_id);
            })
            ->get();
            
        foreach ($admins as $admin) {
            $admin->notify(new CongeCreatedNotification($conge));
        }

        try {
            broadcast(new CongeRequested($conge))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Erreur de broadcast: ' . $e->getMessage());
        }

        return redirect()->route('employe.conges.index')
            ->with('success', 'Votre demande de congé a été enregistrée.');
    }

    public function annulerConge(Conge $conge)
    {
        $employe = Auth::user()->employe;
        if (!$employe || $conge->employe_id !== $employe->id) {
            return redirect()->route('employe.conges.index')
                ->with('error', 'Vous n\'êtes pas autorisé à annuler cette demande de congé.');
        }

        if ($conge->statut !== 'en_attente') {
            return redirect()->route('employe.conges.index')
                ->with('error', 'Vous ne pouvez annuler que les demandes en attente.');
        }

        $conge->delete();

        return redirect()->route('employe.conges.index')
            ->with('success', 'Votre demande de congé a été annulée.');
    }

    public function congesCalendar()
    {
        return view('conges.calendar');
    }

    public function getCongesEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $societe = Auth::user()->employe->societe;
        
        $conges = Conge::query()
            ->select('conges.*', 'employes.nom', 'employes.prenom', 'employes.id as employe_id')
            ->join('employes', 'conges.employe_id', '=', 'employes.id')
            ->where('employes.societe_id', $societe->id)
            ->where('statut', 'accepte')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('date_debut', [$start, $end])
                      ->orWhereBetween('date_fin', [$start, $end]);
            })
            ->get();

        // Couleurs pour les employés
        $colors = [
            '#FF6B6B', // Rouge
            '#4ECDC4', // Turquoise
            '#45B7D1', // Bleu
            '#96CEB4', // Vert
            '#D4A5A5', // Rose
            '#9370DB', // Violet
            '#FFB347', // Orange
            '#87CEEB', // Bleu ciel
            '#98FB98', // Vert clair
            '#DDA0DD', // Violet clair
            '#F0E68C', // Jaune
            '#E6E6FA', // Lavande
            '#FFA07A', // Saumon
            '#20B2AA', // Vert mer
            '#FFB6C1', // Rose clair
        ];

        $events = [];
        foreach ($conges as $conge) {
            // Utiliser l'ID de l'employé pour choisir une couleur
            $colorIndex = $conge->employe_id % count($colors);
            $color = $colors[$colorIndex];

            $events[] = [
                'id' => $conge->id,
                'title' => $conge->nom . ' ' . $conge->prenom,
                'start' => $conge->date_debut->format('Y-m-d'),
                'end' => Carbon::parse($conge->date_fin)->addDay()->format('Y-m-d'),
                'backgroundColor' => $color,
                'borderColor' => 'transparent',
                'textColor' => '#000000',
                'allDay' => true,
                'classNames' => ['calendar-event-block'],
                'display' => 'block',
                'extendedProps' => [
                    'duree' => $conge->duree
                ]
            ];
        }

        return response()->json($events);
    }

    public function calendar()
    {
        $user = auth()->user();
        $employe = $user->employe;

        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Profil employé non trouvé.');
        }

        $conges = Conge::where('employe_id', $employe->id)
            ->with(['type'])
            ->orderBy('date_debut', 'desc')
            ->get()
            ->map(function ($conge) {
                $conge->periode = 'Du ' . $conge->date_debut->format('d/m/Y') . ' au ' . $conge->date_fin->format('d/m/Y');
                return $conge;
            });

        return view('conges.calendar', [
            'conges' => $conges
        ]);
    }

    /**
     * Affiche le calendrier des congés (vue admin)
     */
    public function adminCalendar()
    {
        return view('conges.admin-calendar');
    }

    /**
     * Récupère les événements pour le calendrier admin
     */
    public function getAdminEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $societe = Auth::user()->societe;
        
        $conges = Conge::query()
            ->select('conges.*', 'employes.nom', 'employes.prenom')
            ->join('employes', 'conges.employe_id', '=', 'employes.id')
            ->where('employes.societe_id', $societe->id)
            ->where('conges.statut', 'accepte')
            ->whereBetween('date_debut', [$start, $end])
            ->get();

        return response()->json($conges->map(function ($conge) {
            return [
                'id' => $conge->id,
                'title' => $conge->employe->nom . ' ' . $conge->employe->prenom,
                'start' => $conge->date_debut,
                'end' => Carbon::parse($conge->date_fin)->addDay()->format('Y-m-d'),
                'backgroundColor' => '#4F46E5',
                'borderColor' => '#4F46E5',
                'textColor' => '#ffffff',
            ];
        }));
    }
    
    /**
     * Affiche la liste des congés pour un employé
     */
    public function indexEmploye()
    {
        $employe = Auth::user()->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil employé pour accéder à cette page.');
        }
        
        // Récupérer les congés de l'employé connecté
        $conges = $employe->conges()
            ->orderBy('date_debut', 'desc')
            ->get();
            
        // Récupérer les congés des collègues (autres employés de la même société)
        $autresConges = Conge::query()
            ->with('employe')
            ->whereHas('employe', function($query) use ($employe) {
                $query->where('societe_id', $employe->societe_id)
                      ->where('id', '!=', $employe->id);
            })
            ->where('statut', 'accepte')
            ->where(function($query) {
                $query->where('date_debut', '>=', now()->startOfMonth())
                      ->orWhere('date_fin', '>=', now());
            })
            ->orderBy('date_debut')
            ->get();
            
        return view('conges.mes-conges', compact('conges', 'autresConges'));
    }
    
    /**
     * Affiche les détails d'un congé pour un employé
     */
    public function showEmploye(Conge $conge)
    {
        $employe = Auth::user()->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil employé pour accéder à cette page.');
        }
        
        // Vérifier que l'employé a le droit de voir ce congé (soit c'est le sien, soit il est de la même société)
        if ($conge->employe_id !== $employe->id && $conge->employe->societe_id !== $employe->societe_id) {
            abort(403, 'Vous n\'\u00eates pas autoris\u00e9 \u00e0 voir cette demande de cong\u00e9.');
        }
        
        return view('conges.show-employe', compact('conge'));
    }
} 