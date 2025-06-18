/**
 * Solution directe pour le problème des congés payés
 * Ce script utilise une approche radicale pour forcer la mise à jour des CP
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script fix-cp.js chargé - Solution radicale pour les CP');
    
    // Fonction pour mettre à jour uniquement les CP
    function updateCP(employeId, valeur) {
        console.log(`Tentative de mise à jour CP pour employé ${employeId} avec valeur ${valeur}`);
        
        // Convertir en nombre et formater avec 1 décimale
        const valeurFormatee = parseFloat(valeur).toFixed(1);
        
        // 1. Cibler les éléments dans la vue de gestion des soldes (index)
        document.querySelectorAll(`[data-employe-id="${employeId}"][data-solde-type="conges"]`).forEach(element => {
            console.log('Élément CP trouvé (index):', element);
            
            // Forcer la mise à jour directe du contenu
            element.innerHTML = valeurFormatee;
            
            // Animation flash
            element.style.backgroundColor = '#FFFF00';
            setTimeout(() => {
                element.style.backgroundColor = '';
            }, 1500);
            
            console.log('Contenu mis à jour directement:', element.innerHTML);
        });
        
        // 2. Cibler l'élément dans la vue d'édition
        const currentSoldeConges = document.getElementById('current-solde-conges');
        if (currentSoldeConges) {
            console.log('Élément current-solde-conges trouvé');
            
            // Forcer la mise à jour directe du contenu
            currentSoldeConges.innerHTML = `${valeurFormatee} jours`;
            
            // Animation flash
            currentSoldeConges.style.backgroundColor = '#FFFF00';
            setTimeout(() => {
                currentSoldeConges.style.backgroundColor = '';
            }, 1500);
            
            console.log('Contenu current-solde-conges mis à jour:', currentSoldeConges.innerHTML);
        }
        
        // 3. Cibler spécifiquement l'élément dans le tableau de bord employé
        const dashboardCP = document.querySelector('.solde-conges-value');
        if (dashboardCP) {
            console.log('Élément CP du tableau de bord trouvé:', dashboardCP);
            
            // Forcer la mise à jour directe du contenu
            dashboardCP.innerHTML = valeurFormatee;
            
            // Animation flash
            dashboardCP.style.backgroundColor = '#FFFF00';
            setTimeout(() => {
                dashboardCP.style.backgroundColor = '';
            }, 1500);
            
            console.log('Contenu CP du tableau de bord mis à jour:', dashboardCP.innerHTML);
        }
        
        // 4. Notification
        if (typeof window.showToast === 'function') {
            window.showToast('Solde de congés payés mis à jour', 'blue');
        }
    }
    
    // Écouter directement l'événement SoldesCongesUpdated
    if (window.Echo) {
        // Canal employeur
        if (window.societeId) {
            window.Echo.private(`societe.${window.societeId}`)
                .listen('.SoldesCongesUpdated', (e) => {
                    console.log('Événement SoldesCongesUpdated reçu dans fix-cp.js:', e);
                    if (e.employe_id && e.solde_conges !== undefined) {
                        updateCP(e.employe_id, e.solde_conges);
                    }
                });
        }
        
        // Canal employé
        if (window.employeId) {
            window.Echo.private(`employe.${window.employeId}`)
                .listen('.solde.updated', (e) => {
                    console.log('Événement solde.updated reçu dans fix-cp.js:', e);
                    if (e.employe_id && e.solde_conges !== undefined) {
                        updateCP(e.employe_id, e.solde_conges);
                    }
                });
        }
    }
    
    // Ajouter un style pour l'animation
    const style = document.createElement('style');
    style.textContent = `
        .cp-updated {
            transition: background-color 0.5s ease;
        }
    `;
    document.head.appendChild(style);
});
