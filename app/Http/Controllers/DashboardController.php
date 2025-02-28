<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Conge;
use App\Models\Planning;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        Log::info('Dashboard access', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'password_changed' => $user->password_changed
        ]);

        // Si l'utilisateur n'a pas changé son mot de passe et qu'il est employé, le rediriger
        if ($user->role === 'employe' && $user->password_changed === false) {
            Log::info('Redirecting to password change', [
                'user_id' => $user->id
            ]);
            return redirect('/change-password')
                ->with('warning', 'Vous devez changer votre mot de passe avant de continuer.');
        }

        // Si c'est un employeur
        if ($user->role === 'employeur') {
            $societe = $user->societe;

            // Si l'employeur n'a pas encore créé sa société, le rediriger vers la création
            if (!$societe) {
                return redirect()->route('societes.create')
                    ->with('warning', 'Veuillez d\'abord créer votre société.');
            }

            $stats = [
                'employes_count' => $societe->employes()->count(),
                'lieux_count' => $societe->lieux()->count(),
                'plannings_count' => \App\Models\Employe::query()
                    ->join('users', 'employes.user_id', '=', 'users.id')
                    ->join('plannings', 'employes.id', '=', 'plannings.employe_id')
                    ->where('users.societe_id', $societe->id)
                    ->where('users.role', 'employe')
                    ->whereBetween('plannings.date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                    ->distinct()
                    ->count('employes.id'),
                'conges_en_attente' => \App\Models\Employe::query()
                    ->join('users', 'employes.user_id', '=', 'users.id')
                    ->join('conges', 'employes.id', '=', 'conges.employe_id')
                    ->where('users.societe_id', $societe->id)
                    ->where('users.role', 'employe')
                    ->where('conges.statut', 'en_attente')
                    ->distinct()
                    ->count('employes.id')
            ];

            // Récupération des plannings via le modèle Planning
            $plannings = Planning::query()
                ->select('plannings.*')
                ->join('employes', 'plannings.employe_id', '=', 'employes.id')
                ->join('users', 'employes.user_id', '=', 'users.id')
                ->where('users.societe_id', $societe->id)
                ->whereBetween('plannings.date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->with(['employe', 'lieu'])
                ->orderBy('plannings.date')
                ->orderBy('plannings.heure_debut')
                ->get();

            // Récupération des congés avec leurs relations
            $conges = Conge::query()
                ->select('conges.*')
                ->join('employes', 'conges.employe_id', '=', 'employes.id')
                ->join('users', 'employes.user_id', '=', 'users.id')
                ->where('users.societe_id', $societe->id)
                ->where('conges.statut', 'en_attente')
                ->with('employe') // Eager loading de la relation employe
                ->orderBy('conges.date_debut')
                ->limit(5)
                ->get();

            $demandesConges = $societe->conges()->count();

            // Récupérer les employés qui travaillent aujourd'hui
            $today = now()->format('Y-m-d');
            $employesAujourdhui = Planning::where('date', $today)
                ->where('societe_id', $societe->id)
                ->with(['employe', 'lieu'])
                ->orderBy('heure_debut')
                ->get()
                ->groupBy('employe_id')
                ->map(function ($plannings) {
                    return [
                        'employe' => $plannings->first()->employe,
                        'lieu' => $plannings->first()->lieu,
                        'heures' => $plannings->map(function ($planning) {
                            return [
                                'debut' => $planning->heure_debut,
                                'fin' => $planning->heure_fin
                            ];
                        })
                    ];
                });

            // Récupérer tous les employés de la société pour les sélecteurs d'export
            $employes = \App\Models\Employe::query()
                ->join('users', 'employes.user_id', '=', 'users.id')
                ->where('users.societe_id', $societe->id)
                ->where('users.role', 'employe')
                ->select('employes.*')
                ->orderBy('employes.nom')
                ->get();

            return view('dashboard.employeur', compact(
                'societe',
                'stats',
                'plannings',
                'conges',
                'demandesConges',
                'employesAujourdhui',
                'employes'
            ));
        }

        // Pour les employés
        return $this->employeDashboard();
    }

    private function employeDashboard()
    {
        $user = Auth::user();
        $employe = $user->employe;
        $societe = $user->societe;

        return view('dashboard.employe', compact('employe', 'societe'));
    }
}