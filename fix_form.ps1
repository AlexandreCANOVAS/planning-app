# Script pour corriger le problème de mise à jour des soldes de congés
# Ce script va vérifier et corriger les formulaires de modification des soldes de congés

# Chemin vers le fichier de vue pour la modification des soldes de congés
$editFilePath = "c:\Users\monst\Desktop\planning-app\resources\views\conges\solde\edit.blade.php"

# Vérifier si le fichier existe
if (Test-Path $editFilePath) {
    Write-Host "Vérification du fichier de formulaire de modification des soldes de congés..."
    $content = Get-Content $editFilePath -Raw
    
    # Vérifier si le formulaire utilise la méthode POST avec @method('PUT')
    if ($content -match '@method\(''PUT''\)') {
        Write-Host "Le formulaire utilise déjà la méthode PUT correctement."
    } else {
        Write-Host "Correction de la méthode du formulaire..."
        $pattern = '(<form.*?method="POST".*?>)\s*@csrf'
        $replacement = '$1
            @csrf
            @method(''PUT'')'
        $content = $content -replace $pattern, $replacement
        Set-Content -Path $editFilePath -Value $content
        Write-Host "Méthode du formulaire corrigée."
    }
    
    # Vérifier si les champs du formulaire ont les noms corrects
    $fieldsToCheck = @("solde_conges", "solde_rtt", "solde_conges_exceptionnels")
    $missingFields = @()
    
    foreach ($field in $fieldsToCheck) {
        if ($content -notmatch "name=`"$field`"") {
            $missingFields += $field
        }
    }
    
    if ($missingFields.Count -gt 0) {
        Write-Host "Champs manquants détectés: $($missingFields -join ', ')"
    } else {
        Write-Host "Tous les champs requis sont présents dans le formulaire."
    }
} else {
    Write-Host "Le fichier de formulaire de modification des soldes de congés n'a pas été trouvé à l'emplacement attendu."
}

# Vérifier le contrôleur SoldeCongeController
$controllerPath = "c:\Users\monst\Desktop\planning-app\app\Http\Controllers\SoldeCongeController.php"

if (Test-Path $controllerPath) {
    Write-Host "Vérification du contrôleur SoldeCongeController..."
    $controllerContent = Get-Content $controllerPath -Raw
    
    # Vérifier si la méthode update contient le code pour rafraîchir l'employé
    if ($controllerContent -match '\$employe->refresh\(\);') {
        Write-Host "Le contrôleur contient déjà le code pour rafraîchir l'employé."
    } else {
        Write-Host "Ajout du code pour rafraîchir l'employé après la mise à jour..."
        $pattern = '(\$employe->update\(\[.*?\]\);)'
        $replacement = '$1
        
        // S''assurer que les données sont bien enregistrées en base de données
        $employe->refresh();'
        $controllerContent = $controllerContent -replace $pattern, $replacement
        Set-Content -Path $controllerPath -Value $controllerContent
        Write-Host "Code de rafraîchissement ajouté au contrôleur."
    }
} else {
    Write-Host "Le fichier du contrôleur SoldeCongeController n'a pas été trouvé à l'emplacement attendu."
}

Write-Host "Vérification terminée. Veuillez redémarrer votre serveur Laravel pour appliquer les modifications."
