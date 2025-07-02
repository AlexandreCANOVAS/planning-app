<x-guest-layout>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour afficher/masquer les réponses aux questions
            window.toggleFaq = function(id) {
                const element = document.getElementById(id);
                const icon = document.getElementById('icon-' + id);
                
                if (element.classList.contains('hidden')) {
                    element.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    element.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            };
            
            // Fonction pour filtrer les questions par catégorie
            window.filterCategory = function(category) {
                // Mettre à jour les boutons actifs
                const buttons = document.querySelectorAll('.category-btn');
                buttons.forEach(button => {
                    button.classList.remove('active', 'bg-purple-100', 'text-purple-700');
                    button.classList.add('bg-gray-100', 'text-gray-700');
                });
                
                const activeButton = document.querySelector(`[onclick="filterCategory('${category}')"]`);
                activeButton.classList.add('active', 'bg-purple-100', 'text-purple-700');
                activeButton.classList.remove('bg-gray-100', 'text-gray-700');
                
                // Filtrer les questions
                const items = document.querySelectorAll('.faq-item');
                if (category === 'all') {
                    items.forEach(item => {
                        item.style.display = 'block';
                    });
                } else {
                    items.forEach(item => {
                        if (item.classList.contains(category)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            };
        });
    </script>
    
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Foire Aux Questions
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Trouvez des réponses à toutes vos questions sur Planify
                </p>
            </div>

            <div class="mt-12">
                <!-- Catégories de FAQ -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button onclick="filterCategory('all')" class="category-btn active px-4 py-2 bg-purple-100 text-purple-700 rounded-full hover:bg-purple-200 transition-colors">
                        Toutes les questions
                    </button>
                    <button onclick="filterCategory('general')" class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-purple-100 hover:text-purple-700 transition-colors">
                        Général
                    </button>
                    <button onclick="filterCategory('planning')" class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-purple-100 hover:text-purple-700 transition-colors">
                        Planning
                    </button>
                    <button onclick="filterCategory('conges')" class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-purple-100 hover:text-purple-700 transition-colors">
                        Congés
                    </button>
                    <button onclick="filterCategory('compta')" class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-purple-100 hover:text-purple-700 transition-colors">
                        Comptabilité
                    </button>
                    <button onclick="filterCategory('tech')" class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-purple-100 hover:text-purple-700 transition-colors">
                        Support technique
                    </button>
                    <button onclick="filterCategory('abonnement')" class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-purple-100 hover:text-purple-700 transition-colors">
                        Abonnement
                    </button>
                </div>

                <!-- Questions et réponses -->
                <div class="space-y-6">
                    <!-- Général -->
                    <div class="faq-item general">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-1')">
                                <span class="text-lg font-medium text-gray-900">Qu'est-ce que Planify ?</span>
                                <svg id="icon-faq-1" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-1" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Planify est une plateforme complète de gestion des ressources humaines conçue pour les entreprises de toutes tailles. Notre solution permet de gérer efficacement les plannings, les congés, les fiches de paie et la comptabilité liée aux ressources humaines, le tout dans une interface intuitive et moderne.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item general">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-2')">
                                <span class="text-lg font-medium text-gray-900">Comment puis-je m'inscrire à Planify ?</span>
                                <svg id="icon-faq-2" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-2" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour vous inscrire, cliquez sur le bouton "S'inscrire" en haut à droite de la page d'accueil. Remplissez le formulaire avec les informations de votre entreprise et créez votre compte administrateur. Vous pourrez ensuite ajouter des employés et configurer votre espace selon vos besoins.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Planning -->
                    <div class="faq-item planning">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-3')">
                                <span class="text-lg font-medium text-gray-900">Comment créer un planning pour mon équipe ?</span>
                                <svg id="icon-faq-3" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-3" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour créer un planning, connectez-vous à votre compte administrateur, accédez à la section "Plannings" depuis le tableau de bord, puis cliquez sur le bouton "Créer un planning". Suivez les instructions pour sélectionner les employés, définir les horaires et les lieux de travail. Vous pouvez créer des plannings hebdomadaires ou mensuels selon vos besoins.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item planning">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-4')">
                                <span class="text-lg font-medium text-gray-900">Comment modifier un planning existant ?</span>
                                <svg id="icon-faq-4" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-4" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour modifier un planning existant, accédez à la section "Plannings", sélectionnez le planning concerné, puis cliquez sur "Modifier". Vous pouvez ajuster les horaires, changer les employés assignés ou modifier les lieux de travail. N'oubliez pas de sauvegarder vos modifications et de notifier les employés concernés si nécessaire.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Congés -->
                    <div class="faq-item conges">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-5')">
                                <span class="text-lg font-medium text-gray-900">Comment gérer les demandes de congés ?</span>
                                <svg id="icon-faq-5" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-5" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour gérer les congés, accédez à la section "Congés" depuis le tableau de bord. Vous y trouverez toutes les demandes en attente, que vous pouvez approuver ou refuser. Le système calcule automatiquement les soldes de congés et met à jour les plannings en conséquence. Les employés sont notifiés automatiquement de la décision.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Comptabilité -->
                    <div class="faq-item compta">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-6')">
                                <span class="text-lg font-medium text-gray-900">Comment exporter les données de comptabilité ?</span>
                                <svg id="icon-faq-6" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-6" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour exporter les données de comptabilité, accédez à la section "Comptabilité", sélectionnez la période souhaitée (semaine, mois, trimestre ou année), puis cliquez sur "Exporter". Vous pouvez choisir entre différents formats (PDF, Excel, CSV) selon vos besoins et vos logiciels de comptabilité.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Support technique -->
                    <div class="faq-item tech">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-7')">
                                <span class="text-lg font-medium text-gray-900">Comment contacter le support technique ?</span>
                                <svg id="icon-faq-7" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-7" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Vous pouvez contacter notre support technique par email à support@planify.fr, par téléphone au +33 1 23 45 67 89 (du lundi au vendredi, de 9h à 18h), ou via le chat en direct disponible dans votre espace administrateur. Notre équipe s'engage à vous répondre dans les 24 heures ouvrées.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Questions supplémentaires -->
                    <div class="faq-item tech">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-8')">
                                <span class="text-lg font-medium text-gray-900">Comment réinitialiser mon mot de passe ?</span>
                                <svg id="icon-faq-8" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-8" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour réinitialiser votre mot de passe, cliquez sur "Mot de passe oublié" sur la page de connexion. Entrez votre adresse email et vous recevrez un lien pour créer un nouveau mot de passe. Si vous êtes déjà connecté, vous pouvez également modifier votre mot de passe depuis votre profil dans les paramètres de votre compte.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item planning">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-9')">
                                <span class="text-lg font-medium text-gray-900">Comment gérer les échanges de planning entre employés ?</span>
                                <svg id="icon-faq-9" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-9" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Planify permet aux employés de proposer des échanges de planning entre eux. Pour activer cette fonctionnalité, accédez aux paramètres de l'application et activez l'option "Autoriser les échanges". Les employés pourront alors proposer des échanges depuis leur calendrier personnel. En tant qu'administrateur, vous recevrez des notifications pour approuver ou refuser ces demandes d'échange.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item compta">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-10')">
                                <span class="text-lg font-medium text-gray-900">Comment gérer les heures supplémentaires ?</span>
                                <svg id="icon-faq-10" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-10" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Planify détecte automatiquement les heures supplémentaires en fonction des horaires planifiés et des heures effectivement travaillées. Vous pouvez configurer les règles de calcul des heures supplémentaires dans les paramètres de comptabilité, en définissant les seuils hebdomadaires ou mensuels. Les heures supplémentaires sont automatiquement incluses dans les rapports comptables et les fiches de paie.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item conges">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-11')">
                                <span class="text-lg font-medium text-gray-900">Comment configurer les types de congés ?</span>
                                <svg id="icon-faq-11" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-11" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour configurer les types de congés, accédez à la section "Paramètres" puis "Types de congés". Vous pouvez y créer des catégories personnalisées (congés payés, RTT, congés maladie, etc.) et définir pour chacune les règles d'acquisition, les plafonds, et si ces congés sont payés ou non. Vous pouvez également définir des règles spécifiques par type de contrat ou par ancienneté.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Abonnement -->
                    <div class="faq-item abonnement">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-12')">
                                <span class="text-lg font-medium text-gray-900">Comment fonctionne l'abonnement à Planify ?</span>
                                <svg id="icon-faq-12" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-12" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Planify fonctionne sur un modèle d'abonnement mensuel. Vous payez un tarif fixe de 99,99€ par mois qui vous donne accès à toutes les fonctionnalités premium de la plateforme. L'abonnement est automatiquement renouvelé chaque mois, mais vous pouvez l'annuler à tout moment depuis votre espace de gestion d'abonnement.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item abonnement">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-13')">
                                <span class="text-lg font-medium text-gray-900">Comment puis-je annuler mon abonnement ?</span>
                                <svg id="icon-faq-13" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-13" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour annuler votre abonnement, connectez-vous à votre compte, accédez à la section "Mon abonnement" depuis votre tableau de bord, puis cliquez sur le bouton "Annuler mon abonnement". Votre abonnement restera actif jusqu'à la fin de la période de facturation en cours, après quoi il ne sera pas renouvelé. Vous pouvez également reprendre votre abonnement à tout moment avant la fin de la période.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item abonnement">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-14')">
                                <span class="text-lg font-medium text-gray-900">Quels moyens de paiement acceptez-vous ?</span>
                                <svg id="icon-faq-14" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-14" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Nous acceptons les cartes de crédit et de débit principales (Visa, Mastercard, American Express). Le paiement est sécurisé via Stripe, l'un des leaders mondiaux du paiement en ligne. Vos informations de paiement sont cryptées et ne sont jamais stockées sur nos serveurs.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item abonnement">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-15')">
                                <span class="text-lg font-medium text-gray-900">Puis-je obtenir une facture pour mon abonnement ?</span>
                                <svg id="icon-faq-15" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-15" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Oui, une facture est automatiquement générée et disponible dans votre espace de gestion d'abonnement après chaque paiement. Vous pouvez les consulter, les télécharger au format PDF ou les recevoir par email. Toutes nos factures sont conformes à la législation française et incluent la TVA.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item abonnement">
                        <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-6">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-16')">
                                <span class="text-lg font-medium text-gray-900">Y a-t-il une période d'essai gratuite ?</span>
                                <svg id="icon-faq-16" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-16" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Nous proposons une période d'essai gratuite de 14 jours pour tous les nouveaux utilisateurs. Pendant cette période, vous avez accès à toutes les fonctionnalités premium sans engagement. À la fin de la période d'essai, vous pouvez choisir de souscrire à l'abonnement ou votre compte sera automatiquement rétrogradé vers la version gratuite avec des fonctionnalités limitées.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vous n'avez pas trouvé votre réponse ? -->
                <div class="mt-16 bg-purple-50 p-8 rounded-lg shadow-sm border border-purple-100 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Vous n'avez pas trouvé votre réponse ?</h2>
                    <p class="text-gray-600 mb-6">
                        Notre équipe de support est là pour vous aider avec toutes vos questions.
                    </p>
                    <a href="#" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Contacter le support
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
