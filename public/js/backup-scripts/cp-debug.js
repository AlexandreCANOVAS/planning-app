/**
 * Script de diagnostic et correction pour les soldes CP
 * Ce script analyse et corrige les problèmes de mise à jour des soldes CP
 */

(function() {
    // Configuration
    const config = {
        debug: true,
        fixSelectors: true,
        fixFormatting: true,
        fixEventHandling: true
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
        
        console.log(`%c[CP-DEBUG] ${message}`, styles[type], data || '');
    }

    /**
     * Fonction pour analyser les sélecteurs actuels
     */
    function analyzeSelectors() {
        log('Analyse des sélecteurs pour les éléments CP...', null, 'info');
        
        // Vérifier les sélecteurs pour les soldes CP
        const selectors = [
            '[data-employe-id][data-solde-type="conges"]',
            '.cp[data-employe-id], .cp-value[data-employe-id]',
            'input[name="solde_conges"][data-employe-id]',
            'input[name="cp"][data-employe-id]',
            'input[name="soldeConges"][data-employe-id]',
            '[data-dashboard-employe-id][data-dashboard-solde-type="conges"]',
            '[data-dashboard="conges"][data-employe-id]',
            '[data-type="conges"][data-employe-id]',
            '[data-solde="cp"][data-employe-id]',
            '[data-solde="conges"][data-employe-id]',
            '[data-type="cp"][data-employe-id]',
            'tr[data-employe-id] td.cp, tr[data-employe-id] td.solde-cp, tr[data-employe-id] td.solde-conges'
        ];
        
        // Compter les éléments trouvés pour chaque sélecteur
        const results = {};
        selectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            results[selector] = elements.length;
            
            if (elements.length > 0) {
                log(`Sélecteur "${selector}" : ${elements.length} élément(s) trouvé(s)`, elements, 'success');
                
                // Analyser le contenu des éléments
                elements.forEach((el, index) => {
                    const content = el.tagName.toLowerCase() === 'input' ? el.value : el.textContent.trim();
                    const employeId = el.getAttribute('data-employe-id');
                    log(`  Élément #${index+1}: contenu="${content}", employeId=${employeId}`, el);
                });
            } else {
                log(`Sélecteur "${selector}" : aucun élément trouvé`, null, 'warning');
            }
        });
        
        return results;
    }

    /**
     * Fonction pour corriger les sélecteurs manquants
     */
    function fixMissingSelectors() {
        if (!config.fixSelectors) return;
        
        log('Correction des sélecteurs manquants...', null, 'info');
        
        // 1. Rechercher les éléments qui pourraient être des soldes CP mais sans attributs
        const potentialCpElements = document.querySelectorAll('.solde, .solde-value, .conges, .cp');
        
        potentialCpElements.forEach(element => {
            // Vérifier si c'est probablement un élément CP
            const text = element.textContent.trim();
            if (/^\d+(\.\d+)?\s*(jour|jours)?$/.test(text)) {
                // Chercher l'ID de l'employé dans les éléments parents
                let employeId = null;
                let parent = element.parentElement;
                
                // Remonter jusqu'à 5 niveaux pour trouver un ID d'employé
                for (let i = 0; i < 5 && parent && !employeId; i++) {
                    employeId = parent.getAttribute('data-employe-id') || 
                                parent.getAttribute('data-employee-id') || 
                                parent.getAttribute('data-id');
                    
                    if (!employeId) {
                        // Chercher dans les attributs data-* personnalisés
                        for (const attr of parent.attributes) {
                            if (attr.name.startsWith('data-') && /\d+/.test(attr.value)) {
                                employeId = attr.value;
                                break;
                            }
                        }
                    }
                    
                    parent = parent.parentElement;
                }
                
                if (employeId) {
                    // Ajouter les attributs nécessaires
                    if (!element.hasAttribute('data-employe-id')) {
                        element.setAttribute('data-employe-id', employeId);
                    }
                    
                    if (!element.hasAttribute('data-solde-type')) {
                        element.setAttribute('data-solde-type', 'conges');
                    }
                    
                    if (!element.classList.contains('cp-value')) {
                        element.classList.add('cp-value');
                    }
                    
                    log(`Élément corrigé avec employeId=${employeId}`, element, 'success');
                }
            }
        });
        
        // 2. Corriger les tableaux qui contiennent des soldes CP
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            const headers = Array.from(table.querySelectorAll('th, thead td'));
            
            // Trouver l'index de la colonne CP
            let cpColumnIndex = -1;
            headers.forEach((header, index) => {
                const text = header.textContent.trim().toLowerCase();
                if (text.includes('cp') || text.includes('congés payés') || text.includes('conges payes')) {
                    cpColumnIndex = index;
                }
            });
            
            if (cpColumnIndex >= 0) {
                // Parcourir les lignes et marquer les cellules CP
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const employeId = row.getAttribute('data-employe-id') || 
                                     row.getAttribute('data-employee-id') || 
                                     row.getAttribute('data-id');
                    
                    if (employeId) {
                        const cells = row.querySelectorAll('td');
                        if (cells.length > cpColumnIndex) {
                            const cpCell = cells[cpColumnIndex];
                            
                            // Ajouter les attributs nécessaires
                            if (!cpCell.hasAttribute('data-employe-id')) {
                                cpCell.setAttribute('data-employe-id', employeId);
                            }
                            
                            if (!cpCell.hasAttribute('data-solde-type')) {
                                cpCell.setAttribute('data-solde-type', 'conges');
                            }
                            
                            if (!cpCell.classList.contains('cp-value')) {
                                cpCell.classList.add('cp-value');
                            }
                            
                            log(`Cellule de tableau corrigée avec employeId=${employeId}`, cpCell, 'success');
                        }
                    }
                });
            }
        });
    }

    /**
     * Fonction pour corriger le formatage des valeurs CP
     */
    function fixCpFormatting() {
        if (!config.fixFormatting) return;
        
        log('Correction du formatage des valeurs CP...', null, 'info');
        
        // Trouver tous les éléments CP
        const cpElements = document.querySelectorAll('.cp-value, [data-solde-type="conges"]');
        
        cpElements.forEach(element => {
            if (element.tagName.toLowerCase() !== 'input') {
                const text = element.textContent.trim();
                const match = text.match(/^(\d+(?:\.\d+)?)(?:\s*jours?)?$/);
                
                if (match) {
                    const value = parseFloat(match[1]);
                    const formattedValue = value.toFixed(1) + ' jours';
                    
                    if (text !== formattedValue) {
                        element.textContent = formattedValue;
                        log(`Formatage corrigé: "${text}" -> "${formattedValue}"`, element, 'success');
                    }
                }
            }
        });
    }

    /**
     * Fonction pour corriger la gestion des événements
     */
    function fixEventHandling() {
        if (!config.fixEventHandling) return;
        
        log('Correction de la gestion des événements...', null, 'info');
        
        // 1. S'assurer que la fonction forceUpdateCpElements est correctement définie
        if (typeof window.forceUpdateCpElements !== 'function' && typeof window.CPMonitor?.update === 'function') {
            window.forceUpdateCpElements = function(employeId, cpValue) {
                log(`forceUpdateCpElements appelée pour l'employé ${employeId} avec la valeur ${cpValue}`, null, 'info');
                return window.CPMonitor.update(employeId, cpValue);
            };
            log('Fonction forceUpdateCpElements définie', null, 'success');
        }
        
        // 2. Intercepter les soumissions de formulaire pour les soldes CP
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Vérifier si c'est un formulaire de soldes
            const cpInputs = form.querySelectorAll('input[name="solde_conges"], input[name="cp"], input[name="soldeConges"]');
            
            if (cpInputs.length > 0 && !form.getAttribute('data-cp-debug-fixed')) {
                form.setAttribute('data-cp-debug-fixed', 'true');
                
                form.addEventListener('submit', function(event) {
                    // Ne pas empêcher la soumission normale du formulaire
                    
                    // Récupérer les valeurs
                    cpInputs.forEach(input => {
                        const employeId = input.getAttribute('data-employe-id') || 
                                         input.getAttribute('data-employee-id') || 
                                         input.getAttribute('value');
                        
                        if (employeId) {
                            const cpValue = parseFloat(input.value);
                            
                            log(`Soumission de formulaire détectée pour l'employé ${employeId} avec la valeur CP ${cpValue}`, input, 'info');
                            
                            // Stocker la valeur pour une mise à jour ultérieure
                            localStorage.setItem('cp-debug-update', JSON.stringify({
                                employeId: employeId,
                                cpValue: cpValue,
                                timestamp: Date.now()
                            }));
                            
                            // Diffuser un événement personnalisé
                            document.dispatchEvent(new CustomEvent('cp-debug-update', {
                                detail: { employeId: employeId, cpValue: cpValue }
                            }));
                        }
                    });
                });
                
                log('Interception de soumission de formulaire ajoutée', form, 'success');
            }
        });
        
        // Drapeau pour éviter les boucles infinies
        let isProcessingDebugUpdate = false;
        
        // 3. Écouter les événements personnalisés
        document.addEventListener('cp-debug-update', function(event) {
            // Éviter les boucles infinies
            if (isProcessingDebugUpdate) return;
            
            if (event.detail && event.detail.employeId && event.detail.cpValue !== undefined) {
                // Activer le drapeau
                isProcessingDebugUpdate = true;
                
                log(`Événement cp-debug-update reçu pour l'employé ${event.detail.employeId} avec la valeur ${event.detail.cpValue}`, event.detail, 'info');
                
                // Appeler les méthodes de mise à jour disponibles
                if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
                    window.CPMonitor.update(event.detail.employeId, event.detail.cpValue);
                }
                
                if (typeof window.forceUpdateCpElements === 'function' && window.forceUpdateCpElements !== window.CPMonitor?.update) {
                    window.forceUpdateCpElements(event.detail.employeId, event.detail.cpValue);
                }
                
                // Ne pas appeler updateCongesList pour éviter la boucle infinie
                // car updateCongesList émet des événements qui peuvent créer une boucle
                
                // Réinitialiser le drapeau après un court délai
                setTimeout(() => {
                    isProcessingDebugUpdate = false;
                }, 50);
            }
        });
        
        // Drapeau pour éviter les boucles infinies dans les événements storage
        let isProcessingStorageUpdate = false;
        
        // 4. Écouter les changements localStorage
        window.addEventListener('storage', function(event) {
            if (event.key === 'cp-debug-update') {
                // Éviter les boucles infinies
                if (isProcessingStorageUpdate) return;
                
                try {
                    const data = JSON.parse(event.newValue);
                    
                    if (data && data.employeId && data.cpValue !== undefined) {
                        // Activer le drapeau
                        isProcessingStorageUpdate = true;
                        
                        log(`Événement storage cp-debug-update reçu pour l'employé ${data.employeId} avec la valeur ${data.cpValue}`, data, 'info');
                        
                        // Appeler les méthodes de mise à jour disponibles
                        if (window.CPMonitor && typeof window.CPMonitor.update === 'function') {
                            window.CPMonitor.update(data.employeId, data.cpValue);
                        }
                        
                        if (typeof window.forceUpdateCpElements === 'function' && window.forceUpdateCpElements !== window.CPMonitor?.update) {
                            window.forceUpdateCpElements(data.employeId, data.cpValue);
                        }
                        
                        // Ne pas appeler updateCongesList pour éviter la boucle infinie
                        
                        // Réinitialiser le drapeau après un court délai
                        setTimeout(() => {
                            isProcessingStorageUpdate = false;
                        }, 50);
                    }
                } catch (err) {
                    log('Erreur lors du traitement de l\'événement storage', err, 'error');
                    isProcessingStorageUpdate = false; // Réinitialiser en cas d'erreur
                }
            }
        });
    }

    /**
     * Fonction pour créer le bouton de test
     */
    function createDebugButton() {
        const button = document.createElement('button');
        button.textContent = 'Diagnostiquer CP';
        button.style.cssText = `
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 9999;
            padding: 10px 15px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        `;
        
        button.addEventListener('click', function() {
            // Analyser les sélecteurs
            const results = analyzeSelectors();
            
            // Afficher un résumé
            let summary = 'Résumé du diagnostic CP:\n\n';
            let totalElements = 0;
            
            for (const selector in results) {
                summary += `${selector}: ${results[selector]} élément(s)\n`;
                totalElements += results[selector];
            }
            
            summary += `\nTotal: ${totalElements} élément(s) CP trouvé(s)`;
            
            if (totalElements === 0) {
                summary += '\n\nAucun élément CP trouvé. Correction automatique activée.';
                fixMissingSelectors();
                fixCpFormatting();
                fixEventHandling();
            } else {
                summary += '\n\nDes éléments CP ont été trouvés. Vérifiez la console pour plus de détails.';
            }
            
            alert(summary);
        });
        
        document.body.appendChild(button);
        log('Bouton de diagnostic ajouté', null, 'success');
    }

    /**
     * Fonction d'initialisation
     */
    function initialize() {
        log('Initialisation du script de diagnostic CP', null, 'info');
        
        // Analyser les sélecteurs
        analyzeSelectors();
        
        // Appliquer les corrections
        fixMissingSelectors();
        fixCpFormatting();
        fixEventHandling();
        
        // Créer le bouton de diagnostic
        createDebugButton();
        
        log('Script de diagnostic CP initialisé avec succès', null, 'success');
    }

    // Initialiser le script quand le DOM est chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();

// Afficher un message dans la console pour confirmer que le script est chargé
console.log('%c[CP-DEBUG] Script de diagnostic CP chargé avec succès!', 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;');
