/**
 * cp-integrator.js
 * Script d'intégration pour charger notre solution de mise à jour des CP
 * Ce script doit être inclus dans toutes les pages
 */
(function() {
    // Vérifier si le script est déjà chargé
    if (window.cpDirectFixLoaded) {
        console.log('[CP-INTEGRATOR] Solution CP déjà chargée');
        return;
    }

    // Fonction pour charger notre script de correction
    function loadCpDirectFix() {
        console.log('[CP-INTEGRATOR] Chargement de la solution CP...');
        
        const script = document.createElement('script');
        script.src = '/js/cp-direct-fix.js';
        script.async = true;
        script.onload = function() {
            console.log('[CP-INTEGRATOR] Solution CP chargée avec succès');
            window.cpDirectFixLoaded = true;
            
            // Déclencher un événement pour informer les autres scripts
            document.dispatchEvent(new CustomEvent('cp-fix-loaded'));
        };
        script.onerror = function() {
            console.error('[CP-INTEGRATOR] Erreur lors du chargement de la solution CP');
        };
        
        document.head.appendChild(script);
    }

    // Charger le script immédiatement
    loadCpDirectFix();
})();
