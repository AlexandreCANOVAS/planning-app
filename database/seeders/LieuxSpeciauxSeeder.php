<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LieuTravail;

class LieuxSpeciauxSeeder extends Seeder
{
    public function run()
    {
        // Supprimer les lieux spéciaux existants
        LieuTravail::whereIn('nom', ['RH', 'CP'])->delete();

        // Créer le lieu "RH" pour les repos
        LieuTravail::create([
            'nom' => 'RH',
            'adresse' => 'Repos',
            'couleur' => '#808080', // Gris
            'societe_id' => null // Pour être accessible à toutes les sociétés
        ]);

        // Créer le lieu "CP" pour les congés payés
        LieuTravail::create([
            'nom' => 'CP',
            'adresse' => 'Congés payés',
            'couleur' => '#4CAF50', // Vert
            'societe_id' => null // Pour être accessible à toutes les sociétés
        ]);
    }
}
