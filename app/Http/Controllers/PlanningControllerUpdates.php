<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Lieu;
use App\Models\Employe;
use App\Models\ModificationPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PlanningModifie;
use App\Notifications\PlanningCreatedNotification;
use App\Notifications\PlanningUpdatedNotification;
use App\Notifications\ExchangeRequestNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

// Méthodes à ajouter ou modifier dans le PlanningController

/**
 * Méthode storeMonthlyCalendar avec notification
 */
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
        $createdPlannings = [];
        
        foreach ($data['plannings'] as $date => $planning) {
            // Supprimer les plannings existants pour cette date
            Planning::where('employe_id', $employe->id)
                ->where('date', $date)
                ->delete();

            if ($planning['type_horaire'] === 'simple') {
                // Créer un planning simple pour la journée
                $newPlanning = Planning::create([
                    'employe_id' => $employe->id,
                    'societe_id' => $user->societe_id,
                    'lieu_id' => $planning['lieu_id'],
                    'date' => $date,
                    'periode' => 'journee',
                    'heure_debut' => $planning['horaires']['debut'],
                    'heure_fin' => $planning['horaires']['fin'],
                    'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut'], $planning['horaires']['fin'])
                ]);
                
                $createdPlannings[] = $newPlanning;
            } else {
                // Créer deux plannings pour la journée (matin et après-midi)
                $morningPlanning = Planning::create([
                    'employe_id' => $employe->id,
                    'societe_id' => $user->societe_id,
                    'lieu_id' => $planning['lieu_id'],
                    'date' => $date,
                    'periode' => 'matin',
                    'heure_debut' => $planning['horaires']['debut_matin'],
                    'heure_fin' => $planning['horaires']['fin_matin'],
                    'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_matin'], $planning['horaires']['fin_matin'])
                ]);
                
                $createdPlannings[] = $morningPlanning;

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
        if ($employe->user && !empty($createdPlannings)) {
            $employe->user->notify(new PlanningCreatedNotification($createdPlannings[0]));
        }
        
        return response()->json(['message' => 'Planning enregistré avec succès']);
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Erreur lors de l\'enregistrement du planning: ' . $e->getMessage());
        return response()->json(['error' => 'Une erreur est survenue lors de l\'enregistrement'], 500);
    }
}

/**
 * Méthode updateMonthlyCalendar avec notification
 */
public function updateMonthlyCalendar(Request $request)
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
        $updatedPlannings = [];
        $changes = [];
        
        foreach ($data['plannings'] as $date => $planning) {
            // Récupérer les plannings existants pour cette date
            $existingPlannings = Planning::where('employe_id', $employe->id)
                ->where('date', $date)
                ->get();
            
            // Stocker les changements pour la notification
            if ($existingPlannings->isNotEmpty()) {
                $existingPlanning = $existingPlannings->first();
                $changes[$date] = [
                    'old' => [
                        'lieu_id' => $existingPlanning->lieu_id,
                        'heure_debut' => $existingPlanning->heure_debut,
                        'heure_fin' => $existingPlanning->heure_fin
                    ],
                    'new' => [
                        'lieu_id' => $planning['lieu_id'],
                        'heure_debut' => $planning['type_horaire'] === 'simple' ? $planning['horaires']['debut'] : $planning['horaires']['debut_matin'],
                        'heure_fin' => $planning['type_horaire'] === 'simple' ? $planning['horaires']['fin'] : $planning['horaires']['fin_aprem']
                    ]
                ];
            }
            
            // Supprimer les plannings existants pour cette date
            Planning::where('employe_id', $employe->id)
                ->where('date', $date)
                ->delete();

            if ($planning['type_horaire'] === 'simple') {
                // Créer un planning simple pour la journée
                $updatedPlanning = Planning::create([
                    'employe_id' => $employe->id,
                    'societe_id' => $user->societe_id,
                    'lieu_id' => $planning['lieu_id'],
                    'date' => $date,
                    'periode' => 'journee',
                    'heure_debut' => $planning['horaires']['debut'],
                    'heure_fin' => $planning['horaires']['fin'],
                    'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut'], $planning['horaires']['fin'])
                ]);
                
                $updatedPlannings[] = $updatedPlanning;
            } else {
                // Créer deux plannings pour la journée (matin et après-midi)
                $morningPlanning = Planning::create([
                    'employe_id' => $employe->id,
                    'societe_id' => $user->societe_id,
                    'lieu_id' => $planning['lieu_id'],
                    'date' => $date,
                    'periode' => 'matin',
                    'heure_debut' => $planning['horaires']['debut_matin'],
                    'heure_fin' => $planning['horaires']['fin_matin'],
                    'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_matin'], $planning['horaires']['fin_matin'])
                ]);
                
                $updatedPlannings[] = $morningPlanning;

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
        if ($employe->user && !empty($updatedPlannings)) {
            $employe->user->notify(new PlanningUpdatedNotification($updatedPlannings[0], $changes));
        }
        
        return response()->json(['message' => 'Planning mis à jour avec succès']);
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Erreur lors de la mise à jour du planning: ' . $e->getMessage());
        return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour'], 500);
    }
}

/**
 * Méthode pour demander un échange de planning avec notification
 */
public function demanderEchange(Request $request)
{
    $user = auth()->user();
    $employe = $user->employe;
    
    if (!$employe) {
        return redirect()->back()->with('error', 'Vous devez être connecté en tant qu\'employé pour demander un échange.');
    }
    
    $data = $request->validate([
        'target_employe_id' => 'required|exists:employes,id',
        'date' => 'required|date',
        'target_date' => 'required|date'
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
        'societe_id' => $employe->societe_id
    ]);
    
    // Envoyer une notification à l'employé cible
    if ($targetEmploye->user) {
        $targetEmploye->user->notify(new ExchangeRequestNotification(
            $employe, 
            $targetEmploye, 
            Carbon::parse($data['date']), 
            Carbon::parse($data['target_date'])
        ));
    }
    
    return redirect()->back()->with('success', 'Demande d\'échange envoyée avec succès.');
}

/**
 * Méthode pour répondre à une demande d'échange avec notification
 */
public function repondreEchange(Request $request, $id)
{
    $user = auth()->user();
    $employe = $user->employe;
    
    if (!$employe) {
        return redirect()->back()->with('error', 'Vous devez être connecté en tant qu\'employé pour répondre à un échange.');
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
            $echange,
            $requestingEmploye,
            $employe
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
    return redirect()->back()->with('success', $message);
}
