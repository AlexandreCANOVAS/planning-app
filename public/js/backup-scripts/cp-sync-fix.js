/**
 * Script de correction pour la synchronisation des soldes CP
 * Ce script corrige les problèmes de synchronisation entre les différentes vues
 */

(function() {
    // Configuration
    const config = {
        debug: true
    };

    /**
     * Fonction de journalisation
     */
    function log(message, data, type = 'info') {
        if (!config.debug) return;
        
        const styles = {
            info: 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;',
            success: 'background: #10b981; color: white; padding: 2px 5px; border-radius: 3px;',
            warning: 'background: #f59e0b; color: white; padding: 2px 5px; border-radius: 3px;',
            error: 'background: #ef4444; color: white; padding: 2px 5px; border-radius: 3px;'
        };
        
        console.log(`%c[CP-SYNC-FIX] ${message}`, styles[type], data || '');
    }

    /**
     * Fonction pour exposer globalement la fonction forceUpdateCpElements
     */
    function exposeGlobalFunctions() {
        // Vérifier si CPMonitor existe
        if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
            // Exposer la fonction forceUpdateCpElements si elle n'existe pas déjà
            if (!window.forceUpdateCpElements) {
                window.forceUpdateCpElements = function(employeId, cpValue) {
                    log(`forceUpdateCpElements appelée via CPMonitor pour l'employé ${employeId} avec la valeur ${cpValue}`, null, 'info');
                    return window.CPMonitor.update(employeId, cpValue);
                };
                log('Fonction forceUpdateCpElements exposée globalement via CPMonitor', null, 'success');
            }
            
            // Exposer également updateCongesPayes pour la compatibilité
            if (!window.updateCongesPayes) {
                window.updateCongesPayes = function(employeId, cpValue) {
                    log(`updateCongesPayes appelée pour l'employé ${employeId} avec la valeur ${cpValue}`, null, 'info');
                    return window.CPMonitor.update(employeId, cpValue);
                };
                log('Fonction updateCongesPayes exposée globalement via CPMonitor', null, 'success');
            }
        }
    }

    /**
     * Fonction pour corriger les problèmes de synchronisation
     */
    function fixSyncIssues() {
        // 1. S'assurer que les événements personnalisés sont correctement propagés
        // Drapeau pour éviter les boucles infinies
        let isProcessingCpUpdate = false;
        
        document.addEventListener('conges-cp-updated', function(event) {
            // Vérifier si nous sommes déjà en train de traiter une mise à jour
            if (isProcessingCpUpdate) return;
            
            if (event.detail && event.detail.employeId && event.detail.cpValue !== undefined) {
                // Activer le drapeau pour éviter les appels récursifs
                isProcessingCpUpdate = true;
                
                log(`Événement conges-cp-updated reçu pour l'employé ${event.detail.employeId} avec la valeur ${event.detail.cpValue}`, null, 'info');
                
                if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
                    window.CPMonitor.update(event.detail.employeId, event.detail.cpValue);
                }
                
                if (typeof window.forceUpdateCpElements === 'function' && window.forceUpdateCpElements !== window.CPMonitor.update) {
                    window.forceUpdateCpElements(event.detail.employeId, event.detail.cpValue);
                }
                
                // Ne pas appeler updateCongesList ici pour éviter la boucle infinie
                // car updateCongesList émet l'événement conges-cp-updated
                
                // Réinitialiser le drapeau après un court délai
                setTimeout(() => {
                    isProcessingCpUpdate = false;
                }, 50);
            }
        });
        
        // 2. S'assurer que les mises à jour localStorage sont correctement traitées
        window.addEventListener('storage', function(event) {
            if (event.key === 'conges-sync-update') {
                try {
                    const data = JSON.parse(event.newValue);
                    
                    if (data && data.employeeId && data.cpValue !== undefined) {
                        log(`Événement storage intercepté pour l'employé ${data.employeeId} avec la valeur ${data.cpValue}`, null, 'info');
                        
                        // Utiliser toutes les méthodes disponibles pour mettre à jour les éléments
                        if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
                            window.CPMonitor.update(data.employeeId, data.cpValue);
                        }
                        
                        if (typeof window.forceUpdateCpElements === 'function' && window.forceUpdateCpElements !== window.CPMonitor.update) {
                            window.forceUpdateCpElements(data.employeeId, data.cpValue);
                        }
                        
                        if (typeof window.updateCongesList === 'function') {
                            window.updateCongesList(data.employeeId, data.cpValue);
                        }
                    }
                } catch (err) {
                    log('Erreur lors du traitement de l\'événement storage', err, 'error');
                }
            }
        });
        
        log('Correctifs de synchronisation appliqués', null, 'success');
    }

    /**
     * Fonction d'initialisation
     */
    function initialize() {
        log('Initialisation du script de correction de synchronisation CP', null, 'info');
        
        // Exposer les fonctions globales
        exposeGlobalFunctions();
        
        // Appliquer les correctifs de synchronisation
        fixSyncIssues();
        
        log('Script de correction initialisé avec succès', null, 'success');
    }

    // Initialiser le script quand le DOM est chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();

// Afficher un message dans la console pour confirmer que le script est chargé
console.log('%c[CP-SYNC-FIX] Script de correction chargé avec succès!', 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;');
