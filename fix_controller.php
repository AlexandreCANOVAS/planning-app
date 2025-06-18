<?php

// Script pour corriger les problèmes dans CongeController.php
$file = 'app/Http/Controllers/CongeController.php';
$content = file_get_contents($file);

// 1. Supprimer la méthode employeCalendar redondante (la première occurrence)
$pattern = '/public function congesCalendar\(\).*?public function employeCalendar\(Request \$request\).*?return view\(\'plannings\.employe-calendar\',.*?\]\);.*?\}/s';
$replacement = 'public function congesCalendar()
    {
        return view(\'conges.calendar\');
    }';
$content = preg_replace($pattern, $replacement, $content, 1);

// 2. Corriger les variables $startDate et $endDate dans la méthode getEmployeEvents
$content = str_replace('$startDate', '$start', $content);
$content = str_replace('$endDate', '$end', $content);

// 3. Corriger la vue retournée dans la méthode getEmployeEvents
$content = str_replace("return view('plannings.employe-calendar',", "return view('conges.employe-calendar',", $content);

// Sauvegarder les modifications
file_put_contents($file, $content);

echo "Corrections appliquées avec succès au fichier $file\n";
