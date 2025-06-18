<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des employés') }}
            </h2>
            <a href="{{ route('employes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                <i class="fas fa-user-plus mr-2"></i>
                {{ __('Ajouter un employé') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tableau de bord statistique -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tableau de bord des employés</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Nombre total d'employés -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 mr-4">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-600">Nombre d'employés</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalEmployes }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Taux d'occupation -->
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 mr-4">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-600">Taux d'occupation</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $tauxOccupation }}%</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Congés en cours -->
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 mr-4">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-600">Congés en cours</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $congesEnCours }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Congés à venir -->
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 mr-4">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-purple-600">Congés à venir</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $congesAVenir }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Barre de recherche et options d'affichage -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
                <form action="{{ route('employes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom, prénom ou email..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Rechercher
                        </button>
                        @if($search)
                            <a href="{{ route('employes.index', ['view_mode' => $viewMode]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
                
                <!-- Options d'affichage -->
                <div class="mt-4 flex justify-between items-center border-t border-gray-200 pt-4">
                    <div class="text-sm text-gray-500">
                        {{ isset($employes) && count($employes) > 0 ? (method_exists($employes, 'total') ? $employes->total() : count($employes)) : '0' }} employé(s) trouvé(s)
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('employes.index', ['view_mode' => 'grid', 'search' => $search]) }}" class="px-3 py-1.5 rounded-md {{ $viewMode === 'grid' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }} hover:bg-blue-50 transition-colors duration-150">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </a>
                        <a href="{{ route('employes.index', ['view_mode' => 'list', 'search' => $search]) }}" class="px-3 py-1.5 rounded-md {{ $viewMode === 'list' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }} hover:bg-blue-50 transition-colors duration-150">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </a>
                        <a href="{{ route('employes.index', ['view_mode' => 'cards', 'search' => $search]) }}" class="px-3 py-1.5 rounded-md {{ $viewMode === 'cards' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }} hover:bg-blue-50 transition-colors duration-150">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </a>
                        <div class="flex space-x-2">
                            <!-- Bouton des détails -->
                            <div class="relative" x-data="{openDetails: false}">
                                <button @click="openDetails = !openDetails" class="px-3 py-1.5 rounded-md bg-purple-100 text-purple-700 hover:bg-purple-50 transition-colors duration-150">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </button>
                                <div x-show="openDetails" @click.away="openDetails = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <p class="px-4 py-2 text-xs font-semibold text-gray-500">Détails par employé</p>
                                        @foreach($employes as $emp)
                                            <a href="{{ route('employes.show', $emp) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">
                                                {{ $emp->prenom }} {{ $emp->nom }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bouton des formations -->
                            <div class="relative" x-data="{openFormations: false}">
                                <button @click="openFormations = !openFormations" class="px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-50 transition-colors duration-150">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                </button>
                                <div x-show="openFormations" @click.away="openFormations = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <p class="px-4 py-2 text-xs font-semibold text-gray-500">Formations par employé</p>
                                        @foreach($employes as $emp)
                                            <a href="{{ route('employes.formations', $emp) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50">
                                                {{ $emp->prenom }} {{ $emp->nom }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bouton des statistiques -->
                            <div class="relative" x-data="{openStats: false}">
                                <button @click="openStats = !openStats" class="px-3 py-1.5 rounded-md bg-gray-100 text-gray-700 hover:bg-blue-50 transition-colors duration-150">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </button>
                                <div x-show="openStats" @click.away="openStats = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <p class="px-4 py-2 text-xs font-semibold text-gray-500">Statistiques par employé</p>
                                        @foreach($employes as $emp)
                                            <a href="{{ route('employes.stats', $emp) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                                {{ $emp->prenom }} {{ $emp->nom }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($viewMode === 'grid')
                <!-- Vue en grille -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if(isset($employes) && count($employes) > 0)
                        @foreach($employes as $employe)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center">
                                            <h3 class="text-lg font-semibold text-gray-800">{{ $employe->prenom }} {{ $employe->nom }}</h3>
                                            @php
                                                $statutClass = [
                                                    'disponible' => 'bg-green-100 text-green-800',
                                                    'en_conge' => 'bg-yellow-100 text-yellow-800',
                                                    'en_service' => 'bg-blue-100 text-blue-800'
                                                ][$employe->statut_actuel] ?? 'bg-gray-100 text-gray-800';
                                                
                                                $statutLabel = [
                                                    'disponible' => 'En repos',
                                                    'en_conge' => 'En congé',
                                                    'en_service' => 'En service'
                                                ][$employe->statut_actuel] ?? 'Indéterminé';
                                            @endphp
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutClass }}">
                                                {{ $statutLabel }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm mt-1">{{ $employe->email }}</p>
                                        @if($employe->telephone)
                                            <p class="text-gray-600 text-sm">{{ $employe->telephone }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-1">
                                        <a href="{{ route('employes.edit', $employe) }}" class="text-blue-600 hover:text-blue-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('employes.formations', $employe) }}" class="text-green-600 hover:text-green-800" title="Voir les formations et diplômes">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('employes.destroy', $employe) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Indicateurs visuels -->
                                <div class="mt-4 space-y-3">
                                    <!-- Charge de travail -->
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <h4 class="text-xs font-medium text-gray-700">Charge de travail</h4>
                                            <span class="text-xs font-medium {{ $employe->charge_de_travail > 80 ? 'text-red-600' : ($employe->charge_de_travail > 50 ? 'text-yellow-600' : 'text-green-600') }}">{{ $employe->charge_de_travail }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $employe->charge_de_travail > 80 ? 'bg-red-500' : ($employe->charge_de_travail > 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $employe->charge_de_travail }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Progression des formations -->
                                    @if(isset($employe->formations) && $employe->formations->isNotEmpty())
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <h4 class="text-xs font-medium text-gray-700">Formations</h4>
                                                <span class="text-xs font-medium {{ $employe->progression_formations < 50 ? 'text-red-600' : ($employe->progression_formations < 80 ? 'text-yellow-600' : 'text-green-600') }}">{{ $employe->progression_formations }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $employe->progression_formations < 50 ? 'bg-red-500' : ($employe->progression_formations < 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $employe->progression_formations }}%"></div>
                                            </div>
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @foreach($employe->formations as $formation)
                                                    @php
                                                        $isExpired = $formation->pivot->date_recyclage && \Carbon\Carbon::parse($formation->pivot->date_recyclage)->isPast();
                                                        $badgeClass = $isExpired ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badgeClass }}">
                                                        {{ $formation->nom }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
                
            @elseif($viewMode === 'list')
                <!-- Vue en liste -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charge de travail</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formations</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($employes as $employe)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $employe->prenom }} {{ $employe->nom }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $employe->email }}</div>
                                        @if($employe->telephone)
                                            <div class="text-sm text-gray-500">{{ $employe->telephone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statutClass = [
                                                'disponible' => 'bg-green-100 text-green-800',
                                                'en_conge' => 'bg-yellow-100 text-yellow-800',
                                                'en_service' => 'bg-blue-100 text-blue-800'
                                            ][$employe->statut_actuel] ?? 'bg-gray-100 text-gray-800';
                                            
                                            $statutLabel = [
                                                'disponible' => 'En repos',
                                                'en_conge' => 'En congé',
                                                'en_service' => 'En service'
                                            ][$employe->statut_actuel] ?? 'Indéterminé';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutClass }}">
                                            {{ $statutLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full max-w-xs">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-xs font-medium {{ $employe->charge_de_travail > 80 ? 'text-red-600' : ($employe->charge_de_travail > 50 ? 'text-yellow-600' : 'text-green-600') }}">{{ $employe->charge_de_travail }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $employe->charge_de_travail > 80 ? 'bg-red-500' : ($employe->charge_de_travail > 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $employe->charge_de_travail }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(isset($employe->formations) && $employe->formations->isNotEmpty())
                                            <div class="mb-2">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-xs font-medium {{ $employe->progression_formations < 50 ? 'text-red-600' : ($employe->progression_formations < 80 ? 'text-yellow-600' : 'text-green-600') }}">{{ $employe->progression_formations }}% à jour</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $employe->progression_formations < 50 ? 'bg-red-500' : ($employe->progression_formations < 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $employe->progression_formations }}%"></div>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($employe->formations as $formation)
                                                    @php
                                                        $isExpired = $formation->pivot->date_recyclage && \Carbon\Carbon::parse($formation->pivot->date_recyclage)->isPast();
                                                        $badgeClass = $isExpired ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badgeClass }}" title="{{ $isExpired ? 'Recyclage requis' : 'À jour' }}">
                                                        {{ $formation->nom }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500">Aucune formation</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('employes.edit', $employe) }}" class="text-blue-600 hover:text-blue-900">Modifier</a>
                                            <a href="{{ route('employes.formations', $employe) }}" class="text-green-600 hover:text-green-900">Formations</a>
                                            <form action="{{ route('employes.destroy', $employe) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
            @elseif($viewMode === 'cards')
                <!-- Vue en trombinoscope -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($employes as $employe)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col items-center p-4">
                            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center mb-3">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-center font-medium text-gray-800">{{ $employe->prenom }} {{ $employe->nom }}</h3>
                            <p class="text-center text-gray-500 text-sm mt-1 truncate w-full">{{ $employe->email }}</p>
                            
                            <div class="mt-3 flex space-x-2">
                                <a href="{{ route('employes.edit', $employe) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('employes.destroy', $employe) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer cet employé ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if(!isset($employes) || $employes->isEmpty())
                <div class="bg-gray-50 rounded-xl p-8 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p class="text-lg">Aucun employé n'a été ajouté pour le moment.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>