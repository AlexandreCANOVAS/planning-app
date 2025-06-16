<?php
/**
 * Ce fichier contient les modifications à apporter au PlanningController pour ajouter les notifications
 * lors de la création et de la mise à jour des plannings.
 */

// 1. Ajouter ces imports en haut du fichier PlanningController.php (déjà fait)
// use App\Notifications\PlanningCreatedNotification;
// use App\Notifications\PlanningUpdatedNotification;

/**
 * 2. Modifications à apporter à la méthode storeMonthlyCalendar
 * Ajouter ce code juste avant le return response()->json(['message' => 'Planning enregistré avec succès']);
 */

// Récupérer les plannings créés pour envoyer une notification
$createdPlannings = Planning::where('employe_id', $employe->id)
    ->whereIn('date', array_keys($data['plannings']))
    ->orderBy('date')
    ->get();
    
// Envoyer une notification à l'employé concerné
if ($employe->user && !$createdPlannings->isEmpty()) {
    $employe->user->notify(new PlanningCreatedNotification($createdPlannings->first()));
}

/**
 * 3. Modifications à apporter à la méthode updateMonthlyCalendar
 * Ajouter ce code juste avant le return response()->json(['message' => 'Planning mis à jour avec succès']);
 */

// Récupérer les plannings mis à jour pour envoyer une notification
$updatedPlannings = Planning::where('employe_id', $employe->id)
    ->whereIn('date', array_keys($data['plannings']))
    ->orderBy('date')
    ->get();
    
// Envoyer une notification à l'employé concerné
if ($employe->user && !$updatedPlannings->isEmpty()) {
    $employe->user->notify(new PlanningUpdatedNotification($updatedPlannings->first(), [
        'message' => 'Votre planning a été mis à jour',
        'dates' => implode(', ', array_keys($data['plannings']))
    ]));
}

/**
 * 4. Modifications à apporter à la méthode storeMensuel
 * Ajouter ce code juste avant le return redirect()->route('plannings.index')->with('success', 'Planning mensuel créé avec succès');
 */

// Récupérer les plannings créés pour envoyer une notification
$createdPlannings = Planning::where('employe_id', $employe->id)
    ->where('date', '>=', $dateDebut)
    ->where('date', '<=', $dateFin)
    ->orderBy('date')
    ->get();
    
// Envoyer une notification à l'employé concerné
if ($employe->user && !$createdPlannings->isEmpty()) {
    $employe->user->notify(new PlanningCreatedNotification($createdPlannings->first()));
}

/**
 * 5. Modifications à apporter à la méthode store
 * Ajouter ce code juste avant le return response()->json(['message' => 'Planning créé avec succès']);
 */

// Récupérer le planning créé pour envoyer une notification
$planning = Planning::where('employe_id', $data['employe_id'])
    ->where('date', $data['date'])
    ->where('periode', $data['periode'])
    ->orderBy('created_at', 'desc')
    ->first();
    
// Envoyer une notification à l'employé concerné
$employe = Employe::find($data['employe_id']);
if ($employe && $employe->user && $planning) {
    $employe->user->notify(new PlanningCreatedNotification($planning));
}

/**
 * 6. Modifications à apporter à la méthode storeMonthly
 * Ajouter ce code juste avant le return response()->json(['message' => 'Planning mensuel créé avec succès']);
 */

// Récupérer les plannings créés pour envoyer une notification
$createdPlannings = Planning::where('employe_id', $employe->id)
    ->where('date', '>=', $dateDebut)
    ->where('date', '<=', $dateFin)
    ->orderBy('date')
    ->get();
    
// Envoyer une notification à l'employé concerné
if ($employe->user && !$createdPlannings->isEmpty()) {
    $employe->user->notify(new PlanningCreatedNotification($createdPlannings->first()));
}
