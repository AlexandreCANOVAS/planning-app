<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tableau de bord') }}
            </h2>
            <div class="flex space-x-4">
                <span class="text-gray-500">{{ now()->format('d/m/Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->role === 'employeur')
                @if(auth()->user()->societe)
                    <!-- En-tête société -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ auth()->user()->societe->nom }}</h3>
                                <p class="text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('societes.edit', auth()->user()->societe) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-cog"></i> Paramètres
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 mr-4">
                                    <i class="fas fa-users text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Employés</p>
                                    <p class="text-2xl font-bold">{{ auth()->user()->societe->employes->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 mr-4">
                                    <i class="fas fa-calendar text-green-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Plannings actifs</p>
                                    <p class="text-2xl font-bold">{{ auth()->user()->societe->plannings()->whereMonth('date', now()->month)->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                                    <i class="fas fa-clock text-yellow-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Heures ce mois</p>
                                    <p class="text-2xl font-bold">{{ auth()->user()->societe->plannings()->whereMonth('date', now()->month)->sum('heures_travaillees') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 mr-4">
                                    <i class="fas fa-umbrella-beach text-red-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Congés en attente</p>
                                    <p class="text-2xl font-bold">{{ auth()->user()->societe->conges()->where('statut', 'en_attente')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h4 class="text-lg font-semibold mb-4">Actions rapides</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="{{ route('employes.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-user-plus text-blue-500 mr-3"></i>
                                    <span>Ajouter un employé</span>
                                </a>
                                <a href="{{ route('plannings.create_monthly') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-calendar-plus text-green-500 mr-3"></i>
                                    <span>Créer un planning mensuel</span>
                                </a>
                                <a href="{{ route('plannings.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-calendar-plus text-green-500 mr-2"></i>
                                    <span>Créer un planning</span>
                                </a>
                                <a href="{{ route('conges.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-plus-circle text-yellow-500 mr-2"></i>
                                    <span>Gérer les congés</span>
                                </a>
                                <a href="{{ route('comptabilite.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-calculator text-red-500 mr-2"></i>
                                    <span>Voir la comptabilité</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                        <div class="max-w-md mx-auto">
                            <i class="fas fa-building text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-xl font-semibold mb-2">Bienvenue sur votre espace employeur</h3>
                            <p class="text-gray-600 mb-6">Pour commencer, créez votre société pour pouvoir gérer vos employés et leurs plannings.</p>
                            <a href="{{ route('societes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Créer ma société
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <!-- Tableau de bord employé -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Carte Planning -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Mon Planning</h3>
                            <a href="{{ route('employe.plannings.index') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <p class="text-gray-600 mb-4">Consultez votre planning et vos horaires de travail.</p>
                        <a href="{{ route('employe.plannings.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Voir mon planning
                        </a>
                    </div>

                    <!-- Carte Congés -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Mes Congés</h3>
                            <a href="{{ route('employe.mes-conges') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <p class="text-gray-600 mb-4">Gérez vos demandes de congés et consultez leur statut.</p>
                        <a href="{{ route('employe.mes-conges') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Gérer mes congés
                        </a>
                    </div>

                    <!-- Carte Profil -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Mon Profil</h3>
                            <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <p class="text-gray-600 mb-4">Mettez à jour vos informations personnelles.</p>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Modifier mon profil
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
