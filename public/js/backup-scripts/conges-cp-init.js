/**
 * Script d'initialisation pour la correction des problèmes de mise à jour des congés payés
 * Ce script charge et coordonne les différentes solutions
 */
(function() {
    console.log('Initialisation du système de correction des congés payés');
    
    // Fonction pour charger un script dynamiquement
    function loadScript(url, callback) {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;
        script.onload = callback || function() {};
        document.head.appendChild(script);
        console.log(`Script chargé: ${url}`);
    }
    
    // Fonction pour vérifier si un script est déjà chargé
    function isScriptLoaded(url) {
        return Array.from(document.getElementsByTagName('script'))
            .some(script => script.src.includes(url));
    }
    
    // Charger les scripts nécessaires s'ils ne sont pas déjà chargés
    if (!isScriptLoaded('conges-cp-fix.js')) {
        loadScript('/js/conges-cp-fix.js', function() {
            console.log('Script de correction CP chargé avec succès');
        });
    }
    
    if (!isScriptLoaded('conges-cp-monitor.js')) {
        loadScript('/js/conges-cp-monitor.js', function() {
            console.log('Script de surveillance CP chargé avec succès');
        });
    }
    
    // Désactiver le rechargement forcé de la page
    if (window.location.search.includes('refresh_soldes=1')) {
        // Supprimer le paramètre de l'URL sans recharger la page
        const newUrl = window.location.pathname + 
            window.location.search.replace(/[?&]refresh_soldes=1/, '') + 
            window.location.hash;
        
        window.history.replaceState({}, document.title, newUrl);
        console.log('Paramètre de rechargement supprimé de l\'URL');
    }
    
    // Fonction pour détecter les modifications de solde CP
    function setupCpChangeDetection() {
        // Surveiller les modifications du formulaire
        const soldeCongesInput = document.getElementById('solde_conges');
        if (soldeCongesInput) {
            let lastValue = soldeCongesInput.value;
            
            soldeCongesInput.addEventListener('change', function() {
                const newValue = this.value;
                console.log(`Valeur CP modifiée: ${lastValue} -> ${newValue}`);
                lastValue = newValue;
                
                // Stocker la valeur pour référence future
                sessionStorage.setItem('last_cp_value', newValue);
                sessionStorage.setItem('cp_change_time', new Date().getTime());
            });
        }
        
        // Intercepter les soumissions de formulaire
        const soldeForm = document.getElementById('solde-form');
        if (soldeForm) {
            soldeForm.addEventListener('submit', function(e) {
                console.log('Soumission du formulaire de solde détectée');
                
                // Stocker l'état de soumission
                sessionStorage.setItem('cp_form_submitted', 'true');
                sessionStorage.setItem('cp_submit_time', new Date().getTime());
            });
        }
    }
    
    // Fonction pour vérifier si une mise à jour récente a eu lieu
    function checkRecentUpdate() {
        const formSubmitted = sessionStorage.getItem('cp_form_submitted');
        const submitTime = sessionStorage.getItem('cp_submit_time');
        
        if (formSubmitted === 'true' && submitTime) {
            const elapsed = new Date().getTime() - parseInt(submitTime);
            
            // Si la soumission a eu lieu dans les 5 dernières secondes
            if (elapsed < 5000) {
                console.log('Soumission récente détectée, forçage de la mise à jour des éléments');
                
                // Réinitialiser l'état de soumission
                sessionStorage.removeItem('cp_form_submitted');
                
                // Déclencher un événement personnalisé pour forcer la mise à jour
                setTimeout(function() {
                    const lastValue = sessionStorage.getItem('last_cp_value');
                    if (lastValue) {
                        const employeId = window.employeId || 
                            document.querySelector('input[name="employe_id"]')?.value;
                        
                        if (employeId) {
                            console.log(`Forçage de la mise à jour CP: ${lastValue}`);
                            
                            // Créer et dispatcher un événement personnalisé
                            const event = new CustomEvent('cp-manual-update', {
                                detail: {
                                    employeId: employeId,
                                    value: lastValue
                                }
                            });
                            document.dispatchEvent(event);
                        }
                    }
                }, 500);
            }
        }
    }
    
    // Attendre que le DOM soit complètement chargé
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé, configuration des détecteurs de changement');
        
        // Configurer la détection des changements
        setupCpChangeDetection();
        
        // Vérifier s'il y a eu une mise à jour récente
        checkRecentUpdate();
        
        // Afficher une notification si disponible
        if (typeof window.showToast === 'function') {
            window.showToast('Système de correction des congés payés activé', 'info');
        }
    });
})();
