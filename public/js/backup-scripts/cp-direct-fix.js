/**
 * cp-direct-fix.js
 * Solution directe pour la mise à jour des congés payés en temps réel
 * Ce script fonctionne indépendamment des autres scripts pour garantir la mise à jour
 */
(function() {
    // Configuration
    const config = {
        debug: true,
        selectors: {
            // Sélecteurs pour les éléments affichant les soldes CP
            cpElements: [
                '[data-employe-id][data-solde-type="conges"]',
                '.cp-value',
                '.solde-conges-value',
                '#current-solde-conges',
                '.conges-value',
                '.solde-cp'
            ],
            // Formulaire d'édition des soldes
            editForm: 'form[action*="solde"]',
            submitButton: 'button[type="submit"], input[type="submit"]',
            cpInput: 'input[name*="conge"], input[name*="cp"]',
            employeeIdInput: 'input[name*="employe_id"]'
        }
    };

    // Drapeaux pour éviter les boucles infinies
    let isUpdating = false;
    let lastUpdateTime = 0;

    /**
     * Fonction principale pour mettre à jour les éléments CP
     */
    function updateCPElements(employeeId, cpValue) {
        // Éviter les mises à jour trop fréquentes
        const now = Date.now();
        if (isUpdating || (now - lastUpdateTime < 1000)) {
            console.log('[CP-FIX] Mise à jour ignorée pour éviter une boucle');
            return false;
        }

        isUpdating = true;
        lastUpdateTime = now;
        console.log(`[CP-FIX] Début mise à jour CP: employé ${employeeId}, valeur ${cpValue}`);

        try {
            // Formater la valeur
            const numericValue = parseFloat(cpValue);
            if (isNaN(numericValue)) {
                console.error('[CP-FIX] Valeur CP invalide:', cpValue);
                isUpdating = false;
                return false;
            }

            const formattedValue = numericValue.toFixed(1);
            const formattedValueWithUnit = formattedValue + ' jours';

            // Mettre à jour tous les éléments correspondants
            let updateCount = 0;
            
            // Parcourir tous les sélecteurs possibles
            config.selectors.cpElements.forEach(selector => {
                // Cibler les éléments spécifiques à cet employé si possible
                let elements = [];
                
                if (selector.includes('data-employe-id') && employeeId) {
                    elements = document.querySelectorAll(`${selector.replace('[data-employe-id]', `[data-employe-id="${employeeId}"]`)}`);
                } else {
                    // Pour les sélecteurs génériques, essayer de trouver ceux qui correspondent à cet employé
                    const allElements = document.querySelectorAll(selector);
                    elements = Array.from(allElements).filter(el => {
                        // Vérifier si l'élément a un attribut data-employe-id correspondant
                        if (el.getAttribute('data-employe-id') === employeeId) {
                            return true;
                        }
                        // Vérifier si l'élément est dans un conteneur avec cet ID employé
                        const parent = el.closest(`[data-employe-id="${employeeId}"]`);
                        return parent !== null;
                    });
                }

                // Mettre à jour chaque élément trouvé
                elements.forEach(el => {
                    const oldValue = el.textContent.trim();
                    
                    // Choisir le format approprié selon le contexte
                    const newValue = el.id === 'current-solde-conges' ? formattedValueWithUnit : formattedValue;
                    
                    // Mettre à jour le contenu
                    el.textContent = newValue;
                    
                    // Ajouter une animation pour montrer la mise à jour
                    el.classList.add('updated');
                    setTimeout(() => el.classList.remove('updated'), 2000);
                    
                    updateCount++;
                    console.log(`[CP-FIX] Élément mis à jour: ${el.tagName}#${el.id || 'sans-id'}.${el.className} de ${oldValue} à ${newValue}`);
                });
            });

            // Enregistrer la mise à jour dans sessionStorage pour la persistance
            sessionStorage.setItem('cp_value_' + employeeId, formattedValue);
            sessionStorage.setItem('cp_update_time', now);

            console.log(`[CP-FIX] Mise à jour terminée: ${updateCount} éléments modifiés`);
            
            // Diffuser l'événement pour les autres scripts
            document.dispatchEvent(new CustomEvent('cp-updated', {
                detail: { employeId: employeeId, cpValue: formattedValue }
            }));

            return updateCount > 0;
        } catch (error) {
            console.error('[CP-FIX] Erreur lors de la mise à jour:', error);
            return false;
        } finally {
            // Réinitialiser le drapeau après un délai
            setTimeout(() => {
                isUpdating = false;
            }, 1000);
        }
    }

    /**
     * Intercepter la soumission du formulaire d'édition des soldes
     */
    function setupFormInterceptor() {
        const forms = document.querySelectorAll(config.selectors.editForm);
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Empêcher la soumission normale
                e.preventDefault();
                
                // Récupérer les valeurs
                const cpInput = form.querySelector(config.selectors.cpInput);
                const employeeIdInput = form.querySelector(config.selectors.employeeIdInput);
                
                if (!cpInput || !employeeIdInput) {
                    console.error('[CP-FIX] Champs requis non trouvés dans le formulaire');
                    form.submit(); // Soumettre normalement si on ne peut pas intercepter
                    return;
                }
                
                const cpValue = cpInput.value;
                const employeeId = employeeIdInput.value;
                
                console.log(`[CP-FIX] Formulaire intercepté: employé ${employeeId}, CP ${cpValue}`);
                
                // Désactiver le bouton pendant la soumission
                const submitButton = form.querySelector(config.selectors.submitButton);
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Enregistrement...';
                }
                
                // Soumettre le formulaire en AJAX
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('[CP-FIX] Réponse AJAX:', data);
                    
                    // Mettre à jour les éléments CP immédiatement
                    updateCPElements(employeeId, cpValue);
                    
                    // Afficher un message de succès
                    const message = document.createElement('div');
                    message.className = 'alert alert-success mt-3';
                    message.textContent = 'Modifications enregistrées avec succès';
                    form.appendChild(message);
                    setTimeout(() => message.remove(), 3000);
                })
                .catch(error => {
                    console.error('[CP-FIX] Erreur AJAX:', error);
                    
                    // Afficher un message d'erreur
                    const message = document.createElement('div');
                    message.className = 'alert alert-danger mt-3';
                    message.textContent = 'Erreur lors de l\'enregistrement';
                    form.appendChild(message);
                    setTimeout(() => message.remove(), 3000);
                })
                .finally(() => {
                    // Réactiver le bouton
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Enregistrer les modifications';
                    }
                });
            });
        });
    }

    /**
     * Ajouter les styles CSS nécessaires
     */
    function addStyles() {
        const style = document.createElement('style');
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

    /**
     * Écouter les événements de mise à jour des CP
     */
    function listenForCPUpdates() {
        // Écouter les événements personnalisés
        document.addEventListener('conges-cp-updated', function(event) {
            if (event.detail && event.detail.employeId && event.detail.cpValue !== undefined) {
                updateCPElements(event.detail.employeId, event.detail.cpValue);
            }
        });
        
        // Écouter les changements de localStorage (pour la synchronisation entre onglets)
        window.addEventListener('storage', function(event) {
            if (event.key && (event.key.includes('conges-sync') || event.key.includes('cp_value'))) {
                try {
                    const data = JSON.parse(event.newValue);
                    if (data.employeeId && data.cpValue !== undefined) {
                        updateCPElements(data.employeeId, data.cpValue);
                    }
                } catch (e) {
                    console.error('[CP-FIX] Erreur lors du traitement de l\'événement storage:', e);
                }
            }
        });
    }

    /**
     * Exposer la fonction de mise à jour globalement
     */
    function exposeGlobalFunction() {
        window.directUpdateCP = updateCPElements;
        
        // Remplacer également la fonction updateCongesPayes si elle existe
        if (typeof window.updateCongesPayes === 'function') {
            const originalFn = window.updateCongesPayes;
            window.updateCongesPayes = function(employeeId, cpValue) {
                // Appeler notre fonction en premier
                const result = updateCPElements(employeeId, cpValue);
                
                // Puis la fonction originale
                originalFn(employeeId, cpValue);
                
                return result;
            };
        } else {
            // Définir la fonction si elle n'existe pas
            window.updateCongesPayes = updateCPElements;
        }
    }

    /**
     * Initialisation du script
     */
    function initialize() {
        console.log('[CP-FIX] Initialisation du script de correction CP');
        addStyles();
        setupFormInterceptor();
        listenForCPUpdates();
        exposeGlobalFunction();
        console.log('[CP-FIX] Script initialisé avec succès');
    }

    // Exécuter l'initialisation quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
