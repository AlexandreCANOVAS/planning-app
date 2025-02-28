<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Societe;
use App\Models\Employe;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Créer l'employeur
        $user = User::create([
            'name' => 'Employeur Test',
            'email' => 'employeur@test.com',
            'password' => bcrypt('password'),
            'role' => 'employeur'
        ]);

        // Créer la société
        $societe = Societe::create([
            'nom' => 'Société Test',
            'user_id' => $user->id,
            'siret' => '12345678901234',
            'forme_juridique' => 'SARL',
            'adresse' => '1 rue du Test, 75001 Paris'
        ]);

        // Mettre à jour l'employeur avec l'ID de la société
        $user->societe_id = $societe->id;
        $user->save();

        // Créer l'employé
        $employe = Employe::create([
            'nom' => 'Test',
            'prenom' => 'Employé',
            'email' => 'employe@test.com',
            'telephone' => '0123456789',
            'societe_id' => $societe->id,
            'user_id' => $user->id
        ]);

        // Créer des plannings pour mars 2025
        $date = Carbon::create(2025, 3, 1);
        while ($date->month === 3) {
            if ($date->isWeekday()) { // Du lundi au vendredi
                $planning = $employe->plannings()->create([
                    'date' => $date->format('Y-m-d'),
                    'heure_debut' => '07:00',
                    'heure_fin' => '19:00',
                    'societe_id' => $societe->id
                ]);
            }
            $date->addDay();
        }
    }
}
