/**
 * Script de diagnostic pour les soldes de congés payés
 * Ce script permet de suivre les mises à jour des soldes CP et de vérifier leur persistance
 */
(function() {
    'use strict';

    // Configuration
    const DEBUG = true;
    const LOG_PREFIX = '[CP-DIAGNOSTIC]';
    
    // Fonction de logging
    function log(message, data = null) {
        if (DEBUG) {
            if (data) {
                console.log(`${LOG_PREFIX} ${message}`, data);
            } else {
                console.log(`${LOG_PREFIX} ${message}`);
            }
        }
    }

    // Fonction pour créer un panneau de diagnostic
    function createDiagnosticPanel() {
        const panel = document.createElement('div');
        panel.id = 'cp-diagnostic-panel';
        panel.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.8);
            color: #00ff00;
            border-radius: 5px;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        `;
        
        const header = document.createElement('div');
        header.innerHTML = '<strong>CP Diagnostic</strong>';
        header.style.cssText = 'border-bottom: 1px solid #00ff00; padding-bottom: 5px; margin-bottom: 5px;';
        panel.appendChild(header);
        
        const content = document.createElement('div');
        content.id = 'cp-diagnostic-content';
        panel.appendChild(content);
        
        const controls = document.createElement('div');
        controls.style.cssText = 'margin-top: 10px; display: flex; justify-content: space-between;';
        
        const clearButton = document.createElement('button');
        clearButton.textContent = 'Effacer logs';
        clearButton.style.cssText = 'background-color: #333; color: #fff; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;';
        clearButton.onclick = () => {
            document.getElementById('cp-diagnostic-content').innerHTML = '';
        };
        controls.appendChild(clearButton);
        
        const closeButton = document.createElement('button');
        closeButton.textContent = 'Fermer';
        closeButton.style.cssText = 'background-color: #333; color: #fff; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;';
        closeButton.onclick = () => {
            document.getElementById('cp-diagnostic-panel').style.display = 'none';
        };
        controls.appendChild(closeButton);
        
        panel.appendChild(controls);
        document.body.appendChild(panel);
        
        return panel;
    }
    
    // Fonction pour ajouter un message au panneau de diagnostic
    function addDiagnosticMessage(message, type = 'info') {
        const content = document.getElementById('cp-diagnostic-content');
        if (!content) return;
        
        const messageElement = document.createElement('div');
        messageElement.style.cssText = 'margin-bottom: 5px; border-left: 3px solid; padding-left: 5px;';
        
        switch (type) {
            case 'success':
                messageElement.style.borderColor = '#00ff00';
                break;
            case 'error':
                messageElement.style.borderColor = '#ff0000';
                break;
            case 'warning':
                messageElement.style.borderColor = '#ffff00';
                break;
            default:
                messageElement.style.borderColor = '#00ffff';
        }
        
        const timestamp = new Date().toLocaleTimeString();
        messageElement.innerHTML = `<span style="color: #888;">[${timestamp}]</span> ${message}`;
        content.appendChild(messageElement);
        
        // Auto-scroll to bottom
        content.scrollTop = content.scrollHeight;
    }
    
    // Fonction pour intercepter les requêtes fetch
    function monitorFetchRequests() {
        const originalFetch = window.fetch;
        
        window.fetch = async function(url, options = {}) {
            // Vérifier si c'est une requête de mise à jour des soldes CP
            const isSoldeUpdateRequest = typeof url === 'string' && url.includes('/conges/solde/') && options.method && (options.method.toUpperCase() === 'PUT' || options.method.toUpperCase() === 'POST');
            
            if (isSoldeUpdateRequest) {
                log('Interception d\'une requête de mise à jour des soldes CP', { url, method: options.method });
                addDiagnosticMessage(`Requête détectée: ${options.method} ${url}`, 'info');
                
                // Extraire les données du formulaire si disponibles
                let formData = {};
                if (options.body instanceof FormData) {
                    options.body.forEach((value, key) => {
                        formData[key] = value;
                    });
                    addDiagnosticMessage(`Données envoyées: solde_conges=${formData.solde_conges}, solde_rtt=${formData.solde_rtt}`, 'info');
                }
                
                try {
                    const response = await originalFetch.apply(this, arguments);
                    
                    // Cloner la réponse pour pouvoir la lire
                    const clone = response.clone();
                    
                    // Obtenir à la fois le texte brut et essayer de parser le JSON
                    clone.text().then(rawText => {
                        // Essayer de parser le JSON à partir du texte brut
                        try {
                            // Chercher le début et la fin du JSON dans la réponse
                            const jsonStart = rawText.indexOf('{');
                            const jsonEnd = rawText.lastIndexOf('}') + 1;
                            
                            if (jsonStart >= 0 && jsonEnd > jsonStart) {
                                // Extraire uniquement la partie JSON
                                const jsonPart = rawText.substring(jsonStart, jsonEnd);
                                const data = JSON.parse(jsonPart);
                                
                                log('Réponse de la requête de mise à jour des soldes CP', data);
                                
                                // Vérifier si du contenu supplémentaire est présent avant ou après le JSON
                                if (jsonStart > 0 || jsonEnd < rawText.length) {
                                    const extraContentBefore = jsonStart > 0 ? rawText.substring(0, jsonStart) : '';
                                    const extraContentAfter = jsonEnd < rawText.length ? rawText.substring(jsonEnd) : '';
                                    
                                    log('Contenu supplémentaire détecté dans la réponse JSON', {
                                        before: extraContentBefore,
                                        after: extraContentAfter
                                    });
                                    
                                    addDiagnosticMessage(`Attention: Contenu supplémentaire détecté dans la réponse JSON`, 'warning');
                                    if (extraContentBefore) {
                                        addDiagnosticMessage(`Avant JSON: ${extraContentBefore.substring(0, 50)}${extraContentBefore.length > 50 ? '...' : ''}`, 'warning');
                                    }
                                    if (extraContentAfter) {
                                        addDiagnosticMessage(`Après JSON: ${extraContentAfter.substring(0, 50)}${extraContentAfter.length > 50 ? '...' : ''}`, 'warning');
                                    }
                                }
                                
                                if (data.success) {
                                    addDiagnosticMessage(`Succès: ${data.message || 'Mise à jour réussie'}`, 'success');
                                    
                                    // Vérifier les valeurs mises à jour
                                    if (data.employe) {
                                        addDiagnosticMessage(`Nouvelles valeurs: solde_conges=${data.employe.solde_conges}, solde_rtt=${data.employe.solde_rtt}`, 'success');
                                        
                                        // Programmer une vérification après rechargement
                                        localStorage.setItem('cp_diagnostic_check_after_reload', JSON.stringify({
                                            employe_id: data.employe.id,
                                            expected_solde_conges: data.employe.solde_conges,
                                            expected_solde_rtt: data.employe.solde_rtt,
                                            timestamp: Date.now()
                                        }));
                                    }
                                } else {
                                    addDiagnosticMessage(`Erreur: ${data.message || 'Erreur inconnue'}`, 'error');
                                }
                            } else {
                                throw new Error('Impossible de trouver un objet JSON valide dans la réponse');
                            }
                        } catch (jsonError) {
                            log('Erreur lors du parsing JSON', jsonError);
                            log('Contenu brut de la réponse', rawText);
                            
                            // Afficher le début et la fin du contenu brut pour diagnostic
                            const maxLength = 200;
                            const start = rawText.substring(0, maxLength);
                            const end = rawText.length > maxLength ? rawText.substring(rawText.length - maxLength) : '';
                            
                            addDiagnosticMessage(`Erreur de parsing JSON: ${jsonError.message}`, 'error');
                            addDiagnosticMessage(`Début de la réponse: ${start}${rawText.length > maxLength ? '...' : ''}`, 'error');
                            if (rawText.length > maxLength * 2) {
                                addDiagnosticMessage(`Fin de la réponse: ...${end}`, 'error');
                            }
                        }
                    }).catch(err => {
                        log('Erreur lors de la récupération du texte de la réponse', err);
                        addDiagnosticMessage(`Erreur réseau: ${err.message}`, 'error');
                    });
                    
                    return response;
                } catch (error) {
                    log('Erreur lors de la requête fetch', error);
                    addDiagnosticMessage(`Erreur réseau: ${error.message}`, 'error');
                    throw error;
                }
            }
            
            // Requête normale, pas liée aux soldes CP
            return originalFetch.apply(this, arguments);
        };
    }
    
    // Fonction pour vérifier la persistance après rechargement
    function checkPersistenceAfterReload() {
        const checkData = localStorage.getItem('cp_diagnostic_check_after_reload');
        
        if (checkData) {
            try {
                const data = JSON.parse(checkData);
                const now = Date.now();
                
                // Ne vérifier que si les données ont moins de 60 secondes
                if (now - data.timestamp < 60000) {
                    log('Vérification de la persistance après rechargement', data);
                    addDiagnosticMessage('Vérification de la persistance des soldes...', 'info');
                    
                    // Trouver les éléments affichant les soldes actuels
                    const soldeCongesElements = document.querySelectorAll('.solde-conges, #solde_conges, [name="solde_conges"]');
                    const soldeRttElements = document.querySelectorAll('.solde-rtt, #solde_rtt, [name="solde_rtt"]');
                    
                    if (soldeCongesElements.length > 0) {
                        const currentSoldeConges = soldeCongesElements[0].value || soldeCongesElements[0].textContent;
                        const expectedSoldeConges = data.expected_solde_conges;
                        
                        if (currentSoldeConges == expectedSoldeConges) {
                            addDiagnosticMessage(`✅ Solde CP persisté correctement: ${currentSoldeConges}`, 'success');
                        } else {
                            addDiagnosticMessage(`❌ Problème de persistance! Attendu: ${expectedSoldeConges}, Actuel: ${currentSoldeConges}`, 'error');
                        }
                    }
                    
                    if (soldeRttElements.length > 0) {
                        const currentSoldeRtt = soldeRttElements[0].value || soldeRttElements[0].textContent;
                        const expectedSoldeRtt = data.expected_solde_rtt;
                        
                        if (currentSoldeRtt == expectedSoldeRtt) {
                            addDiagnosticMessage(`✅ Solde RTT persisté correctement: ${currentSoldeRtt}`, 'success');
                        } else {
                            addDiagnosticMessage(`❌ Problème de persistance! Attendu: ${expectedSoldeRtt}, Actuel: ${currentSoldeRtt}`, 'error');
                        }
                    }
                }
                
                // Nettoyer les données après vérification
                localStorage.removeItem('cp_diagnostic_check_after_reload');
            } catch (error) {
                log('Erreur lors de la vérification après rechargement', error);
            }
        }
    }
    
    // Fonction d'initialisation
    function init() {
        log('Initialisation du script de diagnostic CP');
        
        // Créer le panneau de diagnostic
        createDiagnosticPanel();
        addDiagnosticMessage('Script de diagnostic CP initialisé', 'info');
        
        // Intercepter les requêtes fetch
        monitorFetchRequests();
        
        // Vérifier la persistance après rechargement si nécessaire
        checkPersistenceAfterReload();
        
        // Ajouter un bouton pour ouvrir le panneau de diagnostic s'il est fermé
        const toggleButton = document.createElement('button');
        toggleButton.textContent = 'Diagnostic CP';
        toggleButton.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: #00ff00;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            z-index: 9998;
            display: none;
        `;
        toggleButton.onclick = () => {
            const panel = document.getElementById('cp-diagnostic-panel');
            if (panel) {
                panel.style.display = 'block';
                toggleButton.style.display = 'none';
            }
        };
        document.body.appendChild(toggleButton);
        
        // Écouter l'événement de fermeture du panneau
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('cp-diagnostic-panel');
            if (panel && panel.style.display === 'none') {
                toggleButton.style.display = 'block';
            }
        });
    }
    
    // Démarrer le script quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
