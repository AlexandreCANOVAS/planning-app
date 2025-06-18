@extends('layouts.app')

@section('title', 'Historique des soldes de congés')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-5xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
        <div>
            <h1 class="text-xl font-bold text-gray-800 mb-1">
                <i class="fas fa-history mr-1 text-purple-600"></i>Historique des soldes
            </h1>
            <p class="text-gray-600 text-sm">
                <i class="fas fa-user mr-1"></i>{{ $employe->prenom }} {{ $employe->nom }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('conges.index') }}" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-purple-500">
                <i class="fas fa-calendar-alt mr-1"></i>
                Congés
            </a>
            <a href="{{ route('solde.edit', $employe) }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                <i class="fas fa-edit mr-1"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Informations employé -->
            <div class="md:col-span-1">
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-user-circle text-purple-600 text-lg mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informations</h3>
                    </div>
                    <div class="space-y-2 text-gray-600 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Nom:</span> {{ $employe->nom }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Prénom:</span> {{ $employe->prenom }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-briefcase text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Poste:</span> {{ $employe->poste }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Embauche:</span> {{ $employe->date_embauche ? $employe->date_embauche->format('d/m/Y') : 'Non définie' }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-file-contract text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Contrat:</span> {{ $employe->type_contrat }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Soldes actuels -->
            <div class="md:col-span-3">
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-calculator text-purple-600 text-lg mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Soldes actuels</h3>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg text-center">
                            <div class="flex justify-center mb-1">
                                <i class="fas fa-umbrella-beach text-blue-500 text-xl"></i>
                            </div>
                            <span class="block text-xs font-medium text-blue-600">Congés payés</span>
                            <span class="block text-2xl font-bold text-blue-800">{{ number_format($employe->solde_conges, 1) }}</span>
                            <span class="text-blue-600 text-xs">jours</span>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg text-center">
                            <div class="flex justify-center mb-1">
                                <i class="fas fa-clock text-green-500 text-xl"></i>
                            </div>
                            <span class="block text-xs font-medium text-green-600">RTT</span>
                            <span class="block text-2xl font-bold text-green-800">{{ number_format($employe->solde_rtt, 1) }}</span>
                            <span class="text-green-600 text-xs">jours</span>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-lg text-center">
                            <div class="flex justify-center mb-1">
                                <i class="fas fa-star text-purple-500 text-xl"></i>
                            </div>
                            <span class="block text-xs font-medium text-purple-600">Congés exceptionnels</span>
                            <span class="block text-2xl font-bold text-purple-800">{{ number_format($employe->solde_conges_exceptionnels, 1) }}</span>
                            <span class="text-purple-600 text-xs">jours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="flex items-center mb-3">
                <i class="fas fa-list-alt text-purple-600 text-lg mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-800">Historique des modifications</h3>
            </div>
            
            @if($historique->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Congés payés</th>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RTT</th>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Congés exceptionnels</th>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modifié par</th>
                                <th class="px-3 py-2 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($historique as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-xs text-gray-700">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        @if($item->type_modification == 'ajustement_manuel')
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 border border-blue-200">Ajustement manuel</span>
                                        @elseif($item->type_modification == 'conge_accepte')
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 border border-green-200">Congé accepté</span>
                                        @elseif($item->type_modification == 'conge_refuse')
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 border border-red-200">Congé refusé</span>
                                        @elseif($item->type_modification == 'conge_annule')
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">Congé annulé</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700 border border-gray-200">{{ $item->type_modification }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <div class="flex items-center">
                                            <span class="text-gray-600">{{ number_format($item->ancien_solde_conges, 1) }}</span>
                                            <i class="fas fa-arrow-right mx-1 text-gray-400 text-xs"></i>
                                            <span class="text-gray-600">{{ number_format($item->nouveau_solde_conges, 1) }}</span>
                                            @if($item->nouveau_solde_conges > $item->ancien_solde_conges)
                                                <span class="ml-1 text-green-600 text-xs">(+{{ number_format($item->nouveau_solde_conges - $item->ancien_solde_conges, 1) }})</span>
                                            @elseif($item->nouveau_solde_conges < $item->ancien_solde_conges)
                                                <span class="ml-1 text-red-600 text-xs">(-{{ number_format($item->ancien_solde_conges - $item->nouveau_solde_conges, 1) }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <div class="flex items-center">
                                            <span class="text-gray-600">{{ number_format($item->ancien_solde_rtt, 1) }}</span>
                                            <i class="fas fa-arrow-right mx-1 text-gray-400 text-xs"></i>
                                            <span class="text-gray-600">{{ number_format($item->nouveau_solde_rtt, 1) }}</span>
                                            @if($item->nouveau_solde_rtt > $item->ancien_solde_rtt)
                                                <span class="ml-1 text-green-600 text-xs">(+{{ number_format($item->nouveau_solde_rtt - $item->ancien_solde_rtt, 1) }})</span>
                                            @elseif($item->nouveau_solde_rtt < $item->ancien_solde_rtt)
                                                <span class="ml-1 text-red-600 text-xs">(-{{ number_format($item->ancien_solde_rtt - $item->nouveau_solde_rtt, 1) }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <div class="flex items-center">
                                            <span class="text-gray-600">{{ number_format($item->ancien_solde_conges_exceptionnels, 1) }}</span>
                                            <i class="fas fa-arrow-right mx-1 text-gray-400 text-xs"></i>
                                            <span class="text-gray-600">{{ number_format($item->nouveau_solde_conges_exceptionnels, 1) }}</span>
                                            @if($item->nouveau_solde_conges_exceptionnels > $item->ancien_solde_conges_exceptionnels)
                                                <span class="ml-1 text-green-600 text-xs">(+{{ number_format($item->nouveau_solde_conges_exceptionnels - $item->ancien_solde_conges_exceptionnels, 1) }})</span>
                                            @elseif($item->nouveau_solde_conges_exceptionnels < $item->ancien_solde_conges_exceptionnels)
                                                <span class="ml-1 text-red-600 text-xs">(-{{ number_format($item->ancien_solde_conges_exceptionnels - $item->nouveau_solde_conges_exceptionnels, 1) }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-700">{{ $item->user->name }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700">{{ $item->commentaire }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $historique->links() }}
                </div>
            @else
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                    <div class="flex flex-col items-center justify-center py-4">
                        <i class="fas fa-history text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500">Aucun historique de modification disponible pour cet employé.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
