/**
 * Script de correction des attributs pour les éléments CP
 * Ce script identifie les éléments affichant des soldes CP et leur ajoute les attributs nécessaires
 * pour qu'ils soient correctement ciblés par les scripts de mise à jour
 */

(function() {
    // Configuration
    const config = {
        debug: true,
        autoApply: true,
        refreshInterval: 2000 // ms
    };

    // Fonction de journalisation
    function log(message, data, type = 'info') {
        if (!config.debug) return;
        
        const styles = {
            info: 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;',
            success: 'background: #10b981; color: white; padding: 2px 5px; border-radius: 3px;',
            warning: 'background: #f59e0b; color: white; padding: 2px 5px; border-radius: 3px;',
            error: 'background: #ef4444; color: white; padding: 2px 5px; border-radius: 3px;'
        };
        
        console.log(`%c[CP-ATTRIBUTE-FIX] ${message}`, styles[type], data || '');
    }

    /**
     * Fonction pour identifier les éléments qui affichent des soldes CP
     */
    function identifyCpElements() {
        log('Recherche des éléments affichant des soldes CP...', null, 'info');
        
        // 1. Rechercher par texte dans les éléments
        const allElements = document.querySelectorAll('span, div, td, p, h1, h2, h3, h4, h5, h6, strong, b');
        const cpElements = [];
        
        allElements.forEach(element => {
            const text = element.textContent.trim();
            
            // Vérifier si le texte ressemble à un solde CP (nombre avec ou sans "jours")
            if (/^\d+(\.\d+)?\s*(jour|jours)?$/.test(text)) {
                // Vérifier le contexte pour confirmer qu'il s'agit d'un solde CP
                const context = getElementContext(element);
                
                if (isCpContext(context)) {
                    cpElements.push({
                        element: element,
                        value: parseFloat(text),
                        context: context,
                        employeId: findEmployeId(element)
                    });
                }
            }
        });
        
        log(`${cpElements.length} éléments CP potentiels identifiés`, cpElements, 'info');
        return cpElements;
    }

    /**
     * Fonction pour obtenir le contexte textuel d'un élément
     */
    function getElementContext(element) {
        // Récupérer le texte des éléments parents et frères
        const context = {
            parentText: element.parentElement ? element.parentElement.textContent.toLowerCase() : '',
            siblingTexts: [],
            nearbyLabels: []
        };
        
        // Récupérer le texte des éléments frères
        if (element.parentElement) {
            Array.from(element.parentElement.children).forEach(child => {
                if (child !== element) {
                    context.siblingTexts.push(child.textContent.toLowerCase());
                }
            });
        }
        
        // Rechercher des labels à proximité
        const labels = document.querySelectorAll('label, th, .label, .header, h3, h4');
        labels.forEach(label => {
            const rect1 = element.getBoundingClientRect();
            const rect2 = label.getBoundingClientRect();
            
            // Vérifier si les éléments sont proches horizontalement ou verticalement
            const horizontalDistance = Math.min(
                Math.abs(rect1.left - rect2.right),
                Math.abs(rect2.left - rect1.right)
            );
            
            const verticalDistance = Math.min(
                Math.abs(rect1.top - rect2.bottom),
                Math.abs(rect2.top - rect1.bottom)
            );
            
            // Si les éléments sont suffisamment proches
            if (horizontalDistance < 100 && verticalDistance < 50) {
                context.nearbyLabels.push(label.textContent.toLowerCase());
            }
        });
        
        return context;
    }

    /**
     * Fonction pour déterminer si le contexte correspond à un solde CP
     */
    function isCpContext(context) {
        const cpKeywords = ['cp', 'congés payés', 'conges payes', 'congés', 'conges', 'solde cp', 'solde congés'];
        const nonCpKeywords = ['rtt', 'récupération', 'recuperation', 'maladie', 'absence'];
        
        // Vérifier dans le texte parent
        for (const keyword of cpKeywords) {
            if (context.parentText.includes(keyword)) {
                return true;
            }
        }
        
        // Vérifier dans les textes frères
        for (const siblingText of context.siblingTexts) {
            for (const keyword of cpKeywords) {
                if (siblingText.includes(keyword)) {
                    return true;
                }
            }
            
            // Vérifier que ce n'est pas un autre type de congé
            for (const keyword of nonCpKeywords) {
                if (siblingText.includes(keyword) && !siblingText.includes('cp') && !siblingText.includes('congés payés')) {
                    return false;
                }
            }
        }
        
        // Vérifier dans les labels à proximité
        for (const labelText of context.nearbyLabels) {
            for (const keyword of cpKeywords) {
                if (labelText.includes(keyword)) {
                    return true;
                }
            }
        }
        
        // Par défaut, retourner false pour éviter les faux positifs
        return false;
    }

    /**
     * Fonction pour trouver l'ID de l'employé associé à un élément
     */
    function findEmployeId(element) {
        // 1. Vérifier si l'élément a déjà un attribut data-employe-id
        let employeId = element.getAttribute('data-employe-id') || 
                        element.getAttribute('data-employee-id') || 
                        element.getAttribute('data-id');
        
        if (employeId) {
            return employeId;
        }
        
        // 2. Chercher dans les éléments parents
        let parent = element.parentElement;
        let depth = 0;
        
        while (parent && depth < 5) {
            employeId = parent.getAttribute('data-employe-id') || 
                       parent.getAttribute('data-employee-id') || 
                       parent.getAttribute('data-id');
            
            if (employeId) {
                return employeId;
            }
            
            // Vérifier les attributs data-* personnalisés
            for (const attr of parent.attributes) {
                if (attr.name.startsWith('data-') && /^\d+$/.test(attr.value)) {
                    return attr.value;
                }
            }
            
            parent = parent.parentElement;
            depth++;
        }
        
        // 3. Chercher dans l'URL de la page
        const urlMatch = window.location.href.match(/employe[\/=](\d+)/i) || 
                         window.location.href.match(/employee[\/=](\d+)/i) ||
                         window.location.href.match(/user[\/=](\d+)/i);
        
        if (urlMatch) {
            return urlMatch[1];
        }
        
        // 4. Chercher dans les éléments voisins
        const siblings = element.parentElement ? element.parentElement.children : [];
        for (const sibling of siblings) {
            if (sibling !== element) {
                // Chercher un lien contenant un ID d'employé
                const links = sibling.querySelectorAll('a');
                for (const link of links) {
                    const href = link.getAttribute('href');
                    if (href) {
                        const linkMatch = href.match(/employe[\/=](\d+)/i) || 
                                         href.match(/employee[\/=](\d+)/i) ||
                                         href.match(/user[\/=](\d+)/i);
                        if (linkMatch) {
                            return linkMatch[1];
                        }
                    }
                }
            }
        }
        
        // Si aucun ID n'est trouvé, retourner null
        return null;
    }

    /**
     * Fonction pour ajouter les attributs nécessaires aux éléments CP
     */
    function addAttributesToCpElements(cpElements) {
        let updatedCount = 0;
        
        cpElements.forEach(item => {
            if (!item.element || !item.employeId) return;
            
            const element = item.element;
            const employeId = item.employeId;
            
            // Ajouter les attributs nécessaires
            if (!element.hasAttribute('data-employe-id')) {
                element.setAttribute('data-employe-id', employeId);
                updatedCount++;
            }
            
            if (!element.hasAttribute('data-solde-type')) {
                element.setAttribute('data-solde-type', 'conges');
                updatedCount++;
            }
            
            // Ajouter des classes pour faciliter la sélection
            if (!element.classList.contains('cp-value')) {
                element.classList.add('cp-value');
                updatedCount++;
            }
            
            log(`Attributs ajoutés à l'élément pour l'employé ${employeId}`, element, 'success');
        });
        
        log(`${updatedCount} attributs ajoutés à ${cpElements.length} éléments CP`, null, 'success');
        return updatedCount;
    }

    /**
     * Fonction pour appliquer automatiquement les corrections
     */
    function applyFixes() {
        const cpElements = identifyCpElements();
        const updatedCount = addAttributesToCpElements(cpElements);
        
        if (updatedCount > 0) {
            log(`Corrections appliquées avec succès (${updatedCount} attributs ajoutés)`, null, 'success');
            
            // Notifier les autres scripts que des éléments ont été mis à jour
            document.dispatchEvent(new CustomEvent('cp-elements-updated', {
                detail: { count: cpElements.length }
            }));
        }
    }

    /**
     * Fonction d'initialisation
     */
    function initialize() {
        log('Initialisation du script de correction des attributs CP', null, 'info');
        
        // Appliquer les corrections immédiatement si autoApply est activé
        if (config.autoApply) {
            applyFixes();
            
            // Configurer une vérification périodique pour les nouveaux éléments
            setInterval(applyFixes, config.refreshInterval);
        }
        
        // Écouter les événements de mise à jour du DOM
        document.addEventListener('DOMContentLoaded', applyFixes);
        window.addEventListener('load', applyFixes);
        
        // Écouter les événements personnalisés qui pourraient indiquer des changements dans le DOM
        document.addEventListener('conges-cp-updated', function() {
            setTimeout(applyFixes, 100);
        });
        
        log('Script de correction des attributs CP initialisé avec succès', null, 'success');
    }

    // Initialiser le script
    initialize();
})();

// Afficher un message dans la console pour confirmer que le script est chargé
console.log('%c[CP-ATTRIBUTE-FIX] Script de correction des attributs CP chargé avec succès!', 'background: #3b82f6; color: white; padding: 2px 5px; border-radius: 3px;');
