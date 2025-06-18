/**
 * Script pour rafraîchir automatiquement les données des soldes de congés
 * après une modification
 */
document.addEventListener('DOMContentLoaded', function() {
    // Écouter les événements de mise à jour des soldes de congés
    if (window.Echo && typeof window.societeId !== 'undefined') {
        console.log('Initialisation de l\'écoute des événements de soldes de congés');
        
        window.Echo.private(`societe.${window.societeId}`)
            .listen('.SoldesCongesUpdated', (e) => {
                console.log('Événement SoldesCongesUpdated reçu:', e);
                
                // Si nous sommes sur la page de détail d'un employé
                if (window.location.pathname.includes('/conges/solde/') && 
                    window.location.pathname.includes('/edit')) {
                    
                    // Vérifier si c'est le même employé
                    const urlParts = window.location.pathname.split('/');
                    const employeId = parseInt(urlParts[urlParts.indexOf('solde') + 1]);
                    
                    if (employeId === e.employe.id) {
                        console.log('Mise à jour des soldes pour l\'employé courant');
                        
                        // Mettre à jour les affichages
                        const soldeConges = document.getElementById('current-solde-conges');
                        const soldeRtt = document.getElementById('current-solde-rtt');
                        const soldeExceptionnels = document.getElementById('current-solde-exceptionnels');
                        
                        if (soldeConges) soldeConges.textContent = `${parseFloat(e.employe.solde_conges).toFixed(1)} jours`;
                        if (soldeRtt) soldeRtt.textContent = `${parseFloat(e.employe.solde_rtt).toFixed(1)} jours`;
                        if (soldeExceptionnels) soldeExceptionnels.textContent = `${parseFloat(e.employe.solde_conges_exceptionnels).toFixed(1)} jours`;
                        
                        // Mettre à jour les champs du formulaire
                        const inputSoldeConges = document.getElementById('solde_conges');
                        const inputSoldeRtt = document.getElementById('solde_rtt');
                        const inputSoldeExceptionnels = document.getElementById('solde_conges_exceptionnels');
                        
                        if (inputSoldeConges) inputSoldeConges.value = parseFloat(e.employe.solde_conges);
                        if (inputSoldeRtt) inputSoldeRtt.value = parseFloat(e.employe.solde_rtt);
                        if (inputSoldeExceptionnels) inputSoldeExceptionnels.value = parseFloat(e.employe.solde_conges_exceptionnels);
                        
                        // Afficher une notification
                        if (window.showToast) {
                            window.showToast('Les soldes de congés ont été mis à jour', 'purple');
                        }
                    }
                }
            });
    }
});
