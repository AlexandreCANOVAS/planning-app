/**
 * cp-direct-fix-v2.js
 * Solution directe et simplifiée pour la mise à jour des soldes CP
 * Cette version corrige les problèmes de persistance et de synchronisation
 */

(function() {
    // Configuration
    const config = {
        debug: true,
        selectors: {
            form: '#solde-form',
            cpInput: 'input[name="solde_conges"]',
            rttInput: 'input[name="solde_rtt"]',
            exceptionnelsInput: 'input[name="solde_conges_exceptionnels"]',
            submitButton: 'button[type="submit"]',
            cpElements: [
                '[data-employe-id][data-solde-type="conges"]',
                '.solde-conges-value',
                '#current-solde-conges',
                '.cp-value',
                '.conges-value',
                '.solde-cp'
            ]
        }
    };

    // Fonction de journalisation
    function log(message, data = null, level = 'info') {
        if (!config.debug) return;
        
        const prefix = '[CP-DIRECT-FIX-V2]';
        
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
    function updateVisualElements(employeeId, cpValue) {
        log('Mise à jour visuelle des éléments CP', { employeeId, cpValue });
        
        // Formater la valeur
        const formattedValue = parseFloat(cpValue).toFixed(1);
        const formattedValueWithUnit = formattedValue + ' jours';
        
        // Mettre à jour tous les éléments correspondants
        config.selectors.cpElements.forEach(selector => {
            let elements = [];
            
            if (selector.includes('data-employe-id') && employeeId) {
                elements = document.querySelectorAll(`${selector.replace('[data-employe-id]', `[data-employe-id="${employeeId}"]`)}`);
            } else {
                elements = document.querySelectorAll(selector);
            }
            
            elements.forEach(el => {
                // Vérifier si l'élément est lié à l'employé concerné
                const elEmployeeId = el.getAttribute('data-employe-id');
                if (elEmployeeId && elEmployeeId !== employeeId.toString()) {
                    return;
                }
                
                // Récupérer l'ancienne valeur pour le log
                const oldValue = el.textContent.trim();
                
                // Mettre à jour avec la nouvelle valeur
                if (el.id === 'current-solde-conges' || el.textContent.includes('jours')) {
                    el.textContent = formattedValueWithUnit;
                } else {
                    el.textContent = formattedValue;
                }
                
                // Animation pour montrer la mise à jour
                el.classList.add('cp-updated');
                setTimeout(() => el.classList.remove('cp-updated'), 2000);
                
                log(`Élément CP mis à jour: ${el.tagName}#${el.id || 'sans-id'} de ${oldValue} à ${el.textContent}`);
            });
        });
        
        // Mettre à jour les inputs du formulaire si présents
        const cpInputs = document.querySelectorAll(config.selectors.cpInput);
        cpInputs.forEach(input => {
            input.value = formattedValue;
        });
        
        // Stocker la valeur dans sessionStorage pour persistance temporaire
        sessionStorage.setItem(`cp_value_${employeeId}`, formattedValue);
        sessionStorage.setItem('cp_update_time', Date.now());
        
        // Notifier les autres onglets via localStorage
        try {
            localStorage.setItem('cp_sync_update', JSON.stringify({
                employeeId: employeeId,
                cpValue: formattedValue,
                timestamp: Date.now()
            }));
        } catch (e) {
            log('Erreur lors de la synchronisation inter-onglets', e, 'error');
        }
    }

    // Fonction pour intercepter la soumission du formulaire
    function setupFormInterception() {
        const form = document.querySelector(config.selectors.form);
        
        if (!form) {
            log('Formulaire non trouvé', null, 'warning');
            return;
        }
        
        log('Configuration de l\'interception du formulaire', form);
        
        // Marquer le formulaire pour éviter les initialisations multiples
        if (form.getAttribute('data-cp-fix-initialized') === 'true') {
            log('Formulaire déjà initialisé, abandon', null, 'warning');
            return;
        }
        
        form.setAttribute('data-cp-fix-initialized', 'true');
        
        form.addEventListener('submit', function(e) {
            // Empêcher la soumission normale
            e.preventDefault();
            
            // Récupérer l'ID de l'employé depuis l'URL du formulaire
            const employeeId = form.action.split('/').pop();
            
            if (!employeeId) {
                log('Impossible de récupérer l\'ID de l\'employé, soumission normale', null, 'error');
                form.submit();
                return;
            }
            
            // Récupérer les valeurs du formulaire
            const formCpInput = form.querySelector(config.selectors.cpInput);
            
            if (!formCpInput) {
                log('Champ de solde CP non trouvé, soumission normale', null, 'error');
                form.submit();
                return;
            }
            
            const cpValue = formCpInput.value;
            
            log('Formulaire intercepté', { employeeId, cpValue });
            
            // Désactiver le bouton pendant la soumission
            const submitButton = form.querySelector(config.selectors.submitButton);
            let originalText = 'Enregistrer';
            if (submitButton) {
                submitButton.disabled = true;
                originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement en cours...';
            }
            
            // Préparer les données du formulaire
            const formData = new FormData(form);
            
            // Gérer correctement les méthodes PUT/PATCH pour Laravel
            const method = form.getAttribute('method').toUpperCase();
            
            // S'assurer que les valeurs numériques sont correctement formatées
            // Convertir explicitement les valeurs en nombres à virgule flottante
            const rttInput = form.querySelector(config.selectors.rttInput);
            const exceptionnelsInput = form.querySelector(config.selectors.exceptionnelsInput);
            
            // Assurer que la valeur CP est un nombre à virgule flottante
            const cpValueFloat = parseFloat(formCpInput.value).toFixed(1);
            formData.set('solde_conges', cpValueFloat);
            log('Valeur CP formatée pour envoi:', cpValueFloat);
            
            if (rttInput) {
                formData.set('solde_rtt', parseFloat(rttInput.value).toFixed(1));
            }
            
            if (exceptionnelsInput) {
                formData.set('solde_conges_exceptionnels', parseFloat(exceptionnelsInput.value).toFixed(1));
            }
            
            // Pour les méthodes PUT/PATCH, Laravel attend un champ _method
            if (method === 'PUT' || method === 'PATCH') {
                formData.append('_method', method);
            }
            
            // S'assurer que le token CSRF est inclus
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }
            
            // Afficher les données qui seront envoyées pour le débogage
            if (config.debug) {
                log('Données du formulaire à envoyer:');
                for (let pair of formData.entries()) {
                    log(`${pair[0]}: ${pair[1]}`);
                }
            }
            
            // Soumettre le formulaire en AJAX
            fetch(form.action, {
                method: 'POST', // Toujours utiliser POST et laisser _method gérer le type réel
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
                
                // Cloner la réponse pour pouvoir l'examiner en texte brut si nécessaire
                const clonedResponse = response.clone();
                
                // Essayer de parser la réponse en JSON
                return response.json().catch(jsonError => {
                    // En cas d'erreur de parsing JSON, récupérer le texte brut pour diagnostic
                    return clonedResponse.text().then(rawText => {
                        log('Erreur de parsing JSON, contenu brut de la réponse:', rawText.substring(0, 500) + '...', 'error');
                        throw new Error(`Erreur de parsing JSON: ${jsonError.message}. Vérifiez les logs pour plus de détails.`);
                    });
                });
            })
            .then(data => {
                log('Réponse du serveur', data, 'success');
                
                if (data.success) {
                    // Récupérer les valeurs depuis la réponse du serveur pour s'assurer d'avoir les valeurs correctes
                    const serverCpValue = data.employe && data.employe.solde_conges ? data.employe.solde_conges : cpValue;
                    const serverRttValue = data.employe && data.employe.solde_rtt ? data.employe.solde_rtt : (rttInput ? rttInput.value : null);
                    const serverExceptionnelsValue = data.employe && data.employe.solde_conges_exceptionnels ? data.employe.solde_conges_exceptionnels : (exceptionnelsInput ? exceptionnelsInput.value : null);
                    
                    log('Valeurs retournées par le serveur', {
                        solde_conges: serverCpValue,
                        solde_rtt: serverRttValue,
                        solde_conges_exceptionnels: serverExceptionnelsValue
                    }, 'success');
                    
                    // Mettre à jour visuellement les éléments
                    updateVisualElements(employeeId, serverCpValue);
                    
                    // Mettre à jour les valeurs des champs du formulaire pour éviter les incohérences
                    if (formCpInput && serverCpValue) {
                        formCpInput.value = serverCpValue;
                    }
                    if (rttInput && serverRttValue) {
                        rttInput.value = serverRttValue;
                    }
                    if (exceptionnelsInput && serverExceptionnelsValue) {
                        exceptionnelsInput.value = serverExceptionnelsValue;
                    }
                    
                    // Afficher un message de succès
                    showMessage('Soldes de congés mis à jour avec succès', 'success');
                    
                    // Stocker les valeurs dans le localStorage pour la synchronisation entre onglets
                    try {
                        localStorage.setItem('cp_last_update', JSON.stringify({
                            employeId: employeeId,
                            solde_conges: serverCpValue,
                            solde_rtt: serverRttValue,
                            solde_conges_exceptionnels: serverExceptionnelsValue,
                            timestamp: Date.now()
                        }));
                        
                        // Déclencher un événement de stockage pour la synchronisation entre onglets
                        window.dispatchEvent(new StorageEvent('storage', {
                            key: 'cp_last_update',
                            newValue: localStorage.getItem('cp_last_update')
                        }));
                    } catch (e) {
                        log('Erreur lors du stockage des valeurs dans localStorage', e, 'error');
                    }
                    
                    // Déclencher un événement personnalisé pour informer les autres scripts
                    document.dispatchEvent(new CustomEvent('conges-cp-updated', {
                        detail: {
                            employeId: employeeId,
                            cpValue: serverCpValue,
                            rttValue: serverRttValue,
                            exceptionnelsValue: serverExceptionnelsValue
                        }
                    }));
                } else {
                    showMessage('Erreur lors de la mise à jour des soldes: ' + (data.message || 'Erreur inconnue'), 'error');
                }
            })
            .catch(error => {
                log('Erreur lors de la soumission du formulaire', error, 'error');
                showMessage('Erreur lors de la mise à jour des soldes: ' + error.message, 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
        });
    }

    // Fonction pour afficher un message à l'utilisateur
    function showMessage(message, type = 'info') {
        // Utiliser la fonction showToast si elle existe
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }
        
        // Sinon, créer notre propre message
        const messageContainer = document.createElement('div');
        messageContainer.className = `alert alert-${type === 'error' ? 'danger' : type} fixed-top mx-auto mt-4 w-75 shadow-lg`;
        messageContainer.style.zIndex = '9999';
        messageContainer.style.maxWidth = '500px';
        messageContainer.style.left = '50%';
        messageContainer.style.transform = 'translateX(-50%)';
        messageContainer.textContent = message;
        
        document.body.appendChild(messageContainer);
        
        // Faire disparaître le message après quelques secondes
        setTimeout(() => {
            messageContainer.style.opacity = '0';
            messageContainer.style.transition = 'opacity 0.5s ease-out';
            
            setTimeout(() => {
                document.body.removeChild(messageContainer);
            }, 500);
        }, 3000);
    }

    // Fonction pour ajouter les styles CSS nécessaires
    function addStyles() {
        const styleId = 'cp-direct-fix-styles';
        
        // Ne pas ajouter les styles s'ils existent déjà
        if (document.getElementById(styleId)) return;
        
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .cp-updated {
                animation: cp-highlight-update 2s ease-in-out;
            }
            
            @keyframes cp-highlight-update {
                0% { background-color: transparent; }
                30% { background-color: rgba(59, 130, 246, 0.3); }
                100% { background-color: transparent; }
            }
        `;
        document.head.appendChild(style);
    }

    // Fonction d'initialisation
    function initialize() {
        log('Initialisation du script CP Direct Fix V2');
        
        // Ajouter les styles CSS
        addStyles();
        
        // Configurer l'interception du formulaire
        setupFormInterception();
        
        // Écouter les événements de synchronisation inter-onglets
        window.addEventListener('storage', function(event) {
            if (event.key === 'cp_sync_update') {
                try {
                    const data = JSON.parse(event.newValue);
                    if (data && data.employeeId && data.cpValue !== undefined) {
                        log('Mise à jour reçue d\'un autre onglet', data);
                        updateVisualElements(data.employeeId, data.cpValue);
                    }
                } catch (e) {
                    log('Erreur lors du traitement de la mise à jour inter-onglets', e, 'error');
                }
            }
        });
        
        log('Script CP Direct Fix V2 initialisé avec succès');
    }

    // Exécuter l'initialisation quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
