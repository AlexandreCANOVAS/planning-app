/**
 * Script pour gérer la soumission du formulaire de modification des soldes de congés
 */
// Variable globale pour indiquer que le script est chargé
window.soldeFormInitialized = false;

document.addEventListener('DOMContentLoaded', function() {
    const soldeForm = document.getElementById('solde-form');
    
    if (soldeForm) {
        // Marquer le formulaire comme initialisé par JavaScript
        soldeForm.setAttribute('data-js-initialized', 'true');
        window.soldeFormInitialized = true;
        
        soldeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Désactiver le bouton de soumission et afficher un indicateur de chargement
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement en cours...';
            
            // Récupérer les données du formulaire
            const formData = new FormData(this);
            
            // Vérifier si la fonction showToast existe
            if (typeof window.showToast !== 'function') {
                console.warn('La fonction showToast n\'est pas définie, création d\'une version de secours');
                window.showToast = function(message, type) {
                    console.log(`[${type}] ${message}`);
                    alert(message);
                };
            }
            
            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }
            
            // Envoyer la requête AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            // Essayer de parser le JSON
                            const data = JSON.parse(text);
                            console.log('Réponse d\'erreur du serveur:', data);
                            throw new Error(data.message || 'Erreur lors de la soumission du formulaire (Code ' + response.status + ')');
                        } catch (e) {
                            // Si ce n'est pas du JSON, renvoyer le texte brut
                            console.log('Réponse d\'erreur non-JSON:', text);
                            throw new Error('Erreur lors de la soumission du formulaire (Code ' + response.status + ')');
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse du serveur (complète):', data);
                console.log('Type de données des soldes:', {
                    'solde_conges': typeof data.employe.solde_conges,
                    'solde_rtt': typeof data.employe.solde_rtt,
                    'solde_conges_exceptionnels': typeof data.employe.solde_conges_exceptionnels
                });
                
                if (data.success) {
                    // Mettre à jour les soldes affichés
                    if (data.employe) {
                        const soldeConges = document.getElementById('current-solde-conges');
                        const soldeRtt = document.getElementById('current-solde-rtt');
                        const soldeExceptionnels = document.getElementById('current-solde-exceptionnels');
                        
                        // Logs de débogage pour comprendre pourquoi les congés payés ne sont pas mis à jour
                        console.log('Débogage soldes:', {
                            'soldeConges_element': soldeConges,
                            'soldeRtt_element': soldeRtt,
                            'soldeExceptionnels_element': soldeExceptionnels,
                            'data.employe.solde_conges': data.employe.solde_conges,
                            'data.employe.solde_rtt': data.employe.solde_rtt,
                            'data.employe.solde_conges_exceptionnels': data.employe.solde_conges_exceptionnels
                        });
                        
                        // Forcer la conversion en nombre pour tous les soldes
                        const soldeCongesValue = Number(data.employe.solde_conges);
                        const soldeRttValue = Number(data.employe.solde_rtt);
                        const soldeExceptionnelsValue = Number(data.employe.solde_conges_exceptionnels);
                        
                        // Récupérer les valeurs actuelles affichées pour comparer
                        const currentCongesText = soldeConges ? soldeConges.innerHTML.trim() : '';
                        const currentCongesValue = currentCongesText ? parseFloat(currentCongesText) : 0;
                        
                        console.log('Valeurs converties en nombre:', {
                            'soldeCongesValue': soldeCongesValue,
                            'soldeRttValue': soldeRttValue,
                            'soldeExceptionnelsValue': soldeExceptionnelsValue
                        });
                        
                        // Formater les nombres avec une décimale et ajouter "jours"
                        if (soldeConges) {
                            const newValue = soldeCongesValue.toFixed(1) + ' jours';
                            console.log('Mise à jour soldeConges de', soldeConges.innerHTML, 'à', newValue);
                            soldeConges.innerHTML = newValue;
                            
                            // Animation pour attirer l'attention
                            setTimeout(() => {
                                soldeConges.classList.add('bg-blue-100');
                                setTimeout(() => {
                                    soldeConges.classList.remove('bg-blue-100');
                                }, 1000);
                            }, 100);
                        }
                        if (soldeRtt) {
                            const newValue = soldeRttValue.toFixed(1) + ' jours';
                            console.log('Mise à jour soldeRtt de', soldeRtt.innerHTML, 'à', newValue);
                            soldeRtt.innerHTML = newValue;
                        }
                        if (soldeExceptionnels) {
                            const newValue = soldeExceptionnelsValue.toFixed(1) + ' jours';
                            console.log('Mise à jour soldeExceptionnels de', soldeExceptionnels.innerHTML, 'à', newValue);
                            soldeExceptionnels.innerHTML = newValue;
                        }
                    }
                    
                    // Afficher un message de succès
                    window.showToast(data.message || 'Soldes mis à jour avec succès', 'success');
                } else {
                    // Afficher un message d'erreur
                    window.showToast(data.message || 'Erreur lors de la mise à jour des soldes', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                window.showToast('Une erreur est survenue lors de la mise à jour des soldes: ' + error.message, 'error');
            })
            .finally(() => {
                // Réactiver le bouton de soumission
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    }
});
