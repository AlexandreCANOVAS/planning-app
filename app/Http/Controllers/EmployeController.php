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

    public function index()
    {
        $user = auth()->user();
        
        if (!$user->societe) {
            return redirect()->route('societes.create')
                ->with('error', 'Vous devez d\'abord créer votre société.');
        }

        $employes = Employe::with(['formations' => function($query) {
                $query->select('formations.*', 'employe_formation.date_obtention', 'employe_formation.date_recyclage', 'employe_formation.commentaire');
            }])
            ->where('societe_id', auth()->user()->societe_id)
            ->paginate(9);

        return view('employes.index', compact('employes'));
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

        // Récupérer tous les plannings de l'employé
        $plannings = Planning::where('employe_id', $employe->id)
            ->with('lieu')
            ->get();

        // Calculer les heures par lieu de travail
        $workByLocation = $plannings
            ->whereNotNull('lieu_id')
            ->groupBy('lieu.nom')
            ->map(function ($plannings) {
                return $plannings->sum('heures_travaillees');
            })
            ->toArray();

        // Calculer les heures par mois
        $workByMonth = $plannings
            ->groupBy(function ($planning) {
                return Carbon::parse($planning->date)->format('Y-m');
            })
            ->map(function ($plannings) {
                return $plannings->sum('heures_travaillees');
            })
            ->toArray();

        // Récupérer les formations
        $formations = $employe->formations ?? collect();

        // Informations de débogage
        $debug = [
            'plannings_count' => $plannings->count(),
            'has_locations' => !empty($workByLocation),
            'has_months' => !empty($workByMonth),
        ];

        return view('employes.stats', compact(
            'employe',
            'workByLocation',
            'workByMonth',
            'formations',
            'debug'
        ));
    }
}