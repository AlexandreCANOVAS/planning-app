/**
 * Script de test pour vérifier la mise à jour des soldes de congés
 * À exécuter dans la console du navigateur sur la page de modification des soldes
 */
(function() {
    console.log('Test de mise à jour des soldes de congés...');
    
    // Récupérer les valeurs actuelles
    const soldeCongesActuel = parseFloat(document.getElementById('current-solde-conges').textContent);
    const soldeRttActuel = parseFloat(document.getElementById('current-solde-rtt').textContent);
    const soldeExceptionnelsActuel = parseFloat(document.getElementById('current-solde-exceptionnels').textContent);
    
    console.log('Valeurs actuelles:', {
        soldeConges: soldeCongesActuel,
        soldeRtt: soldeRttActuel,
        soldeExceptionnels: soldeExceptionnelsActuel
    });
    
    // Récupérer les champs du formulaire
    const inputSoldeConges = document.getElementById('solde_conges');
    const inputSoldeRtt = document.getElementById('solde_rtt');
    const inputSoldeExceptionnels = document.getElementById('solde_conges_exceptionnels');
    
    if (!inputSoldeConges || !inputSoldeRtt || !inputSoldeExceptionnels) {
        console.error('Impossible de trouver les champs du formulaire');
        return;
    }
    
    // Modifier les valeurs (ajouter 1 jour à chaque solde)
    const nouvelleSoldeConges = parseFloat(inputSoldeConges.value) + 1;
    const nouvelleSoldeRtt = parseFloat(inputSoldeRtt.value) + 1;
    const nouvelleSoldeExceptionnels = parseFloat(inputSoldeExceptionnels.value) + 1;
    
    inputSoldeConges.value = nouvelleSoldeConges;
    inputSoldeRtt.value = nouvelleSoldeRtt;
    inputSoldeExceptionnels.value = nouvelleSoldeExceptionnels;
    
    console.log('Nouvelles valeurs:', {
        soldeConges: nouvelleSoldeConges,
        soldeRtt: nouvelleSoldeRtt,
        soldeExceptionnels: nouvelleSoldeExceptionnels
    });
    
    // Simuler la soumission du formulaire
    const event = new Event('submit', {
        bubbles: true,
        cancelable: true
    });
    
    document.getElementById('solde-form').dispatchEvent(event);
    
    console.log('Formulaire soumis. Vérifiez que les valeurs ont été mises à jour dans l\'interface.');
})();
