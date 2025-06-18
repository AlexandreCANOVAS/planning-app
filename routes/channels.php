<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Employe;

// Canal pour le modèle User (format Laravel Echo par défaut)
Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    // Autoriser uniquement si l'utilisateur accède à son propre canal
    return (int) $user->id === (int) $id;
});

Broadcast::channel('societe.{id}', function (User $user, $id) {
    return true; // Temporairement autoriser tout le monde pour tester
});

Broadcast::channel('employe.{id}', function (User $user, $id) {
    // Log de débogage pour l'autorisation du canal
    \Illuminate\Support\Facades\Log::info('Tentative d\'accès au canal employe.' . $id, [
        'user_id' => $user->id,
        'user_email' => $user->email,
        'employe_id' => $id
    ]);
    
    // Vérifier si l'utilisateur est l'employé concerné ou son employeur
    $employe = Employe::find($id);
    
    if (!$employe) {
        \Illuminate\Support\Facades\Log::warning('Employé non trouvé: ' . $id);
        return false;
    }
    
    // Autoriser l'employé lui-même
    if ($employe->user_id === $user->id) {
        \Illuminate\Support\Facades\Log::info('Accès autorisé: l\'utilisateur est l\'employé lui-même');
        return true;
    }
    
    // Autoriser l'employeur de l'employé
    if ($employe->societe && $employe->societe->user_id === $user->id) {
        \Illuminate\Support\Facades\Log::info('Accès autorisé: l\'utilisateur est l\'employeur de l\'employé');
        return true;
    }
    
    \Illuminate\Support\Facades\Log::warning('Accès refusé au canal employe.' . $id . ' pour l\'utilisateur ' . $user->id);
    return false;
});