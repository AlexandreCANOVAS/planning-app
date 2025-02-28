<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conge;

class EmployeurController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $societe = $user->societe;

        $stats = [
            'employes_count' => $societe->employes()->count(),
            'lieux_count' => $societe->lieux()->count(),
            'plannings_count' => $societe->plannings()->count(),
            'conges_en_attente' => Conge::whereHas('employe.user', function($query) use ($societe) {
                $query->where('societe_id', $societe->id);
            })->where('statut', 'en_attente')->count()
        ];

        return view('employeurs.index', compact('societe', 'stats'));
    }
}
