<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'employé') }}
            </h2>
            <a href="{{ route('employes.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                    <p class="font-bold">Succès!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('password'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded" role="alert">
                    <p class="font-bold">Informations de connexion</p>
                    <p>Un compte utilisateur a été créé pour cet employé.</p>
                    <p class="mt-2"><strong>Email :</strong> {{ $employe->email }}</p>
                    <p><strong>Mot de passe temporaire :</strong> <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ session('password') }}</span></p>
                    <p class="mt-2 text-sm">Veuillez communiquer ces informations à l'employé. Il devra changer son mot de passe lors de sa première connexion.</p>
                </div>
            @endif

            <!-- Informations principales -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="bg-gray-100 rounded-full p-3 mr-4">
                            <svg class="h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $employe->prenom }} {{ $employe->nom }}</h3>
                            <p class="text-gray-600">{{ $employe->poste ?? 'Poste non spécifié' }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('employes.edit', $employe) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Modifier
                        </a>
                        <a href="{{ route('employes.formations', $employe) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            Formations
                        </a>
                        <a href="{{ route('employes.stats', $employe) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Statistiques
                        </a>
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
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
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
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Durée du contrat -->
                        @if($employe->type_contrat == 'CDD' || $employe->date_debut_contrat || $employe->date_fin_contrat)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Durée du contrat</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">
                                        @if($employe->date_debut_contrat && $employe->date_fin_contrat)
                                            Du {{ \Carbon\Carbon::parse($employe->date_debut_contrat)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($employe->date_fin_contrat)->format('d/m/Y') }}
                                            ({{ \Carbon\Carbon::parse($employe->date_debut_contrat)->diffInMonths(\Carbon\Carbon::parse($employe->date_fin_contrat)) }} mois)
                                        @elseif($employe->date_debut_contrat)
                                            Depuis le {{ \Carbon\Carbon::parse($employe->date_debut_contrat)->format('d/m/Y') }}
                                        @elseif($employe->date_fin_contrat)
                                            Jusqu'au {{ \Carbon\Carbon::parse($employe->date_fin_contrat)->format('d/m/Y') }}
                                        @else
                                            Non renseignée
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Temps de travail -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Temps de travail</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">
                                        @if($employe->temps_travail)
                                            {{ $employe->temps_travail }}
                                            @if($employe->pourcentage_travail && $employe->temps_travail == 'Temps partiel')
                                                ({{ $employe->pourcentage_travail }}%)
                                            @endif
                                        @else
                                            Non renseigné
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Informations personnelles -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h3>
                    <div class="space-y-4">
                        <!-- Photo de profil -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Photo</p>
                            <div class="flex items-center">
                                @if($employe->photo_profil)
                                    <img src="{{ asset('storage/' . $employe->photo_profil) }}" alt="Photo de {{ $employe->prenom }} {{ $employe->nom }}" class="h-16 w-16 rounded-full object-cover">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xl font-semibold">{{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}</span>
                                    </div>
                                @endif
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Date de naissance -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date de naissance</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-birthday-cake text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->date_naissance ? \Carbon\Carbon::parse($employe->date_naissance)->format('d/m/Y') . ' (' . \Carbon\Carbon::parse($employe->date_naissance)->age . ' ans)' : 'Non renseignée' }}</p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
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
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
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
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Nombre d'enfants -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre d'enfants</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-child text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->nombre_enfants }}</p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Contact d'urgence -->
                        <div>
                            <p class="text-sm font-medium text-gray-500">Contact d'urgence</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-ambulance text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->contact_urgence_nom ? $employe->contact_urgence_nom . ' - ' . $employe->contact_urgence_telephone : 'Non renseigné' }}</p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Coordonnées -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Coordonnées</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-800">{{ $employe->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Téléphone</p>
                            <p class="text-gray-800">{{ $employe->telephone ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Adresse</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->adresse ?? 'Non renseignée' }}</p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date d'embauche</p>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                    <p class="text-gray-800">{{ $employe->date_embauche ? \Carbon\Carbon::parse($employe->date_embauche)->format('d/m/Y') : 'Non renseignée' }}</p>
                                </div>
                                <a href="{{ route('employes.edit', $employe) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charge de travail -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Charge de travail</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-sm font-medium text-gray-500">Charge actuelle</p>
                                <span class="text-sm font-medium {{ $employe->charge_de_travail > 80 ? 'text-red-600' : ($employe->charge_de_travail > 50 ? 'text-yellow-600' : 'text-green-600') }}">{{ $employe->charge_de_travail ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $employe->charge_de_travail > 80 ? 'bg-red-500' : ($employe->charge_de_travail > 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $employe->charge_de_travail ?? 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-2">Plannings récents</p>
                            @if($employe->plannings && $employe->plannings->count() > 0)
                                <div class="space-y-2">
                                    @foreach($employe->plannings->take(5) as $planning)
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between">
                                                <p class="text-sm font-medium">{{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}</p>
                                                <p class="text-sm">{{ $planning->duree_heures }}h</p>
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $planning->lieu->nom ?? 'Lieu non spécifié' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Aucun planning récent</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Formations et congés -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Formations et congés</h3>
                    
                    <!-- Formations -->
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-500 mb-2">Formations</p>
                        @if($employe->formations && $employe->formations->count() > 0)
                            <div class="space-y-2">
                                @foreach($employe->formations->take(3) as $formation)
                                    @php
                                        $dateObtention = \Carbon\Carbon::parse($formation->pivot->date_obtention);
                                        $dateRecyclage = $formation->pivot->date_recyclage ? \Carbon\Carbon::parse($formation->pivot->date_recyclage) : null;
                                        $isValid = $dateRecyclage ? $dateRecyclage->isFuture() : true;
                                    @endphp
                                    <div class="p-3 {{ $isValid ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                                        <div class="flex justify-between">
                                            <p class="text-sm font-medium">{{ $formation->nom }}</p>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isValid ? 'Valide' : 'Expiré' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            Obtention: {{ $dateObtention->format('d/m/Y') }} 
                                            @if($dateRecyclage)
                                                | Recyclage: {{ $dateRecyclage->format('d/m/Y') }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            @if($employe->formations->count() > 3)
                                <div class="mt-2 text-center">
                                    <a href="{{ route('employes.formations', $employe) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                        Voir toutes les formations ({{ $employe->formations->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">Aucune formation enregistrée</p>
                        @endif
                    </div>
                    
                    <!-- Soldes de congés -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm font-medium text-gray-500">Soldes de congés</p>
                            @if(auth()->user()->isEmployeur())
                                <a href="{{ route('solde.edit', $employe->id) }}" class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded hover:bg-purple-200 flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </a>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs text-blue-600 font-medium">Congés payés</p>
                                <p class="text-lg font-bold text-blue-800" data-employe-id="{{ $employe->id }}" data-solde-type="conges">{{ number_format($employe->solde_conges, 1) }} jours</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded-lg">
                                <p class="text-xs text-green-600 font-medium">RTT</p>
                                <p class="text-lg font-bold text-green-800" data-employe-id="{{ $employe->id }}" data-solde-type="rtt">{{ number_format($employe->solde_rtt, 1) }} jours</p>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-lg">
                                <p class="text-xs text-amber-600 font-medium">Congés exceptionnels</p>
                                <p class="text-lg font-bold text-amber-800" data-employe-id="{{ $employe->id }}" data-solde-type="exceptionnels">{{ number_format($employe->solde_conges_exceptionnels, 1) }} jours</p>
                            </div>
                        </div>
                        @if(auth()->user()->isEmployeur())
                            <div class="mt-2 text-center">
                                <a href="{{ route('solde.historique', $employe->id) }}" class="text-xs text-gray-600 hover:text-gray-800 flex items-center justify-center">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Voir l'historique des modifications
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Congés -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Congés à venir</p>
                        @if($employe->conges && $employe->conges->count() > 0)
                            <div class="space-y-2">
                                @foreach($employe->conges->take(3) as $conge)
                                    <div class="p-3 bg-blue-50 rounded-lg">
                                        <div class="flex justify-between">
                                            <p class="text-sm font-medium">{{ $conge->type }}</p>
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                {{ $conge->statut }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }} 
                                            au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucun congé à venir</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Informations administratives -->            
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-8 mb-4 px-6 lg:px-8">
                {{ __('Informations administratives') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Documents administratifs -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Documents administratifs</h3>
                        <a href="{{ route('employes.edit', $employe) }}#documents" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter
                        </a>
                    </div>
                    
                    @if($employe->documentsAdministratifs && $employe->documentsAdministratifs->count() > 0)
                        <div class="space-y-3">
                            @foreach($employe->documentsAdministratifs as $document)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium">{{ $document->nom }}</p>
                                            <p class="text-xs text-gray-500">{{ $document->type }} {{ $document->numero ? '- N° '.$document->numero : '' }}</p>
                                            @if($document->date_emission || $document->date_expiration)
                                                <p class="text-xs text-gray-500">
                                                    @if($document->date_emission)
                                                        Émis le {{ \Carbon\Carbon::parse($document->date_emission)->format('d/m/Y') }}
                                                    @endif
                                                    @if($document->date_expiration)
                                                        | Expire le {{ \Carbon\Carbon::parse($document->date_expiration)->format('d/m/Y') }}
                                                        @php
                                                            $isExpired = \Carbon\Carbon::parse($document->date_expiration)->isPast();
                                                        @endphp
                                                        <span class="px-1.5 py-0.5 text-xs rounded-full {{ $isExpired ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                            {{ $isExpired ? 'Expiré' : 'Valide' }}
                                                        </span>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-1">
                                            @if($document->fichier)
                                                <a href="{{ asset('storage/'.$document->fichier) }}" target="_blank" class="p-1 text-blue-600 hover:text-blue-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('employes.edit', $employe) }}#documents" class="p-1 text-blue-600 hover:text-blue-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucun document administratif enregistré</p>
                    @endif
                </div>
                
                <!-- Matériel attribué -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Matériel attribué</h3>
                        <a href="{{ route('employes.edit', $employe) }}#materiel" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter
                        </a>
                    </div>
                    
                    @if($employe->materiels && $employe->materiels->count() > 0)
                        <div class="space-y-3">
                            @foreach($employe->materiels as $materiel)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium">{{ $materiel->type }} {{ $materiel->marque }} {{ $materiel->modele }}</p>
                                            @if($materiel->numero_serie || $materiel->identifiant)
                                                <p class="text-xs text-gray-500">
                                                    @if($materiel->numero_serie) N° série: {{ $materiel->numero_serie }} @endif
                                                    @if($materiel->identifiant) @if($materiel->numero_serie) | @endif ID: {{ $materiel->identifiant }} @endif
                                                </p>
                                            @endif
                                            @if($materiel->date_attribution)
                                                <p class="text-xs text-gray-500">
                                                    Attribué le {{ \Carbon\Carbon::parse($materiel->date_attribution)->format('d/m/Y') }}
                                                    @if($materiel->date_retour)
                                                        | Retour prévu le {{ \Carbon\Carbon::parse($materiel->date_retour)->format('d/m/Y') }}
                                                    @endif
                                                </p>
                                            @endif
                                            @if($materiel->etat)
                                                <span class="px-1.5 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800">
                                                    État: {{ $materiel->etat }}
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('employes.edit', $employe) }}#materiel" class="p-1 text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucun matériel attribué</p>
                    @endif
                </div>
                
                <!-- Badges d'accès -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Badges d'accès</h3>
                        <a href="{{ route('employes.edit', $employe) }}#badges" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter
                        </a>
                    </div>
                    
                    @if($employe->badgesAcces && $employe->badgesAcces->count() > 0)
                        <div class="space-y-3">
                            @foreach($employe->badgesAcces as $badge)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium">Badge {{ $badge->type }}</p>
                                                <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full {{ $badge->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $badge->actif ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500">N° {{ $badge->numero_badge }}</p>
                                            @if($badge->zones_acces)
                                                <p class="text-xs text-gray-500">Zones: {{ $badge->zones_acces }}</p>
                                            @endif
                                            @if($badge->date_emission || $badge->date_expiration)
                                                <p class="text-xs text-gray-500">
                                                    @if($badge->date_emission)
                                                        Émis le {{ \Carbon\Carbon::parse($badge->date_emission)->format('d/m/Y') }}
                                                    @endif
                                                    @if($badge->date_expiration)
                                                        | Expire le {{ \Carbon\Carbon::parse($badge->date_expiration)->format('d/m/Y') }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                        <a href="{{ route('employes.edit', $employe) }}#badges" class="p-1 text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucun badge d'accès enregistré</p>
                    @endif
                </div>
                
                <!-- Accès informatiques -->                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Accès informatiques</h3>
                        <a href="{{ route('employes.edit', $employe) }}#acces" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter
                        </a>
                    </div>
                    
                    @if($employe->accesInformatiques && $employe->accesInformatiques->count() > 0)
                        <div class="space-y-3">
                            @foreach($employe->accesInformatiques as $acces)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium">{{ $acces->systeme }}</p>
                                                <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full {{ $acces->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $acces->actif ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500">Identifiant: {{ $acces->identifiant }}</p>
                                            @if($acces->niveau_acces)
                                                <p class="text-xs text-gray-500">Niveau d'accès: {{ $acces->niveau_acces }}</p>
                                            @endif
                                            @if($acces->permissions)
                                                <p class="text-xs text-gray-500">Permissions: {{ $acces->permissions }}</p>
                                            @endif
                                            @if($acces->date_creation || $acces->date_expiration)
                                                <p class="text-xs text-gray-500">
                                                    @if($acces->date_creation)
                                                        Créé le {{ \Carbon\Carbon::parse($acces->date_creation)->format('d/m/Y') }}
                                                    @endif
                                                    @if($acces->date_expiration)
                                                        | Expire le {{ \Carbon\Carbon::parse($acces->date_expiration)->format('d/m/Y') }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                        <a href="{{ route('employes.edit', $employe) }}#acces" class="p-1 text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucun accès informatique enregistré</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
