<?php
/**
 * Ce fichier contient les modifications à apporter au PlanningController pour ajouter les notifications
 * lors de la création et de la mise à jour des plannings.
 * 
 * Instructions:
 * 1. Ajouter les imports nécessaires en haut du fichier PlanningController.php
 * 2. Ajouter les notifications dans les méthodes de création et de mise à jour des plannings
 */

// 1. Ajouter ces imports en haut du fichier PlanningController.php, après les imports existants
use App\Notifications\PlanningCreatedNotification;
use App\Notifications\PlanningUpdatedNotification;

/**
 * 2. Modifications à apporter aux méthodes de création et de mise à jour des plannings
 * 
 * Dans la méthode storeMonthlyCalendar, ajouter ce code juste avant le return response()->json():
 */

// Envoyer une notification à l'employé concerné
if ($employe->user && !empty($createdPlannings)) {
    $employe->user->notify(new PlanningCreatedNotification($createdPlannings[0]));
}

/**
 * Dans la méthode updateMonthlyCalendar, ajouter ce code juste avant le return response()->json():
 */

// Envoyer une notification à l'employé concerné
if ($employe->user && !empty($updatedPlannings)) {
    $employe->user->notify(new PlanningUpdatedNotification($updatedPlannings[0], $changes));
}

/**
 * Dans la méthode store, ajouter ce code juste avant le return response()->json():
 */

// Envoyer une notification à l'employé concerné
$employe = Employe::find($data['employe_id']);
if ($employe && $employe->user) {
    $planning = Planning::where('employe_id', $data['employe_id'])
        ->orderBy('created_at', 'desc')
        ->first();
    
    if ($planning) {
        $employe->user->notify(new PlanningCreatedNotification($planning));
    }
}

/**
 * Dans la méthode storeMensuel, ajouter ce code juste avant le return redirect():
 */

// Notifier l'employé
if (!empty($plannings)) {
    $employe->user->notify(new PlanningCreatedNotification($plannings[0]));
}

/**
 * Dans la méthode storeMonthly, ajouter ce code juste avant le return response()->json():
 */

// Envoyer une notification à l'employé concerné
if ($employe->user) {
    $planning = Planning::where('employe_id', $employe->id)
        ->orderBy('created_at', 'desc')
        ->first();
    
    if ($planning) {
        $employe->user->notify(new PlanningCreatedNotification($planning));
    }
}
