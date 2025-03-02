<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lieu;

class SpecialLieuxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le lieu RH s'il n'existe pas
        if (!Lieu::where('nom', 'RH')->where('is_special', true)->exists()) {
            Lieu::create([
                'nom' => 'RH',
                'adresse' => 'N/A',
                'ville' => 'N/A',
                'code_postal' => '00000',
                'is_special' => true,
                'couleur' => '#FF0000', // Rouge
            ]);
        }

        // Créer le lieu CP s'il n'existe pas
        if (!Lieu::where('nom', 'CP')->where('is_special', true)->exists()) {
            Lieu::create([
                'nom' => 'CP',
                'adresse' => 'N/A',
                'ville' => 'N/A',
                'code_postal' => '00000',
                'is_special' => true,
                'couleur' => '#00FF00', // Vert
            ]);
        }
    }
}
