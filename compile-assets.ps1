# Script pour compiler les assets JavaScript avec contournement temporaire de la politique d'exécution
Write-Host "Compilation des assets JavaScript..." -ForegroundColor Cyan

# Contournement temporaire de la politique d'exécution pour cette session uniquement
$currentPolicy = Get-ExecutionPolicy -Scope Process
Write-Host "Politique d'exécution actuelle: $currentPolicy" -ForegroundColor Yellow
Write-Host "Définition temporaire de la politique d'exécution sur Bypass pour cette session..." -ForegroundColor Yellow
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force

try {
    # Exécution de npm run dev pour compiler les assets
    Write-Host "Exécution de 'npm run dev'..." -ForegroundColor Green
    npm run dev
    
    Write-Host "Compilation terminée avec succès!" -ForegroundColor Green
}
catch {
    Write-Host "Erreur lors de la compilation des assets: $_" -ForegroundColor Red
}
finally {
    # Restauration de la politique d'exécution précédente
    Write-Host "Restauration de la politique d'exécution à $currentPolicy..." -ForegroundColor Yellow
    Set-ExecutionPolicy -Scope Process -ExecutionPolicy $currentPolicy -Force
}
