/**
 * Script de test pour la mise à jour des soldes de congés
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== SCRIPT DE TEST POUR LES SOLDES DE CONGÉS ===');
    
    // Créer un bouton de test pour modifier automatiquement les valeurs du formulaire
    const createTestButton = function() {
        const soldeForm = document.getElementById('solde-form');
        if (!soldeForm) return;
        
        const testButton = document.createElement('button');
        testButton.type = 'button';
        testButton.className = 'px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 mt-4 mr-2';
        testButton.textContent = 'Modifier les valeurs (+5)';
        testButton.onclick = function() {
            // Récupérer les champs du formulaire
            const inputSoldeConges = document.getElementById('solde_conges');
            const inputSoldeRtt = document.getElementById('solde_rtt');
            const inputSoldeExceptionnels = document.getElementById('solde_conges_exceptionnels');
            const inputCommentaire = document.getElementById('commentaire');
            
            // Modifier les valeurs (+5 à chaque solde, +10 aux congés payés pour s'assurer qu'ils changent)
            if (inputSoldeConges) {
                const oldValue = parseFloat(inputSoldeConges.value);
                const newValue = (oldValue + 10).toFixed(1); // +10 pour les congés payés
                console.log('Modification des congés payés:', oldValue, '->', newValue);
                inputSoldeConges.value = newValue;
            }
            if (inputSoldeRtt) inputSoldeRtt.value = (parseFloat(inputSoldeRtt.value) + 5).toFixed(1);
            if (inputSoldeExceptionnels) inputSoldeExceptionnels.value = (parseFloat(inputSoldeExceptionnels.value) + 5).toFixed(1);
            if (inputCommentaire) inputCommentaire.value = 'Test automatique - Ajout de 10 jours aux congés payés et 5 jours aux autres';
            
            console.log('Valeurs modifiées:', {
                soldeConges: inputSoldeConges ? inputSoldeConges.value : 'Non trouvé',
                soldeRtt: inputSoldeRtt ? inputSoldeRtt.value : 'Non trouvé',
                soldeExceptionnels: inputSoldeExceptionnels ? inputSoldeExceptionnels.value : 'Non trouvé'
            });
        };
        
        // Créer un autre bouton pour soumettre automatiquement le formulaire
        const submitTestButton = document.createElement('button');
        submitTestButton.type = 'button';
        submitTestButton.className = 'px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 mt-4';
        submitTestButton.textContent = 'Modifier et soumettre';
        submitTestButton.onclick = function() {
            // D'abord modifier les valeurs
            testButton.click();
            
            // Puis soumettre le formulaire
            setTimeout(() => {
                const submitButton = soldeForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    console.log('Soumission automatique du formulaire...');
                    submitButton.click();
                }
            }, 500);
        };
        
        // Ajouter les boutons après le bouton de soumission
        const submitButton = soldeForm.querySelector('button[type="submit"]');
        if (submitButton && submitButton.parentNode) {
            submitButton.parentNode.insertBefore(testButton, submitButton.nextSibling);
            submitButton.parentNode.insertBefore(submitTestButton, testButton.nextSibling);
        }
    };
    
    // Attendre un peu pour s'assurer que tous les autres scripts sont chargés
    setTimeout(createTestButton, 500);
    
    console.log('=== FIN DU SCRIPT DE TEST ===');
});
