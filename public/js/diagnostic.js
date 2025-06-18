/**
 * Script de diagnostic pour la mise à jour des soldes de congés
 * À exécuter dans la console du navigateur sur la page de modification des soldes
 */
(function() {
    console.log('%c=== DIAGNOSTIC DES SOLDES DE CONGÉS ===', 'background: #4a148c; color: white; padding: 5px; font-weight: bold;');
    
    // Vérifier que les éléments HTML nécessaires sont présents
    console.log('%c1. Vérification des éléments HTML', 'font-weight: bold; color: #4a148c;');
    const elements = [
        { id: 'solde-form', type: 'form', name: 'Formulaire de solde' },
        { id: 'current-solde-conges', type: 'span', name: 'Affichage solde congés' },
        { id: 'current-solde-rtt', type: 'span', name: 'Affichage solde RTT' },
        { id: 'current-solde-exceptionnels', type: 'span', name: 'Affichage solde exceptionnels' },
        { id: 'solde_conges', type: 'input', name: 'Champ solde congés' },
        { id: 'solde_rtt', type: 'input', name: 'Champ solde RTT' },
        { id: 'solde_conges_exceptionnels', type: 'input', name: 'Champ solde exceptionnels' }
    ];
    
    elements.forEach(el => {
        const element = document.getElementById(el.id);
        if (element) {
            console.log(`✅ ${el.name} (${el.id}) trouvé`);
            if (el.type === 'form') {
                console.log(`   - Action: ${element.getAttribute('action')}`);
                console.log(`   - Méthode: ${element.getAttribute('method')}`);
                const methodField = element.querySelector('input[name="_method"]');
                if (methodField) {
                    console.log(`   - Méthode HTTP réelle: ${methodField.value}`);
                }
            }
            if (el.type === 'input') {
                console.log(`   - Valeur: ${element.value}`);
            }
            if (el.type === 'span') {
                console.log(`   - Contenu: ${element.textContent}`);
            }
        } else {
            console.log(`❌ ${el.name} (${el.id}) non trouvé`);
        }
    });
    
    // Vérifier que les scripts JS sont chargés
    console.log('%c2. Vérification des scripts JavaScript', 'font-weight: bold; color: #4a148c;');
    const scripts = [
        { name: 'toast.js', check: () => typeof window.showToast === 'function' },
        { name: 'conges-solde.js', check: () => document.getElementById('solde-form') && document.getElementById('solde-form').hasAttribute('data-js-initialized') },
        { name: 'conges-refresh.js', check: () => window.Echo && window.societeId }
    ];
    
    scripts.forEach(script => {
        if (script.check()) {
            console.log(`✅ ${script.name} chargé correctement`);
        } else {
            console.log(`❌ ${script.name} non chargé ou non initialisé`);
        }
    });
    
    // Vérifier la configuration CSRF
    console.log('%c3. Vérification de la configuration CSRF', 'font-weight: bold; color: #4a148c;');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        console.log(`✅ Token CSRF trouvé: ${csrfToken.getAttribute('content').substring(0, 10)}...`);
    } else {
        console.log(`❌ Token CSRF non trouvé`);
    }
    
    // Vérifier la configuration de Laravel Echo
    console.log('%c4. Vérification de Laravel Echo', 'font-weight: bold; color: #4a148c;');
    if (window.Echo) {
        console.log(`✅ Laravel Echo est configuré`);
        console.log(`   - Broadcaster: ${window.Echo.connector.options.broadcaster}`);
        console.log(`   - Key: ${window.Echo.connector.options.key.substring(0, 5)}...`);
        
        if (window.societeId) {
            console.log(`✅ ID de la société trouvé: ${window.societeId}`);
        } else {
            console.log(`❌ ID de la société non trouvé`);
        }
    } else {
        console.log(`❌ Laravel Echo n'est pas configuré`);
    }
    
    // Fonction pour tester la soumission du formulaire
    window.testFormSubmission = function() {
        console.log('%c5. Test de soumission du formulaire', 'font-weight: bold; color: #4a148c;');
        const form = document.getElementById('solde-form');
        if (!form) {
            console.log(`❌ Formulaire non trouvé`);
            return;
        }
        
        // Récupérer les valeurs actuelles
        const soldeConges = document.getElementById('solde_conges').value;
        const soldeRtt = document.getElementById('solde_rtt').value;
        const soldeExceptionnels = document.getElementById('solde_conges_exceptionnels').value;
        
        console.log(`Valeurs actuelles: CP=${soldeConges}, RTT=${soldeRtt}, CE=${soldeExceptionnels}`);
        
        // Créer les données du formulaire
        const formData = new FormData(form);
        const url = form.getAttribute('action');
        
        // Ajouter le token CSRF si nécessaire
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }
        
        console.log(`Envoi de la requête à ${url}`);
        
        // Envoyer la requête AJAX
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log(`Statut de la réponse: ${response.status}`);
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.log(`Réponse non-JSON:`, text);
                    throw new Error('Réponse non-JSON');
                }
            });
        })
        .then(data => {
            console.log(`✅ Réponse reçue:`, data);
            if (data.success) {
                console.log(`✅ Mise à jour réussie!`);
            } else {
                console.log(`❌ Erreur lors de la mise à jour`);
            }
        })
        .catch(error => {
            console.error(`❌ Erreur:`, error);
        });
    };
    
    console.log('%c=== FIN DU DIAGNOSTIC ===', 'background: #4a148c; color: white; padding: 5px; font-weight: bold;');
    console.log('Pour tester la soumission du formulaire, exécutez: window.testFormSubmission()');
})();
