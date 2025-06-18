/**
 * Script de test pour vérifier la mise à jour des soldes CP
 * Ce script ajoute un bouton flottant qui permet de simuler une mise à jour
 * des soldes CP pour un employé spécifique
 */

(function() {
    // Configuration
    const config = {
        debug: true,
        testEmployeeId: null, // Sera détecté automatiquement
        testCpValue: 10.5     // Valeur de test pour les CP
    };

    /**
     * Fonction de journalisation
     */
    function log(message, data, type = 'info') {
        if (!config.debug) return;
        
        const styles = {
            info: 'background: #8b5cf6; color: white; padding: 2px 5px; border-radius: 3px;',
            success: 'background: #10b981; color: white; padding: 2px 5px; border-radius: 3px;',
            warning: 'background: #f59e0b; color: white; padding: 2px 5px; border-radius: 3px;',
            error: 'background: #ef4444; color: white; padding: 2px 5px; border-radius: 3px;'
        };
        
        console.log(`%c[TEST-CP-UPDATE] ${message}`, styles[type], data || '');
    }

    /**
     * Fonction pour détecter un ID d'employé dans la page
     */
    function detectEmployeeId() {
        // Essayer de trouver un ID d'employé dans les éléments de la page
        const elements = document.querySelectorAll('[data-employe-id], [data-employee-id], [data-id]');
        
        for (const element of elements) {
            const id = element.getAttribute('data-employe-id') || 
                      element.getAttribute('data-employee-id') || 
                      element.getAttribute('data-id');
            
            if (id && !isNaN(parseInt(id))) {
                return id;
            }
        }
        
        // Si aucun ID n'est trouvé, demander à l'utilisateur
        const id = prompt('Entrez l\'ID de l\'employé pour le test:', '1');
        return id;
    }

    /**
     * Fonction pour simuler une mise à jour des soldes CP
     */
    function simulateUpdate() {
        // Détecter l'ID de l'employé si nécessaire
        if (!config.testEmployeeId) {
            config.testEmployeeId = detectEmployeeId();
        }
        
        if (!config.testEmployeeId) {
            log('Impossible de détecter un ID d\'employé pour le test', null, 'error');
            return;
        }
        
        log(`Simulation de mise à jour pour l'employé ${config.testEmployeeId} avec la valeur ${config.testCpValue}`, null, 'info');
        
        // Essayer toutes les méthodes de mise à jour disponibles
        let updateMethods = [];
        
        // 1. Méthode CPMonitor.update
        if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
            updateMethods.push({
                name: 'CPMonitor.update',
                fn: () => window.CPMonitor.update(config.testEmployeeId, config.testCpValue)
            });
        }
        
        // 2. Méthode forceUpdateCpElements
        if (typeof window.forceUpdateCpElements === 'function') {
            updateMethods.push({
                name: 'forceUpdateCpElements',
                fn: () => window.forceUpdateCpElements(config.testEmployeeId, config.testCpValue)
            });
        }
        
        // 3. Méthode updateCongesList
        if (typeof window.updateCongesList === 'function') {
            updateMethods.push({
                name: 'updateCongesList',
                fn: () => window.updateCongesList(config.testEmployeeId, config.testCpValue)
            });
        }
        
        // 4. Événement personnalisé
        updateMethods.push({
            name: 'Événement conges-cp-updated',
            fn: () => document.dispatchEvent(new CustomEvent('conges-cp-updated', {
                detail: { employeId: config.testEmployeeId, cpValue: config.testCpValue }
            }))
        });
        
        // 5. LocalStorage
        updateMethods.push({
            name: 'localStorage',
            fn: () => {
                localStorage.setItem('conges-sync-update', JSON.stringify({
                    employeeId: config.testEmployeeId,
                    cpValue: config.testCpValue,
                    timestamp: Date.now()
                }));
                
                // Déclencher manuellement l'événement storage pour simuler un autre onglet
                const storageEvent = new StorageEvent('storage', {
                    key: 'conges-sync-update',
                    newValue: JSON.stringify({
                        employeeId: config.testEmployeeId,
                        cpValue: config.testCpValue,
                        timestamp: Date.now()
                    })
                });
                window.dispatchEvent(storageEvent);
            }
        });
        
        // Exécuter toutes les méthodes de mise à jour avec un délai entre chaque
        let delay = 0;
        updateMethods.forEach(method => {
            setTimeout(() => {
                log(`Exécution de la méthode de mise à jour: ${method.name}`, null, 'info');
                try {
                    const result = method.fn();
                    log(`Résultat de ${method.name}:`, result, 'success');
                } catch (err) {
                    log(`Erreur lors de l'exécution de ${method.name}:`, err, 'error');
                }
            }, delay);
            delay += 500; // 500ms entre chaque méthode
        });
        
        // Afficher un résumé
        setTimeout(() => {
            log(`Test de mise à jour terminé. ${updateMethods.length} méthodes testées.`, null, 'success');
            
            // Vérifier si des éléments ont été mis à jour
            const updatedElements = document.querySelectorAll('.cp-updating');
            log(`${updatedElements.length} éléments ont été animés pendant la mise à jour.`, null, 'info');
        }, delay + 500);
    }

    /**
     * Fonction pour créer le bouton de test
     */
    function createTestButton() {
        const button = document.createElement('button');
        button.textContent = 'Tester la mise à jour CP';
        button.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            padding: 10px 15px;
            background-color: #8b5cf6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        `;
        
        button.addEventListener('click', simulateUpdate);
        document.body.appendChild(button);
        
        log('Bouton de test ajouté', null, 'success');
    }

    /**
     * Fonction d'initialisation
     */
    function initialize() {
        log('Initialisation du script de test de mise à jour CP', null, 'info');
        
        // Créer le bouton de test
        createTestButton();
        
        log('Script de test initialisé avec succès', null, 'success');
    }

    // Initialiser le script quand le DOM est chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();

// Afficher un message dans la console pour confirmer que le script est chargé
console.log('%c[TEST-CP-UPDATE] Script de test chargé avec succès!', 'background: #8b5cf6; color: white; padding: 2px 5px; border-radius: 3px;');
