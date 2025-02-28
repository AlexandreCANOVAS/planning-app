<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddTestFormations extends Command
{
    protected $signature = 'formations:add-test';
    protected $description = 'Add test formations';

    public function handle()
    {
        $this->info('Starting to add test formations...');
        
        // Vérifier si l'employé existe
        $employe = DB::table('employes')->where('id', 1)->first();
        if (!$employe) {
            $this->error('Employee with ID 1 not found!');
            return;
        }
        $this->info('Found employee: ' . $employe->nom . ' ' . $employe->prenom);
        
        // Créer les formations
        $formations = [
            ['nom' => 'SST', 'societe_id' => 1],
            ['nom' => 'CACES R489', 'societe_id' => 1],
            ['nom' => 'Habilitation électrique', 'societe_id' => 1]
        ];

        foreach ($formations as $formation) {
            $formation['created_at'] = now();
            $formation['updated_at'] = now();
            
            try {
                $this->info('Adding formation: ' . $formation['nom']);
                $id = DB::table('formations')->insertGetId($formation);
                $this->info('Formation added with ID: ' . $id);
                
                // Vérifier si la formation a bien été créée
                $createdFormation = DB::table('formations')->where('id', $id)->first();
                if (!$createdFormation) {
                    $this->error('Formation was not created properly!');
                    continue;
                }
                $this->info('Formation verified in database');
                
                // Associer à l'employé
                $this->info('Associating formation with employee...');
                $pivot = [
                    'employe_id' => 1,
                    'formation_id' => $id,
                    'date_obtention' => '2024-01-01',
                    'date_recyclage' => $formation['nom'] === 'SST' ? '2026-01-01' : '2024-12-31',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                DB::table('employe_formation')->insert($pivot);
                
                // Vérifier si l'association a bien été créée
                $createdPivot = DB::table('employe_formation')
                    ->where('employe_id', 1)
                    ->where('formation_id', $id)
                    ->first();
                if (!$createdPivot) {
                    $this->error('Formation association was not created properly!');
                    continue;
                }
                $this->info('Formation association verified in database');
            } catch (\Exception $e) {
                $this->error('Error adding formation: ' . $e->getMessage());
            }
        }

        // Vérifier le nombre total de formations
        $formationCount = DB::table('formations')->count();
        $this->info('Total formations in database: ' . $formationCount);
        
        // Vérifier le nombre total d'associations
        $pivotCount = DB::table('employe_formation')->count();
        $this->info('Total formation associations in database: ' . $pivotCount);

        $this->info('Test formations added successfully!');
    }
}
