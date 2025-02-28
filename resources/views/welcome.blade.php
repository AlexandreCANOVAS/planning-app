<x-guest-layout>
    <!-- Hero Section avec animation et gradient moderne -->
    <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-purple-800 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 grid-background opacity-30"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/90 via-purple-600/90 to-purple-800/90"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-8 animate-fade-in">
                    Simplifiez la gestion <br class="hidden sm:block" />de votre entreprise
                </h1>
                <p class="text-xl md:text-2xl text-indigo-100 mb-12 max-w-3xl mx-auto">
                    Une solution tout-en-un pour gérer efficacement vos plannings, 
                    congés et temps de travail. Optimisez votre productivité dès aujourd'hui.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 border-2 border-white text-lg font-semibold rounded-full text-white hover:bg-white hover:text-indigo-600 transition-all duration-300">
                        Commencer gratuitement
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#features" class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-full text-white hover:bg-white/10 transition-all duration-300">
                        Découvrir les fonctionnalités
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Caractéristiques avec des icônes modernes -->
    <div id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Une suite complète d'outils pour votre entreprise
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Des fonctionnalités puissantes et intuitives pour optimiser la gestion de vos équipes
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <!-- Planning intelligent -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Planning intelligent</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Créez et gérez facilement les emplois du temps de vos équipes. 
                        Interface intuitive et visualisation claire des plannings.
                    </p>
                </div>

                <!-- Gestion des congés -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestion des congés</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Simplifiez le processus de demande et de validation des congés.
                        Suivez facilement les soldes de congés de vos employés.
                    </p>
                </div>

                <!-- Suivi du temps -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-pink-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Suivi du temps</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Analysez le temps de travail et optimisez la productivité.
                        Générez des rapports détaillés en quelques clics.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Avantages avec statistiques -->
    <div class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">
                        Pourquoi choisir Planify ?
                    </h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold text-gray-900">Gain de temps considérable</h3>
                                <p class="mt-2 text-gray-600">Automatisez vos tâches administratives et gagnez jusqu'à 10 heures par semaine.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold text-gray-900">Sécurité maximale</h3>
                                <p class="mt-2 text-gray-600">Vos données sont cryptées et sécurisées selon les normes les plus strictes.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <div class="bg-gray-50 rounded-2xl p-8 text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">98%</div>
                        <p class="text-gray-600">Taux de satisfaction client</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8 text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">10h</div>
                        <p class="text-gray-600">Économisées par semaine</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8 text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">1000+</div>
                        <p class="text-gray-600">Entreprises nous font confiance</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8 text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">24/7</div>
                        <p class="text-gray-600">Support client disponible</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section CTA -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-12">
                <div class="text-center">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                        Prêt à optimiser la gestion de votre entreprise ?
                    </h2>
                    <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                        Commencez dès aujourd'hui avec notre offre d'essai gratuite de 30 jours.
                        Aucune carte de crédit requise.
                    </p>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 border-2 border-white text-lg font-semibold rounded-full text-indigo-600 bg-white hover:bg-indigo-50 transition-all duration-300">
                        Essayer gratuitement
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ajout du style pour l'animation -->
    <style>
        .grid-background {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='grid' width='10' height='10' patternUnits='userSpaceOnUse'%3E%3Cpath d='M 10 0 L 0 0 0 10' fill='none' stroke='white' stroke-width='0.5' stroke-opacity='0.2'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23grid)'/%3E%3C/svg%3E");
        }
        .animate-fade-in {
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-guest-layout>