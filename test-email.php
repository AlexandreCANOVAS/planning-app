<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Mail;
use App\Mail\PlanningCreated;
use App\Models\Planning;
use App\Models\User;

// Récupérer un utilisateur et un planning pour le test
$user = User::first();
$planning = Planning::first();

if ($planning && $user) {
    echo "Envoi d'un email de test à {$user->email} via Mailtrap...\n";
    
    try {
        // Envoyer un email de test
        Mail::to($user->email)->send(new PlanningCreated($planning, $user));
        echo "Email envoyé avec succès! Vérifiez votre boîte Mailtrap.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'envoi de l'email : " . $e->getMessage() . "\n";
    }
} else {
    echo "Impossible de trouver un utilisateur ou un planning pour le test.\n";
}
