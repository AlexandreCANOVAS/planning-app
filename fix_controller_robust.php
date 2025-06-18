<?php

// Script pour corriger les problèmes dans CongeController.php de manière plus robuste
$file = 'app/Http/Controllers/CongeController.php';
$content = file_get_contents($file);

// Compter les occurrences de la méthode employeCalendar
$count = substr_count($content, 'public function employeCalendar');
echo "Nombre d'occurrences de 'public function employeCalendar': $count\n";

// Trouver les positions des deux méthodes employeCalendar
$pos1 = strpos($content, 'public function employeCalendar');
$pos2 = strpos($content, 'public function employeCalendar', $pos1 + 1);

if ($pos1 !== false && $pos2 !== false) {
    echo "Première méthode trouvée à la position: $pos1\n";
    echo "Deuxième méthode trouvée à la position: $pos2\n";
    
    // Trouver la fin de la première méthode
    $openBraces = 0;
    $startPos = $pos1;
    $endPos = $startPos;
    $inMethod = false;
    
    for ($i = $startPos; $i < strlen($content); $i++) {
        if ($content[$i] === '{') {
            $openBraces++;
            $inMethod = true;
        } elseif ($content[$i] === '}') {
            $openBraces--;
            if ($inMethod && $openBraces === 0) {
                $endPos = $i + 1;
                break;
            }
        }
    }
    
    echo "Fin de la première méthode à la position: $endPos\n";
    
    // Extraire la première méthode pour vérification
    $firstMethod = substr($content, $pos1, $endPos - $pos1);
    echo "Extrait de la première méthode (100 premiers caractères):\n";
    echo substr($firstMethod, 0, 100) . "...\n";
    
    // Supprimer la première méthode
    $newContent = substr($content, 0, $pos1) . substr($content, $endPos);
    
    // Sauvegarder les modifications
    file_put_contents($file, $newContent);
    echo "La première méthode employeCalendar a été supprimée.\n";
    
    // Corriger les variables $startDate et $endDate dans la méthode getEmployeEvents
    $newContent = str_replace('$startDate', '$start', $newContent);
    $newContent = str_replace('$endDate', '$end', $newContent);
    file_put_contents($file, $newContent);
    echo "Les variables \$startDate et \$endDate ont été remplacées par \$start et \$end.\n";
} else {
    echo "Impossible de trouver deux occurrences de la méthode employeCalendar.\n";
}

echo "Corrections terminées.\n";
