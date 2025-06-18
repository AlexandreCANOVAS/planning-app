/**
 * Script de synchronisation des soldes de congés entre les différentes vues
 * Ce script complète conges-cp-monitor.js en ciblant spécifiquement les éléments
 * dans la vue de liste des employés qui ne sont pas correctement mis à jour
 */

(function() {
    // Configuration
    const config = {
        debug: true,
        refreshInterval: 2000, // 2 secondes
        selectors: {
            // Sélecteurs pour la vue de liste des employés
            listView: {
                employeeCards: '.card, .employee-card, .employe-card, .bg-white, tr[data-employe-id], tr[data-id], .employe-row',
                cpValues: '.cp, .solde-cp, .text-blue-800, span:contains("CP"), div:contains("CP"), td:contains("CP")',
                cpElements: '[data-type="cp"], [data-solde="cp"], [id*="cp"], [id*="conges"], .solde-cp'
            },
            // Sélecteurs spécifiques par vue
            specificViews: {
                // Vue /conges/solde
                congesSolde: {
                    container: '.conges-container, .soldes-container, .table-container',
                    rows: 'tr[data-employe-id], tr[data-id], .employe-row',
                    cpCells: 'td.cp, td.solde-cp, td:contains("CP"), .text-blue-800'
                },
                // Vue /conges
                conges: {
                    container: '.conges-container, .table-responsive',
                    rows: 'tr[data-employe-id], tr[data-id], .employe-row',
                    cpCells: 'td.cp, td.solde-cp, td:contains("CP"), .text-blue-800'
                },
                // Vue /dashboard
                dashboard: {
                    container: '.dashboard-container, .stats-container, .employes-container',
                    cards: '.card, .employee-card, .employe-card, .bg-white',
                    cpElements: '.cp, .solde-cp, .text-blue-800, .badge-cp'
                }
            },
            // Sélecteurs pour la vue d'édition
            editView: {
                form: 'form',
                cpInput: 'input[name="solde_conges"], input[name="cp"]',
                employeeIdInput: 'input[name="employe_id"], input[name="employeId"]',
                submitButton: 'button[type="submit"], input[type="submit"], .btn-save, .btn-primary'
            }
        }
    };

    // Variables globales
    let lastKnownValues = {};
    let isListeningForChanges = false;

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
        
        console.log(`%c[CONGES-SYNC] ${message}`, styles[type], data || '');
    }

    /**
     * Fonction pour détecter si nous sommes dans la vue de liste des employés
     */
    function isListView() {
        // Cibler spécifiquement les vues demandées
        const targetPaths = [
            '/conges/solde',
            '/conges',
            '/dashboard'
        ];
        
        // Vérifier si nous sommes sur l'une des vues cibles
        for (const path of targetPaths) {
            if (window.location.pathname === path || window.location.pathname.startsWith(path + '/')) {
                // Ne pas inclure la vue d'édition des soldes
                if (path === '/conges/solde' && window.location.pathname.includes('/edit')) {
                    continue;
                }
                return true;
            }
        }
        
        // Inclure également la page d'accueil et la page des employés
        return window.location.pathname === '/' || 
               window.location.pathname.includes('/employes');
    }

    /**
     * Fonction pour détecter si nous sommes dans la vue d'édition des soldes
     */
    function isEditView() {
        return window.location.pathname.includes('/conges/solde') && 
               window.location.pathname.includes('/edit');
    }

    /**
     * Fonction pour extraire l'ID de l'employé à partir de l'URL
     */
    function getEmployeeIdFromUrl() {
        const match = window.location.pathname.match(/\/solde\/(\d+)\/edit/);
        return match ? match[1] : null;
    }

    /**
     * Fonction pour mettre à jour visuellement un élément avec animation
     */
    function updateElementWithAnimation(element, value) {
        if (!element) return false;
        
        try {
            // Sauvegarder l'ancienne valeur
            const oldValue = element.textContent.trim();
            
            // Formater la nouvelle valeur (conserver le même format)
            let newValue = value;
            if (oldValue.includes('.')) {
                // Si l'ancienne valeur contient un point, formater avec 1 décimale
                newValue = parseFloat(value).toFixed(1);
            }
            
            // Appliquer l'animation seulement si la valeur a changé
            if (oldValue !== newValue.toString()) {
                // Ajouter une classe pour l'animation
                element.classList.add('cp-updating');
                
                // Mettre à jour la valeur
                element.textContent = newValue;
                
                // Retirer la classe après l'animation
                setTimeout(() => {
                    element.classList.remove('cp-updating');
                }, 1500);
                
                log(`Élément mis à jour: ${oldValue} -> ${newValue}`, element, 'success');
                return true;
            }
            
            return false;
        } catch (e) {
            log('Erreur lors de l\'animation', e, 'error');
            return false;
        }
    }

    /**
     * Fonction pour déterminer la vue actuelle
     */
    function getCurrentView() {
        if (window.location.pathname === '/conges/solde' || window.location.pathname.startsWith('/conges/solde/')) {
            return 'congesSolde';
        } else if (window.location.pathname === '/conges' || window.location.pathname.startsWith('/conges/')) {
            return 'conges';
        } else if (window.location.pathname === '/dashboard' || window.location.pathname.startsWith('/dashboard/')) {
            return 'dashboard';
        } else {
            return 'generic';
        }
    }

    /**
     * Fonction pour mettre à jour les éléments de la vue liste avec la nouvelle valeur CP
     */
    function updateListViewElements(employeId, cpValue) {
        log(`Mise à jour des éléments CP pour l'employé ${employeId} avec la valeur ${cpValue}`);
        
        // Sélecteurs pour les éléments affichant les soldes CP
        const selectors = [
            // Sélecteur principal pour les éléments ajoutés par cp-attribute-fix.js
            `.cp-value[data-employe-id="${employeId}"]`,
            
            // Sélecteurs originaux
            `[data-employe-id="${employeId}"][data-solde-type="conges"]`,
            `.cp[data-employe-id="${employeId}"]`,
            `input[name="solde_conges"][data-employe-id="${employeId}"]`,
            `input[name="cp"][data-employe-id="${employeId}"]`,
            `input[name="soldeConges"][data-employe-id="${employeId}"]`,
            `[data-dashboard-employe-id="${employeId}"][data-dashboard-solde-type="conges"]`,
            `[data-dashboard="conges"][data-employe-id="${employeId}"]`,
            `[data-type="conges"][data-employe-id="${employeId}"]`,
            `[data-solde="cp"][data-employe-id="${employeId}"]`,
            `[data-solde="conges"][data-employe-id="${employeId}"]`,
            `[data-type="cp"][data-employe-id="${employeId}"]`,
            `tr[data-employe-id="${employeId}"] td.cp, tr[data-employe-id="${employeId}"] td.solde-cp, tr[data-employe-id="${employeId}"] td.solde-conges`
        ];
        
        // Combiner tous les sélecteurs
        const combinedSelector = selectors.join(', ');
        const elements = document.querySelectorAll(combinedSelector);
        
        log(`Nombre d'éléments trouvés avec les sélecteurs : ${elements.length}`);
        
        // Mettre à jour chaque élément trouvé
        let updatedCount = 0;
        elements.forEach(element => {
            // Déterminer comment mettre à jour l'élément en fonction de son type
            if (element.tagName === 'INPUT') {
                // Pour les champs de formulaire
                element.value = cpValue;
            } else {
                // Pour les éléments d'affichage
                const currentText = element.textContent.trim();
                const hasJours = currentText.includes('jour') || currentText.includes('jours');
                
                if (hasJours) {
                    element.textContent = `${cpValue} jours`;
                } else {
                    element.textContent = cpValue;
                }
            }
            
            // Ajouter une animation pour indiquer la mise à jour
            element.classList.add('updated');
            setTimeout(() => {
                element.classList.remove('updated');
            }, 2000);
            
            updatedCount++;
        });
        
        // Si aucun élément n'a été trouvé, essayer de forcer une mise à jour via CPMonitor
        if (updatedCount === 0 && window.CPMonitor && typeof window.CPMonitor.update === 'function') {
            log(`Aucun élément trouvé avec les sélecteurs standards, tentative via CPMonitor`, null, 'warning');
            window.CPMonitor.update(employeId, cpValue);
        }
        
        log(`Mise à jour terminée: ${updatedCount} éléments mis à jour`);
        return updatedCount;
    }

    /**
     * Fonction pour intercepter les soumissions de formulaire dans la vue d'édition
     */
    function setupFormInterceptor() {
        if (!isEditView()) return;
        
        log('Configuration de l\'intercepteur de formulaire dans la vue d\'édition', null, 'info');
        
        const form = document.querySelector(config.selectors.editView.form);
        if (!form) {
            log('Aucun formulaire trouvé dans la vue d\'édition', null, 'warning');
            return;
        }
        
        // Intercepter la soumission du formulaire
        form.addEventListener('submit', function(e) {
            // Empêcher la soumission normale du formulaire pour éviter le rechargement de la page
            e.preventDefault();
            
            // Indiquer visuellement que la soumission est en cours
            const submitButton = form.querySelector(config.selectors.editView.submitButton);
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('processing');
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Enregistrement...';
            }
            
            const cpInput = form.querySelector(config.selectors.editView.cpInput);
            const employeeIdInput = form.querySelector(config.selectors.editView.employeeIdInput);
            
            if (cpInput && employeeIdInput) {
                const cpValue = cpInput.value;
                const employeeId = employeeIdInput.value;
                
                log(`Soumission de formulaire détectée: employé ${employeeId}, CP ${cpValue}`, null, 'info');
                
                // Stocker les valeurs pour la synchronisation
                lastKnownValues[employeeId] = cpValue;
                
                // Utiliser localStorage pour synchroniser avec d'autres onglets
                try {
                    localStorage.setItem('conges-sync-update', JSON.stringify({
                        employeeId: employeeId,
                        cpValue: cpValue,
                        timestamp: Date.now()
                    }));
                    
                    log('Mise à jour enregistrée dans localStorage pour synchronisation', null, 'success');
                } catch (err) {
                    log('Erreur lors de l\'enregistrement dans localStorage', err, 'error');
                }
                
                // Utiliser les fonctions disponibles pour mettre à jour les éléments
                setTimeout(() => {
                    // Essayer d'abord avec CongesCpMonitor
                    if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
                        window.CPMonitor.update(employeeId, cpValue);
                        log('Mise à jour via CPMonitor.update', null, 'success');
                    } 
                    // Ensuite avec forceUpdateCpElements
                    else if (typeof window.forceUpdateCpElements === 'function') {
                        window.forceUpdateCpElements(employeeId, cpValue);
                        log('Mise à jour via forceUpdateCpElements', null, 'success');
                    }
                    // Ensuite avec updateCongesPayes
                    else if (typeof window.updateCongesPayes === 'function') {
                        window.updateCongesPayes(employeeId, cpValue);
                        log('Mise à jour via updateCongesPayes', null, 'success');
                    }
                    // Enfin, utiliser notre propre fonction
                    else {
                        updateListViewElements(employeeId, cpValue);
                        log('Mise à jour via updateListViewElements', null, 'success');
                    }
                    
                    // Diffuser l'événement pour d'autres scripts
                    document.dispatchEvent(new CustomEvent('conges-cp-updated', {
                        detail: { employeId: employeeId, cpValue: cpValue }
                    }));
                    
                    // Soumettre le formulaire en AJAX
                    const formData = new FormData(form);
                    const url = form.getAttribute('action');
                    const method = form.getAttribute('method') || 'POST';
                    
                    fetch(url, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        log('Formulaire soumis avec succès via AJAX', data, 'success');
                        
                        // Afficher un message de succès
                        const messageContainer = document.createElement('div');
                        messageContainer.className = 'alert alert-success mt-3';
                        messageContainer.textContent = 'Modifications enregistrées avec succès';
                        form.appendChild(messageContainer);
                        
                        // Faire disparaître le message après quelques secondes
                        setTimeout(() => {
                            messageContainer.remove();
                        }, 3000);
                        
                        // Restaurer l'état du bouton
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.classList.remove('processing');
                            submitButton.textContent = originalText;
                        }
                    })
                    .catch(error => {
                        log('Erreur lors de la soumission du formulaire', error, 'error');
                        
                        // Afficher un message d'erreur
                        const messageContainer = document.createElement('div');
                        messageContainer.className = 'alert alert-danger mt-3';
                        messageContainer.textContent = 'Erreur lors de l\'enregistrement des modifications';
                        form.appendChild(messageContainer);
                        
                        // Faire disparaître le message après quelques secondes
                        setTimeout(() => {
                            messageContainer.remove();
                        }, 3000);
                        
                        // Restaurer l'état du bouton
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.classList.remove('processing');
                            submitButton.textContent = originalText;
                        }
                    });
                }, 500);
            }
        });
        
        // Intercepter également les clics sur le bouton de soumission
        const submitButton = form.querySelector(config.selectors.editView.submitButton);
        if (submitButton) {
            submitButton.addEventListener('click', function() {
                log('Clic sur le bouton de soumission détecté', null, 'info');
                // La logique est déjà gérée par l'événement submit du formulaire
            });
        }
        
        log('Intercepteur de formulaire configuré avec succès', null, 'success');
    }

    /**
     * Fonction pour écouter les changements de localStorage (synchronisation entre onglets)
     */
    function listenForStorageChanges() {
        if (isListeningForChanges) return;
        
        log('Configuration de l\'écoute des changements de localStorage', null, 'info');
        
        window.addEventListener('storage', function(event) {
            if (event.key === 'conges-sync-update') {
                try {
                    const data = JSON.parse(event.newValue);
                    
                    log('Mise à jour détectée via localStorage', data, 'info');
                    
                    if (data.employeeId && data.cpValue !== undefined) {
                        // Mettre à jour les éléments dans la vue de liste
                        updateListViewElements(data.employeeId, data.cpValue);
                    }
                } catch (err) {
                    log('Erreur lors du traitement de l\'événement de stockage', err, 'error');
                }
            }
        });
        
        isListeningForChanges = true;
        log('Écoute des changements de localStorage configurée', null, 'success');
    }

    /**
     * Fonction pour démarrer les vérifications périodiques
     */
    function startPeriodicChecks() {
        log('Démarrage des vérifications périodiques', null, 'info');
        
        setInterval(function() {
            // Si nous sommes dans la vue de liste, vérifier les mises à jour
            if (isListView()) {
                // Vérifier s'il y a des mises à jour dans localStorage
                try {
                    const updateData = localStorage.getItem('conges-sync-update');
                    if (updateData) {
                        const data = JSON.parse(updateData);
                        
                        // Vérifier si c'est une nouvelle mise à jour
                        const lastUpdate = localStorage.getItem('conges-sync-last-update');
                        if (lastUpdate !== updateData) {
                            log('Nouvelle mise à jour détectée lors de la vérification périodique', data, 'info');
                            
                            if (data.employeeId && data.cpValue !== undefined) {
                                // Mettre à jour les éléments dans la vue de liste
                                updateListViewElements(data.employeeId, data.cpValue);
                                
                                // Marquer cette mise à jour comme traitée
                                localStorage.setItem('conges-sync-last-update', updateData);
                            }
                        }
                    }
                } catch (err) {
                    log('Erreur lors de la vérification périodique', err, 'error');
                }
            }
        }, config.refreshInterval);
        
        log('Vérifications périodiques démarrées', null, 'success');
    }

    /**
     * Fonction d'initialisation
     */
    function initialize() {
        log('Initialisation du script de synchronisation des congés', null, 'info');
        
        // Ajouter les styles CSS pour l'animation
        const style = document.createElement('style');
        style.textContent = `
            .cp-updating {
                animation: cp-update-animation 1.5s ease;
            }
            
            @keyframes cp-update-animation {
                0% { background-color: transparent; }
                30% { background-color: rgba(139, 92, 246, 0.3); }
                100% { background-color: transparent; }
            }
        `;
        document.head.appendChild(style);
        
        // Configurer l'intercepteur de formulaire si nous sommes dans la vue d'édition
        if (isEditView()) {
            setupFormInterceptor();
        }
        
        // Écouter les changements de localStorage pour la synchronisation entre onglets
        listenForStorageChanges();
        
        // Démarrer les vérifications périodiques
        startPeriodicChecks();
        
        // Drapeau pour éviter les boucles infinies
        let isUpdatingCongesList = false;
        
        // Exposer la fonction globalement pour permettre les mises à jour depuis d'autres scripts
        window.updateCongesList = function(employeeId, cpValue) {
            // Éviter les appels récursifs
            if (isUpdatingCongesList) {
                log(`Mise à jour ignorée pour éviter une boucle infinie`, null, 'warning');
                return false;
            }
            
            // Activer le drapeau pour éviter la récursion
            isUpdatingCongesList = true;
            
            log(`Mise à jour des soldes CP pour l'employé ${employeeId} avec la valeur ${cpValue}`, null, 'info');
            
            const result = updateListViewElements(employeeId, cpValue);
            
            // Synchroniser avec conges-cp-monitor si disponible
            if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
                window.CPMonitor.update(employeeId, cpValue);
            } else if (typeof window.forceUpdateCpElements === 'function') {
                window.forceUpdateCpElements(employeeId, cpValue);
            }
            
            // Diffuser l'événement pour d'autres scripts
            document.dispatchEvent(new CustomEvent('conges-cp-updated', {
                detail: { employeId: employeeId, cpValue: cpValue }
            }));
            
            // Réinitialiser le drapeau après un délai suffisant
            setTimeout(() => {
                isUpdatingCongesList = false;
            }, 500);
            
            return result;
        };
        
        // Drapeau pour éviter les boucles infinies dans les écouteurs d'événements
        let isProcessingCpEvent = false;
        
        // Écouter les événements personnalisés de mise à jour des congés
        document.addEventListener('conges-cp-updated', function(event) {
            // Éviter les boucles infinies
            if (isProcessingCpEvent) return;
            
            if (event.detail && event.detail.employeId && event.detail.cpValue !== undefined) {
                // Activer le drapeau pour éviter la récursion
                isProcessingCpEvent = true;
                
                log(`Événement conges-cp-updated reçu: employé ${event.detail.employeId}, CP ${event.detail.cpValue}`, null, 'info');
                updateListViewElements(event.detail.employeId, event.detail.cpValue);
                
                // Réinitialiser le drapeau après un délai suffisant
                setTimeout(() => {
                    isProcessingCpEvent = false;
                }, 500);
            }
        });
        
        // Drapeau pour éviter les boucles infinies dans CPMonitor.update
        let isUpdatingCPMonitor = false;
        
        // S'intégrer avec le script conges-cp-monitor.js s'il existe
        if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
            const originalUpdate = window.CPMonitor.update;
            window.CPMonitor.update = function(employeId, cpValue) {
                // Éviter les boucles infinies
                if (isUpdatingCPMonitor) {
                    log(`Mise à jour CPMonitor ignorée pour éviter une boucle infinie`, null, 'warning');
                    return false;
                }
                
                // Activer le drapeau
                isUpdatingCPMonitor = true;
                
                // Appeler la fonction originale
                originalUpdate(employeId, cpValue);
                
                // Puis notre fonction de mise à jour
                updateListViewElements(employeId, cpValue);
                
                // Diffuser l'événement pour d'autres scripts UNIQUEMENT si nous ne sommes pas déjà en train de traiter un événement
                if (!isProcessingCpEvent) {
                    document.dispatchEvent(new CustomEvent('conges-cp-updated', {
                        detail: { employeId: employeId, cpValue: cpValue }
                    }));
                }
                
                // Réinitialiser le drapeau après un délai suffisant
                setTimeout(() => {
                    isUpdatingCPMonitor = false;
                }, 500);
                
                return true;
            };
            log('Fonction CPMonitor.update étendue avec succès et protection contre les boucles infinies', null, 'success');
        } else {
            // Essayer avec la fonction globale forceUpdateCpElements si elle existe
            if (typeof window.forceUpdateCpElements === 'function') {
                const originalForceUpdateCpElements = window.forceUpdateCpElements;
                window.forceUpdateCpElements = function(employeId, cpValue) {
                    // Appeler la fonction originale
                    originalForceUpdateCpElements(employeId, cpValue);
                    // Puis notre fonction de mise à jour
                    updateListViewElements(employeId, cpValue);
                    // Diffuser l'événement pour d'autres scripts
                    document.dispatchEvent(new CustomEvent('conges-cp-updated', {
                        detail: { employeId: employeId, cpValue: cpValue }
                    }));
                };
                log('Fonction forceUpdateCpElements étendue avec succès', null, 'success');
            }
        }
        
        log('Script de synchronisation des congés initialisé avec succès', null, 'success');
    }

    // Initialiser le script quand le DOM est chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
