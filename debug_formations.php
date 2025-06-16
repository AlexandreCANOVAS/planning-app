<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ID de l'employé à vérifier
$employeId = 4;

// Vérifier si l'employé existe dans la base de données
$employe = \App\Models\Employe::find($employeId);
if (!$employe) {
    echo "Employé #$employeId non trouvé\n";
    exit;
}

echo "Employé trouvé: {$employe->prenom} {$employe->nom}\n";

// Vérifier si des formations sont associées à l'employé dans la table pivot
$formationsCount = \DB::table('employe_formation')->where('employe_id', $employeId)->count();
echo "Nombre de formations dans la table pivot: $formationsCount\n";

// Récupérer les IDs des formations associées à l'employé
$formationIds = \DB::table('employe_formation')->where('employe_id', $employeId)->pluck('formation_id')->toArray();
echo "IDs des formations associées: " . implode(', ', $formationIds) . "\n";

// Charger l'employé avec ses formations
$employe->load('formations');

// Vérifier si les formations ont été chargées
echo "Formations chargées via Eloquent: " . ($employe->formations ? $employe->formations->count() : 'null') . "\n";
if ($employe->formations && $employe->formations->count() > 0) {
    echo "IDs des formations chargées: " . implode(', ', $employe->formations->pluck('id')->toArray()) . "\n";
    
    // Afficher les détails de chaque formation
    foreach ($employe->formations as $formation) {
        echo "Formation #{$formation->id}: {$formation->nom}\n";
        echo "  Date d'obtention: " . ($formation->pivot->date_obtention ?? 'non définie') . "\n";
        echo "  Date de recyclage: " . ($formation->pivot->date_recyclage ?? 'non définie') . "\n";
        echo "  Dernier recyclage: " . ($formation->pivot->last_recyclage ?? 'non défini') . "\n";
    }
} else {
    echo "Aucune formation chargée via Eloquent\n";
    
    // Essayons de charger manuellement les formations
    $formations = \App\Models\Formation::whereIn('id', $formationIds)->get();
    echo "Formations récupérées manuellement: " . $formations->count() . "\n";
    
    foreach ($formations as $formation) {
        echo "Formation #{$formation->id}: {$formation->nom}\n";
        
        // Récupérer les données pivot manuellement
        $pivotData = \DB::table('employe_formation')
            ->where('employe_id', $employeId)
            ->where('formation_id', $formation->id)
            ->first();
        
        if ($pivotData) {
            echo "  Date d'obtention: " . ($pivotData->date_obtention ?? 'non définie') . "\n";
            echo "  Date de recyclage: " . ($pivotData->date_recyclage ?? 'non définie') . "\n";
            echo "  Dernier recyclage: " . ($pivotData->last_recyclage ?? 'non défini') . "\n";
        } else {
            echo "  Aucune donnée pivot trouvée\n";
        }
    }
}
