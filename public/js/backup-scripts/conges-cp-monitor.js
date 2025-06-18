/**
 * Script de surveillance des mises à jour des congés payés
 * Surveille les modifications des soldes CP dans le DOM, intercepte les requêtes AJAX
 * et écoute les événements WebSocket pour assurer la mise à jour en temps réel
 * sur toutes les pages du site (côté employeur et employé)
 */

// Créer un espace de noms pour éviter les conflits
window.CPMonitor = (function() {
    // Configuration
    const config = {
        debug: true,
        maxRetries: 5,
        retryDelay: 500,
        observerDelay: 100,
        ajaxInterceptDelay: 300,
        refreshInterval: 2000, // 2 secondes
        webSocketMonitoring: true,
        domMonitoring: true,
        ajaxMonitoring: true,
        broadcastChannel: true, // Utiliser BroadcastChannel pour synchroniser entre onglets
        storageSync: true,      // Utiliser localStorage pour synchroniser entre onglets
        globalSync: true        // Synchronisation globale entre toutes les pages
    };
    
    // Compteurs et état
    let updateAttempts = 0;
    let lastUpdateTime = 0;
    let isUpdating = false;
    let observers = [];
    let webSocketEvents = [];
    let ajaxRequests = [];
    
    // Canal de diffusion pour synchroniser entre les onglets/fenêtres
    let broadcastChannel = null;
    
    // Identifiant unique pour cette instance
    const instanceId = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    
    // Stockage des valeurs
    const cpValues = {
        lastKnownValues: {},
        pendingUpdates: {},
        updateAttempts: {}
    };
    
    // Fonction de journalisation avec style
    function logDebug(message, data) {
        if (config.debug) {
            console.log('%c[CP-MONITOR] ' + message, 'background: #8b5cf6; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
        }
    }
    
    // Fonction de journalisation d'erreur avec style
    function logError(message, data) {
        console.error('%c[CP-MONITOR ERROR] ' + message, 'background: #ef4444; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Fonction de journalisation de succès avec style
    function logSuccess(message, data) {
        console.log('%c[CP-MONITOR SUCCESS] ' + message, 'background: #10b981; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Fonction de journalisation d'avertissement avec style
    function logWarning(message, data) {
        console.warn('%c[CP-MONITOR WARNING] ' + message, 'background: #f59e0b; color: white; padding: 2px 5px; border-radius: 3px;', data || '');
    }
    
    // Fonction pour obtenir tous les éléments CP dans la page
    function getAllCpElements() {
        const elements = {
            // Éléments dans la vue de gestion des soldes (index)
            indexElements: document.querySelectorAll('[data-solde-type="conges"]'),
            
            // Éléments CP dans la vue employeur (valeurs numériques)
            employerViewElements: document.querySelectorAll('.cp'),
            
            // Éléments CP spécifiques par leur contenu
            cpValueElements: Array.from(document.querySelectorAll('div')).filter(el => {
                // Recherche des éléments contenant des valeurs CP (20.0, 25.0, etc.)
                const text = el.textContent.trim();
                return /^\d+\.\d+$/.test(text) && el.closest('.cp');
            }),
            
            // Élément dans la vue d'édition des soldes
            editElement: document.getElementById('current-solde-conges'),
            
            // Champ de formulaire
            inputElement: document.getElementById('solde_conges'),
            
            // Élément dans le tableau de bord
            dashboardElement: document.querySelector('.solde-conges-value')
        };
        
        // Log détaillé des éléments trouvés
        logDebug('Éléments CP trouvés', {
            index: elements.indexElements.length,
            employerView: elements.employerViewElements.length,
            cpValues: elements.cpValueElements.length,
            edit: elements.editElement ? 'Trouvé' : 'Non trouvé',
            input: elements.inputElement ? 'Trouvé' : 'Non trouvé',
            dashboard: elements.dashboardElement ? 'Trouvé' : 'Non trouvé'
        });
        
        // Log détaillé des éléments dans la vue employeur
        if (elements.employerViewElements.length > 0) {
            elements.employerViewElements.forEach((el, index) => {
                const parent = el.closest('[data-employe-id]');
                const employeId = parent ? parent.getAttribute('data-employe-id') : 'inconnu';
                const value = el.textContent.trim();
                logDebug(`Élément CP #${index} dans la vue employeur:`, {
                    employeId: employeId,
                    valeur: value,
                    element: el
                });
            });
        } else {
            logWarning('Aucun élément CP trouvé dans la vue employeur');
        }
        
        return elements;
    }
    
    // Fonction pour extraire l'ID de l'employé
    function getEmployeId() {
        // Essayer plusieurs méthodes pour obtenir l'ID de l'employé
        const methods = [
            // Depuis la variable globale
            () => window.employeId,
            
            // Depuis un champ caché dans le formulaire
            () => {
                const input = document.querySelector('input[name="employe_id"]');
                return input ? input.value : null;
            },
            
            // Depuis l'attribut data sur un élément
            () => {
                const element = document.querySelector('[data-employe-id]');
                return element ? element.getAttribute('data-employe-id') : null;
            },
            
            // Depuis l'URL
            () => {
                const match = window.location.pathname.match(/employes\/(\d+)/);
                return match ? match[1] : null;
            }
        ];
        
        // Essayer chaque méthode jusqu'à ce qu'une fonctionne
        for (const method of methods) {
            try {
                const id = method();
                if (id) {
                    logDebug('ID employé trouvé', id);
                    return id;
                }
            } catch (e) {
                logError('Erreur lors de la récupération de l\'ID employé', e);
            }
        }
        
        logWarning('Impossible de trouver l\'ID employé');
        return null;
    }
    
    // Fonction pour mettre à jour visuellement un élément avec animation
    function updateElementWithAnimation(element, value, withUnit = false) {
        if (!element) {
            logError('Élément non trouvé pour l\'animation');
            return false;
        }
        
        try {
            // Sauvegarder l'ancienne valeur
            const oldValue = element.textContent.trim();
            
            // Formater la nouvelle valeur
            const newValue = withUnit ? `${value} jours` : value;
            
            // Appliquer l'animation seulement si la valeur a changé
            if (oldValue !== newValue) {
                // Ajouter une classe pour l'animation
                element.classList.add('cp-updating');
                
                // Mettre à jour la valeur
                element.textContent = newValue;
                
                // Retirer la classe après l'animation
                setTimeout(() => {
                    element.classList.remove('cp-updating');
                }, 1500);
                
                logSuccess('Élément mis à jour avec animation', {
                    element: element,
                    oldValue: oldValue,
                    newValue: newValue
                });
                
                return true;
            }
            
            return false;
        } catch (e) {
            logError('Erreur lors de l\'animation', e);
            return false;
        }
    }
    
    // Fonction pour forcer la mise à jour des éléments CP
    function forceUpdateCpElements(employeId, cpValue) {
        if (!employeId || cpValue === undefined) {
            logWarning('Tentative de mise à jour avec des données incomplètes', { employeId, cpValue });
            return false;
        }
        
        logDebug(`Tentative de mise à jour forcée des éléments CP pour l'employé ${employeId} avec la valeur ${cpValue}`);
        
        // Convertir en string pour la comparaison et en nombre pour le formatage
        employeId = employeId.toString();
        const numericValue = typeof cpValue === 'string' ? parseFloat(cpValue) : cpValue;
        const formattedValue = numericValue.toFixed(1);
        const formattedValueWithUnit = `${formattedValue} jours`;
        
        let updateCount = 0;
        
        // 1. Mettre à jour les éléments dans la vue index (avec data-solde-type="conges")
        const indexElements = document.querySelectorAll(`[data-employe-id="${employeId}"][data-solde-type="conges"]`);
        indexElements.forEach(element => {
            updateElementWithAnimation(element, formattedValueWithUnit);
            updateCount++;
            logDebug(`Élément index mis à jour pour l'employé ${employeId}:`, element);
        });
        
        // 2. Mettre à jour les éléments dans la vue employeur (avec classe .cp)
        const employerElements = document.querySelectorAll(`.cp[data-employe-id="${employeId}"], .cp-value[data-employe-id="${employeId}"]`);
        employerElements.forEach(element => {
            updateElementWithAnimation(element, formattedValue);
            updateCount++;
            logDebug(`Élément employeur mis à jour pour l'employé ${employeId}:`, element);
        });
        
        // 3. Mettre à jour les éléments dans la vue édition (inputs)
        const editInputs = document.querySelectorAll(`
            input[name="solde_conges"][data-employe-id="${employeId}"], 
            input[name="cp"][data-employe-id="${employeId}"],
            input[name="soldeConges"][data-employe-id="${employeId}"],
            input[name="solde_conges"][value="${employeId}"],
            input[name="cp"][value="${employeId}"],
            input[name="soldeConges"][value="${employeId}"]
        `);
        editInputs.forEach(input => {
            if (input.value !== formattedValue) {
                input.value = formattedValue;
                updateCount++;
                logDebug(`Input édition mis à jour pour l'employé ${employeId}:`, input);
            }
        });
        
        // 4. Mettre à jour les éléments dans le tableau de bord
        const dashboardElements = document.querySelectorAll(`
            [data-dashboard-employe-id="${employeId}"][data-dashboard-solde-type="conges"],
            [data-dashboard="conges"][data-employe-id="${employeId}"],
            [data-type="conges"][data-employe-id="${employeId}"]
        `);
        dashboardElements.forEach(element => {
            updateElementWithAnimation(element, formattedValueWithUnit);
            updateCount++;
            logDebug(`Élément tableau de bord mis à jour pour l'employé ${employeId}:`, element);
        });
        
        // 5. Recherche plus générale pour les éléments dans la vue employeur
        // Trouver les cartes d'employés
        const employeeCards = document.querySelectorAll('.card, .employee-card, .employe-card, .user-card, .solde-card');
        employeeCards.forEach(card => {
            // Vérifier si cette carte correspond à l'employé
            const cardEmployeId = card.getAttribute('data-employe-id') || 
                                card.getAttribute('data-employee-id') || 
                                card.getAttribute('data-id') ||
                                card.getAttribute('data-user-id');
            
            if (cardEmployeId === employeId) {
                // Trouver les éléments CP dans cette carte
                const cpElements = card.querySelectorAll('.cp, .solde-cp, .solde-conges, .cp-value');
                cpElements.forEach(element => {
                    updateElementWithAnimation(element, formattedValue);
                    updateCount++;
                    logDebug(`Élément CP dans carte employé mis à jour:`, element);
                });
                
                // Si aucun élément spécifique n'est trouvé, chercher des éléments avec des valeurs numériques
                if (cpElements.length === 0) {
                    const allElements = card.querySelectorAll('div, span, p');
                    allElements.forEach(el => {
                        const text = el.textContent.trim();
                        if (/^\d+(\.\d+)?$/.test(text)) {
                            updateElementWithAnimation(el, formattedValue);
                            updateCount++;
                            logDebug(`Élément numérique dans carte employé mis à jour:`, el);
                            // Ajouter une classe pour identifier cet élément comme un solde CP
                            if (!el.classList.contains('cp-value')) {
                                el.classList.add('cp-value');
                                el.setAttribute('data-employe-id', employeId);
                            }
                        }
                    });
                }
            }
        });
        
        // 6. Recherche par attributs spécifiques aux soldes CP
        const soldeElements = document.querySelectorAll(`
            [data-solde="cp"][data-employe-id="${employeId}"],
            [data-solde="conges"][data-employe-id="${employeId}"],
            [data-type="cp"][data-employe-id="${employeId}"],
            [data-type="conges"][data-employe-id="${employeId}"]
        `);
        soldeElements.forEach(element => {
            updateElementWithAnimation(element, formattedValueWithUnit);
            updateCount++;
            logDebug(`Élément solde spécifique mis à jour:`, element);
        });
        
        // 7. Recherche dans les tableaux (tr/td)
        const tableRows = document.querySelectorAll(`tr[data-employe-id="${employeId}"], tr[data-id="${employeId}"]`);
        tableRows.forEach(row => {
            // Chercher les cellules contenant des soldes CP
            const cpCells = row.querySelectorAll('td.cp, td.solde-cp, td.solde-conges');
            cpCells.forEach(cell => {
                updateElementWithAnimation(cell, formattedValue);
                updateCount++;
                logDebug(`Cellule de tableau mise à jour:`, cell);
            });
            
            // Si aucune cellule spécifique n'est trouvée, chercher des cellules avec des valeurs numériques
            if (cpCells.length === 0) {
                const allCells = row.querySelectorAll('td');
                allCells.forEach(cell => {
                    const text = cell.textContent.trim();
                    if (/^\d+(\.\d+)?$/.test(text)) {
                        updateElementWithAnimation(cell, formattedValue);
                        updateCount++;
                        logDebug(`Cellule numérique mise à jour:`, cell);
                        // Ajouter une classe pour identifier cette cellule comme un solde CP
                        if (!cell.classList.contains('cp-value')) {
                            cell.classList.add('cp-value');
                            cell.setAttribute('data-employe-id', employeId);
                        }
                    }
                });
            }
        });
        
        // 8. Recherche dans les éléments avec l'ID de l'employé
        const idElements = document.querySelectorAll(`#employe-${employeId}, #employee-${employeId}, #user-${employeId}`);
        idElements.forEach(element => {
            const cpElements = element.querySelectorAll('.cp, .solde-cp, .solde-conges, .cp-value');
            cpElements.forEach(cpElement => {
                updateElementWithAnimation(cpElement, formattedValue);
                updateCount++;
                logDebug(`Élément CP par ID employé mis à jour:`, cpElement);
            });
        });
        
        // Mettre à jour la valeur dans le stockage
        cpValues.lastKnownValues[employeId] = numericValue;
        
        logSuccess(`Mise à jour forcée des éléments CP pour l'employé ${employeId} terminée (${updateCount} éléments mis à jour)`);
        return updateCount > 0;
    }
    
    // Fonction pour vérifier si une mise à jour est en attente
    function checkPendingUpdates() {
        if (isUpdating) {
            logDebug('Vérification des mises à jour en attente ignorée (déjà en cours)');
            return;
        }
        
        const pendingIds = Object.keys(cpValues.pendingUpdates);
        if (pendingIds.length === 0) {
            return;
        }
        
        logDebug(`${pendingIds.length} mise(s) à jour en attente trouvée(s)`, pendingIds);
        
        isUpdating = true;
        
        // Traiter chaque mise à jour en attente
        pendingIds.forEach(employeId => {
            const cpValue = cpValues.pendingUpdates[employeId];
            const attempts = cpValues.updateAttempts[employeId] || 0;
            
            if (attempts >= config.maxRetries) {
                logError(`Abandon de la mise à jour pour l'employé ${employeId} après ${attempts} tentatives`);
                delete cpValues.pendingUpdates[employeId];
                delete cpValues.updateAttempts[employeId];
                return;
            }
            
            // Incrémenter le compteur de tentatives
            cpValues.updateAttempts[employeId] = attempts + 1;
            
            // Tenter la mise à jour
            if (forceUpdateCpElements(employeId, cpValue)) {
                delete cpValues.pendingUpdates[employeId];
                delete cpValues.updateAttempts[employeId];
            }
        });
        
        isUpdating = false;
    }
    
    // Fonction pour intercepter les requêtes AJAX
    function setupAjaxInterceptor() {
        if (!config.ajaxMonitoring) {
            return;
        }
        
        logDebug('Configuration de l\'intercepteur AJAX');
        
        // Sauvegarder la fonction originale
        const originalXHROpen = XMLHttpRequest.prototype.open;
        const originalXHRSend = XMLHttpRequest.prototype.send;
        
        // Remplacer la fonction open pour intercepter les URL
        XMLHttpRequest.prototype.open = function(method, url) {
            this._cpMonitorMethod = method;
            this._cpMonitorUrl = url;
            
            // Vérifier si c'est une requête liée aux congés (avec plus de mots-clés)
            if (url.includes('conges') || 
                url.includes('solde') || 
                url.includes('employe') || 
                url.includes('cp') || 
                url.includes('update') || 
                url.includes('edit')) {
                
                logDebug('Requête AJAX potentiellement liée aux congés détectée', { method, url });
                this._cpMonitorIsCongesRequest = true;
                
                // Identifier le type de requête plus précisément
                if (url.includes('solde') && url.includes('update')) {
                    this._cpMonitorRequestType = 'update_solde';
                    logDebug('Requête de mise à jour de solde détectée', { url });
                } else if (url.includes('conges') && url.includes('edit')) {
                    this._cpMonitorRequestType = 'edit_conges';
                    logDebug('Requête d\'\u00e9dition de congés détectée', { url });
                }
            }
            
            return originalXHROpen.apply(this, arguments);
        };
        
        // Remplacer la fonction send pour intercepter les données
        XMLHttpRequest.prototype.send = function(data) {
            if (this._cpMonitorIsCongesRequest) {
                logDebug('Envoi de requête AJAX liée aux congés', {
                    method: this._cpMonitorMethod,
                    url: this._cpMonitorUrl,
                    type: this._cpMonitorRequestType || 'inconnu',
                    data: data
                });
                
                // Essayer d'extraire les données du formulaire si c'est un FormData
                if (data instanceof FormData) {
                    logDebug('Données de formulaire détectées');
                    try {
                        // Afficher les clés du FormData
                        const formDataKeys = [];
                        for (let pair of data.entries()) {
                            formDataKeys.push({
                                key: pair[0],
                                value: pair[1]
                            });
                            
                            // Vérifier si c'est une donnée de solde CP
                            if (pair[0] === 'solde_conges' || pair[0] === 'soldeConges' || pair[0] === 'cp') {
                                logDebug('Valeur de solde CP trouvée dans les données du formulaire', {
                                    key: pair[0],
                                    value: pair[1]
                                });
                            }
                            
                            // Vérifier si c'est un ID employé
                            if (pair[0] === 'employe_id' || pair[0] === 'employeId') {
                                logDebug('ID employé trouvé dans les données du formulaire', {
                                    key: pair[0],
                                    value: pair[1]
                                });
                            }
                        }
                        logDebug('Clés du FormData', formDataKeys);
                    } catch (e) {
                        logError('Erreur lors de l\'analyse du FormData', e);
                    }
                }
                
                // Enregistrer la requête
                const requestId = Date.now();
                ajaxRequests.push({
                    id: requestId,
                    method: this._cpMonitorMethod,
                    url: this._cpMonitorUrl,
                    type: this._cpMonitorRequestType || 'inconnu',
                    data: data,
                    timestamp: new Date().toISOString()
                });
                
                // Intercepter la réponse
                this.addEventListener('load', function() {
                    try {
                        // Vérifier si la réponse est du JSON
                        let response;
                        try {
                            response = JSON.parse(this.responseText);
                            logDebug('Réponse AJAX JSON reçue', response);
                        } catch (e) {
                            // Ce n'est pas du JSON, vérifier si c'est du HTML contenant des données CP
                            logDebug('Réponse non-JSON reçue', {
                                status: this.status,
                                contentType: this.getResponseHeader('Content-Type'),
                                responseLength: this.responseText.length
                            });
                            
                            // Vérifier si la réponse HTML contient des éléments CP
                            if (this.responseText.includes('solde-conges') || 
                                this.responseText.includes('cp-value') || 
                                this.responseText.includes('class="cp"')) {
                                
                                logDebug('Réponse HTML contenant potentiellement des données CP');
                                
                                // Forcer une vérification des éléments après un court délai
                                setTimeout(() => {
                                    logDebug('Vérification forcée des éléments CP après réponse HTML');
                                    getAllCpElements(); // Juste pour le log
                                    checkPendingUpdates();
                                }, config.ajaxInterceptDelay);
                            }
                            return;
                        }
                        
                        // Vérifier si la réponse contient des données CP
                        const cpData = extractCPData(response);
                        if (cpData) {
                            logSuccess('Données CP trouvées dans la réponse AJAX', cpData);
                            
                            // Planifier une mise à jour après un court délai
                            setTimeout(() => {
                                logDebug('Application de la mise à jour CP depuis la réponse AJAX', cpData);
                                if (typeof window.updateCongesPayes === 'function') {
                                    window.updateCongesPayes(cpData.employeId, cpData.soldeConges);
                                } else {
                                    // Ajouter à la liste des mises à jour en attente
                                    cpValues.pendingUpdates[cpData.employeId] = cpData.soldeConges;
                                    // Forcer une vérification immédiate
                                    setTimeout(checkPendingUpdates, 50);
                                }
                            }, config.ajaxInterceptDelay);
                        } else {
                            // Aucune donnée CP trouvée, mais vérifions si c'est une réponse de succès
                            if (response.success === true || response.status === 'success') {
                                logDebug('Réponse de succès détectée, recherche d\'ID employé dans l\'URL');
                                
                                // Essayer d'extraire l'ID employé de l'URL
                                const urlMatch = this._cpMonitorUrl.match(/employes?\/([0-9]+)/);
                                if (urlMatch && urlMatch[1]) {
                                    const employeId = urlMatch[1];
                                    logDebug('ID employé extrait de l\'URL:', employeId);
                                    
                                    // Forcer une vérification des éléments après un court délai
                                    setTimeout(() => {
                                        logDebug('Vérification forcée des éléments CP après réponse de succès');
                                        getAllCpElements(); // Juste pour le log
                                    }, config.ajaxInterceptDelay);
                                }
                            }
                        }
                    } catch (e) {
                        logError('Erreur lors du traitement de la réponse AJAX', e);
                    }
                });
                
                // Intercepter les erreurs
                this.addEventListener('error', function(e) {
                    logError('Erreur lors de la requête AJAX', {
                        url: this._cpMonitorUrl,
                        error: e
                    });
                });
            }
            
            return originalXHRSend.apply(this, arguments);
        };
        
        logSuccess('Intercepteur AJAX configuré');
    }
    
    // Fonction pour surveiller les événements WebSocket
    function monitorWebSocketEvents() {
        if (!config.webSocketMonitoring) {
            return;
        }
        
        logDebug('Configuration de la surveillance des événements WebSocket');
        
        if (!window.Echo) {
            logError('Echo n\'est pas disponible, impossible de surveiller les événements WebSocket');
            return;
        }
        
        // Variable pour suivre l'état d'authentification des canaux privés
        let privateChannelsAvailable = true;
        
        // Intercepter les erreurs d'authentification au niveau le plus bas possible
        // Intercepter directement la méthode authorize de Laravel Echo
        if (window.axios && !window.axios._originalRequest) {
            logDebug('Interception des requêtes axios pour gérer les erreurs d\'authentification');
            
            // Sauvegarder la fonction originale
            window.axios._originalRequest = window.axios.request;
            
            // Remplacer par notre version qui gère les erreurs
            window.axios.request = function(config) {
                // Vérifier si c'est une requête d'authentification WebSocket
                if (config.url && config.url.includes('/broadcasting/auth')) {
                    logDebug('Requête d\'authentification WebSocket détectée', config);
                    
                    return window.axios._originalRequest(config).catch(error => {
                        if (error.response && error.response.status === 403) {
                            logWarning('Erreur 403 interceptée pour l\'authentification WebSocket', error);
                            privateChannelsAvailable = false;
                            
                            // Configurer un intervalle de vérification plus fréquent pour compenser
                            if (config.refreshInterval > 1000) {
                                config.refreshInterval = 1000; // Réduire à 1 seconde
                                logDebug('Intervalle de rafraîchissement réduit à 1 seconde pour compenser l\'absence de canaux privés');
                            }
                            
                            // Activer la surveillance AJAX et DOM plus agressive
                            config.ajaxMonitoring = true;
                            config.domMonitoring = true;
                            
                            // Augmenter la fréquence des vérifications
                            startPeriodicChecks();
                            
                            // Retourner une réponse factice pour éviter les erreurs
                            return Promise.resolve({
                                data: { auth: {} },
                                status: 200,
                                statusText: 'OK (simulé par CPMonitor)',
                                headers: {},
                                config: config
                            });
                        }
                        
                        // Pour les autres erreurs, propager normalement
                        return Promise.reject(error);
                    });
                }
                
                // Pour les autres requêtes, comportement normal
                return window.axios._originalRequest(config);
            };
            
            logSuccess('Interception des requêtes axios configurée');
        }
        
        // Configurer un gestionnaire global d'erreurs pour les WebSockets
        if (window.Echo && window.Echo.connector && window.Echo.connector.socket) {
            logDebug('Configuration du gestionnaire d\'erreurs WebSocket');
            
            window.Echo.connector.socket.on('connect_error', (error) => {
                logWarning('Erreur de connexion WebSocket', error);
                privateChannelsAvailable = false;
                
                // Activer les alternatives
                config.ajaxMonitoring = true;
                config.domMonitoring = true;
                config.refreshInterval = 1000;
                
                logDebug('Surveillance alternative activée suite à une erreur de connexion WebSocket');
            });
            
            logSuccess('Gestionnaire d\'erreurs WebSocket configuré');
        }
        
        // Sauvegarder la fonction originale d'écoute pour l'intercepter
        if (window.Echo.private && !window.Echo.private._originalListen) {
            logDebug('Interception de la méthode listen de Echo.private');
            
            // Sauvegarder la fonction originale
            window.Echo.private._originalListen = window.Echo.private.listen;
            
            // Remplacer par notre version qui intercepte les événements
            window.Echo.private.listen = function(event, callback) {
                logDebug('Echo.private.listen intercepté pour l\'événement', event);
                
                // Si les canaux privés ne sont pas disponibles, logger et continuer
                if (!privateChannelsAvailable) {
                    logWarning(`Canal privé non disponible pour l'événement ${event}. Les mises à jour seront détectées par d'autres moyens.`);
                }
                
                // Créer un wrapper pour le callback
                const wrappedCallback = function(data) {
                    logDebug(`Événement WebSocket reçu: ${event}`, data);
                    
                    // Enregistrer l'événement pour analyse
                    webSocketEvents.push({
                        event: event,
                        data: data,
                        timestamp: new Date().toISOString(),
                        channel: this.channel ? this.channel.name : 'inconnu'
                    });
                    
                    // Vérifier si c'est un événement lié aux congés payés
                    if (event.includes('Solde') || event.includes('solde')) {
                        logDebug('Événement lié aux soldes détecté', {
                            event: event,
                            données: data
                        });
                        
                        // Vérifier si les données contiennent des informations sur les CP
                        let cpData = extractCPData(data);
                        if (cpData) {
                            logSuccess('Données CP détectées dans l\'événement WebSocket', cpData);
                            
                            // Tenter de mettre à jour les CP
                            if (typeof window.updateCongesPayes === 'function') {
                                window.updateCongesPayes(cpData.employeId, cpData.soldeConges);
                            } else {
                                // Ajouter à la liste des mises à jour en attente
                                cpValues.pendingUpdates[cpData.employeId] = cpData.soldeConges;
                            }
                        }
                    }
                    
                    // Appeler le callback original
                    return callback.apply(this, arguments);
                };
                
                // Appeler la fonction originale avec notre wrapper
                return window.Echo.private._originalListen.call(this, event, wrappedCallback);
            };
            
            logSuccess('Interception de Echo.private.listen réussie');
        }
        
        // Faire de même pour les canaux publics si nécessaire
        if (window.Echo.channel && !window.Echo.channel._originalListen) {
            logDebug('Interception de la méthode listen de Echo.channel');
            
            // Sauvegarder la fonction originale
            window.Echo.channel._originalListen = window.Echo.channel.listen;
            
            // Remplacer par notre version qui intercepte les événements
            window.Echo.channel.listen = function(event, callback) {
                logDebug('Echo.channel.listen intercepté pour l\'événement', event);
                
                // Créer un wrapper pour le callback
                const wrappedCallback = function(data) {
                    logDebug(`Événement WebSocket public reçu: ${event}`, data);
                    
                    // Enregistrer l'événement pour analyse
                    webSocketEvents.push({
                        event: event,
                        data: data,
                        timestamp: new Date().toISOString(),
                        channel: this.channel ? this.channel.name : 'inconnu',
                        type: 'public'
                    });
                    
                    // Vérifier si c'est un événement lié aux congés payés
                    if (event.includes('Solde') || event.includes('solde')) {
                        logDebug('Événement public lié aux soldes détecté', {
                            event: event,
                            données: data
                        });
                        
                        // Vérifier si les données contiennent des informations sur les CP
                        let cpData = extractCPData(data);
                        if (cpData) {
                            logSuccess('Données CP détectées dans l\'événement WebSocket public', cpData);
                            
                            // Tenter de mettre à jour les CP
                            if (typeof window.updateCongesPayes === 'function') {
                                window.updateCongesPayes(cpData.employeId, cpData.soldeConges);
                            } else {
                                // Ajouter à la liste des mises à jour en attente
                                cpValues.pendingUpdates[cpData.employeId] = cpData.soldeConges;
                            }
                        }
                    }
                    
                    // Appeler le callback original
                    return callback.apply(this, arguments);
                };
                
                // Appeler la fonction originale avec notre wrapper
                return window.Echo.channel._originalListen.call(this, event, wrappedCallback);
            };
            
            logSuccess('Interception de Echo.channel.listen réussie');
        }
    }
    
    // Fonction pour extraire les données CP d'un objet
    function extractCPData(data) {
        if (!data) {
            logWarning('Données vides passées à extractCPData');
            return null;
        }
        
        logDebug('Tentative d\'extraction des données CP', data);
        
        try {
            // Cas 1: Données directement dans l'objet
            if (data.employe_id && (data.solde_conges !== undefined || data.soldeConges !== undefined || data.cp !== undefined)) {
                const solde = data.solde_conges || data.soldeConges || data.cp;
                logDebug('Cas 1: Données directement dans l\'objet', { employeId: data.employe_id, solde });
                return {
                    employeId: data.employe_id.toString(),
                    soldeConges: solde
                };
            }
            
            // Cas 2: Données dans un sous-objet 'employe'
            if (data.employe && data.employe.id && 
                (data.employe.solde_conges !== undefined || data.employe.soldeConges !== undefined || data.employe.cp !== undefined)) {
                const solde = data.employe.solde_conges || data.employe.soldeConges || data.employe.cp;
                logDebug('Cas 2: Données dans un sous-objet employe', { employeId: data.employe.id, solde });
                return {
                    employeId: data.employe.id.toString(),
                    soldeConges: solde
                };
            }
            
            // Cas 3: Données dans un sous-objet 'data'
            if (data.data) {
                logDebug('Cas 3: Recherche dans le sous-objet data');
                const result = extractCPData(data.data);
                if (result) return result;
            }
            
            // Cas 4: Données dans un sous-objet 'solde'
            if (data.solde) {
                if (data.solde.employe_id) {
                    const solde = data.solde.valeur || data.solde.solde_conges || data.solde.soldeConges || data.solde.cp;
                    logDebug('Cas 4a: Données dans un sous-objet solde avec employe_id', { employeId: data.solde.employe_id, solde });
                    return {
                        employeId: data.solde.employe_id.toString(),
                        soldeConges: solde
                    };
                } else if (data.id || data.employe_id) {
                    // Le solde est dans data.solde mais l'ID est au niveau supérieur
                    const employeId = data.id || data.employe_id;
                    const solde = data.solde.valeur || data.solde.solde_conges || data.solde.soldeConges || data.solde;
                    logDebug('Cas 4b: ID au niveau supérieur et solde dans sous-objet', { employeId, solde });
                    return {
                        employeId: employeId.toString(),
                        soldeConges: typeof solde === 'object' ? solde.cp || solde.valeur : solde
                    };
                }
            }
            
            // Cas 5: Format spécifique à la vue employeur
            if (data.cp !== undefined && (data.id || data.employeId)) {
                logDebug('Cas 5: Format spécifique vue employeur', { employeId: data.id || data.employeId, solde: data.cp });
                return {
                    employeId: (data.id || data.employeId).toString(),
                    soldeConges: data.cp
                };
            }
            
            // Cas 6: Données dans un tableau d'employés ou de soldes
            if (Array.isArray(data)) {
                logDebug('Cas 6: Recherche dans un tableau', { length: data.length });
                for (let i = 0; i < data.length; i++) {
                    const result = extractCPData(data[i]);
                    if (result) {
                        logDebug('Données CP trouvées dans le tableau à l\'index', i);
                        return result;
                    }
                }
            }
            
            // Cas 7: Parcourir les propriétés de premier niveau pour trouver un objet contenant les données
            for (const key in data) {
                if (typeof data[key] === 'object' && data[key] !== null) {
                    logDebug(`Cas 7: Recherche récursive dans la propriété ${key}`);
                    const extracted = extractCPData(data[key]);
                    if (extracted) {
                        return extracted;
                    }
                }
            }
            
            // Cas 8: Vérifier si l'objet contient des propriétés qui ressemblent à des soldes CP
            const cpRelatedKeys = ['cp', 'conges', 'solde', 'soldeConges', 'solde_conges'];
            for (const key of cpRelatedKeys) {
                if (data[key] !== undefined && typeof data[key] !== 'object') {
                    // Trouver un ID d'employé potentiel
                    let employeId = null;
                    for (const idKey of ['id', 'employe_id', 'employeId', 'userId', 'user_id']) {
                        if (data[idKey]) {
                            employeId = data[idKey];
                            break;
                        }
                    }
                    
                    if (employeId) {
                        logDebug(`Cas 8: Clé ${key} trouvée avec ID employé`, { employeId, solde: data[key] });
                        return {
                            employeId: employeId.toString(),
                            soldeConges: data[key]
                        };
                    }
                }
            }
            
            logDebug('Aucune donnée CP trouvée dans l\'objet');
        } catch (e) {
            logError('Erreur lors de l\'extraction des données CP', e);
        }
        
        return null;
    }
    
    // Fonction spécifique pour détecter et mettre à jour les éléments dans la vue employeur
    function detectEmployerViewElements() {
        logDebug('Détection des éléments dans la vue employeur');
        
        // Détecter si nous sommes dans la vue employeur
        const isEmployerView = window.location.pathname.includes('/conges/solde') || 
                              document.querySelector('.solde-conges') || 
                              document.querySelector('.cp');
        
        if (!isEmployerView) {
            logDebug('Pas dans la vue employeur');
            return false;
        }
        
        logDebug('Vue employeur détectée');
        
        // Rechercher tous les éléments avec la classe CP
        const cpElements = document.querySelectorAll('.cp');
        logDebug(`${cpElements.length} éléments CP trouvés dans la vue employeur`);
        
        if (cpElements.length === 0) {
            // Essayer une approche alternative
            const possibleCpElements = Array.from(document.querySelectorAll('div')).filter(el => {
                // Recherche des éléments contenant des valeurs CP (20.0, 25.0, etc.)
                const text = el.textContent.trim();
                return /^\d+\.\d+$/.test(text);
            });
            
            logDebug(`${possibleCpElements.length} éléments CP potentiels trouvés par contenu`);
            
            // Ajouter une classe pour les identifier
            possibleCpElements.forEach((el, index) => {
                if (!el.classList.contains('cp-value')) {
                    el.classList.add('cp-value');
                    logDebug(`Classe cp-value ajoutée à l'élément #${index}:`, el.textContent);
                }
            });
        }
        
        // Ajouter des attributs data-employe-id aux cartes d'employés si nécessaires
        const employeCards = document.querySelectorAll('.card');
        employeCards.forEach(card => {
            if (!card.hasAttribute('data-employe-id')) {
                // Essayer de trouver l'ID employé dans les éléments enfants
                const idElement = card.querySelector('[data-employe-id]');
                if (idElement) {
                    const employeId = idElement.getAttribute('data-employe-id');
                    card.setAttribute('data-employe-id', employeId);
                    logDebug(`Attribut data-employe-id=${employeId} ajouté à la carte`, card);
                } else {
                    // Essayer de trouver par le nom de l'employé
                    const nameElement = card.querySelector('h3, h4, .employe-name');
                    if (nameElement) {
                        logDebug('Carte employé sans ID mais avec nom:', nameElement.textContent);
                    }
                }
            }
        });
        
        return true;
    }
    
    // Fonction pour configurer la synchronisation globale entre les pages
    function setupGlobalSync() {
        if (!config.globalSync) {
            return;
        }
        
        logDebug('Configuration de la synchronisation globale');
        
        // 1. Utiliser BroadcastChannel pour synchroniser entre les onglets (API moderne)
        if (config.broadcastChannel && window.BroadcastChannel) {
            try {
                broadcastChannel = new BroadcastChannel('cp-monitor-sync');
                
                broadcastChannel.onmessage = function(event) {
                    const data = event.data;
                    
                    if (data.type === 'cp-update' && data.source !== instanceId) {
                        logDebug('Mise à jour CP reçue via BroadcastChannel', data);
                        
                        if (data.employeId && data.soldeConges !== undefined) {
                            // Ajouter à la liste des mises à jour en attente
                            cpValues.pendingUpdates[data.employeId] = data.soldeConges;
                            
                            // Déclencher une vérification immédiate
                            setTimeout(checkPendingUpdates, 50);
                        }
                    }
                };
                
                logSuccess('BroadcastChannel configuré pour la synchronisation');
            } catch (e) {
                logError('Erreur lors de la configuration de BroadcastChannel', e);
            }
        }
        
        // 2. Utiliser localStorage comme fallback pour les navigateurs plus anciens
        if (config.storageSync && window.localStorage) {
            try {
                // Écouter les événements de stockage (déclenchés dans d'autres onglets)
                window.addEventListener('storage', function(event) {
                    // Traiter les événements de notre propre système de synchronisation
                    if (event.key === 'cp-monitor-update') {
                        try {
                            const data = JSON.parse(event.newValue);
                            
                            if (data.source !== instanceId) {
                                logDebug('Mise à jour CP reçue via localStorage (cp-monitor-update)', data);
                                
                                if (data.employeId && data.soldeConges !== undefined) {
                                    // Ajouter à la liste des mises à jour en attente
                                    cpValues.pendingUpdates[data.employeId] = data.soldeConges;
                                    
                                    // Déclencher une vérification immédiate
                                    setTimeout(checkPendingUpdates, 50);
                                }
                            }
                        } catch (e) {
                            logError('Erreur lors du traitement de l\'\u00e9v\u00e9nement de stockage cp-monitor-update', e);
                        }
                    }
                    
                    // Traiter les événements du script cp-direct-fix-v2.js
                    if (event.key === 'cp_last_update') {
                        try {
                            const data = JSON.parse(event.newValue);
                            
                            logDebug('Mise à jour CP reçue via localStorage (cp_last_update)', data);
                            
                            if (data.employeId && data.solde_conges !== undefined) {
                                // Ajouter à la liste des mises à jour en attente
                                cpValues.pendingUpdates[data.employeId] = data.solde_conges;
                                
                                // Mettre à jour les valeurs connues
                                cpValues.lastKnownValues[data.employeId] = {
                                    soldeConges: data.solde_conges,
                                    soldeRtt: data.solde_rtt,
                                    soldeExceptionnels: data.solde_conges_exceptionnels,
                                    timestamp: data.timestamp || Date.now()
                                };
                                
                                // Déclencher une vérification immédiate
                                setTimeout(checkPendingUpdates, 50);
                            }
                        } catch (e) {
                            logError('Erreur lors du traitement de l\'\u00e9v\u00e9nement de stockage cp_last_update', e);
                        }
                    }
                });
                
                logSuccess('Synchronisation via localStorage configurée');
            } catch (e) {
                logError('Erreur lors de la configuration de la synchronisation via localStorage', e);
            }
        }
    }
    
    // Fonction pour diffuser une mise à jour CP à toutes les pages
    function broadcastCPUpdate(employeId, soldeConges) {
        if (!config.globalSync) {
            return;
        }
        
        const updateData = {
            type: 'cp-update',
            source: instanceId,
            employeId: employeId,
            soldeConges: soldeConges,
            timestamp: Date.now()
        };
        
        logDebug('Diffusion de la mise à jour CP à toutes les pages', updateData);
        
        // 1. Utiliser BroadcastChannel
        if (broadcastChannel) {
            try {
                broadcastChannel.postMessage(updateData);
                logDebug('Mise à jour envoyée via BroadcastChannel');
            } catch (e) {
                logError('Erreur lors de l\'envoi via BroadcastChannel', e);
            }
        }
        
        // 2. Utiliser localStorage comme fallback
        if (config.storageSync && window.localStorage) {
            try {
                // Stocker la mise à jour dans localStorage pour la synchronisation entre onglets
                localStorage.setItem('cp-monitor-update', JSON.stringify(updateData));
                logDebug('Mise à jour envoyée via localStorage');
            } catch (e) {
                logError('Erreur lors de l\'envoi via localStorage', e);
            }
        }
    }
    
    // Fonction pour intercepter les formulaires de modification de solde CP
    function setupFormInterceptor() {
        logDebug('Configuration de l\'intercepteur de formulaires');
        
        // Écouter les événements de formulaire pour détecter les soumissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Vérifier si c'est un formulaire lié aux congés
            if (form.action && (form.action.includes('solde') || 
                               form.action.includes('conges') || 
                               form.action.includes('employe'))) {
                               
                logDebug('Soumission de formulaire lié aux congés détectée', {
                    action: form.action,
                    method: form.method
                });
                
                // Rechercher les champs de solde CP et d'ID employé
                const soldeInput = form.querySelector('input[name="solde_conges"], input[name="cp"], input[name="soldeConges"]');
                const employeIdInput = form.querySelector('input[name="employe_id"], input[name="employeId"]');
                
                if (soldeInput && employeIdInput) {
                    const solde = parseFloat(soldeInput.value);
                    const employeId = employeIdInput.value;
                    
                    logDebug('Données de formulaire de congés détectées', { employeId, solde });
                    
                    // Planifier une vérification après la soumission
                    setTimeout(() => {
                        logDebug('Diffusion de la mise à jour après soumission de formulaire');
                        
                        // Diffuser la mise à jour à toutes les pages
                        broadcastCPUpdate(employeId, solde);
                        
                        // Mettre à jour localement
                        cpValues.pendingUpdates[employeId] = solde;
                        checkPendingUpdates();
                        
                        // Forcer une mise à jour immédiate
                        forceUpdateCpElements(employeId, solde);
                    }, 500);
                }
            }
        });
        
        // Écouter les clics sur les boutons de sauvegarde/modification
        document.addEventListener('click', function(e) {
            // Vérifier si c'est un bouton de sauvegarde/modification
            if (e.target && (e.target.type === 'submit' || 
                            e.target.tagName === 'BUTTON' || 
                            e.target.classList.contains('btn-save') || 
                            e.target.classList.contains('btn-primary'))) {
                
                const button = e.target;
                const buttonText = button.textContent.toLowerCase();
                
                // Vérifier si le bouton est lié à la sauvegarde des congés
                if (buttonText.includes('enregistrer') || 
                    buttonText.includes('sauvegarder') || 
                    buttonText.includes('modifier') || 
                    buttonText.includes('valider')) {
                    
                    logDebug('Clic sur bouton de sauvegarde détecté', button);
                    
                    // Rechercher le formulaire parent
                    const form = button.closest('form');
                    if (form) {
                        logDebug('Formulaire parent trouvé', form);
                    } else {
                        // Si pas de formulaire, vérifier s'il y a des champs de solde CP à proximité
                        const container = button.closest('.card, .modal, .form-container');
                        if (container) {
                            const soldeInput = container.querySelector('input[name="solde_conges"], input[name="cp"], input[name="soldeConges"]');
                            const employeIdInput = container.querySelector('input[name="employe_id"], input[name="employeId"]');
                            
                            if (soldeInput && employeIdInput) {
                                const solde = parseFloat(soldeInput.value);
                                const employeId = employeIdInput.value;
                                
                                logDebug('Données de congés détectées après clic sur bouton', { employeId, solde });
                                
                                // Planifier une vérification après le clic
                                setTimeout(() => {
                                    logDebug('Diffusion de la mise à jour après clic sur bouton');
                                    
                                    // Diffuser la mise à jour à toutes les pages
                                    broadcastCPUpdate(employeId, solde);
                                    
                                    // Mettre à jour localement
                                    cpValues.pendingUpdates[employeId] = solde;
                                    checkPendingUpdates();
                                    
                                    // Forcer une mise à jour immédiate
                                    forceUpdateCpElements(employeId, solde);
                                }, 500);
                            }
                        }
                    }
                }
            }
        });
        
        logSuccess('Intercepteur de formulaires configuré');
    }
    
    // Fonction pour initialiser le moniteur
    function initialize() {
        logDebug('Initialisation du moniteur de congés payés');
        
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
            
            /* Style pour les éléments CP dans la vue employeur */
            .cp-value {
                position: relative;
            }
            
            .cp-value::after {
                content: 'CP';
                position: absolute;
                top: -10px;
                right: -10px;
                font-size: 8px;
                background-color: rgba(139, 92, 246, 0.2);
                padding: 2px 4px;
                border-radius: 4px;
                opacity: 0.7;
            }
        `;
        document.head.appendChild(style);
        
        // Détecter les éléments dans la vue employeur
        detectEmployerViewElements();
        
        // Vérifier si des erreurs WebSocket sont déjà présentes
        const hasWebSocketErrors = checkForWebSocketErrors();
        if (hasWebSocketErrors) {
            logWarning('Erreurs WebSocket détectées, activation du mode de secours');
            config.webSocketMonitoring = false;
            config.ajaxMonitoring = true;
            config.domMonitoring = true;
            config.refreshInterval = 1000; // Intervalle plus court pour compenser
        }
        
        // Configurer l'intercepteur AJAX
        if (config.ajaxMonitoring) {
            setupAjaxInterceptor();
        }
        
        // Configurer la surveillance WebSocket
        if (config.webSocketMonitoring) {
            monitorWebSocketEvents();
        }
        
        // Configurer la synchronisation globale
        if (config.globalSync) {
            setupGlobalSync();
        }
        
        // Configurer l'intercepteur de formulaires
        setupFormInterceptor();
        
        // Démarrer les vérifications périodiques
        startPeriodicChecks();
        
        // Exposer la fonction de mise à jour globale
        window.updateCongesPayes = function(employeId, cpValue) {
            logDebug(`Fonction globale updateCongesPayes appelée pour l'employé ${employeId} avec la valeur ${cpValue}`);
            broadcastCPUpdate(employeId, cpValue);
            forceUpdateCpElements(employeId, cpValue);
        };
        
        // Ajouter des gestionnaires d'erreurs globaux
        setupGlobalErrorHandlers();
        
        logSuccess('Moniteur de congés payés initialisé avec succès');
    }
    
    // Fonction pour vérifier si des erreurs WebSocket sont déjà présentes
    function checkForWebSocketErrors() {
        logDebug('Vérification des erreurs WebSocket existantes');
        
        // Vérifier si des erreurs sont visibles dans la console (simulation)
        let hasErrors = false;
        
        // Vérifier si des erreurs 403 sont déjà présentes dans le DOM
        const errorMessages = [];
        const scripts = document.querySelectorAll('script');
        scripts.forEach(script => {
            if (script.textContent && script.textContent.includes('403') && 
                (script.textContent.includes('broadcasting/auth') || script.textContent.includes('WebSocket'))) {
                errorMessages.push('Erreur 403 détectée dans un script');
                hasErrors = true;
            }
        });
        
        // Vérifier si Echo est disponible mais a des erreurs
        if (window.Echo && window.Echo.connector) {
            try {
                // Tenter d'accéder à des propriétés qui pourraient générer des erreurs si Echo a des problèmes
                const socketId = window.Echo.socketId();
                if (!socketId) {
                    errorMessages.push('Echo est disponible mais socketId est null ou undefined');
                    hasErrors = true;
                }
            } catch (e) {
                errorMessages.push(`Erreur lors de l'accès à Echo: ${e.message}`);
                hasErrors = true;
            }
        }
        
        // Vérifier si des erreurs sont visibles dans la console (simulation)
        if (window.performance && window.performance.getEntries) {
            const entries = window.performance.getEntries();
            const authRequests = entries.filter(entry => 
                entry.name && entry.name.includes('/broadcasting/auth') && entry.initiatorType === 'xmlhttprequest'
            );
            
            if (authRequests.length > 0) {
                // Vérifier si certaines requêtes ont échoué (durée très courte peut indiquer une erreur)
                const failedRequests = authRequests.filter(req => req.duration < 50);
                if (failedRequests.length > 0) {
                    errorMessages.push(`${failedRequests.length} requêtes d'authentification WebSocket semblent avoir échoué`);
                    hasErrors = true;
                }
            }
        }
        
        if (errorMessages.length > 0) {
            logWarning('Erreurs WebSocket détectées:', errorMessages);
        }
        
        return hasErrors;
    }
    
    // Fonction pour configurer des gestionnaires d'erreurs globaux
    function setupGlobalErrorHandlers() {
        logDebug('Configuration des gestionnaires d\'erreurs globaux');
        
        // Intercepter les erreurs non gérées
        window.addEventListener('error', function(event) {
            // Vérifier si l'erreur est liée aux WebSockets ou à l'authentification
            if (event.message && (
                event.message.includes('WebSocket') || 
                event.message.includes('Echo') || 
                event.message.includes('403') ||
                event.message.includes('auth') ||
                event.message.includes('broadcasting')
            )) {
                logWarning('Erreur WebSocket non gérée détectée:', event.message);
                
                // Activer les méthodes alternatives
                config.webSocketMonitoring = false;
                config.ajaxMonitoring = true;
                config.domMonitoring = true;
                config.refreshInterval = Math.min(config.refreshInterval, 1000);
                
                // Forcer une vérification immédiate
                setTimeout(checkPendingUpdates, 100);
            }
        });
        
        // Intercepter les rejets de promesse non gérés
        window.addEventListener('unhandledrejection', function(event) {
            const reason = event.reason ? event.reason.toString() : '';
            
            // Vérifier si le rejet est lié aux WebSockets ou à l'authentification
            if (reason && (
                reason.includes('WebSocket') || 
                reason.includes('Echo') || 
                reason.includes('403') ||
                reason.includes('auth') ||
                reason.includes('broadcasting')
            )) {
                logWarning('Rejet de promesse WebSocket non géré détecté:', reason);
                
                // Activer les méthodes alternatives
                config.webSocketMonitoring = false;
                config.ajaxMonitoring = true;
                config.domMonitoring = true;
                config.refreshInterval = Math.min(config.refreshInterval, 1000);
                
                // Forcer une vérification immédiate
                setTimeout(checkPendingUpdates, 100);
            }
        });
        
        logSuccess('Gestionnaires d\'erreurs globaux configurés');
    }
    
    // Fonction pour démarrer les vérifications périodiques
    function startPeriodicChecks() {
        logDebug('Démarrage des vérifications périodiques');
        
        // Vérifier périodiquement les mises à jour en attente
        setInterval(checkPendingUpdates, config.refreshInterval);
        
        // Vérifier périodiquement les éléments dans la vue employeur (en cas de changement de DOM)
        setInterval(detectEmployerViewElements, config.refreshInterval * 2);
        
        logSuccess('Vérifications périodiques configurées');
    }
    
    // Exposer l'API publique
    return {
        init: initialize,
        update: function(employeId, cpValue) {
            return forceUpdateCpElements(employeId, cpValue);
        },
        getLastKnownValues: function() {
            return { ...cpValues.lastKnownValues };
        },
        getConfig: function() {
            return { ...config };
        },
        setConfig: function(newConfig) {
            Object.assign(config, newConfig);
            return { ...config };
        },
        getWebSocketEvents: function() {
            return [...webSocketEvents];
        },
        getAjaxRequests: function() {
            return [...ajaxRequests];
        },
        debug: {
            logDebug,
            logError,
            logSuccess,
            logWarning
        }
    };
})();

// Ajouter la fonction updateCongesPayes pour compatibilité avec le code WebSocket
window.updateCongesPayes = function(employeId, soldeConges) {
    console.log(`%c[CP-MONITOR] Fonction updateCongesPayes appelée pour l'employé ${employeId} avec la valeur ${soldeConges}`, 'background: #10b981; color: white; padding: 2px 5px; border-radius: 3px;');
    return window.CPMonitor.update(employeId, soldeConges);
};

// Initialiser automatiquement le moniteur quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    window.CPMonitor.init();
});