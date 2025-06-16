<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Récupérer tous les lieux
$lieux = App\Models\Lieu::all();

// Coordonnées pour quelques villes françaises
$coordonnees = [
    'Paris' => ['latitude' => 48.8566, 'longitude' => 2.3522],
    'Lyon' => ['latitude' => 45.7640, 'longitude' => 4.8357],
    'Marseille' => ['latitude' => 43.2965, 'longitude' => 5.3698],
    'Toulouse' => ['latitude' => 43.6043, 'longitude' => 1.4437],
    'Nice' => ['latitude' => 43.7102, 'longitude' => 7.2620],
    'Nantes' => ['latitude' => 47.2184, 'longitude' => -1.5536],
    'Strasbourg' => ['latitude' => 48.5734, 'longitude' => 7.7521],
    'Bordeaux' => ['latitude' => 44.8378, 'longitude' => -0.5792],
    'Lille' => ['latitude' => 50.6292, 'longitude' => 3.0573],
    'Rennes' => ['latitude' => 48.1173, 'longitude' => -1.6778]
];

// Mise à jour des lieux avec des coordonnées aléatoires
foreach ($lieux as $lieu) {
    // Choisir une ville aléatoire
    $ville = array_rand($coordonnees);
    $coords = $coordonnees[$ville];
    
    // Ajouter une petite variation pour éviter que tous les marqueurs ne se superposent
    $variation = 0.02; // ~2km
    $coords['latitude'] += (mt_rand(-100, 100) / 10000) * $variation;
    $coords['longitude'] += (mt_rand(-100, 100) / 10000) * $variation;
    
    // Mettre à jour le lieu
    $lieu->latitude = $coords['latitude'];
    $lieu->longitude = $coords['longitude'];
    $lieu->save();
    
    echo "Lieu '{$lieu->nom}' mis à jour avec les coordonnées de {$ville} (latitude: {$lieu->latitude}, longitude: {$lieu->longitude})\n";
}

echo "\nMise à jour terminée. Tous les lieux ont maintenant des coordonnées géographiques.\n";
