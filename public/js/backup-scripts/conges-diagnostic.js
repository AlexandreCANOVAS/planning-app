/**
 * Script de diagnostic pour les problèmes de mise à jour des soldes de congés
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DIAGNOSTIC DES SOLDES DE CONGÉS ===');
    
    // Vérifier les éléments HTML
    const soldeConges = document.getElementById('current-solde-conges');
    const soldeRtt = document.getElementById('current-solde-rtt');
    const soldeExceptionnels = document.getElementById('current-solde-exceptionnels');
    
    console.log('Éléments HTML des soldes:', {
        'soldeConges': soldeConges ? soldeConges.outerHTML : 'Non trouvé',
        'soldeRtt': soldeRtt ? soldeRtt.outerHTML : 'Non trouvé',
        'soldeExceptionnels': soldeExceptionnels ? soldeExceptionnels.outerHTML : 'Non trouvé'
    });
    
    // Tester la modification directe des éléments
    if (soldeConges) {
        console.log('Test de modification directe de soldeConges:');
        console.log('  - Avant:', soldeConges.textContent);
        const valeurOriginale = soldeConges.textContent;
        soldeConges.textContent = 'TEST';
        console.log('  - Pendant (devrait être "TEST"):', soldeConges.textContent);
        soldeConges.textContent = valeurOriginale;
        console.log('  - Après (restauré):', soldeConges.textContent);
    }
    
    // Vérifier le formulaire
    const soldeForm = document.getElementById('solde-form');
    if (soldeForm) {
        console.log('Formulaire trouvé:', soldeForm.getAttribute('action'));
        
        // Vérifier les champs du formulaire
        const inputSoldeConges = document.getElementById('solde_conges');
        const inputSoldeRtt = document.getElementById('solde_rtt');
        const inputSoldeExceptionnels = document.getElementById('solde_conges_exceptionnels');
        
        console.log('Champs du formulaire:', {
            'inputSoldeConges': inputSoldeConges ? inputSoldeConges.value : 'Non trouvé',
            'inputSoldeRtt': inputSoldeRtt ? inputSoldeRtt.value : 'Non trouvé',
            'inputSoldeExceptionnels': inputSoldeExceptionnels ? inputSoldeExceptionnels.value : 'Non trouvé'
        });
        
        // Ajouter un bouton de test pour modifier les soldes sans soumettre le formulaire
        const testButton = document.createElement('button');
        testButton.type = 'button';
        testButton.className = 'px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 mt-4';
        testButton.textContent = 'Test mise à jour directe';
        testButton.onclick = function() {
            if (soldeConges) soldeConges.textContent = '99.9 jours';
            if (soldeRtt) soldeRtt.textContent = '99.9 jours';
            if (soldeExceptionnels) soldeExceptionnels.textContent = '99.9 jours';
            console.log('Test de mise à jour directe effectué');
        };
        
        // Ajouter après le bouton de soumission
        const submitButton = soldeForm.querySelector('button[type="submit"]');
        if (submitButton && submitButton.parentNode) {
            submitButton.parentNode.appendChild(testButton);
        }
    }
    
    // Vérifier si le script conges-solde.js est chargé
    console.log('Scripts chargés:', {
        'conges-solde.js': typeof window.soldeFormInitialized !== 'undefined',
        'toast.js': typeof window.showToast === 'function'
    });
    
    // Ajouter un indicateur global pour ce script
    window.congesDiagnosticLoaded = true;
    console.log('=== FIN DU DIAGNOSTIC ===');
});
