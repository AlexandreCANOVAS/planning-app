Write-Host "Redemarrage du serveur Laravel..." -ForegroundColor Cyan

# Arreter le serveur existant (si en cours d'execution)
Write-Host "Arret des processus PHP existants..." -ForegroundColor Yellow
Get-Process -Name "php" -ErrorAction SilentlyContinue | Where-Object { $_.MainWindowTitle -match "artisan serve" } | Stop-Process -Force

# Vider le cache de l'application
Write-Host "Vidage du cache de l'application..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Redemarrer le serveur
Write-Host "Demarrage du serveur Laravel..." -ForegroundColor Green
Start-Process -FilePath "php" -ArgumentList "artisan serve" -NoNewWindow

Write-Host "Serveur redemarré avec succès!" -ForegroundColor Green
Write-Host "Accédez à http://127.0.0.1:8000 pour tester l'application" -ForegroundColor White

Write-Host "`nPour tester la mise à jour des soldes de congés:" -ForegroundColor Cyan
Write-Host "1. Accédez à la page de modification des soldes d'un employé" -ForegroundColor White
Write-Host "2. Ouvrez la console du navigateur (F12)" -ForegroundColor White
Write-Host "3. Exécutez le diagnostic avec:" -ForegroundColor White
Write-Host "   fetch('/js/diagnostic.js').then(r => r.text()).then(code => eval(code))" -ForegroundColor White -BackgroundColor DarkBlue
Write-Host "4. Testez la soumission du formulaire avec:" -ForegroundColor White
Write-Host "   window.testFormSubmission()" -ForegroundColor White -BackgroundColor DarkBlue
