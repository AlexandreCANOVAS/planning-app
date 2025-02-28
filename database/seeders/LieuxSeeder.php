<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lieu;
use App\Models\Societe;

class LieuxSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer toutes les sociétés
        $societes = Societe::all();

        // Pour chaque société, créer les lieux spéciaux
        foreach ($societes as $societe) {
            // Créer RH et CP pour toutes les sociétés
            Lieu::firstOrCreate(
                ['nom' => 'RH', 'societe_id' => $societe->id],
                [
                    'couleur' => '#FF0000', // Rouge
                    'description' => 'Ressources Humaines',
                    'adresse' => 'N/A',
                    'ville' => 'N/A',
                    'code_postal' => '00000'
                ]
            );

            Lieu::firstOrCreate(
                ['nom' => 'CP', 'societe_id' => $societe->id],
                [
                    'couleur' => '#00FF00', // Vert
                    'description' => 'Congés Payés',
                    'adresse' => 'N/A',
                    'ville' => 'N/A',
                    'code_postal' => '00000'
                ]
            );

            // Créer quelques lieux de travail pour chaque société
            $lieux = [
                [
                    'nom' => 'Bureau Principal',
                    'couleur' => '#0000FF', // Bleu
                    'description' => 'Bureau principal de la société',
                    'adresse' => '1 rue Principale',
                    'ville' => 'Paris',
                    'code_postal' => '75001'
                ],
                [
                    'nom' => 'Site A',
                    'couleur' => '#FFA500', // Orange
                    'description' => 'Site de production A',
                    'adresse' => '2 rue des Industries',
                    'ville' => 'Lyon',
                    'code_postal' => '69001'
                ],
                [
                    'nom' => 'Site B',
                    'couleur' => '#800080', // Violet
                    'description' => 'Site de production B',
                    'adresse' => '3 avenue du Travail',
                    'ville' => 'Marseille',
                    'code_postal' => '13001'
                ]
            ];

            foreach ($lieux as $lieu) {
                Lieu::firstOrCreate(
                    ['nom' => $lieu['nom'], 'societe_id' => $societe->id],
                    [
                        'couleur' => $lieu['couleur'],
                        'description' => $lieu['description'],
                        'adresse' => $lieu['adresse'],
                        'ville' => $lieu['ville'],
                        'code_postal' => $lieu['code_postal']
                    ]
                );
            }
        }
    }
}
