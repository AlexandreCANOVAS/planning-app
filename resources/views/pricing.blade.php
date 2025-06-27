<x-guest-layout>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-purple-800 py-16 md:py-24">
        <div class="absolute inset-0">
            <div class="absolute inset-0 grid-background opacity-30"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/90 via-purple-600/90 to-purple-800/90"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Tarification simple et transparente</h1>
            <p class="text-xl text-indigo-100 max-w-3xl mx-auto">
                Une offre unique qui vous donne accès à toutes les fonctionnalités de Planify.
            </p>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center">
                <div class="w-full max-w-md transform hover:scale-105 transition-transform duration-300">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center">
                            <h3 class="text-2xl font-bold text-white">Offre Premium</h3>
                            <div class="mt-4 flex items-baseline justify-center">
                                <span class="text-5xl font-extrabold text-white">69,99€</span>
                                <span class="ml-1 text-xl text-indigo-100">/mois</span>
                            </div>
                            <p class="mt-4 text-indigo-100">Toutes les fonctionnalités incluses</p>
                        </div>
                        
                        <!-- Features -->
                        <div class="p-8">
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Gestion complète des plannings</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Gestion des congés et absences</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Suivi du temps de travail</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Rapports et analyses détaillés</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Gestion des équipes illimitée</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Support client prioritaire</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Mises à jour régulières</p>
                                </li>
                            </ul>
                            
                            <div class="mt-8">
                                <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 md:py-4 md:text-lg md:px-10 shadow-md transition-all duration-300">
                                    Commencer maintenant
                                </a>
                                <p class="mt-2 text-center text-sm text-gray-500">
                                    Essai gratuit de 14 jours, sans engagement
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="mt-20">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Questions fréquentes</h2>
                
                <div class="max-w-3xl mx-auto space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-medium text-gray-900">Puis-je annuler à tout moment ?</h3>
                        <p class="mt-2 text-gray-600">Oui, vous pouvez annuler votre abonnement à tout moment. Vous ne serez pas facturé pour le mois suivant.</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-medium text-gray-900">Y a-t-il une limite d'utilisateurs ?</h3>
                        <p class="mt-2 text-gray-600">Non, notre offre unique vous permet d'ajouter autant d'utilisateurs que nécessaire sans frais supplémentaires.</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-medium text-gray-900">Comment fonctionne l'essai gratuit ?</h3>
                        <p class="mt-2 text-gray-600">Vous bénéficiez de 14 jours d'essai gratuit avec accès à toutes les fonctionnalités. Aucune carte de crédit n'est requise pour commencer.</p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-medium text-gray-900">Proposez-vous un support technique ?</h3>
                        <p class="mt-2 text-gray-600">Oui, tous nos clients bénéficient d'un support technique prioritaire par email et chat en direct pendant les heures de bureau.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-12">
                <div class="text-center">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                        Prêt à optimiser la gestion de votre entreprise ?
                    </h2>
                    <p class="text-xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                        Rejoignez des milliers d'entreprises qui font confiance à Planify pour gérer leurs plannings, congés et temps de travail.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-lg font-semibold rounded-full text-white hover:bg-white hover:text-indigo-600 transition-all duration-300">
                            Commencer gratuitement
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold rounded-full text-white hover:bg-white/10 transition-all duration-300">
                            Nous contacter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-cookie-consent />
</x-guest-layout>
