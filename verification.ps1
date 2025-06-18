Write-Host "Verification des modifications"
Write-Host "1. Verification des fichiers JavaScript"
$jsFiles = @("public/js/conges-solde.js", "public/js/conges-refresh.js", "public/js/test-conges-update.js")
foreach ($file in $jsFiles) {
    if (Test-Path "c:\Users\monst\Desktop\planning-app\$file") {
        Write-Host "Le fichier $file existe" -ForegroundColor Green
    } else {
        Write-Host "Le fichier $file n'existe pas" -ForegroundColor Red
    }
}

Write-Host "2. Verification du middleware"
if (Test-Path "c:\Users\monst\Desktop\planning-app\app\Http\Middleware\RefreshSoldeCongeData.php") {
    Write-Host "Le middleware RefreshSoldeCongeData existe" -ForegroundColor Green
} else {
    Write-Host "Le middleware RefreshSoldeCongeData n'existe pas" -ForegroundColor Red
}

Write-Host "3. Verification du controleur"
if (Test-Path "c:\Users\monst\Desktop\planning-app\app\Http\Controllers\SoldeCongeController.php") {
    Write-Host "Le controleur SoldeCongeController existe" -ForegroundColor Green
} else {
    Write-Host "Le controleur SoldeCongeController n'existe pas" -ForegroundColor Red
}

Write-Host "4. Verification de la vue"
if (Test-Path "c:\Users\monst\Desktop\planning-app\resources\views\conges\solde\edit.blade.php") {
    Write-Host "La vue edit.blade.php existe" -ForegroundColor Green
} else {
    Write-Host "La vue edit.blade.php n'existe pas" -ForegroundColor Red
}

Write-Host "Verification terminee"
