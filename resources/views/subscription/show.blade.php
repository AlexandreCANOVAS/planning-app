<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Abonnement') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notification d'alerte -->
            @if (session('warning'))
                <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-yellow-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            <!-- En-tête de la page -->
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold text-purple-800 mb-3">Abonnement Premium</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Accédez à toutes les fonctionnalités de Planify pour gérer efficacement vos plannings et vos employés.
                </p>
            </div>

            <!-- Carte d'abonnement -->
            <div class="mb-12 max-w-3xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all hover:scale-[1.01]">
                    <!-- En-tête de la carte -->
                    <div class="bg-gradient-to-r from-purple-800 to-purple-600 p-6 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold">Plan Premium</h2>
                                <p class="text-purple-200 mt-1">Accès complet à toutes les fonctionnalités</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">99,99€</div>
                                <div class="text-purple-200">par mois</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Corps de la carte -->
                    <div class="p-8">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Nombre illimité d'employés</span>
                                    <p class="text-sm text-gray-500">Ajoutez autant d'employés que nécessaire sans frais supplémentaires</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Gestion complète des plannings</span>
                                    <p class="text-sm text-gray-500">Créez et gérez facilement les plannings de tous vos employés</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Suivi des congés et absences</span>
                                    <p class="text-sm text-gray-500">Gérez efficacement les demandes de congés et les absences</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Export des données comptables</span>
                                    <p class="text-sm text-gray-500">Générez des rapports détaillés pour votre comptabilité</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Support technique prioritaire</span>
                                    <p class="text-sm text-gray-500">Bénéficiez d'une assistance rapide et personnalisée</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section de paiement -->
            <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden mb-12">
                <div class="bg-gradient-to-r from-purple-700 to-purple-500 p-6">
                    <h3 class="text-xl font-bold text-white">Informations de paiement</h3>
                    <p class="text-purple-100 text-sm mt-1">Vos données de paiement sont sécurisées avec Stripe</p>
                </div>
                
                <div class="p-8">
                    <form id="payment-form" action="{{ route('subscription.create') }}" method="POST">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">
                                <div class="flex items-center mb-2">
                                    <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">Une erreur est survenue</span>
                                </div>
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-6">
                            <label for="card-holder-name" class="block text-sm font-medium text-gray-700 mb-2">Nom sur la carte</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input id="card-holder-name" type="text" class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Jean Dupont" required>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="card-element" class="block text-sm font-medium text-gray-700 mb-2">Carte de crédit</label>
                            <div id="card-element" class="p-4 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></div>
                            <div id="card-errors" class="mt-2 text-red-600 text-sm" role="alert"></div>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <svg class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Vos informations de paiement sont chiffrées et sécurisées
                            </p>
                        </div>

                        <input type="hidden" name="payment_method" id="payment-method">

                        <div class="flex justify-center">
                            <button id="card-button" data-secret="{{ $intent->client_secret }}" type="button" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-purple-700 to-purple-500 hover:from-purple-800 hover:to-purple-600 text-white font-bold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 flex items-center justify-center">
                                <span>S'abonner maintenant</span>
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de Stripe
            const stripe = Stripe('{{ config('services.stripe.key') }}');
            
            // Configuration des éléments Stripe avec un style personnalisé
            const elements = stripe.elements({
                fonts: [
                    {
                        cssSrc: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap',
                    },
                ],
            });
            
            // Style personnalisé pour l'élément de carte
            const style = {
                base: {
                    color: document.querySelector('html').classList.contains('dark') ? '#fff' : '#424770',
                    fontFamily: '"Inter", system-ui, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: document.querySelector('html').classList.contains('dark') ? '#9ca3af' : '#aab7c4',
                    },
                    ':-webkit-autofill': {
                        color: document.querySelector('html').classList.contains('dark') ? '#fff' : '#424770',
                    },
                },
                invalid: {
                    color: '#ef4444',
                    iconColor: '#ef4444',
                },
            };
            
            // Création de l'élément de carte avec le style personnalisé
            const cardElement = elements.create('card', { style: style });
            
            // Récupération des éléments du DOM
            const cardHolderName = document.getElementById('card-holder-name');
            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;
            const paymentForm = document.getElementById('payment-form');
            const paymentMethodInput = document.getElementById('payment-method');
            const cardErrors = document.getElementById('card-errors');
            
            // S'assurer que l'élément de carte existe avant de le monter
            const cardElementContainer = document.getElementById('card-element');
            if (cardElementContainer) {
                // Montage de l'élément de carte dans le DOM
                cardElement.mount('#card-element');
                
                // Gestion des erreurs de validation de la carte
                cardElement.addEventListener('change', function(event) {
                    if (event.error) {
                        showError(event.error.message);
                    } else {
                        clearError();
                    }
                });
            } else {
                console.error("L'élément #card-element n'a pas été trouvé dans le DOM");
                return;
            }
            
            // Fonction pour afficher les erreurs
            function showError(message) {
                if (cardErrors) {
                    cardErrors.innerHTML = `
                        <div class="flex items-center text-red-600">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>${message}</span>
                        </div>
                    `;
                }
            }
            
            // Fonction pour effacer les erreurs
            function clearError() {
                if (cardErrors) {
                    cardErrors.innerHTML = '';
                }
            }
            
            // Fonction pour mettre à jour l'état du bouton
            function updateButtonState(isLoading) {
                if (!cardButton) return;
                
                if (isLoading) {
                    cardButton.disabled = true;
                    cardButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Traitement en cours...</span>
                    `;
                } else {
                    cardButton.disabled = false;
                    cardButton.innerHTML = `
                        <span>S'abonner maintenant</span>
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    `;
                }
            }
            
            // Vérifier que tous les éléments nécessaires sont présents
            if (!cardButton || !paymentForm || !paymentMethodInput) {
                console.error("Un ou plusieurs éléments nécessaires n'ont pas été trouvés dans le DOM");
                return;
            }
            
            // Gestion du clic sur le bouton de paiement
            cardButton.addEventListener('click', async (e) => {
                e.preventDefault();
                clearError();
                
                // Vérifier que l'élément de carte est toujours monté
                if (!document.getElementById('card-element')) {
                    showError("Une erreur technique est survenue. Veuillez rafraîchir la page et réessayer.");
                    return;
                }
                
                // Validation du nom sur la carte
                if (!cardHolderName.value.trim()) {
                    showError('Veuillez entrer le nom figurant sur la carte.');
                    cardHolderName.focus();
                    return;
                }
                
                // Mise à jour de l'état du bouton (chargement)
                updateButtonState(true);
                
                try {
                    // Confirmation de la configuration de la carte avec Stripe
                    const { setupIntent, error } = await stripe.confirmCardSetup(
                        clientSecret, {
                            payment_method: {
                                card: cardElement,
                                billing_details: { name: cardHolderName.value }
                            }
                        }
                    );
                    
                    // Gestion des erreurs de paiement
                    if (error) {
                        showError(error.message);
                        updateButtonState(false);
                    } else if (setupIntent && setupIntent.payment_method) {
                        // Soumission du formulaire si tout est OK
                        paymentMethodInput.value = setupIntent.payment_method;
                        paymentForm.submit();
                    } else {
                        showError('Une erreur inattendue est survenue. Veuillez réessayer.');
                        updateButtonState(false);
                    }
                } catch (err) {
                    console.error(err);
                    showError('Une erreur est survenue lors du traitement de votre paiement. Veuillez réessayer.');
                    updateButtonState(false);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
