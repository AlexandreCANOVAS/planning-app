/**
 * Script pour la mise à jour des soldes de congés payés
 * Version améliorée qui utilise AJAX au lieu de recharger la page
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script force-reload-cp.js chargé - Version AJAX sans rechargement');
    
    // Fonction pour mettre à jour les soldes sans rechargement
    function updateSoldesDisplay(data) {
        console.log('Mise à jour des soldes de congés via AJAX:', data);
        
        // Afficher une notification si disponible
        if (typeof window.showToast === 'function') {
            window.showToast('Mise à jour des soldes de congés effectuée', 'success');
        }
        
        // Mettre à jour les affichages
        const soldeConges = document.getElementById('current-solde-conges');
        const soldeRtt = document.getElementById('current-solde-rtt');
        const soldeExceptionnels = document.getElementById('current-solde-exceptionnels');
        
        // Mettre à jour les valeurs avec animation
        if (soldeConges && data.solde_conges !== undefined) {
            const newValue = parseFloat(data.solde_conges).toFixed(1) + ' jours';
            soldeConges.innerHTML = newValue;
            soldeConges.classList.add('bg-blue-100');
            setTimeout(() => soldeConges.classList.remove('bg-blue-100'), 1000);
        }
        
        if (soldeRtt && data.solde_rtt !== undefined) {
            const newValue = parseFloat(data.solde_rtt).toFixed(1) + ' jours';
            soldeRtt.innerHTML = newValue;
            soldeRtt.classList.add('bg-green-100');
            setTimeout(() => soldeRtt.classList.remove('bg-green-100'), 1000);
        }
        
        if (soldeExceptionnels && data.solde_conges_exceptionnels !== undefined) {
            const newValue = parseFloat(data.solde_conges_exceptionnels).toFixed(1) + ' jours';
            soldeExceptionnels.innerHTML = newValue;
            soldeExceptionnels.classList.add('bg-purple-100');
            setTimeout(() => soldeExceptionnels.classList.remove('bg-purple-100'), 1000);
        }
        
        // Mettre à jour les champs du formulaire
        const inputSoldeConges = document.getElementById('solde_conges');
        const inputSoldeRtt = document.getElementById('solde_rtt');
        const inputSoldeExceptionnels = document.getElementById('solde_conges_exceptionnels');
        
        if (inputSoldeConges && data.solde_conges !== undefined) {
            inputSoldeConges.value = parseFloat(data.solde_conges);
        }
        
        if (inputSoldeRtt && data.solde_rtt !== undefined) {
            inputSoldeRtt.value = parseFloat(data.solde_rtt);
        }
        
        if (inputSoldeExceptionnels && data.solde_conges_exceptionnels !== undefined) {
            inputSoldeExceptionnels.value = parseFloat(data.solde_conges_exceptionnels);
        }
    }
    
    // Écouter les événements de mise à jour des soldes
    if (window.Echo) {
        // Canal employeur
        if (typeof window.societeId !== 'undefined') {
            console.log('Écoute des événements sur le canal societe.' + window.societeId);
            
            window.Echo.private(`societe.${window.societeId}`)
                .listen('.SoldesCongesUpdated', (e) => {
                    console.log('Événement SoldesCongesUpdated reçu:', e);
                    
                    // Mettre à jour l'affichage sans recharger la page
                    if (e.employe) {
                        updateSoldesDisplay({
                            solde_conges: e.employe.solde_conges,
                            solde_rtt: e.employe.solde_rtt,
                            solde_conges_exceptionnels: e.employe.solde_conges_exceptionnels
                        });
                    }
                });
        }
        
        // Canal employé
        if (typeof window.employeId !== 'undefined') {
            console.log('Écoute des événements sur le canal employe.' + window.employeId);
            
            window.Echo.private(`employe.${window.employeId}`)
                .listen('.solde.updated', (e) => {
                    console.log('Événement solde.updated reçu:', e);
                    
                    // Mettre à jour l'affichage sans recharger la page
                    updateSoldesDisplay({
                        solde_conges: e.solde_conges,
                        solde_rtt: e.solde_rtt,
                        solde_conges_exceptionnels: e.solde_conges_exceptionnels
                    });
                });
        }
    } else {
        console.warn('Echo n\'est pas disponible, les mises à jour en temps réel ne fonctionneront pas');
    }
    
    // Vérifier si nous venons d'une mise à jour (paramètre dans l'URL)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('refresh_soldes')) {
        console.log('Détection du paramètre refresh_soldes, mise à jour de l\'interface');
        
        // Mettre en évidence les soldes actuels
        const soldeConges = document.getElementById('current-solde-conges');
        const soldeRtt = document.getElementById('current-solde-rtt');
        const soldeExceptionnels = document.getElementById('current-solde-exceptionnels');
        
        if (soldeConges) {
            soldeConges.classList.add('bg-blue-100');
            setTimeout(() => soldeConges.classList.remove('bg-blue-100'), 2000);
        }
        
        if (soldeRtt) {
            soldeRtt.classList.add('bg-green-100');
            setTimeout(() => soldeRtt.classList.remove('bg-green-100'), 2000);
        }
        
        if (soldeExceptionnels) {
            soldeExceptionnels.classList.add('bg-purple-100');
            setTimeout(() => soldeExceptionnels.classList.remove('bg-purple-100'), 2000);
        }
        
        // Afficher une notification
        if (typeof window.showToast === 'function') {
            window.showToast('Les soldes de congés ont été mis à jour', 'success');
        }
    }
});
