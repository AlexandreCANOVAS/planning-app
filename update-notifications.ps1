$filePath = "c:\Users\monst\Desktop\planning-app\app\Http\Controllers\PlanningController.php"
$content = Get-Content -Path $filePath -Raw

# Remplacer les références à ExchangeStatusChangedNotification
$content = $content -replace 'new \\App\\Notifications\\ExchangeStatusChangedNotification', 'new ExchangeStatusChangedNotification'

# Écrire le contenu mis à jour dans le fichier
Set-Content -Path $filePath -Value $content

Write-Host "Mise à jour des références aux notifications terminée."
