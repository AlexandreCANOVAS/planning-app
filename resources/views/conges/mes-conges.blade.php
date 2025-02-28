<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mes demandes de congés
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('employe.conges.calendar') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Voir le calendrier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Formulaire de demande de congé -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nouvelle demande de congé</h3>
                    <form action="{{ route('employe.conges.demande') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="date_debut" value="Date de début" />
                                <x-text-input id="date_debut" type="date" name="date_debut" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date_fin" value="Date de fin" />
                                <x-text-input id="date_fin" type="date" name="date_fin" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="motif" value="Motif" />
                                <x-text-input id="motif" type="text" name="motif" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('motif')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <x-primary-button>Soumettre la demande</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des congés -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des demandes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($conges as $conge)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $conge->motif }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $conge->statut === 'accepte' ? 'bg-green-100 text-green-800' : 
                                                   ($conge->statut === 'refuse' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($conge->statut) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($conge->statut === 'en_attente')
                                                <form action="{{ route('employe.conges.annuler', $conge) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Annuler</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Aucune demande de congé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
