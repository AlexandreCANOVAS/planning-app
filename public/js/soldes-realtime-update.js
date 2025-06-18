/**
 * Script pour mettre à jour en temps réel les soldes de congés
 * sur les tableaux de bord employeur et employé
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation du script de mise à jour en temps réel des soldes');
    
    // Fonction pour mettre à jour les soldes dans l'interface
    function updateSoldes(employeId, soldeConges, soldeRtt, soldeExceptionnels) {
        console.log('Mise à jour des soldes pour employé ID:', employeId, {
            soldeConges: soldeConges,
            soldeRtt: soldeRtt,
            soldeExceptionnels: soldeExceptionnels
        });
        
        // 1. Mise à jour sur la page de gestion des soldes (vue employeur)
        // Format: CP, RTT, CE dans des cards avec data-employe-id et data-solde-type
        const soldeElements = document.querySelectorAll(`[data-employe-id="${employeId}"]`);
        console.log(`Éléments trouvés avec data-employe-id=${employeId}:`, soldeElements.length);
        
        if (soldeElements.length > 0) {
            soldeElements.forEach(el => {
                const type = el.getAttribute('data-solde-type');
                console.log(`Élément trouvé avec type=${type}:`, el);
                let newValue;
                
                if (type === 'conges' && soldeConges !== undefined) {
                    // Traitement spécial pour les congés payés
                    newValue = parseFloat(soldeConges).toFixed(1);
                    console.log(`Mise à jour CP de ${el.textContent} à ${newValue}`);
                    
                    // Créer un nouvel élément pour remplacer l'ancien (comme dans conges-solde.js)
                    const parent = el.parentNode;
                    const newElement = document.createElement('span');
                    newElement.className = el.className;
                    newElement.setAttribute('data-employe-id', employeId);
                    newElement.setAttribute('data-solde-type', 'conges');
                    newElement.textContent = newValue;
                    
                    // Remplacer l'ancien élément par le nouveau
                    if (parent) {
                        parent.replaceChild(newElement, el);
                        console.log('Élément CP remplacé avec succès');
                        animateUpdate(newElement);
                    } else {
                        // Fallback si le parent n'est pas trouvé
                        el.textContent = newValue;
                        animateUpdate(el);
                    }
                } else if (type === 'rtt' && soldeRtt !== undefined) {
                    newValue = parseFloat(soldeRtt).toFixed(1);
                    el.textContent = newValue;
                    animateUpdate(el);
                } else if (type === 'exceptionnels' && soldeExceptionnels !== undefined) {
                    newValue = parseFloat(soldeExceptionnels).toFixed(1);
                    el.textContent = newValue;
                    animateUpdate(el);
                }
            });
        }
        
        // 2. Mise à jour sur le tableau de bord employé
        // Vérifier si on est sur le tableau de bord de l'employé concerné
        if (window.location.pathname.includes('/dashboard')) {
            console.log('Mise à jour du tableau de bord employé');
            
            // Soldes dans le tableau de bord employé
            const dashboardSoldeConges = document.querySelector('.solde-conges-value');
            const dashboardSoldeRtt = document.querySelector('.solde-rtt-value');
            const dashboardSoldeExceptionnels = document.querySelector('.solde-exceptionnels-value');
            
            console.log('Éléments trouvés sur le dashboard:', {
                'CP': dashboardSoldeConges,
                'RTT': dashboardSoldeRtt,
                'CE': dashboardSoldeExceptionnels
            });
            
            if (dashboardSoldeConges && soldeConges !== undefined) {
                // Traitement spécial pour les congés payés
                const newValue = parseFloat(soldeConges).toFixed(1);
                console.log(`Mise à jour CP dashboard de ${dashboardSoldeConges.textContent} à ${newValue}`);
                
                // Créer un nouvel élément pour remplacer l'ancien
                const parent = dashboardSoldeConges.parentNode;
                const newElement = document.createElement('span');
                newElement.className = dashboardSoldeConges.className;
                newElement.setAttribute('data-employe-id', employeId);
                newElement.setAttribute('data-solde-type', 'conges');
                newElement.textContent = newValue;
                
                // Remplacer l'ancien élément par le nouveau
                if (parent) {
                    parent.replaceChild(newElement, dashboardSoldeConges);
                    console.log('Élément CP dashboard remplacé avec succès');
                    animateUpdate(newElement);
                } else {
                    // Fallback
                    dashboardSoldeConges.textContent = newValue;
                    animateUpdate(dashboardSoldeConges);
                }
            }
            
            if (dashboardSoldeRtt && soldeRtt !== undefined) {
                dashboardSoldeRtt.textContent = parseFloat(soldeRtt).toFixed(1);
                animateUpdate(dashboardSoldeRtt);
            }
            
            if (dashboardSoldeExceptionnels && soldeExceptionnels !== undefined) {
                dashboardSoldeExceptionnels.textContent = parseFloat(soldeExceptionnels).toFixed(1);
                animateUpdate(dashboardSoldeExceptionnels);
            }
        }
        
        // Notification toast si disponible
        if (typeof window.showToast === 'function') {
            window.showToast('Les soldes de congés ont été mis à jour', 'purple');
        }
    }
    
    // Fonction pour animer la mise à jour
    function animateUpdate(element) {
        // Sauvegarder la classe d'origine
        const originalClass = element.className;
        
        // Ajouter une classe d'animation
        element.classList.add('bg-yellow-100');
        element.classList.add('transition-colors');
        element.classList.add('duration-1000');
        
        // Retirer la classe d'animation après un délai
        setTimeout(() => {
            element.classList.remove('bg-yellow-100');
        }, 1500);
    }
    
    // Écouter les événements de mise à jour des soldes via WebSocket
    if (window.Echo) {
        // Si nous sommes connectés en tant qu'employeur
        if (typeof window.societeId !== 'undefined') {
            console.log('Écoute des événements sur le canal societe.' + window.societeId);
            
            window.Echo.private(`societe.${window.societeId}`)
                .listen('.SoldesCongesUpdated', (event) => {
                    console.log('Événement SoldesCongesUpdated reçu:', event);
                    
                    // Mise à jour des soldes dans l'interface
                    updateSoldes(
                        event.employe_id,
                        event.solde_conges,
                        event.solde_rtt,
                        event.solde_conges_exceptionnels
                    );
                });
        }
        
        // Si nous sommes connectés en tant qu'employé
        if (typeof window.employeId !== 'undefined') {
            console.log('Écoute des événements sur le canal employe.' + window.employeId);
            
            window.Echo.private(`employe.${window.employeId}`)
                .listen('.solde.updated', (event) => {
                    console.log('Événement solde.updated reçu:', event);
                    
                    // Mise à jour des soldes dans l'interface
                    updateSoldes(
                        event.employe_id,
                        event.solde_conges,
                        event.solde_rtt,
                        event.solde_conges_exceptionnels
                    );
                });
        }
    } else {
        console.warn('Echo n\'est pas disponible, les mises à jour en temps réel ne fonctionneront pas');
    }
});
