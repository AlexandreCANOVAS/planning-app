// Script pour forcer l'application du thème sombre sur tous les éléments
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si le thème sombre est actif
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    if (isDarkMode) {
        // Appliquer le thème sombre à tous les éléments existants
        applyDarkThemeToAllElements();
        
        // Observer les changements dans le DOM pour appliquer le thème sombre aux nouveaux éléments
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    // Appliquer le thème sombre aux nouveaux éléments
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // 1 = ELEMENT_NODE
                            applyDarkThemeToElement(node);
                            
                            // Appliquer aux enfants
                            const children = node.querySelectorAll('*');
                            children.forEach(applyDarkThemeToElement);
                        }
                    });
                }
            });
        });
        
        // Observer tout le body pour les changements
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Fonction pour appliquer le thème sombre à tous les éléments
    function applyDarkThemeToAllElements() {
        const allElements = document.querySelectorAll('*');
        allElements.forEach(applyDarkThemeToElement);
    }
    
    // Fonction pour appliquer le thème sombre à un élément spécifique
    function applyDarkThemeToElement(element) {
        // Ignorer les éléments qui doivent conserver leurs couleurs spécifiques
        if (element.classList) {
            const classesToPreserve = [
                'btn-primary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger',
                'disponible', 'en-service', 
                'progress-bar-danger', 'progress-bar-warning', 'progress-bar-success'
            ];
            
            // Vérifier uniquement avec classList.contains qui est plus sûr
            const shouldPreserve = classesToPreserve.some(cls => 
                element.classList.contains(cls)
            );
            
            // Vérifier aussi la propriété className si c'est une chaîne
            if (typeof element.className === 'string') {
                for (const cls of classesToPreserve) {
                    if (element.className.indexOf(cls) !== -1) {
                        return; // Préserver cet élément
                    }
                }
            }
            
            if (shouldPreserve) {
                return;
            }
            
            // Appliquer les styles du thème sombre
            if (element.tagName !== 'HTML' && element.tagName !== 'BODY') {
                if (element.classList.contains('bg-white') || 
                    element.classList.contains('bg-gray-50') || 
                    element.classList.contains('bg-gray-100')) {
                    element.style.backgroundColor = 'rgb(31, 41, 55)';
                    element.style.color = 'rgb(229, 231, 235)';
                }
            }
        }
    }
});
