<x-guest-layout>
    <div class="bg-gradient-to-b from-indigo-50 to-white">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight">
                        Des tarifs adaptés à vos besoins
                    </h1>
                    <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-500">
                        Choisissez le plan qui correspond le mieux à votre entreprise
                    </p>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Gratuit Plan -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Gratuit</h3>
                        <div class="flex items-baseline mb-8">
                            <span class="text-5xl font-extrabold tracking-tight text-gray-900">0€</span>
                            <span class="text-gray-500 ml-1">/mois</span>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">Jusqu'à 5 employés</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">Gestion des plannings basique</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">Gestion des congés</span>
                            </li>
                        </ul>
                        <div class="mt-8">
                            <a href="{{ route('register') }}" 
                               class="block w-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 py-3 px-4 rounded-lg text-center font-medium transition duration-150 ease-in-out">
                                Commencer gratuitement
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="bg-gradient-to-b from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden transform hover:-translate-y-1 transition-all duration-300 hover:shadow-2xl relative">
                    <div class="absolute top-0 right-0 mt-4 mr-4">
                        <span class="bg-white px-3 py-1 rounded-full text-sm font-medium text-purple-600">
                            Populaire
                        </span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-white mb-4">Pro</h3>
                        <div class="flex items-baseline mb-8">
                            <span class="text-5xl font-extrabold tracking-tight text-white">29€</span>
                            <span class="text-indigo-200 ml-1">/mois</span>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-indigo-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-indigo-100">Jusqu'à 20 employés</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-indigo-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-indigo-100">Toutes les fonctionnalités gratuites</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-indigo-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-indigo-100">Export de données</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-indigo-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-indigo-100">Support prioritaire</span>
                            </li>
                        </ul>
                        <div class="mt-8">
                            <a href="{{ route('register') }}" 
                               class="block w-full bg-white text-indigo-600 hover:bg-indigo-50 py-3 px-4 rounded-lg text-center font-medium transition duration-150 ease-in-out">
                                Choisir Pro
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Entreprise</h3>
                        <div class="flex items-baseline mb-8">
                            <span class="text-5xl font-extrabold tracking-tight text-gray-900">99€</span>
                            <span class="text-gray-500 ml-1">/mois</span>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">Employés illimités</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">Toutes les fonctionnalités Pro</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">API personnalisée</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-600">Support dédié 24/7</span>
                            </li>
                        </ul>
                        <div class="mt-8">
                            <a href="{{ route('contact') }}" 
                               class="block w-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 py-3 px-4 rounded-lg text-center font-medium transition duration-150 ease-in-out">
                                Contacter les ventes
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-24">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Questions fréquentes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Puis-je changer de forfait à tout moment ?</h3>
                        <p class="text-gray-600">Oui, vous pouvez passer à un forfait supérieur ou inférieur à tout moment. La facturation sera ajustée au prorata.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Y a-t-il un engagement minimum ?</h3>
                        <p class="text-gray-600">Non, nos forfaits sont sans engagement. Vous pouvez annuler à tout moment.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Comment fonctionne la période d'essai ?</h3>
                        <p class="text-gray-600">Vous bénéficiez de 14 jours d'essai gratuit sur tous nos forfaits payants, sans engagement.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Le support est-il inclus ?</h3>
                        <p class="text-gray-600">Oui, tous les forfaits incluent un support. Le niveau de support varie selon le forfait choisi.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-24 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-12 md:p-12 text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">Vous ne savez pas quel forfait choisir ?</h2>
                    <p class="text-lg text-indigo-100 mb-8">
                        Contactez notre équipe commerciale pour obtenir des conseils personnalisés
                    </p>
                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition duration-150 ease-in-out">
                        Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
