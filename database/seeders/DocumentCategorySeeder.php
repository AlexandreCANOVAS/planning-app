<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Catégories par défaut pour tous les utilisateurs
        $defaultCategories = [
            [
                'name' => 'Contrat de travail',
                'description' => 'Documents liés aux contrats de travail des employés',
                'is_default' => true
            ],
            [
                'name' => 'Bulletin de paie',
                'description' => 'Fiches de paie mensuelles des employés',
                'is_default' => true
            ],
            [
                'name' => 'Attestation',
                'description' => 'Attestations diverses (mutuelle, emploi, etc.)',
                'is_default' => true
            ],
            [
                'name' => 'Document administratif',
                'description' => 'Documents administratifs divers',
                'is_default' => true
            ],
            [
                'name' => 'Formation',
                'description' => 'Documents liés aux formations des employés',
                'is_default' => true
            ],
            [
                'name' => 'Congés',
                'description' => 'Documents liés aux congés des employés',
                'is_default' => true
            ],
            [
                'name' => 'Avenant',
                'description' => 'Avenants aux contrats de travail',
                'is_default' => true
            ],
            [
                'name' => 'Note de service',
                'description' => 'Notes de service et communications internes',
                'is_default' => true
            ],
            [
                'name' => 'Règlement intérieur',
                'description' => 'Règlement intérieur de l\'entreprise',
                'is_default' => true
            ],
            [
                'name' => 'Autre',
                'description' => 'Autres types de documents',
                'is_default' => true
            ],
        ];

        foreach ($defaultCategories as $category) {
            DocumentCategory::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'is_default' => $category['is_default']
                ]
            );
        }
    }
}
