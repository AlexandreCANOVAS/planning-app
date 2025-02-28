<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Heures cette semaine</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['heures_semaine'], 2) }}h</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Congés restants</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['conges_restants'] }} jours</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Prochaine journée</div>
                        <div class="mt-2 text-xl font-semibold text-gray-900">
                            @if($stats['prochain_planning'])
                                {{ \Carbon\Carbon::parse($stats['prochain_planning']->date)->format('d/m/Y') }}
                            @else
                                Aucun planning prévu
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mon planning de la semaine -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Mon planning de la semaine</h3>
                        <a href="{{ route('employe.plannings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Voir tout mon planning →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaires</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($plannings as $planning)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $planning->lieu->nom }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ number_format($planning->heures_travaillees, 2) }}h
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Aucun planning pour cette semaine
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mes demandes de congés -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Mes demandes de congés</h3>
                        <a href="{{ route('employe.conges.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Nouvelle demande
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($conges as $conge)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                            au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $conge->duree }} jours
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $conge->statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $conge->statut === 'accepte' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $conge->statut === 'refuse' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $conge->statut }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
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