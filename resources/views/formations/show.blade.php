<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $formation->nom }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('formations.edit', $formation) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Modifier
                </a>
                <a href="{{ route('formations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Colonne principale -->
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Description</h3>
                                <div class="prose max-w-none">
                                    {{ $formation->description ?? 'Aucune description disponible.' }}
                                </div>
                            </div>

                            <!-- Objectifs pédagogiques -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Objectifs pédagogiques</h3>
                                <div class="prose max-w-none">
                                    {{ $formation->objectifs_pedagogiques ?? 'Aucun objectif pédagogique spécifié.' }}
                                </div>
                            </div>

                            <!-- Prérequis -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Prérequis</h3>
                                <div class="prose max-w-none">
                                    {{ $formation->prerequis ?? 'Aucun prérequis spécifié.' }}
                                </div>
                            </div>
                        </div>

                        <!-- Colonne d'informations -->
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>
                                
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Durée de validité</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $formation->duree_validite_mois ? $formation->duree_validite_mois . ' mois' : 'Non spécifiée' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Durée recommandée</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $formation->duree_recommandee_heures ? $formation->duree_recommandee_heures . ' heures' : 'Non spécifiée' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Organisme formateur</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $formation->organisme_formateur ?? 'Non spécifié' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Type de formateur</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($formation->formateur_interne)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Formateur interne
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Formateur externe
                                                </span>
                                            @endif
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Coût</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $formation->cout ? number_format($formation->cout, 2, ',', ' ') . ' €' : 'Non spécifié' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
