<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employe;
use Illuminate\Support\Facades\Auth;

class EmployeProfileController extends Controller
{
    /**
     * Affiche le profil de l'employé connecté en lecture seule
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // Récupérer l'employé connecté
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil employé non trouvé.');
        }
        
        return view('employes.profile', compact('employe'));
    }
}
