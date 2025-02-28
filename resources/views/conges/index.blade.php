<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des congés') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('conges.calendar') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Voir le calendrier
                </a>
                <a href="{{ auth()->user()->isEmploye() ? route('employe.mes-conges') : '#' }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Ajouter un congé
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($conges as $conge)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($conge->employe)
                                                {{ $conge->employe->nom }} {{ $conge->employe->prenom }}
                                            @else
                                                <span class="text-gray-400">Employé non trouvé</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                            au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($conge->statut === 'en_attente')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            @elseif($conge->statut === 'accepte')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Accepté
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Refusé
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($conge->statut === 'en_attente' && $conge->employe)
                                                <form action="{{ route('conges.update-status', $conge) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="statut" value="accepte">
                                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                                        Accepter
                                                    </button>
                                                </form>
                                                <form action="{{ route('conges.update-status', $conge) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="statut" value="refuse">
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Refuser
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('conges.show', $conge) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Détails
                                                </a>
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