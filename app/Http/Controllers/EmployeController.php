<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\User;
use App\Models\Formation;
use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class EmployeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }
        
        // Récupérer le paramètre de recherche et le mode d'affichage
        $search = $request->input('search');
        $viewMode = $request->input('view_mode', 'grid'); // Par défaut: grille
        
        // Construire la requête de base
        $query = Employe::with(['formations' => function($query) {
                $query->select('formations.*', 'employe_formation.date_obtention', 'employe_formation.date_recyclage', 'employe_formation.commentaire');
            }])
            ->where('societe_id', auth()->user()->societe_id);
        
        // Appliquer le filtre de recherche par nom/prénom/email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Ajuster le nombre d'employés par page selon le mode d'affichage
        $perPage = $viewMode === 'list' ? 15 : 9;
        
        // Exécuter la requête avec pagination
        try {
            $employes = $query->paginate($perPage)->appends([
                'search' => $search,
                'view_mode' => $viewMode
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, initialiser avec une collection vide paginée
            $employes = new \Illuminate\Pagination\LengthAwarePaginator(
                [], // items
                0,  // total
                $perPage, // perPage
                1,  // currentPage
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
        
        // Statistiques pour le tableau de bord
        $societeId = auth()->user()->societe_id;
        $now = Carbon::now();
        
        // Nombre total d'employés
        $totalEmployes = Employe::where('societe_id', $societeId)->count();
        
        // Taux d'occupation moyen (employés avec plannings actifs / total)
        $today = $now->format('Y-m-d');
        $employesActifs = DB::table('employes')
            ->join('plannings', 'employes.id', '=', 'plannings.employe_id')
            ->where('employes.societe_id', $societeId)
            ->where('plannings.date', '=', $today)
            ->distinct('employes.id')
            ->count('employes.id');
            
        $tauxOccupation = $totalEmployes > 0 ? round(($employesActifs / $totalEmployes) * 100) : 0;
        
        // Nombre de congés en cours et à venir
        $today = $now->format('Y-m-d');
        $congesEnCours = DB::table('conges')
            ->join('employes', 'conges.employe_id', '=', 'employes.id')
            ->where('employes.societe_id', $societeId)
            ->where('conges.date_debut', '<=', $today)
            ->where('conges.date_fin', '>=', $today)
            ->where('conges.statut', 'accepte')
            ->count();
            
        $congesAVenir = DB::table('conges')
            ->join('employes', 'conges.employe_id', '=', 'employes.id')
            ->where('employes.societe_id', $societeId)
            ->where('conges.date_debut', '>', $today)
            ->where('conges.statut', 'accepte')
            ->count();
        
        return view('employes.index', compact(
            'employes', 
            'search', 
            'viewMode',
            'totalEmployes', 
            'tauxOccupation', 
            'congesEnCours', 
            'congesAVenir'
        ));
    }

    public function create()
    {
        return view('employes.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:employes|unique:users',
                'telephone' => 'nullable|string|max:20',
            ]);

            if (!Auth::user()->societe_id) {
                throw new \Exception('L\'employeur doit avoir une société avant de pouvoir ajouter des employés.');
            }

            $tempPassword = Str::random(12);

            DB::beginTransaction();
            
            try {
                $user = new User();
                $user->forceFill([
                    'name' => $validated['prenom'] . ' ' . $validated['nom'],
                    'email' => $validated['email'],
                    'password' => Hash::make($tempPassword),
                    'role' => 'employe',
                    'societe_id' => Auth::user()->societe_id,
                    'password_changed' => false
                ]);
                $user->save();

                $employe = new Employe();
                $employe->forceFill([
                    'nom' => $validated['nom'],
                    'prenom' => $validated['prenom'],
                    'email' => $validated['email'],
                    'telephone' => $validated['telephone'] ?? null,
                    'user_id' => $user->id,
                    'societe_id' => Auth::user()->societe_id
                ]);
                $employe->save();
                
                DB::commit();
                
                session()->flash('temp_password', $tempPassword);
                
                return redirect()->route('employes.index')
                    ->with('success', 'Employé créé avec succès. Mot de passe temporaire : ' . $tempPassword);
                    
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    public function edit(Employe $employe)
    {
        $this->authorize('update', $employe);
        // Charger explicitement la relation formations avec l'employé
        $employe->load('formations');
        $formations = Formation::where('societe_id', auth()->user()->societe_id)->get();
        return view('employes.edit', compact('employe', 'formations'));
    }

    public function update(Request $request, Employe $employe)
    {
        $this->authorize('update', $employe);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:employes,email,' . $employe->id,
            'telephone' => 'nullable|string|max:20',
            'formations' => 'nullable|array',
            'formations.*.selected' => 'nullable|boolean',
            'formations.*.date_obtention' => 'nullable|date',
            'formations.*.date_recyclage' => 'nullable|date',
            'formations.*.commentaire' => 'nullable|string'
        ]);

        $employe->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone']
        ]);

        if (isset($validated['formations'])) {
            $formationsData = [];
            foreach ($validated['formations'] as $formationId => $data) {
                if (!empty($data['selected'])) {
                    $formationsData[$formationId] = [
                        'date_obtention' => $data['date_obtention'] ?? null,
                        'date_recyclage' => $data['date_recyclage'] ?? null,
                        'commentaire' => $data['commentaire'] ?? null
                    ];
                }
            }
            $employe->formations()->sync($formationsData);
        } else {
            $employe->formations()->detach();
        }

        return redirect()->route('employes.index')
            ->with('success', 'Employé mis à jour avec succès');
    }

    public function destroy(Employe $employe)
    {
        $this->authorize('delete', $employe);
        $employe->delete();
        return redirect()->route('employes.index')
            ->with('success', 'Employé supprimé avec succès');
    }

    public function formations(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }

        if ($request->route('employe')) {
            $employe = Employe::with('formations')->findOrFail($request->route('employe'));
            $employes = collect([$employe]);
        } else {
            $employes = Employe::where('societe_id', $user->societe_id)
                ->with(['formations' => function($query) {
                    $query->select('formations.*', 'employe_formation.date_obtention', 'employe_formation.date_recyclage', 'employe_formation.commentaire');
                }])
                ->get();
        }

        return view('employes.formations', compact('employes'));
    }

    public function stats(Employe $employe)
    {
        $user = auth()->user();
        
        // Vérifier que l'employé appartient à la société de l'utilisateur
        if ($employe->societe_id !== $user->societe_id) {
            abort(403);
        }

        // Charger les formations avec les données du pivot
        $employe->load('formations');

        // Récupérer tous les plannings de l'employé
        $plannings = Planning::where('employe_id', $employe->id)
            ->with('lieu')
            ->orderBy('date')
            ->get();

        // Calculer les heures par lieu de travail
        $workByLocation = [];
        foreach ($plannings as $planning) {
            if ($planning->lieu) {
                $lieu = $planning->lieu->nom;
                // Exclure RH et CP
                if (!in_array($lieu, ['RH', 'CP'])) {
                    if (!isset($workByLocation[$lieu])) {
                        $workByLocation[$lieu] = 0;
                    }
                    $workByLocation[$lieu] += $planning->heures_travaillees;
                }
            }
        }

        // Calculer les heures par mois
        $workByMonth = [];
        foreach ($plannings as $planning) {
            $monthKey = Carbon::parse($planning->date)->format('Y-m');
            if (!isset($workByMonth[$monthKey])) {
                $workByMonth[$monthKey] = 0;
            }
            $workByMonth[$monthKey] += $planning->heures_travaillees;
        }

        // Trier les mois par ordre chronologique
        ksort($workByMonth);

        // Récupérer les formations avec leur statut
        $formations = collect();
        if ($employe->formations) {
            $formations = $employe->formations->map(function ($formation) {
                $dateObtention = Carbon::parse($formation->pivot->date_obtention);
                $dateRecyclage = $formation->pivot->date_recyclage ? Carbon::parse($formation->pivot->date_recyclage) : null;
                
                return [
                    'nom' => $formation->nom,
                    'date_obtention' => $dateObtention,
                    'date_recyclage' => $dateRecyclage,
                    'status' => $dateRecyclage && $dateRecyclage->isPast() ? 'expired' : 'valid'
                ];
            });
        }

        // Informations de débogage
        $debug = [
            'plannings_count' => $plannings->count(),
            'has_locations' => !empty($workByLocation),
            'has_months' => !empty($workByMonth),
            'formations_count' => $formations->count()
        ];

        // Convertir les données en format adapté pour Chart.js
        $chartData = [
            'locations' => [
                'labels' => array_keys($workByLocation),
                'data' => array_values($workByLocation)
            ],
            'months' => [
                'labels' => array_map(function($month) {
                    return Carbon::createFromFormat('Y-m', $month)->format('M Y');
                }, array_keys($workByMonth)),
                'data' => array_values($workByMonth)
            ]
        ];

        return view('employes.stats', compact(
            'employe',
            'workByLocation',
            'workByMonth',
            'formations',
            'debug',
            'chartData'
        ));
    }
}