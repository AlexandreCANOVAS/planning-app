/**
 * conges-solde-sync.js
 * Solution intégrée MVC + JavaScript pour la mise à jour des soldes de congés
 * Ce script assure que les mises à jour sont correctement sauvegardées en base de données
 * et synchronisées visuellement sans rechargement de page
 */
(function() {
    // Configuration
    const config = {
        debug: true,
        selectors: {
            // Formulaire d'édition des soldes
            editForm: 'form[action*="solde"]',
            submitButton: 'button[type="submit"], input[type="submit"]',
            cpInput: 'input[name="solde_conges"]',
            rttInput: 'input[name="solde_rtt"]',
            exceptionnelsInput: 'input[name="solde_conges_exceptionnels"]',
            employeeIdInput: 'input[name*="employe_id"]',
            // Sélecteurs pour les éléments affichant les soldes
            cpElements: [
                '[data-employe-id][data-solde-type="conges"]',
                '.cp-value',
                '.solde-conges-value',
                '#current-solde-conges',
                '.conges-value',
                '.solde-cp'
            ]
        }
    };

    // Fonction de journalisation
    function log(message, data = null, level = 'info') {
        if (!config.debug) return;
        
        const prefix = '[CONGES-SYNC]';
        
        switch (level) {
            case 'success':
                console.log(`%c${prefix} ${message}`, 'color: green; font-weight: bold;', data);
                break;
            case 'error':
                console.error(`${prefix} ${message}`, data);
                break;
            case 'warning':
                console.warn(`${prefix} ${message}`, data);
                break;
            default:
                console.log(`${prefix} ${message}`, data);
        }
    }

    // Fonction pour mettre à jour visuellement les éléments
    function updateVisualElements(employeeId, cpValue, rttValue, exceptionnelsValue) {
        log('Mise à jour visuelle des éléments', {
            employeeId,
            cpValue,
            rttValue,
            exceptionnelsValue
        });
        
        // Formater les valeurs
        const formattedCp = parseFloat(cpValue).toFixed(1);
        const formattedRtt = parseFloat(rttValue).toFixed(1);
        const formattedExceptionnels = parseFloat(exceptionnelsValue).toFixed(1);
        
        // Mettre à jour les éléments CP
        config.selectors.cpElements.forEach(selector => {
            let elements = [];
            
            if (selector.includes('data-employe-id') && employeeId) {
                elements = document.querySelectorAll(`${selector.replace('[data-employe-id]', `[data-employe-id="${employeeId}"]`)}`);
            } else {
                const allElements = document.querySelectorAll(selector);
                elements = Array.from(allElements).filter(el => {
                    if (el.getAttribute('data-employe-id') === employeeId) return true;
                    const parent = el.closest(`[data-employe-id="${employeeId}"]`);
                    return parent !== null;
                });
            }
            
            elements.forEach(el => {
                const oldValue = el.textContent.trim();
                el.textContent = formattedCp;
                
                // Animation pour montrer la mise à jour
                el.classList.add('updated');
                setTimeout(() => el.classList.remove('updated'), 2000);
                
                log(`Élément CP mis à jour: ${el.tagName}#${el.id || 'sans-id'} de ${oldValue} à ${formattedCp}`);
            });
        });
        
        // Mettre à jour les éléments RTT et congés exceptionnels si nécessaire
        // (code similaire pour RTT et congés exceptionnels si besoin)
        
        // Stocker les valeurs dans sessionStorage pour persistance temporaire
        sessionStorage.setItem(`cp_value_${employeeId}`, formattedCp);
        sessionStorage.setItem(`rtt_value_${employeeId}`, formattedRtt);
        sessionStorage.setItem(`exceptionnels_value_${employeeId}`, formattedExceptionnels);
        sessionStorage.setItem('soldes_update_time', Date.now());
    }

    // Fonction pour intercepter la soumission du formulaire
    function setupFormInterceptor() {
        const forms = document.querySelectorAll(config.selectors.editForm);
        
        forms.forEach(form => {
            log('Configuration de l\'intercepteur pour le formulaire', form.action);
            
            form.addEventListener('submit', function(e) {
                // Empêcher la soumission normale
                e.preventDefault();
                
                // Récupérer les valeurs du formulaire
                const cpInput = form.querySelector(config.selectors.cpInput);
                const rttInput = form.querySelector(config.selectors.rttInput);
                const exceptionnelsInput = form.querySelector(config.selectors.exceptionnelsInput);
                const employeeIdInput = form.querySelector(config.selectors.employeeIdInput);
                
                if (!cpInput || !employeeIdInput) {
                    log('Champs requis non trouvés dans le formulaire', null, 'error');
                    form.submit(); // Soumettre normalement si on ne peut pas intercepter
                    return;
                }
                
                const cpValue = cpInput.value;
                const rttValue = rttInput ? rttInput.value : '0';
                const exceptionnelsValue = exceptionnelsInput ? exceptionnelsInput.value : '0';
                const employeeId = employeeIdInput.value;
                
                log('Formulaire intercepté', {
                    employeeId,
                    cpValue,
                    rttValue,
                    exceptionnelsValue
                });
                
                // Désactiver le bouton pendant la soumission
                const submitButton = form.querySelector(config.selectors.submitButton);
                if (submitButton) {
                    submitButton.disabled = true;
                    const originalText = submitButton.textContent;
                    submitButton.textContent = 'Enregistrement...';
                }
                
                // Préparer les données du formulaire
                const formData = new FormData(form);
                
                // Soumettre le formulaire en AJAX en respectant le format attendu par Laravel
                fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    log('Réponse du serveur', data, 'success');
                    
                    if (data.success) {
                        // Mise à jour visuelle des éléments
                        updateVisualElements(employeeId, cpValue, rttValue, exceptionnelsValue);
                        
                        // Afficher un message de succès
                        const messageContainer = document.createElement('div');
                        messageContainer.className = 'alert alert-success mt-3';
                        messageContainer.textContent = data.message || 'Modifications enregistrées avec succès';
                        form.appendChild(messageContainer);
                        
                        // Faire disparaître le message après quelques secondes
                        setTimeout(() => {
                            messageContainer.remove();
                        }, 3000);
                        
                        // Déclencher un événement personnalisé pour informer les autres scripts
                        document.dispatchEvent(new CustomEvent('conges-updated', {
                            detail: {
                                employeId: employeeId,
                                cpValue: cpValue,
                                rttValue: rttValue,
                                exceptionnelsValue: exceptionnelsValue
                            }
                        }));
                    } else {
                        throw new Error(data.message || 'Erreur lors de l\'enregistrement');
                    }
                })
                .catch(error => {
                    log('Erreur lors de la soumission du formulaire', error, 'error');
                    
                    // Afficher un message d'erreur
                    const messageContainer = document.createElement('div');
                    messageContainer.className = 'alert alert-danger mt-3';
                    messageContainer.textContent = 'Erreur lors de l\'enregistrement des modifications: ' + error.message;
                    form.appendChild(messageContainer);
                    
                    // Faire disparaître le message après quelques secondes
                    setTimeout(() => {
                        messageContainer.remove();
                    }, 5000);
                })
                .finally(() => {
                    // Réactiver le bouton
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText || 'Enregistrer les modifications';
                    }
                });
            });
        });
    }

    // Ajouter les styles CSS nécessaires
    function addStyles() {
        const styleId = 'conges-solde-sync-styles';
        
        // Ne pas ajouter les styles s'ils existent déjà
        if (document.getElementById(styleId)) return;
        
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .updated {
                animation: highlight-update 2s ease-in-out;
            }
            
            @keyframes highlight-update {
                0% { background-color: transparent; }
                30% { background-color: rgba(255, 255, 0, 0.5); }
                100% { background-color: transparent; }
            }
        `;
        document.head.appendChild(style);
    }

    // Initialisation du script
    function initialize() {
        log('Initialisation du script de synchronisation des soldes de congés');
        addStyles();
        setupFormInterceptor();
        log('Script initialisé avec succès');
    }

    // Exécuter l'initialisation quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
