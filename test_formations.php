<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ID de l'employé à vérifier
$employeId = 4;

echo "Test de récupération des formations pour l'employé #$employeId\n\n";

// Récupérer directement les formations et leurs données pivot
$formationsData = \DB::table('employe_formation')
    ->join('formations', 'employe_formation.formation_id', '=', 'formations.id')
    ->where('employe_formation.employe_id', $employeId)
    ->select(
        'formations.*',
        'employe_formation.date_obtention',
        'employe_formation.date_recyclage',
        'employe_formation.last_recyclage',
        'employe_formation.commentaire',
        'employe_formation.employe_id',
        'employe_formation.formation_id'
    )
    ->get();

echo "Nombre de formations trouvées: " . $formationsData->count() . "\n\n";

if ($formationsData->count() > 0) {
    foreach ($formationsData as $data) {
        echo "Formation #{$data->id}: {$data->nom}\n";
        echo "  Description: {$data->description}\n";
        echo "  Date d'obtention: " . ($data->date_obtention ?? 'non définie') . "\n";
        echo "  Date de recyclage: " . ($data->date_recyclage ?? 'non définie') . "\n";
        echo "  Dernier recyclage: " . ($data->last_recyclage ?? 'non défini') . "\n";
        echo "  Commentaire: " . ($data->commentaire ?? 'aucun') . "\n\n";
    }
} else {
    echo "Aucune formation trouvée pour cet employé.\n";
}

// Vérifier également la table pivot directement
$pivotData = \DB::table('employe_formation')->where('employe_id', $employeId)->get();
echo "Données de la table pivot:\n";
print_r($pivotData->toArray());
