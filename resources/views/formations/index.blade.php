<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des formations') }}
            </h2>
            <a href="{{ route('formations.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Ajouter une formation
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Barre de recherche et filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('formations.index') }}" method="GET" class="space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                            <div class="flex-grow">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Rechercher par nom, description..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <div class="w-full md:w-1/4">
                                <label for="formateur_type" class="block text-sm font-medium text-gray-700 mb-1">Type de formateur</label>
                                <select id="formateur_type" name="formateur_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Tous</option>
                                    <option value="interne" {{ request('formateur_type') == 'interne' ? 'selected' : '' }}>Formateur interne</option>
                                    <option value="externe" {{ request('formateur_type') == 'externe' ? 'selected' : '' }}>Formateur externe</option>
                                </select>
                            </div>
                            
                            <div class="w-full md:w-1/4 md:self-end">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($formations->isEmpty())
                        <p class="text-center text-gray-500">Aucune formation enregistrée.</p>
                    @else
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($formations as $formation)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('formations.show', $formation) }}" class="hover:text-indigo-600 hover:underline">{{ $formation->nom }}</a>
                                        </h3>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('formations.show', $formation) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Détails
                                            </a>
                                            <a href="{{ route('formations.edit', $formation) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Modifier
                                            </a>
                                            <form action="{{ route('formations.destroy', $formation) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <div class="mb-6">
                                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                                                    <p class="text-gray-700">{{ $formation->description ?? 'Aucune description disponible' }}</p>
                                                </div>
                                                
                                                <div class="mb-6">
                                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Objectifs pédagogiques</h4>
                                                    <p class="text-gray-700">{{ $formation->objectifs_pedagogiques ?? 'Non spécifiés' }}</p>
                                                </div>
                                                
                                                <div class="mb-6">
                                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Prérequis</h4>
                                                    <p class="text-gray-700">{{ $formation->prerequis ?? 'Aucun prérequis' }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informations complémentaires</h4>
                                                
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-500">Durée de validité</p>
                                                        <p class="text-sm font-medium">{{ $formation->duree_validite_mois ? $formation->duree_validite_mois . ' mois' : 'Non spécifiée' }}</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-xs text-gray-500">Durée recommandée</p>
                                                        <p class="text-sm font-medium">{{ $formation->duree_recommandee_heures ? $formation->duree_recommandee_heures . ' heures' : 'Non spécifiée' }}</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-xs text-gray-500">Organisme formateur</p>
                                                        <p class="text-sm font-medium">{{ $formation->organisme_formateur ?? 'Non spécifié' }}</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-xs text-gray-500">Type de formateur</p>
                                                        <p class="text-sm font-medium">{{ $formation->formateur_interne ? 'Formateur interne' : 'Formateur externe' }}</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-xs text-gray-500">Coût</p>
                                                        <p class="text-sm font-medium">{{ $formation->cout ? number_format($formation->cout, 2, ',', ' ') . ' €' : 'Non spécifié' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
