<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employe;
use App\Models\Formation;
use App\Models\DocumentAdministratif;
use App\Models\Materiel;
use App\Models\BadgeAcces;
use App\Models\AccesInformatique;
use App\Models\User;
use App\Models\Planning;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeeInvitation;
use App\Notifications\EmployeeInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EmployeController extends Controller
{
    /**
     * Affiche la liste des employés
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        // Récupérer les paramètres de recherche et d'affichage
        $search = $request->input('search', '');
        $viewMode = $request->input('view_mode', 'cards'); // Mode d'affichage par défaut: cards
        
        // Requête de base pour les employés
        $query = Employe::where('societe_id', $user->societe_id);
        
        // Filtrer par recherche si nécessaire
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }
        
        // Récupérer les employés
        $employes = $query->orderBy('nom')
                         ->orderBy('prenom')
                         ->get();

        // Récupérer les invitations en attente pour la même société
        $invitations = EmployeeInvitation::where('societe_id', $user->societe_id)
            ->where('expires_at', '>', now())
            ->get();
        
        $totalEmployes = $employes->count();
        
        // Calcul du taux d'occupation (valeur par défaut pour l'instant)
        $tauxOccupation = 0;
        if ($totalEmployes > 0) {
            // On pourrait calculer le taux d'occupation réel en fonction des plannings
            // Pour l'instant, on met une valeur par défaut
            $tauxOccupation = 75;
        }
        
        // Nombre de congés en cours et à venir (valeurs par défaut pour l'instant)
        $congesEnCours = 0;
        $congesAVenir = 0;
        
        // On pourrait calculer les congés réels en interrogeant la table des congés
        // Pour l'instant, on met des valeurs par défaut
            
        return view('employes.index', compact(
            'employes',
            'invitations',
            'totalEmployes',
            'tauxOccupation',
            'congesEnCours',
            'congesAVenir',
            'search',
            'viewMode'
        ));
    }
    
    /**
     * Affiche le formulaire de création d'un nouvel employé
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        // Récupérer les formations disponibles pour les associer à l'employé
        $formations = Formation::all();
        
        return view('employes.create', compact('formations'));
    }

    /**
     * Enregistre un nouvel employé (envoi d'une invitation)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        $societe_id = $user->societe->id;

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:employes,email|unique:employee_invitations,email',
            'poste' => 'required|string|max:255',
        ]);

        try {
            $token = Str::uuid()->toString();

            $invitation = EmployeeInvitation::create([
                'email' => $validated['email'],
                'token' => $token,
                'societe_id' => $societe_id,
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'poste' => $validated['poste'],
                'expires_at' => now()->addDays(7),
            ]);

            Notification::route('mail', $validated['email'])
                ->notify(new EmployeeInvitationNotification($invitation->token, $user->societe->nom));

            return redirect()->route('employes.index')
                ->with('success', 'L\'invitation a été envoyée avec succès à ' . $validated['email']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'invitation : ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'envoi de l\'invitation. Veuillez réessayer.')
                ->withInput();
        }
    }
    
    /**
     * Affiche les statistiques d'un employé
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function stats($id)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        try {
            $employe = Employe::where('id', $id)
                ->where('societe_id', $user->societe_id)
                ->firstOrFail();
            
            // Récupérer les plannings de l'employé pour les 12 derniers mois
            $dateDebut = \Carbon\Carbon::now()->subMonths(11)->startOfMonth();
            $dateFin = \Carbon\Carbon::now()->endOfMonth();
            
            $plannings = Planning::where('employe_id', $id)
                ->whereBetween('date', [$dateDebut, $dateFin])
                ->orderBy('date')
                ->get();
            
            // Calculer les statistiques par mois
            $statsMensuelles = [];
            $totalHeures = 0;
            $totalJoursTravailles = 0;
            
            // Préparer les 12 derniers mois
            for ($i = 0; $i < 12; $i++) {
                $mois = \Carbon\Carbon::now()->subMonths($i);
                $key = $mois->format('Y-m');
                $statsMensuelles[$key] = [
                    'mois' => $mois->format('F Y'),
                    'heures' => 0,
                    'jours' => 0,
                    'heures_supplementaires' => 0,
                    'heures_nuit' => 0,
                    'jours_feries' => 0
                ];
            }
            
            // Remplir les statistiques avec les données réelles
            foreach ($plannings as $planning) {
                $moisKey = \Carbon\Carbon::parse($planning->date)->format('Y-m');
                
                if (isset($statsMensuelles[$moisKey])) {
                    // Calculer les heures
                    $heures = $planning->heures_travaillees ?? 0;
                    $statsMensuelles[$moisKey]['heures'] += $heures;
                    $totalHeures += $heures;
                    
                    // Compter les jours travaillés
                    if ($heures > 0) {
                        $statsMensuelles[$moisKey]['jours']++;
                        $totalJoursTravailles++;
                    }
                    
                    // Heures supplémentaires
                    $heuresSupp = $planning->heures_supplementaires ?? 0;
                    $statsMensuelles[$moisKey]['heures_supplementaires'] += $heuresSupp;
                    
                    // Heures de nuit (21h-6h)
                    $heuresNuit = $planning->heures_nuit ?? 0;
                    $statsMensuelles[$moisKey]['heures_nuit'] += $heuresNuit;
                    
                    // Jours fériés
                    if ($planning->jour_ferie) {
                        $statsMensuelles[$moisKey]['jours_feries']++;
                    }
                }
            }
            
            // Inverser l'ordre pour avoir les mois les plus récents en premier
            $statsMensuelles = array_reverse($statsMensuelles);
            
            // Récupérer les formations de l'employé avec les données pivot
            $formations = Formation::join('employe_formation', 'formations.id', '=', 'employe_formation.formation_id')
                ->where('employe_formation.employe_id', $id)
                ->select(
                    'formations.*',
                    'employe_formation.date_obtention',
                    'employe_formation.date_recyclage',
                    'employe_formation.last_recyclage',
                    'employe_formation.commentaire'
                )
                ->get();
                
            // Préparer les données des formations avec leur statut
            foreach ($formations as $key => $formation) {
                $dateRecyclage = $formation->date_recyclage ? \Carbon\Carbon::parse($formation->date_recyclage) : null;
                
                // Déterminer le statut de la formation
                if ($dateRecyclage && $dateRecyclage->isFuture()) {
                    $formations[$key]['status'] = 'valid';
                } else {
                    $formations[$key]['status'] = 'expired';
                }
            }
            
            // Préparer les données pour les graphiques
            $chartData = [
                'months' => [
                    'labels' => [],
                    'data' => []
                ],
                'locations' => [
                    'labels' => [],
                    'data' => []
                ]
            ];
            
            // Données pour le graphique des heures par mois
            foreach ($statsMensuelles as $key => $stats) {
                // Extraire le mois et l'année pour un affichage plus court
                $date = \Carbon\Carbon::createFromFormat('Y-m', $key);
                $chartData['months']['labels'][] = $date->format('M Y');
                $chartData['months']['data'][] = $stats['heures'];
            }
            
            // Données pour le graphique par lieu
            $lieuxStats = DB::table('plannings')
                ->join('lieux', 'plannings.lieu_id', '=', 'lieux.id')
                ->where('plannings.employe_id', $id)
                ->whereBetween('plannings.date', [$dateDebut, $dateFin])
                ->select('lieux.nom', DB::raw('COUNT(*) as count'))
                ->groupBy('lieux.nom')
                ->get();
                
            foreach ($lieuxStats as $lieu) {
                $chartData['locations']['labels'][] = $lieu->nom;
                $chartData['locations']['data'][] = $lieu->count;
            }
            
            return view('employes.stats', compact('employe', 'statsMensuelles', 'totalHeures', 'totalJoursTravailles', 'formations', 'chartData'));
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage des statistiques de l\'employé #' . $id . ': ' . $e->getMessage());
            return redirect()->route('employes.index')
                ->with('error', 'Impossible de charger les statistiques de cet employé.');
        }
    }
    
    /**
     * Affiche les détails d'un employé
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        try {
            $employe = Employe::where('id', $id)
                ->where('societe_id', $user->societe_id)
                ->firstOrFail();
            
            // Récupérer les formations de l'employé avec les données pivot
            $formations = Formation::join('employe_formation', 'formations.id', '=', 'employe_formation.formation_id')
                ->where('employe_formation.employe_id', $id)
                ->select(
                    'formations.*',
                    'employe_formation.date_obtention',
                    'employe_formation.date_recyclage',
                    'employe_formation.last_recyclage',
                    'employe_formation.commentaire'
                )
                ->get();
            
            // Calculer l'âge si la date de naissance est disponible
            $age = null;
            if ($employe->date_naissance) {
                $age = \Carbon\Carbon::parse($employe->date_naissance)->age;
            }
            
            return view('employes.show', compact('employe', 'formations', 'age'));
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage des détails de l\'employé #' . $id . ': ' . $e->getMessage());
            return redirect()->route('employes.index')
                ->with('error', 'Impossible de trouver cet employé.');
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'un employé
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        try {
            $employe = Employe::where('id', $id)
                ->where('societe_id', $user->societe_id)
                ->firstOrFail();
            
            // Récupérer toutes les formations disponibles
            $formations = Formation::orderBy('nom')->get();
            
            // Récupérer les formations de l'employé avec les données pivot
            $employeFormations = $employe->formations()->withPivot(
                'date_obtention', 
                'date_recyclage', 
                'last_recyclage', 
                'commentaire'
            )->get();
            
            return view('employes.edit', compact('employe', 'formations', 'employeFormations'));
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire d\'édition pour l\'employé #' . $id . ': ' . $e->getMessage());
            return redirect()->route('employes.index')
                ->with('error', 'Impossible de trouver cet employé.');
        }
    }

    /**
     * Affiche les formations d'un employé ou de tous les employés
     */
    public function formations(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }

        if ($request->route('employe')) {
            // Récupérer l'ID de l'employé
            $employeId = $request->route('employe');
            
            try {
                // Charger l'employé de base
                $employe = Employe::findOrFail($employeId);
                
                // Récupérer les formations directement depuis la base de données
                $formations = Formation::join('employe_formation', 'formations.id', '=', 'employe_formation.formation_id')
                    ->where('employe_formation.employe_id', $employeId)
                    ->select(
                        'formations.*',
                        'employe_formation.date_obtention',
                        'employe_formation.date_recyclage',
                        'employe_formation.last_recyclage',
                        'employe_formation.commentaire',
                        'employe_formation.employe_id',
                        'employe_formation.formation_id'
                    )
                    ->get();
                
                // Déboguer le nombre de formations trouvées
                Log::debug("Nombre de formations trouvées pour l'employé #{$employeId}: " . $formations->count());
                
                // Créer une collection pour les employés
                $employes = collect([$employe]);
                
                // Retourner la vue avec les données
                return view('employes.formations', compact('employes', 'employe', 'formations'));
                
            } catch (\Exception $e) {
                Log::error('Erreur lors du chargement des formations pour l\'employé #' . $employeId . ': ' . $e->getMessage());
                return redirect()->route('employes.index')
                    ->with('error', 'Impossible de charger les formations de cet employé.');
            }
        } else {
            // Si aucun employé n'est spécifié, afficher les formations de tous les employés
            $employes = Employe::where('societe_id', $user->societe_id)
                ->with(['formations' => function ($query) {
                    $query->withPivot('date_obtention', 'date_recyclage', 'last_recyclage', 'commentaire');
                }])
                ->orderBy('nom')
                ->get();

            // On ne passe pas de variable $employe ou $formations spécifiques
            return view('employes.formations', compact('employes'));
        }
    }

    /**
     * Met à jour les informations d'un employé.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $employe = Employe::where('id', $id)->where('societe_id', $user->societe_id)->firstOrFail();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:employes,email,' . $employe->id,
            'telephone' => 'nullable|string|max:20',
            'poste' => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'numero_securite_sociale' => 'nullable|string',
            'situation_familiale' => 'nullable|string',
            'nombre_enfants' => 'nullable|integer|min:0',
            'contact_urgence_nom' => 'nullable|string',
            'contact_urgence_telephone' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($employe->photo_path) {
                Storage::disk('public')->delete($employe->photo_path);
            }
            $path = $request->file('photo')->store('photos_employes', 'public');
            $validated['photo_path'] = $path;
        }

        $employe->update($validated);

        // Mettre à jour les formations
        if ($request->has('formations')) {
            $formationsData = [];
            foreach ($request->formations as $formation_id => $pivot) {
                $formationsData[$formation_id] = [
                    'date_obtention' => $pivot['date_obtention'] ?? null,
                    'date_recyclage' => $pivot['date_recyclage'] ?? null,
                    'last_recyclage' => $pivot['last_recyclage'] ?? null,
                    'commentaire' => $pivot['commentaire'] ?? null,
                ];
            }
            $employe->formations()->sync($formationsData);
        } else {
            $employe->formations()->detach();
        }

        return redirect()->route('employes.show', $employe->id)->with('success', 'Employé mis à jour avec succès.');
    }

    /**
     * Supprime un employé.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $employe = Employe::where('id', $id)->where('societe_id', $user->societe_id)->firstOrFail();

        // Supprimer la photo de l'employé
        if ($employe->photo_path) {
            Storage::disk('public')->delete($employe->photo_path);
        }

        // Dissocier les formations
        $employe->formations()->detach();

        // Récupérer l'utilisateur associé s'il existe
        $userAccount = User::find($employe->user_id);

        // Supprimer l'employé
        $employe->delete();

        // Supprimer le compte utilisateur associé
        if ($userAccount) {
            $userAccount->delete();
        }

        return redirect()->route('employes.index')->with('success', 'Employé supprimé avec succès.');
    }
}