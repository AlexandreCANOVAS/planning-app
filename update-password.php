<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Rechercher l'utilisateur
$user = User::where('email', 'test2@gmail.com')->first();

if ($user) {
    // Mettre à jour le mot de passe
    $user->password = Hash::make('password123');
    $user->save();
    echo "Le mot de passe de l'utilisateur {$user->email} a été mis à jour avec succès.\n";
} else {
    echo "Aucun utilisateur trouvé avec l'adresse email test2@gmail.com.\n";
    
    // Afficher tous les utilisateurs pour vérification
    echo "\nListe des utilisateurs disponibles :\n";
    $users = User::all();
    foreach ($users as $u) {
        echo "- {$u->email}\n";
    }
}
