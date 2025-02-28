<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du planning') }}
            </h2>
            <div class="flex gap-4">
                <a href="{{ route('plannings.edit', $planning) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    Modifier
                </a>
                <form method="POST" action="{{ route('plannings.destroy', $planning) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce planning ?')">
                        Supprimer
                    </button>
                </form>
                <a href="{{ route('plannings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informations de l'employé</h3>
                            <div class="mt-4 space-y-2">
                                <div>
                                    <span class="font-semibold">Nom :</span>
                                    <span>{{ $planning->employe->nom }} {{ $planning->employe->prenom }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Lieu de travail</h3>
                            <div class="mt-4 space-y-2">
                                <div>
                                    <span class="font-semibold">Nom :</span>
                                    <span>{{ $planning->lieu->nom }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Adresse :</span>
                                    <span>{{ $planning->lieu->adresse_complete }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900">Détails du planning</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <span class="font-semibold">Date :</span>
                                    <span>{{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Heure de début :</span>
                                    <span>{{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Heure de fin :</span>
                                    <span>{{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Heures travaillées :</span>
                                    <span>{{ number_format($planning->heures_travaillees, 2) }}h</span>
                                </div>
                                @if($planning->description)
                                <div class="md:col-span-3">
                                    <span class="font-semibold">Description :</span>
                                    <p class="mt-1">{{ $planning->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
