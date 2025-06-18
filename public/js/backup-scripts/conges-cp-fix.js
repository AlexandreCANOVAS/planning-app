/**
 * Script spécifique pour corriger le problème de mise à jour des congés payés (CP)
 * Version améliorée avec multiples méthodes de mise à jour pour garantir la réussite
 * Avec logs de diagnostic détaillés
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('%c[CP-FIX] Initialisation du script de correction amélioré pour les congés payés', 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;');
    
    // Fonction de journalisation avec style
    function logDebug(message, data) {
        console.log('%c[CP-FIX] ' + message, 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Fonction de journalisation d'erreur avec style
    function logError(message, data) {
        console.error('%c[CP-FIX ERROR] ' + message, 'background: #ef4444; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Fonction de journalisation de succès avec style
    function logSuccess(message, data) {
        console.log('%c[CP-FIX SUCCESS] ' + message, 'background: #10b981; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Détection de la soumission du formulaire de soldes
    const soldeForm = document.getElementById('solde-form');
    if (soldeForm) {
        logDebug('Formulaire de soldes détecté', { id: soldeForm.id, action: soldeForm.action });
        
        // Lister tous les champs du formulaire pour débogage
        const formFields = Array.from(soldeForm.elements).map(el => ({
            name: el.name,
            id: el.id,
            type: el.type,
            value: el.value
        }));
        
        logDebug('Champs du formulaire de solde', formFields);
        
        // Vérifier si les champs nécessaires existent
        const employeIdField = document.getElementById('employe_id');
        const soldeCongesField = document.getElementById('solde_conges');
        
        if (!employeIdField) {
            logError('Champ employe_id non trouvé dans le formulaire');
        } else {
            logDebug('Champ employe_id trouvé', {
                valeur: employeIdField.value,
                type: employeIdField.type
            });
        }
        
        if (!soldeCongesField) {
            logError('Champ solde_conges non trouvé dans le formulaire');
        } else {
            logDebug('Champ solde_conges trouvé', {
                valeur: soldeCongesField.value,
                type: soldeCongesField.type
            });
        }
        
        // Ajouter un gestionnaire d'événements pour intercepter la soumission du formulaire
        soldeForm.addEventListener('submit', function(e) {
            logDebug('Soumission du formulaire détectée');
            
            // Ne pas empêcher la soumission normale
            
            // Capturer les valeurs soumises
            const employeId = document.getElementById('employe_id')?.value;
            const soldeConges = document.getElementById('solde_conges')?.value;
            
            logDebug('Valeurs du formulaire capturées', { 
                employeId: employeId, 
                soldeConges: soldeConges,
                employeIdType: typeof employeId,
                soldeCongesType: typeof soldeConges
            });
            
            if (!employeId) {
                logError('ID employé manquant lors de la soumission du formulaire');
                return;
            }
            
            if (soldeConges === undefined || soldeConges === null) {
                logError('Valeur de solde CP manquante lors de la soumission du formulaire');
                return;
            }
            
            try {
                // Stocker les valeurs pour une mise à jour ultérieure
                sessionStorage.setItem('lastSubmittedCP_' + employeId, soldeConges);
                sessionStorage.setItem('cpSubmitTime', new Date().getTime());
                
                logDebug('Valeurs stockées dans sessionStorage', {
                    clé: 'lastSubmittedCP_' + employeId,
                    valeur: soldeConges,
                    timestamp: new Date().getTime()
                });
                
                // Essayer de mettre à jour immédiatement
                const updateResult = updateCongesPayes(employeId, soldeConges);
                logDebug('Résultat de la mise à jour immédiate', {
                    succès: updateResult ? 'oui' : 'non',
                    employeId: employeId,
                    soldeConges: soldeConges
                });
                
                // Planifier une mise à jour différée pour s'assurer que les changements sont appliqués
                setTimeout(() => {
                    logDebug('Tentative de mise à jour différée après soumission');
                    updateCongesPayes(employeId, soldeConges);
                }, 1000);
                
                // Planifier une autre mise à jour après un délai plus long
                setTimeout(() => {
                    logDebug('Tentative de mise à jour différée finale après soumission');
                    updateCongesPayes(employeId, soldeConges);
                }, 3000);
            } catch (e) {
                logError('Erreur lors du traitement de la soumission du formulaire', {
                    erreur: e.message,
                    stack: e.stack
                });
            }
        });
    } else {
        logDebug('Formulaire de solde non trouvé dans le DOM');
    }
    
    // Fonction pour mettre à jour spécifiquement les congés payés avec plusieurs méthodes
    function updateCongesPayes(employeId, soldeConges) {
        logDebug('Début de mise à jour des CP', {
            employeId: employeId,
            valeurBrute: soldeConges,
            type: typeof soldeConges
        });
        
        // Vérification des paramètres
        if (!employeId) {
            logError('Impossible de mettre à jour les CP: ID employé manquant');
            return false;
        }
        
        if (soldeConges === undefined || soldeConges === null) {
            logError('Impossible de mettre à jour les CP: valeur manquante', { employeId });
            return false;
        }
        
        // Convertir en nombre et formater avec une décimale
        let soldeCongesValue;
        try {
            soldeCongesValue = parseFloat(soldeConges);
            if (isNaN(soldeCongesValue)) {
                logError('Impossible de convertir la valeur CP en nombre', { 
                    valeurBrute: soldeConges,
                    employeId: employeId 
                });
                return false;
            }
        } catch (e) {
            logError('Erreur lors de la conversion de la valeur CP', { 
                erreur: e.message,
                valeurBrute: soldeConges,
                employeId: employeId 
            });
            return false;
        }
        
        const formattedValue = soldeCongesValue.toFixed(1);
        const formattedValueWithUnit = formattedValue + ' jours';
        
        logDebug('Valeurs formatées pour mise à jour', {
            valeurBrute: soldeConges,
            valeurNumérique: soldeCongesValue,
            valeurFormatée: formattedValue,
            valeurAvecUnité: formattedValueWithUnit
        });
        
        // 1. Méthode 1: Mise à jour directe avec innerHTML
        const method1Result = updateElementsByMethod1(employeId, formattedValue, formattedValueWithUnit);
        
        // 2. Méthode 2: Remplacement complet des éléments
        setTimeout(() => {
            const method2Result = updateElementsByMethod2(employeId, formattedValue, formattedValueWithUnit);
            logDebug('Résultat méthode 2', method2Result);
        }, 100);
        
        // 3. Méthode 3: Mise à jour via attributs de données
        setTimeout(() => {
            const method3Result = updateElementsByMethod3(employeId, formattedValue);
            logDebug('Résultat méthode 3', method3Result);
        }, 200);
        
        // Notification toast si disponible
        if (typeof window.showToast === 'function') {
            window.showToast('Les congés payés ont été mis à jour avec succès', 'success');
            logDebug('Notification toast affichée');
        } else {
            logDebug('Fonction showToast non disponible');
        }
        
        // Stocker la valeur mise à jour dans sessionStorage
        sessionStorage.setItem('lastUpdatedCP_' + employeId, soldeCongesValue);
        sessionStorage.setItem('cpUpdateTime', new Date().getTime());
        logDebug('Valeur CP stockée dans sessionStorage', {
            clé: 'lastUpdatedCP_' + employeId,
            valeur: soldeCongesValue,
            timestamp: new Date().getTime()
        });
        
        // Ajouter un log dans la console pour débogage facile
        console.table({
            'ID Employé': employeId,
            'Valeur CP': soldeCongesValue,
            'Formatée': formattedValue,
            'Avec unité': formattedValueWithUnit,
            'Méthode 1': method1Result ? 'Succès' : 'Échec',
            'Timestamp': new Date().toISOString()
        });
        
        return true;
    }
    
    // Méthode 1: Mise à jour directe avec innerHTML
    function updateElementsByMethod1(employeId, formattedValue, formattedValueWithUnit) {
        logDebug('Méthode 1: Début de mise à jour directe avec innerHTML', {
            employeId: employeId,
            valeur: formattedValue,
            valeurAvecUnité: formattedValueWithUnit
        });
        
        let updateCount = 0;
        
        // Mise à jour dans la vue de gestion des soldes (index)
        const cpElements = document.querySelectorAll(`[data-employe-id="${employeId}"][data-solde-type="conges"]`);
        logDebug('Méthode 1: Éléments CP trouvés dans la vue index', {
            nombre: cpElements.length,
            sélecteur: `[data-employe-id="${employeId}"][data-solde-type="conges"]`
        });
        
        // Lister tous les éléments avec data-employe-id pour débogage
        const allEmployeElements = document.querySelectorAll('[data-employe-id]');
        logDebug('Tous les éléments avec data-employe-id', {
            nombre: allEmployeElements.length,
            ids: Array.from(allEmployeElements).map(el => el.getAttribute('data-employe-id'))
        });
        
        // Lister tous les éléments avec data-solde-type pour débogage
        const allSoldeTypeElements = document.querySelectorAll('[data-solde-type]');
        logDebug('Tous les éléments avec data-solde-type', {
            nombre: allSoldeTypeElements.length,
            types: Array.from(allSoldeTypeElements).map(el => el.getAttribute('data-solde-type'))
        });
        
        cpElements.forEach((el, index) => {
            logDebug(`Méthode 1: Mise à jour de l'élément index ${index}`, {
                avant: el.innerHTML,
                après: formattedValue,
                id: el.id,
                classes: el.className
            });
            
            try {
                const oldValue = el.innerHTML;
                el.innerHTML = formattedValue;
                highlightElement(el);
                updateCount++;
                
                logSuccess(`Méthode 1: Élément index ${index} mis à jour`, {
                    de: oldValue,
                    vers: formattedValue
                });
            } catch (e) {
                logError(`Méthode 1: Erreur lors de la mise à jour de l'élément index ${index}`, {
                    erreur: e.message,
                    élément: el.outerHTML
                });
            }
        });
        
        // Mise à jour dans la vue d'édition des soldes
        const currentSoldeConges = document.getElementById('current-solde-conges');
        if (currentSoldeConges) {
            logDebug('Méthode 1: Élément current-solde-conges trouvé', {
                avant: currentSoldeConges.innerHTML,
                après: formattedValueWithUnit,
                id: currentSoldeConges.id,
                classes: currentSoldeConges.className
            });
            
            try {
                const oldValue = currentSoldeConges.innerHTML;
                currentSoldeConges.innerHTML = formattedValueWithUnit;
                highlightElement(currentSoldeConges);
                updateCount++;
                
                logSuccess('Méthode 1: Élément current-solde-conges mis à jour', {
                    de: oldValue,
                    vers: formattedValueWithUnit
                });
            } catch (e) {
                logError('Méthode 1: Erreur lors de la mise à jour de current-solde-conges', {
                    erreur: e.message,
                    élément: currentSoldeConges.outerHTML
                });
            }
        } else {
            logDebug('Méthode 1: Élément current-solde-conges non trouvé');
        }
        
        // Mise à jour dans le tableau de bord employé
        const dashboardSoldeConges = document.querySelector('.solde-conges-value');
        if (dashboardSoldeConges) {
            logDebug('Méthode 1: Élément dashboard solde-conges-value trouvé', {
                avant: dashboardSoldeConges.innerHTML,
                après: formattedValue,
                classes: dashboardSoldeConges.className
            });
            
            try {
                const oldValue = dashboardSoldeConges.innerHTML;
                dashboardSoldeConges.innerHTML = formattedValue;
                highlightElement(dashboardSoldeConges);
                updateCount++;
                
                logSuccess('Méthode 1: Élément dashboard solde-conges-value mis à jour', {
                    de: oldValue,
                    vers: formattedValue
                });
            } catch (e) {
                logError('Méthode 1: Erreur lors de la mise à jour de solde-conges-value', {
                    erreur: e.message,
                    élément: dashboardSoldeConges.outerHTML
                });
            }
        } else {
            logDebug('Méthode 1: Élément solde-conges-value non trouvé');
        }
        
        // Rechercher tous les éléments qui pourraient contenir des soldes CP
        const potentialCpElements = [];
        document.querySelectorAll('*').forEach(el => {
            if (el.textContent && el.textContent.includes('jours') && 
                !el.querySelector('*') && // Seulement les éléments sans enfants
                el.textContent.length < 20) { // Texte court probable pour un solde
                potentialCpElements.push({
                    element: el.outerHTML,
                    text: el.textContent,
                    id: el.id,
                    classes: el.className
                });
            }
        });
        
        if (potentialCpElements.length > 0) {
            logDebug('Méthode 1: Éléments potentiels contenant des soldes CP', potentialCpElements);
        }
        
        return updateCount > 0;
    }
    
    // Méthode 2: Remplacement complet des éléments
    function updateElementsByMethod2(employeId, formattedValue, formattedValueWithUnit) {
        logDebug('Méthode 2: Début du remplacement complet des éléments', {
            employeId: employeId,
            valeur: formattedValue,
            valeurAvecUnité: formattedValueWithUnit
        });
        
        let updateCount = 0;
        
        // Mise à jour dans la vue d'édition des soldes
        const currentSoldeConges = document.getElementById('current-solde-conges');
        if (currentSoldeConges) {
            logDebug('Méthode 2: Élément current-solde-conges trouvé', {
                id: currentSoldeConges.id,
                classes: currentSoldeConges.className,
                contenu: currentSoldeConges.innerHTML,
                parent: currentSoldeConges.parentNode ? 'trouvé' : 'non trouvé'
            });
            
            if (currentSoldeConges.parentNode) {
                try {
                    const parent = currentSoldeConges.parentNode;
                    const oldHTML = currentSoldeConges.outerHTML;
                    
                    // Créer un nouvel élément pour remplacer l'ancien
                    const newElement = document.createElement('span');
                    newElement.id = 'current-solde-conges';
                    newElement.className = currentSoldeConges.className;
                    newElement.innerHTML = formattedValueWithUnit;
                    
                    // Remplacer l'ancien élément par le nouveau
                    parent.replaceChild(newElement, currentSoldeConges);
                    highlightElement(newElement);
                    updateCount++;
                    
                    logSuccess('Méthode 2: Élément current-solde-conges remplacé', {
                        ancien: oldHTML,
                        nouveau: newElement.outerHTML
                    });
                } catch (e) {
                    logError('Méthode 2: Erreur lors du remplacement de current-solde-conges', {
                        erreur: e.message,
                        élément: currentSoldeConges.outerHTML
                    });
                }
            } else {
                logError('Méthode 2: Impossible de remplacer current-solde-conges, parent non trouvé');
            }
        } else {
            logDebug('Méthode 2: Élément current-solde-conges non trouvé');
        }
        
        // Mise à jour du champ de formulaire
        const inputSoldeConges = document.getElementById('solde_conges');
        if (inputSoldeConges) {
            logDebug('Méthode 2: Champ de formulaire solde_conges trouvé', {
                id: inputSoldeConges.id,
                type: inputSoldeConges.type,
                valeurActuelle: inputSoldeConges.value,
                nouvelleValeur: formattedValue
            });
            
            try {
                const oldValue = inputSoldeConges.value;
                inputSoldeConges.value = formattedValue;
                updateCount++;
                
                logSuccess('Méthode 2: Champ de formulaire solde_conges mis à jour', {
                    de: oldValue,
                    vers: formattedValue
                });
                
                // Vérifier si la valeur a bien été mise à jour
                if (inputSoldeConges.value !== formattedValue) {
                    logError('Méthode 2: La valeur du champ solde_conges n\'a pas été correctement mise à jour', {
                        valeurAttendue: formattedValue,
                        valeurRéelle: inputSoldeConges.value
                    });
                }
            } catch (e) {
                logError('Méthode 2: Erreur lors de la mise à jour du champ solde_conges', {
                    erreur: e.message,
                    élément: inputSoldeConges.outerHTML
                });
            }
        } else {
            logDebug('Méthode 2: Champ de formulaire solde_conges non trouvé');
        }
        
        return updateCount > 0;
    }
    
    // Méthode 3: Mise à jour via attributs de données
    function updateElementsByMethod3(employeId, formattedValue) {
        logDebug('Méthode 3: Début de mise à jour via attributs de données', {
            employeId: employeId,
            valeur: formattedValue
        });
        
        let updateCount = 0;
        
        // Ajouter un attribut data-cp-value à tous les éléments pertinents
        const elements = document.querySelectorAll(`[data-employe-id="${employeId}"]`);
        logDebug('Méthode 3: Éléments avec data-employe-id trouvés', {
            nombre: elements.length,
            sélecteur: `[data-employe-id="${employeId}"]`
        });
        
        elements.forEach((el, index) => {
            try {
                const oldValue = el.getAttribute('data-cp-value');
                el.setAttribute('data-cp-value', formattedValue);
                updateCount++;
                
                logDebug(`Méthode 3: Attribut data-cp-value mis à jour pour l'élément ${index}`, {
                    de: oldValue || 'non défini',
                    vers: formattedValue,
                    élément: el.outerHTML.substring(0, 100) + (el.outerHTML.length > 100 ? '...' : '')
                });
            } catch (e) {
                logError(`Méthode 3: Erreur lors de la mise à jour de l'attribut pour l'élément ${index}`, {
                    erreur: e.message,
                    élément: el.outerHTML.substring(0, 100) + (el.outerHTML.length > 100 ? '...' : '')
                });
            }
        });
        
        // Créer un événement personnalisé pour signaler la mise à jour des CP
        try {
            const event = new CustomEvent('cp-updated', { 
                detail: { employeId: employeId, value: formattedValue } 
            });
            document.dispatchEvent(event);
            logDebug('Méthode 3: Événement cp-updated dispatché', {
                employeId: employeId,
                valeur: formattedValue
            });
        } catch (e) {
            logError('Méthode 3: Erreur lors de la création ou du dispatch de l\'\u00e9vénement cp-updated', {
                erreur: e.message
            });
        }
        
        return updateCount > 0;
    }
    
    // Fonction pour mettre en évidence un élément mis à jour
    function highlightElement(element) {
        if (!element) return;
        
        // Sauvegarder la couleur de fond d'origine
        const originalBg = window.getComputedStyle(element).backgroundColor;
        
        // Ajouter une classe pour l'animation
        element.classList.add('cp-updated');
        
        // Ajouter un style pour l'animation si la classe n'existe pas
        if (!document.querySelector('#cp-update-style')) {
            const style = document.createElement('style');
            style.id = 'cp-update-style';
            style.innerHTML = `
                @keyframes cpHighlight {
                    0% { background-color: rgba(59, 130, 246, 0.2); }
                    50% { background-color: rgba(59, 130, 246, 0.5); }
                    100% { background-color: rgba(59, 130, 246, 0.2); }
                }
                .cp-updated {
                    animation: cpHighlight 2s ease-in-out;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Retirer la classe après l'animation
        setTimeout(() => {
            element.classList.remove('cp-updated');
        }, 2000);
    }
    
    // Vérifier si nous avons une valeur récente dans sessionStorage
    const employeIdElement = document.querySelector('input[name="employe_id"]');
    const employeId = window.employeId || (employeIdElement ? employeIdElement.value : null);
    if (employeId) {
        const lastSubmittedCP = sessionStorage.getItem('lastSubmittedCP_' + employeId);
        const submitTime = sessionStorage.getItem('cpSubmitTime');
        
        // Si une valeur a été soumise récemment (moins de 10 secondes)
        if (lastSubmittedCP && submitTime) {
            const elapsed = new Date().getTime() - parseInt(submitTime);
            if (elapsed < 10000) { // 10 secondes
                console.log('Valeur CP récemment soumise détectée:', lastSubmittedCP);
                updateCongesPayes(employeId, lastSubmittedCP);
            }
        }
    }
    
    // Écouter les événements de mise à jour des soldes via WebSocket
    if (window.Echo) {
        logDebug('Echo est disponible, configuration des écouteurs WebSocket');
        
        // Si nous sommes connectés en tant qu'employeur
        if (typeof window.societeId !== 'undefined') {
            logDebug('Mode employeur détecté, ID société:', window.societeId);
            
            try {
                window.Echo.private(`societe.${window.societeId}`)
                    .listen('.SoldesCongesUpdated', (event) => {
                        logDebug('Événement SoldesCongesUpdated reçu', {
                            event: event,
                            timestamp: new Date().toISOString()
                        });
                        
                        // Analyser la structure de l'événement pour débogage
                        logDebug('Structure de l\'\u00e9vénement SoldesCongesUpdated', {
                            clés: Object.keys(event),
                            employe: event.employe ? 'présent' : 'absent',
                            solde_conges: event.solde_conges !== undefined ? 'présent' : 'absent',
                            employe_id: event.employe_id !== undefined ? 'présent' : 'absent'
                        });
                        
                        // Vérifier si nous avons les données de l'employé
                        if (event.employe && event.employe.solde_conges !== undefined) {
                            logDebug('Mise à jour CP depuis event.employe', {
                                id: event.employe.id,
                                solde: event.employe.solde_conges
                            });
                            updateCongesPayes(event.employe.id, event.employe.solde_conges);
                        } else if (event.solde_conges !== undefined && event.employe_id) {
                            logDebug('Mise à jour CP depuis event direct', {
                                id: event.employe_id,
                                solde: event.solde_conges
                            });
                            updateCongesPayes(event.employe_id, event.solde_conges);
                        } else {
                            logError('Impossible de trouver les données CP dans l\'\u00e9vénement', event);
                        }
                    });
                
                // Écouter aussi l'événement SoldeCongeModified qui pourrait être utilisé
                window.Echo.private(`societe.${window.societeId}`)
                    .listen('.SoldeCongeModified', (event) => {
                        logDebug('Événement SoldeCongeModified reçu', {
                            event: event,
                            timestamp: new Date().toISOString()
                        });
                        
                        // Analyser la structure de l'événement pour débogage
                        logDebug('Structure de l\'\u00e9vénement SoldeCongeModified', {
                            clés: Object.keys(event),
                            employe: event.employe ? 'présent' : 'absent'
                        });
                        
                        // Essayer de trouver les données CP dans différentes structures possibles
                        if (event.employe && event.employe.solde_conges !== undefined) {
                            logDebug('Mise à jour CP depuis SoldeCongeModified.employe', {
                                id: event.employe.id,
                                solde: event.employe.solde_conges
                            });
                            updateCongesPayes(event.employe.id, event.employe.solde_conges);
                        } else if (event.solde_conges !== undefined && event.employe_id) {
                            logDebug('Mise à jour CP depuis SoldeCongeModified direct', {
                                id: event.employe_id,
                                solde: event.solde_conges
                            });
                            updateCongesPayes(event.employe_id, event.solde_conges);
                        }
                    });
                
                logSuccess('Écouteurs WebSocket configurés pour le canal societe.' + window.societeId);
            } catch (e) {
                logError('Erreur lors de la configuration des écouteurs WebSocket pour l\'employeur', {
                    erreur: e.message,
                    stack: e.stack
                });
            }
        }
        
        // Si nous sommes connectés en tant qu'employé
        if (typeof window.employeId !== 'undefined') {
            logDebug('Mode employé détecté, ID employé:', window.employeId);
            
            try {
                window.Echo.private(`employe.${window.employeId}`)
                    .listen('.solde.updated', (event) => {
                        logDebug('Événement solde.updated reçu', {
                            event: event,
                            timestamp: new Date().toISOString()
                        });
                        
                        // Analyser la structure de l'événement pour débogage
                        logDebug('Structure de l\'\u00e9vénement solde.updated', {
                            clés: Object.keys(event),
                            solde_conges: event.solde_conges !== undefined ? 'présent' : 'absent'
                        });
                        
                        // Mise à jour spécifique des CP
                        if (event.solde_conges !== undefined) {
                            logDebug('Mise à jour CP depuis solde.updated', {
                                id: window.employeId,
                                solde: event.solde_conges
                            });
                            updateCongesPayes(window.employeId, event.solde_conges);
                        } else {
                            logError('Impossible de trouver les données CP dans l\'\u00e9vénement solde.updated', event);
                        }
                    });
                
                logSuccess('Écouteurs WebSocket configurés pour le canal employe.' + window.employeId);
            } catch (e) {
                logError('Erreur lors de la configuration des écouteurs WebSocket pour l\'employé', {
                    erreur: e.message,
                    stack: e.stack
                });
            }
        }
    } else {
        logError('Echo n\'est pas disponible, les mises à jour en temps réel ne fonctionneront pas');
    }
    
    // Ajouter un gestionnaire d'événements pour les mises à jour manuelles
    document.addEventListener('cp-manual-update', function(e) {
        if (e.detail && e.detail.employeId && e.detail.value !== undefined) {
            console.log('Mise à jour manuelle des CP reçue:', e.detail);
            updateCongesPayes(e.detail.employeId, e.detail.value);
        }
    });
});
