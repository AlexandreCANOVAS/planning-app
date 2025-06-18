# Script pour vérifier et appliquer les correctifs pour la mise à jour des soldes de congés
Write-Host "Vérification des correctifs pour la mise à jour des soldes de congés..." -ForegroundColor Cyan

# Vérifier que tous les fichiers JavaScript sont accessibles
$jsFiles = @(
    "public/js/conges-solde.js",
    "public/js/conges-refresh.js",
    "public/js/test-conges-update.js"
)

foreach ($file in $jsFiles) {
    $fullPath = Join-Path -Path "c:\Users\monst\Desktop\planning-app" -ChildPath $file
    if (Test-Path $fullPath) {
        Write-Host "✓ Le fichier $file existe" -ForegroundColor Green
    } else {
        Write-Host "✗ Le fichier $file est manquant!" -ForegroundColor Red
    }
}

# Vérifier que le middleware est bien enregistré
$kernelPath = "c:\Users\monst\Desktop\planning-app\app\Http\Kernel.php"
$kernelContent = Get-Content $kernelPath -Raw
if ($kernelContent -match "refresh\.soldes") {
    Write-Host "✓ Le middleware RefreshSoldeCongeData est correctement enregistré" -ForegroundColor Green
} else {
    Write-Host "✗ Le middleware RefreshSoldeCongeData n'est pas enregistré correctement!" -ForegroundColor Red
}

# Vérifier que le contrôleur SoldeCongeController est correctement configuré
$controllerPath = "c:\Users\monst\Desktop\planning-app\app\Http\Controllers\SoldeCongeController.php"
$controllerContent = Get-Content $controllerPath -Raw
if ($controllerContent -match "wantsJson") {
    Write-Host "✓ Le contrôleur SoldeCongeController est correctement configuré pour les requêtes AJAX" -ForegroundColor Green
} else {
    Write-Host "✗ Le contrôleur SoldeCongeController n'est pas configuré pour les requêtes AJAX!" -ForegroundColor Red
}

# Vérifier que la vue a bien été modifiée
$viewPath = "c:\Users\monst\Desktop\planning-app\resources\views\conges\solde\edit.blade.php"
$viewContent = Get-Content $viewPath -Raw
if ($viewContent -match "id=`"solde-form`"") {
    Write-Host "✓ Le formulaire de modification des soldes a bien l'ID 'solde-form'" -ForegroundColor Green
} else {
    Write-Host "✗ Le formulaire de modification des soldes n'a pas l'ID 'solde-form'!" -ForegroundColor Red
}

if ($viewContent -match "window\.societeId") {
    Write-Host "✓ La variable societeId est correctement définie dans la vue" -ForegroundColor Green
} else {
    Write-Host "✗ La variable societeId n'est pas définie dans la vue!" -ForegroundColor Red
}

Write-Host "`nInstructions pour tester la mise à jour des soldes de congés:" -ForegroundColor Cyan
Write-Host "1. Redémarrez votre serveur Laravel avec la commande:" -ForegroundColor White
Write-Host "   php artisan serve" -ForegroundColor White -BackgroundColor DarkBlue
Write-Host "2. Accédez à la page de modification des soldes de congés d'un employé" -ForegroundColor White
Write-Host "3. Modifiez les valeurs des soldes et cliquez sur Enregistrer" -ForegroundColor White
Write-Host "4. Vérifiez que les valeurs sont bien mises à jour sans avoir à recharger la page" -ForegroundColor White
Write-Host "`nPour tester avec le script automatique:" -ForegroundColor Cyan
Write-Host "1. Ouvrez la console du navigateur (F12)" -ForegroundColor White
Write-Host "2. Copiez et collez le code suivant dans la console:" -ForegroundColor White
Write-Host "   fetch('/js/test-conges-update.js').then(r => r.text()).then(code => eval(code))" -ForegroundColor White -BackgroundColor DarkBlue
