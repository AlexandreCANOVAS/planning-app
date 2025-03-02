<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lieu;

class LieuxSpeciauxSeeder extends Seeder
{
    public function run()
    {
        // Supprimer les lieux spéciaux existants
        Lieu::whereIn('nom', ['RH', 'CP'])->delete();

        // Créer le lieu "RH" pour les repos
        Lieu::create([
            'nom' => 'RH',
            'adresse' => 'Repos',
            'ville' => 'N/A',
            'code_postal' => 'N/A',
            'couleur' => '#808080', // Gris
            'societe_id' => null // Pour être accessible à toutes les sociétés
        ]);

        // Créer le lieu "CP" pour les congés payés
        Lieu::create([
            'nom' => 'CP',
            'adresse' => 'Congés payés',
            'ville' => 'N/A',
            'code_postal' => 'N/A',
            'couleur' => '#4CAF50', // Vert
            'societe_id' => null // Pour être accessible à toutes les sociétés
        ]);
    }
}
