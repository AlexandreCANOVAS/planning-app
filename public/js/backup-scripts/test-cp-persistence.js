/**
 * test-cp-persistence.js
 * Script de test pour vérifier la persistance des soldes CP
 */

(function() {
    // Configuration
    const config = {
        debug: true,
        testInterval: 5000 // 5 secondes entre chaque test
    };

    // Fonction de journalisation
    function log(message, data = null, level = 'info') {
        if (!config.debug) return;
        
        const prefix = '[TEST-CP-PERSISTENCE]';
        const styles = {
            info: 'color: blue;',
            success: 'color: green; font-weight: bold;',
            error: 'color: red; font-weight: bold;',
            warning: 'color: orange;'
        };
        
        console.log(`%c${prefix} ${message}`, styles[level] || styles.info, data);
    }

    // Fonction pour créer l'interface de test
    function createTestInterface() {
        // Vérifier si l'interface existe déjà
        if (document.getElementById('cp-test-panel')) {
            return;
        }
        
        // Créer le panneau de test
        const panel = document.createElement('div');
        panel.id = 'cp-test-panel';
        panel.style.position = 'fixed';
        panel.style.bottom = '20px';
        panel.style.right = '20px';
        panel.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
        panel.style.border = '1px solid #ccc';
        panel.style.borderRadius = '5px';
        panel.style.padding = '15px';
        panel.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.2)';
        panel.style.zIndex = '9999';
        panel.style.maxWidth = '400px';
        panel.style.fontSize = '14px';
        
        // Titre du panneau
        const title = document.createElement('h3');
        title.textContent = 'Test de persistance des soldes CP';
        title.style.margin = '0 0 10px 0';
        title.style.fontSize = '16px';
        title.style.fontWeight = 'bold';
        panel.appendChild(title);
        
        // Bouton pour fermer le panneau
        const closeButton = document.createElement('button');
        closeButton.textContent = '×';
        closeButton.style.position = 'absolute';
        closeButton.style.top = '5px';
        closeButton.style.right = '10px';
        closeButton.style.background = 'none';
        closeButton.style.border = 'none';
        closeButton.style.fontSize = '20px';
        closeButton.style.cursor = 'pointer';
        closeButton.onclick = () => panel.remove();
        panel.appendChild(closeButton);
        
        // Conteneur pour les résultats
        const resultsContainer = document.createElement('div');
        resultsContainer.id = 'cp-test-results';
        resultsContainer.style.maxHeight = '300px';
        resultsContainer.style.overflowY = 'auto';
        resultsContainer.style.marginBottom = '10px';
        panel.appendChild(resultsContainer);
        
        // Boutons de test
        const buttonContainer = document.createElement('div');
        buttonContainer.style.display = 'flex';
        buttonContainer.style.gap = '10px';
        
        // Bouton pour tester la persistance
        const testButton = document.createElement('button');
        testButton.textContent = 'Tester persistance';
        testButton.className = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700';
        testButton.onclick = runPersistenceTest;
        buttonContainer.appendChild(testButton);
        
        // Bouton pour recharger la page
        const reloadButton = document.createElement('button');
        reloadButton.textContent = 'Recharger la page';
        reloadButton.className = 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50';
        reloadButton.onclick = () => window.location.reload();
        buttonContainer.appendChild(reloadButton);
        
        panel.appendChild(buttonContainer);
        
        // Ajouter le panneau à la page
        document.body.appendChild(panel);
    }

    // Fonction pour ajouter un message au panneau de résultats
    function addTestResult(message, status = 'info') {
        const resultsContainer = document.getElementById('cp-test-results');
        if (!resultsContainer) return;
        
        const resultItem = document.createElement('div');
        resultItem.className = `test-result ${status}`;
        resultItem.style.padding = '5px';
        resultItem.style.marginBottom = '5px';
        resultItem.style.borderLeft = `3px solid ${status === 'success' ? 'green' : status === 'error' ? 'red' : status === 'warning' ? 'orange' : 'blue'}`;
        resultItem.style.paddingLeft = '10px';
        
        const timestamp = new Date().toLocaleTimeString();
        resultItem.innerHTML = `<span style="color: #666; font-size: 12px;">[${timestamp}]</span> ${message}`;
        
        resultsContainer.appendChild(resultItem);
        resultsContainer.scrollTop = resultsContainer.scrollHeight;
    }

    // Fonction pour récupérer les soldes CP actuels
    function getCurrentCpValues() {
        const values = {};
        
        // Récupérer l'ID de l'employé
        let employeeId = null;
        if (window.employeId) {
            employeeId = window.employeId;
        } else {
            // Essayer de récupérer l'ID depuis l'URL
            const urlParts = window.location.pathname.split('/');
            const potentialId = urlParts[urlParts.length - 1];
            if (!isNaN(potentialId)) {
                employeeId = potentialId;
            }
        }
        
        if (!employeeId) {
            log('Impossible de déterminer l\'ID de l\'employé', null, 'error');
            addTestResult('❌ Impossible de déterminer l\'ID de l\'employé', 'error');
            return null;
        }
        
        values.employeeId = employeeId;
        
        // Récupérer les valeurs affichées
        const cpDisplayElements = [
            document.querySelector('#current-solde-conges'),
            document.querySelector('[data-employe-id="' + employeeId + '"][data-solde-type="conges"]'),
            document.querySelector('.solde-conges-value')
        ];
        
        let cpDisplayValue = null;
        for (const el of cpDisplayElements) {
            if (el) {
                const text = el.textContent.trim();
                const match = text.match(/(\d+[.,]?\d*)/);
                if (match) {
                    cpDisplayValue = parseFloat(match[1].replace(',', '.'));
                    break;
                }
            }
        }
        
        values.cpDisplayValue = cpDisplayValue;
        
        // Récupérer la valeur du champ de formulaire
        const cpInput = document.querySelector('input[name="solde_conges"]');
        values.cpInputValue = cpInput ? parseFloat(cpInput.value) : null;
        
        return values;
    }

    // Fonction pour exécuter le test de persistance
    function runPersistenceTest() {
        // Récupérer les valeurs actuelles
        const beforeValues = getCurrentCpValues();
        if (!beforeValues) return;
        
        addTestResult(`🔍 Test de persistance démarré pour l'employé ${beforeValues.employeeId}`, 'info');
        addTestResult(`📊 Valeur actuelle affichée: ${beforeValues.cpDisplayValue}`, 'info');
        addTestResult(`📝 Valeur actuelle du formulaire: ${beforeValues.cpInputValue}`, 'info');
        
        // Modifier la valeur du CP (ajouter 0.5)
        const newValue = (beforeValues.cpInputValue || 0) + 0.5;
        addTestResult(`✏️ Modification de la valeur à ${newValue}`, 'info');
        
        // Trouver le formulaire et le soumettre
        const form = document.querySelector('#solde-form');
        if (!form) {
            addTestResult('❌ Formulaire non trouvé', 'error');
            return;
        }
        
        // Mettre à jour la valeur du champ
        const cpInput = form.querySelector('input[name="solde_conges"]');
        if (!cpInput) {
            addTestResult('❌ Champ solde_conges non trouvé', 'error');
            return;
        }
        
        // Sauvegarder l'ancienne valeur
        const oldValue = cpInput.value;
        
        // Mettre à jour avec la nouvelle valeur
        cpInput.value = newValue;
        addTestResult(`✅ Champ mis à jour avec la nouvelle valeur: ${newValue}`, 'success');
        
        // Soumettre le formulaire
        addTestResult('📤 Soumission du formulaire...', 'info');
        
        // Trouver le bouton de soumission
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            // Simuler un clic sur le bouton
            submitButton.click();
            
            // Vérifier après un délai si la valeur a été mise à jour
            setTimeout(() => {
                const afterValues = getCurrentCpValues();
                
                if (afterValues && afterValues.cpDisplayValue === newValue) {
                    addTestResult(`✅ Mise à jour réussie! Nouvelle valeur affichée: ${afterValues.cpDisplayValue}`, 'success');
                    
                    // Planifier un test après rechargement
                    addTestResult('🔄 Planification d\'un test après rechargement dans 5 secondes...', 'info');
                    
                    localStorage.setItem('cp_test_expected_value', newValue);
                    localStorage.setItem('cp_test_timestamp', Date.now());
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, config.testInterval);
                } else {
                    addTestResult(`❌ Échec de la mise à jour visuelle. Valeur affichée: ${afterValues ? afterValues.cpDisplayValue : 'inconnue'}`, 'error');
                    
                    // Restaurer l'ancienne valeur
                    cpInput.value = oldValue;
                }
            }, 2000);
        } else {
            addTestResult('❌ Bouton de soumission non trouvé', 'error');
        }
    }

    // Fonction pour vérifier si un test est en cours après rechargement
    function checkPendingTest() {
        const expectedValue = localStorage.getItem('cp_test_expected_value');
        const timestamp = localStorage.getItem('cp_test_timestamp');
        
        if (expectedValue && timestamp) {
            // Vérifier si le test est récent (moins de 30 secondes)
            const now = Date.now();
            const testAge = now - parseInt(timestamp);
            
            if (testAge < 30000) { // 30 secondes
                createTestInterface();
                
                // Récupérer les valeurs actuelles
                const currentValues = getCurrentCpValues();
                
                if (currentValues) {
                    const expectedValueFloat = parseFloat(expectedValue);
                    
                    addTestResult('🔄 Page rechargée après test de persistance', 'info');
                    addTestResult(`🎯 Valeur attendue: ${expectedValueFloat}`, 'info');
                    addTestResult(`📊 Valeur actuelle: ${currentValues.cpDisplayValue}`, 'info');
                    
                    if (Math.abs(currentValues.cpDisplayValue - expectedValueFloat) < 0.01) {
                        addTestResult('✅ TEST RÉUSSI! La valeur a bien persisté après rechargement', 'success');
                    } else {
                        addTestResult('❌ TEST ÉCHOUÉ! La valeur n\'a pas persisté après rechargement', 'error');
                    }
                }
                
                // Nettoyer le localStorage
                localStorage.removeItem('cp_test_expected_value');
                localStorage.removeItem('cp_test_timestamp');
            }
        }
    }

    // Fonction d'initialisation
    function initialize() {
        log('Initialisation du script de test de persistance CP');
        
        // Créer l'interface de test
        createTestInterface();
        
        // Vérifier si un test est en cours après rechargement
        checkPendingTest();
    }

    // Exécuter l'initialisation quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
