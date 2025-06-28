<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mon profil') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour au tableau de bord
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations principales -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="bg-gray-100 rounded-full p-3 mr-4">
                            @if($employe->photo_url)
                                <img src="{{ asset('storage/' . $employe->photo_url) }}" alt="{{ $employe->prenom }} {{ $employe->nom }}" class="h-12 w-12 rounded-full object-cover">
                            @else
                                <svg class="h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $employe->prenom }} {{ $employe->nom }}</h3>
                            <p class="text-gray-600">{{ $employe->poste ?? 'Poste non spécifié' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informations professionnelles -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations professionnelles</h3>
                    <div class="space-y-4">
                        <!-- Poste/Fonction -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Poste/Fonction</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-briefcase text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->poste ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Type de contrat -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Type de contrat</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-file-signature text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->type_contrat ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Durée du contrat -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Durée du contrat</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                    @if($employe->date_debut_contrat)
                                        <p class="text-gray-800">
                                            Du {{ \Carbon\Carbon::parse($employe->date_debut_contrat)->format('d/m/Y') }}
                                            @if($employe->date_fin_contrat)
                                                au {{ \Carbon\Carbon::parse($employe->date_fin_contrat)->format('d/m/Y') }}
                                            @else
                                                (sans date de fin)
                                            @endif
                                        </p>
                                    @else
                                        <p class="text-gray-800">Non renseigné</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Temps de travail -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Temps de travail</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">
                                        @if($employe->temps_travail)
                                            {{ $employe->temps_travail }}
                                            @if($employe->temps_travail == 'Temps partiel' && $employe->pourcentage_temps_partiel)
                                                ({{ $employe->pourcentage_temps_partiel }}%)
                                            @endif
                                        @else
                                            Non renseigné
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informations personnelles -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h3>
                    <div class="space-y-4">
                        <!-- Date de naissance -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date de naissance</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-birthday-cake text-gray-400 mr-2"></i>
                                    @if($employe->date_naissance)
                                        <p class="text-gray-800">
                                            {{ \Carbon\Carbon::parse($employe->date_naissance)->format('d/m/Y') }}
                                            ({{ \Carbon\Carbon::parse($employe->date_naissance)->age }} ans)
                                        </p>
                                    @else
                                        <p class="text-gray-800">Non renseignée</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Numéro de sécurité sociale -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Numéro de sécurité sociale</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->numero_securite_sociale ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Situation familiale -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Situation familiale</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-user-friends text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->situation_familiale ?? 'Non renseignée' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nombre d'enfants -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre d'enfants</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-child text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->nombre_enfants ?? '0' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact d'urgence -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Contact d'urgence</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-phone-alt text-gray-400 mr-2"></i>
                                    @if($employe->contact_urgence_nom && $employe->contact_urgence_telephone)
                                        <p class="text-gray-800">
                                            {{ $employe->contact_urgence_nom }} - {{ $employe->contact_urgence_telephone }}
                                        </p>
                                    @else
                                        <p class="text-gray-800">Non renseigné</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Coordonnées -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Coordonnées</h3>
                    <div class="space-y-4">
                        <!-- Email -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Téléphone -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Téléphone</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->telephone ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Adresse -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Adresse</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->adresse ?? 'Non renseignée' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date d'embauche -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date d'embauche</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check text-gray-400 mr-2"></i>
                                    @if($employe->date_embauche)
                                        <p class="text-gray-800">
                                            {{ \Carbon\Carbon::parse($employe->date_embauche)->format('d/m/Y') }}
                                            ({{ \Carbon\Carbon::parse($employe->date_embauche)->diffForHumans(['parts' => 2]) }})
                                        </p>
                                    @else
                                        <p class="text-gray-800">Non renseignée</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Soldes de congés -->
            <div class="mt-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Soldes de congés</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Congés payés -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-blue-700 mb-1">Congés payés</p>
                            <p class="text-2xl font-bold text-blue-800">{{ number_format($employe->solde_conges ?? 0, 1) }} jours</p>
                        </div>
                        
                        <!-- RTT -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-green-700 mb-1">RTT</p>
                            <p class="text-2xl font-bold text-green-800">{{ number_format($employe->solde_rtt ?? 0, 1) }} jours</p>
                        </div>
                        
                        <!-- Récupérations -->
                        <div class="bg-amber-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-amber-700 mb-1">Récupérations</p>
                            <p class="text-2xl font-bold text-amber-800">{{ number_format($employe->solde_recuperation ?? 0, 1) }} jours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
