<?php

namespace App\Http\Controllers;

use App\Models\Echange;
use App\Models\Employe;
use App\Models\User;
use App\Models\Planning;
use App\Notifications\ExchangeRequestNotification;
use App\Notifications\ExchangeStatusChangedNotification;
use App\Notifications\ExchangeAcceptedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EchangeController extends Controller
{
    /**
     * Afficher la liste des demandes d'échange
     */
    public function index()
    {
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Vous devez être connecté en tant qu\'employé pour accéder à cette page.');
        }
        
        // Récupérer les demandes d'échange envoyées par l'employé
        $demandesEnvoyees = Echange::where('employe_id', $employe->id)
            ->with(['targetEmploye'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Récupérer les demandes d'échange reçues par l'employé
        $demandesRecues = Echange::where('target_employe_id', $employe->id)
            ->with(['employe'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('echanges.index', compact('demandesEnvoyees', 'demandesRecues'));
    }
    
    /**
     * Afficher le formulaire de création d'une demande d'échange
     */
    public function create()
    {
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Vous devez être connecté en tant qu\'employé pour accéder à cette page.');
        }
        
        // Récupérer les collègues de la même société
        $collegues = Employe::where('societe_id', $employe->societe_id)
            ->where('id', '!=', $employe->id)
            ->orderBy('nom')
            ->get();
            
        // Récupérer les plannings de l'employé pour les 3 prochains mois
        $dateDebut = Carbon::now()->startOfDay();
        $dateFin = Carbon::now()->addMonths(3)->endOfDay();
        
        $plannings = Planning::where('employe_id', $employe->id)
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->with('lieu')
            ->orderBy('date')
            ->get()
            ->groupBy(function($planning) {
                return $planning->date->format('Y-m-d');
            });
            
        return view('echanges.create', compact('collegues', 'plannings'));
    }
    
    /**
     * Enregistrer une nouvelle demande d'échange
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Vous devez être connecté en tant qu\'employé pour effectuer cette action.');
        }
        
        $data = $request->validate([
            'target_employe_id' => 'required|exists:employes,id',
            'date' => 'required|date',
            'target_date' => 'required|date',
            'commentaire' => 'nullable|string|max:500'
        ]);
        
        $targetEmploye = Employe::findOrFail($data['target_employe_id']);
        
        // Vérifier que les deux employés appartiennent à la même société
        if ($employe->societe_id !== $targetEmploye->societe_id) {
            return redirect()->back()->with('error', 'Vous ne pouvez demander un échange qu\'avec un collègue de votre société.');
        }
        
        // Créer la demande d'échange
        $echange = Echange::create([
            'employe_id' => $employe->id,
            'target_employe_id' => $targetEmploye->id,
            'date' => $data['date'],
            'target_date' => $data['target_date'],
            'status' => 'pending',
            'societe_id' => $employe->societe_id,
            'commentaire' => $data['commentaire'] ?? null
        ]);
        
        // Envoyer une notification à l'employé cible
        if ($targetEmploye->user) {
            $targetEmploye->user->notify(new ExchangeRequestNotification(
                $employe, 
                $targetEmploye, 
                Carbon::parse($data['date']), 
                Carbon::parse($data['target_date']),
                $echange->id
            ));
        }
        
        return redirect()->route('echanges.index')->with('success', 'Demande d\'échange envoyée avec succès.');
    }
    
    /**
     * Répondre à une demande d'échange
     */
    public function repondre(Request $request, $id)
    {
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Vous devez être connecté en tant qu\'employé pour effectuer cette action.');
        }
        
        $echange = Echange::where('id', $id)
            ->where('target_employe_id', $employe->id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        $data = $request->validate([
            'response' => 'required|in:accept,reject'
        ]);
        
        $echange->status = $data['response'] === 'accept' ? 'accepted' : 'rejected';
        $echange->save();
        
        // Récupérer l'employé qui a fait la demande
        $requestingEmploye = Employe::findOrFail($echange->employe_id);
        
        // Envoyer une notification à l'employé qui a fait la demande
        if ($requestingEmploye->user) {
            $requestingEmploye->user->notify(new ExchangeStatusChangedNotification(
                $echange
            ));
        }
        
        // Si la demande est acceptée, notifier également les employeurs
        if ($data['response'] === 'accept') {
            $employeurs = User::where('societe_id', $employe->societe_id)
                ->where('role', 'employeur')
                ->get();
                
            foreach ($employeurs as $employeur) {
                $employeur->notify(new ExchangeAcceptedNotification(
                    $echange,
                    $requestingEmploye,
                    $employe
                ));
            }
        }
        
        $message = $data['response'] === 'accept' ? 'Échange accepté avec succès.' : 'Échange refusé.';
        return redirect()->route('echanges.index')->with('success', $message);
    }
    
    /**
     * Afficher les détails d'un échange
     */
    public function show($id)
    {
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Vous devez être connecté en tant qu\'employé pour accéder à cette page.');
        }
        
        $echange = Echange::where(function($query) use ($employe) {
                $query->where('employe_id', $employe->id)
                    ->orWhere('target_employe_id', $employe->id);
            })
            ->where('id', $id)
            ->with(['employe', 'targetEmploye'])
            ->firstOrFail();
            
        return view('echanges.show', compact('echange'));
    }
    
    /**
     * Annuler une demande d'échange
     */
    public function annuler($id)
    {
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Vous devez être connecté en tant qu\'employé pour effectuer cette action.');
        }
        
        $echange = Echange::where('employe_id', $employe->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();
            
        $echange->delete();
        
        return redirect()->route('echanges.index')->with('success', 'Demande d\'échange annulée avec succès.');
    }
}
