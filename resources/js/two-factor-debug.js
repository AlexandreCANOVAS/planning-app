// Script de débogage pour l'authentification à deux facteurs
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de débogage 2FA chargé');
    
    // Vérifier si Alpine est chargé
    if (window.Alpine) {
        console.log('Alpine.js est chargé correctement');
    } else {
        console.error('Alpine.js n\'est pas chargé');
    }
    
    // Vérifier si Livewire est chargé
    if (window.Livewire) {
        console.log('Livewire est chargé correctement');
    } else {
        console.error('Livewire n\'est pas chargé');
    }
    
    // Rechercher le bouton d'activation
    const activateButton = document.querySelector('button:contains("Activer")') || 
                          document.querySelector('button:contains("ACTIVER")') ||
                          document.querySelector('.profile-content-section button');
    
    if (activateButton) {
        console.log('Bouton d\'activation trouvé:', activateButton);
        
        // Ajouter un gestionnaire d'événement manuel
        activateButton.addEventListener('click', function(e) {
            console.log('Bouton d\'activation cliqué manuellement');
            
            // Trouver le composant modal associé
            const modalContainer = document.querySelector('[x-data*="confirming"]');
            if (modalContainer) {
                console.log('Container modal trouvé');
                
                // Forcer l'ouverture du modal
                try {
                    const alpineComponent = Alpine.$data(modalContainer);
                    console.log('Composant Alpine récupéré:', alpineComponent);
                    alpineComponent.confirming = true;
                } catch (error) {
                    console.error('Erreur lors de la manipulation du composant Alpine:', error);
                }
            } else {
                console.error('Container modal non trouvé');
            }
        });
    } else {
        console.error('Bouton d\'activation non trouvé');
    }
    
    // Vérifier si la section 2FA est visible
    const twoFactorSection = document.getElementById('two-factor-auth');
    if (twoFactorSection) {
        console.log('Section 2FA trouvée, visible:', !twoFactorSection.classList.contains('hidden'));
        
        // Si la section est cachée, la rendre visible pour le débogage
        if (twoFactorSection.classList.contains('hidden')) {
            console.log('La section 2FA est cachée, cela pourrait être le problème');
        }
    } else {
        console.error('Section 2FA non trouvée');
    }
});
