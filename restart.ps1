# Script pour redémarrer l'application Laravel et vider le cache
Write-Host "Redémarrage de l'application Laravel..." -ForegroundColor Cyan

# Vérifier que tous les fichiers JavaScript sont accessibles
$jsFiles = @(
    "public/js/conges-solde.js",
    "public/js/conges-refresh.js",
    "public/js/test-conges-update.js"
)

foreach ($file in $jsFiles) {
    $fullPath = "c:\Users\monst\Desktop\planning-app\$file"
    if (Test-Path $fullPath) {
        Write-Host "✓ Le fichier $file existe" -ForegroundColor Green
    } else {
        Write-Host "✗ Le fichier $file est manquant!" -ForegroundColor Red
    }
}

# Vider le cache de l'application
Write-Host "Vidage du cache de l'application..." -ForegroundColor Cyan
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

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

Write-Host "`nRedémarrage terminé. Veuillez maintenant redémarrer votre serveur Laravel avec la commande:" -ForegroundColor Yellow
Write-Host "php artisan serve" -ForegroundColor White -BackgroundColor DarkBlue

Write-Host "`nPour tester la mise à jour des soldes de congés:" -ForegroundColor Cyan
Write-Host "1. Accédez à la page de modification des soldes de congés d'un employé" -ForegroundColor White
Write-Host "2. Ouvrez la console du navigateur (F12)" -ForegroundColor White
Write-Host "3. Copiez et collez le code suivant dans la console:" -ForegroundColor White
Write-Host "fetch('/js/test-conges-update.js').then(r => r.text()).then(code => eval(code))" -ForegroundColor White -BackgroundColor DarkBlue
