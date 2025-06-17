# Script pour supprimer les anciennes notifications après leur déplacement dans des sous-dossiers

# Liste des fichiers à supprimer
$filesToDelete = @(
    "CongeCreatedNotification.php",
    "CongeStatusChangedNotification.php",
    "CongeStatusModifie.php",
    "ExchangeAcceptedNotification.php",
    "ExchangeRequestNotification.php",
    "ExchangeRequestedNotification.php",
    "ExchangeStatusChangedNotification.php",
    "PlanningCreatedNotification.php",
    "PlanningModifie.php",
    "PlanningUpdatedNotification.php"
)

# Chemin du dossier des notifications
$notificationsPath = "c:\Users\monst\Desktop\planning-app\app\Notifications"

# Supprimer chaque fichier
foreach ($file in $filesToDelete) {
    $filePath = Join-Path -Path $notificationsPath -ChildPath $file
    if (Test-Path $filePath) {
        Remove-Item -Path $filePath -Force
        Write-Host "Fichier supprimé: $file"
    } else {
        Write-Host "Fichier non trouvé: $file"
    }
}

Write-Host "Nettoyage terminé!"
