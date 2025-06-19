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
use Illuminate\Support\Facades\DB;

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
     * Affiche les formations d'un employé ou de tous les employés
     */
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
            // Charger tous les employés avec leurs formations et les données pivot complètes
            $employes = Employe::where('societe_id', $user->societe_id)
                ->with(['formations' => function($query) {
                    $query->select('formations.*', 'employe_formation.date_obtention', 'employe_formation.date_recyclage', 'employe_formation.last_recyclage', 'employe_formation.commentaire');
                }])
                ->get();
            
            return view('employes.formations', compact('employes'));
        }
    }

    /**
     * Enregistre un nouvel employé dans la base de données
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
        
        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:employes,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'date_embauche' => 'nullable|date',
            'date_naissance' => 'nullable|date',
            'numero_securite_sociale' => 'nullable|string|max:21',
            'situation_familiale' => 'nullable|string|max:50',
            'nombre_enfants' => 'nullable|integer|min:0',
            'contact_urgence_nom' => 'nullable|string|max:255',
            'contact_urgence_telephone' => 'nullable|string|max:20',
            'poste' => 'required|string|max:255',
            'type_contrat' => 'required|string|max:50',
            'duree_contrat' => 'nullable|string|max:255',
            'temps_travail' => 'required|string|in:plein,partiel',
            'pourcentage_travail' => 'nullable|integer|min:1|max:99',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            // Début de la transaction
            DB::beginTransaction();
            
            // Création de l'employé
            $employe = new Employe();
            $employe->nom = $request->nom;
            $employe->prenom = $request->prenom;
            $employe->email = $request->email;
            $employe->telephone = $request->telephone;
            $employe->adresse = $request->adresse;
            $employe->date_embauche = $request->date_embauche;
            $employe->date_naissance = $request->date_naissance;
            $employe->numero_securite_sociale = $request->numero_securite_sociale;
            $employe->situation_familiale = $request->situation_familiale;
            $employe->nombre_enfants = $request->nombre_enfants;
            $employe->contact_urgence_nom = $request->contact_urgence_nom;
            $employe->contact_urgence_telephone = $request->contact_urgence_telephone;
            $employe->poste = $request->poste;
            $employe->type_contrat = $request->type_contrat;
            
            // Gérer les champs conditionnels
            if ($request->type_contrat == 'CDD' || $request->type_contrat == 'Intérim' || $request->type_contrat == 'Stage' || $request->type_contrat == 'Alternance') {
                $employe->duree_contrat = $request->duree_contrat;
            }
            
            $employe->temps_travail = $request->temps_travail;
            if ($request->temps_travail == 'partiel') {
                $employe->pourcentage_travail = $request->pourcentage_travail;
            }
            
            $employe->societe_id = $user->societe_id;
            
            // Traitement de la photo de profil
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('photos_profil', $filename, 'public');
                $employe->photo_profil = $path;
            }
            
            // Création d'un compte utilisateur avec mot de passe aléatoire
            $password = Str::random(10); // Génère un mot de passe aléatoire de 10 caractères
            $userAccount = new User();
            $userAccount->name = $request->prenom . ' ' . $request->nom;
            $userAccount->email = $request->email;
            $userAccount->password = bcrypt($password);
            $userAccount->role = 'employe';
            $userAccount->societe_id = $user->societe_id;
            $userAccount->password_changed = false;
            $userAccount->save();
            
            // Association de l'utilisateur à l'employé
            $employe->user_id = $userAccount->id;
            $employe->save();
            
            // Stockage du mot de passe en session pour l'afficher à l'employeur
            session(['temp_password' => $password]);
            
            // Traitement des formations
            if ($request->has('formation_ids')) {
                $formationIds = $request->formation_ids;
                $formationDatesObtention = $request->formation_dates_obtention;
                $formationDatesRecyclage = $request->formation_dates_recyclage;
                $formationCommentaires = $request->formation_commentaires;
                
                foreach ($formationIds as $key => $formationId) {
                    if (!empty($formationId)) {
                        $employe->formations()->attach($formationId, [
                            'date_obtention' => $formationDatesObtention[$key] ?? null,
                            'date_recyclage' => $formationDatesRecyclage[$key] ?? null,
                            'commentaire' => $formationCommentaires[$key] ?? null,
                        ]);
                    }
                }
            }
            
            // Traitement des documents administratifs
            if ($request->hasFile('document_files')) {
                $documentFiles = $request->file('document_files');
                $documentTypes = $request->document_types;
                $documentCommentaires = $request->document_commentaires;
                
                foreach ($documentFiles as $key => $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('documents_employes/' . $employe->id, $filename, 'public');
                        
                        // Enregistrer le document dans la base de données
                        $document = new DocumentAdministratif();
                        $document->employe_id = $employe->id;
                        $document->type = $documentTypes[$key] ?? 'Autre';
                        $document->fichier = $path;
                        $document->notes = $documentCommentaires[$key] ?? null;
                        $document->save();
                    }
                }
            }
            
            // Traitement du matériel attribué
            if ($request->has('materiel_types')) {
                $materielTypes = $request->materiel_types;
                $materielNumeros = $request->materiel_numeros;
                $materielCommentaires = $request->materiel_commentaires;
                
                foreach ($materielTypes as $key => $type) {
                    if (!empty($type)) {
                        $materiel = new Materiel();
                        $materiel->employe_id = $employe->id;
                        $materiel->type = $type;
                        $materiel->numero_serie = $materielNumeros[$key] ?? null;
                        $materiel->commentaire = $materielCommentaires[$key] ?? null;
                        $materiel->save();
                    }
                }
            }
            
            // Traitement des badges d'accès
            if ($request->has('badge_types')) {
                $badgeTypes = $request->badge_types;
                $badgeNumeros = $request->badge_numeros;
                $badgeDateDelivrance = $request->badge_dates_delivrance;
                
                foreach ($badgeTypes as $key => $type) {
                    if (!empty($type)) {
                        $badge = new BadgeAcces();
                        $badge->employe_id = $employe->id;
                        $badge->type = $type;
                        $badge->numero_badge = $badgeNumeros[$key] ?? null;
                        $badge->date_emission = $badgeDateDelivrance[$key] ?? null;
                        $badge->actif = true;
                        $badge->save();
                    }
                }
            }
            
            // Traitement des accès informatiques
            if ($request->has('acces_systemes')) {
                $accesSystemes = $request->acces_systemes;
                $accesIdentifiants = $request->acces_identifiants;
                $accesCommentaires = $request->acces_commentaires;
                
                foreach ($accesSystemes as $key => $systeme) {
                    if (!empty($systeme)) {
                        $acces = new AccesInformatique();
                        $acces->employe_id = $employe->id;
                        $acces->systeme = $systeme;
                        $acces->identifiant = $accesIdentifiants[$key] ?? null;
                        $acces->notes = $accesCommentaires[$key] ?? null;
                        $acces->actif = true;
                        $acces->date_creation = now();
                        $acces->save();
                    }
                }
            }
            
            // Validation de la transaction
            DB::commit();
            
            // Récupération du mot de passe temporaire généré
            $tempPassword = session('temp_password');
            session()->forget('temp_password'); // Suppression de la session après récupération
            
            return redirect()->route('employes.show', $employe->id)
                ->with('success', 'L\'employé a été créé avec succès.')
                ->with('password', $tempPassword);
                
        } catch (\Exception $e) {
            // Annulation de la transaction en cas d'erreur
            DB::rollBack();
            
            Log::error('Erreur lors de la création d\'un employé : ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'employé. Veuillez réessayer.');
        }
    }
    
    /**
     * Supprime un employé de la base de données
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        try {
            // Vérifier que l'employé appartient bien à la société de l'utilisateur connecté
            $employe = Employe::where('id', $id)
                ->where('societe_id', $user->societe_id)
                ->firstOrFail();
            
            // Début de la transaction
            DB::beginTransaction();
            
            // Supprimer le compte utilisateur associé si existant
            if ($employe->user_id) {
                User::where('id', $employe->user_id)->delete();
            }
            
            // Supprimer les formations associées
            $employe->formations()->detach();
            
            // Supprimer les documents administratifs
            $documents = DocumentAdministratif::where('employe_id', $id)->get();
            foreach ($documents as $document) {
                // Supprimer le fichier physique si existant
                if ($document->fichier && Storage::disk('public')->exists($document->fichier)) {
                    Storage::disk('public')->delete($document->fichier);
                }
                $document->delete();
            }
            
            // Supprimer le matériel attribué
            Materiel::where('employe_id', $id)->delete();
            
            // Supprimer les badges d'accès
            BadgeAcces::where('employe_id', $id)->delete();
            
            // Supprimer les accès informatiques
            AccesInformatique::where('employe_id', $id)->delete();
            
            // Supprimer la photo de profil si existante
            if ($employe->photo_profil && Storage::disk('public')->exists($employe->photo_profil)) {
                Storage::disk('public')->delete($employe->photo_profil);
            }
            
            // Supprimer l'employé
            $employe->delete();
            
            // Valider la transaction
            DB::commit();
            
            return redirect()->route('employes.index')
                ->with('success', 'L\'employé a été supprimé avec succès.');
                
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            
            Log::error('Erreur lors de la suppression de l\'employé #' . $id . ': ' . $e->getMessage());
            
            return redirect()->route('employes.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'employé.');
        }
    }
    
    // Autres méthodes du contrôleur...
}
