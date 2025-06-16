@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('employe.plannings.calendar') }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-white/20 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour
                        </a>
                        <h2 class="text-2xl font-bold text-white">
                            Mes échanges de jours
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Onglets -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="showTab('received')" class="tab-button border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Demandes reçues
                            @if($echangesRecus->where('statut', 'en_attente')->count() > 0)
                                <span class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">
                                    {{ $echangesRecus->where('statut', 'en_attente')->count() }}
                                </span>
                            @endif
                        </button>
                        <button onclick="showTab('sent')" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Demandes envoyées
                        </button>
                    </nav>
                </div>
                <!-- Contenu des onglets -->
                <div class="mt-4">
                    <!-- Onglet des demandes reçues -->
                    <div id="tab-received" class="tab-content">
                        @if($echangesRecus->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande reçue</h3>
                                <p class="mt-1 text-sm text-gray-500">Vous n'avez reçu aucune demande d'échange de jours.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour demandé</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour proposé</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($echangesRecus as $echange)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $echange->demandeur->prenom }} {{ $echange->demandeur->nom }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $echange->jour_receveur->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">Votre jour</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $echange->jour_demandeur->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">Son jour</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900 max-w-xs truncate">{{ $echange->motif }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($echange->estEnAttente())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                                    @elseif($echange->estAccepte())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepté</span>
                                                    @elseif($echange->estRefuse())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Refusé</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($echange->estEnAttente())
                                                        <div class="flex space-x-2">
                                                            <form action="{{ route('employe.plannings.accepter-echange', $echange->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="text-green-600 hover:text-green-900">Accepter</button>
                                                            </form>
                                                            <form action="{{ route('employe.plannings.refuser-echange', $echange->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="text-red-600 hover:text-red-900">Refuser</button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-500">{{ $echange->commentaire_reponse ?: 'Aucun commentaire' }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Onglet des demandes envoyées -->
                    <div id="tab-sent" class="tab-content hidden">
                        @if($echangesDemandes->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande envoyée</h3>
                                <p class="mt-1 text-sm text-gray-500">Vous n'avez envoyé aucune demande d'échange de jours.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinataire</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour proposé</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour demandé</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réponse</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($echangesDemandes as $echange)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $echange->receveur->prenom }} {{ $echange->receveur->nom }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $echange->jour_demandeur->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">Votre jour</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $echange->jour_receveur->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">Son jour</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900 max-w-xs truncate">{{ $echange->motif }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($echange->estEnAttente())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                                    @elseif($echange->estAccepte())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepté</span>
                                                    @elseif($echange->estRefuse())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Refusé</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $echange->commentaire_reponse ?: 'Aucun commentaire' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showTab(tabName) {
        // Cacher tous les contenus d'onglets
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Afficher le contenu de l'onglet sélectionné
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Mettre à jour les styles des boutons d'onglets
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-indigo-500', 'text-indigo-600');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });
        
        // Mettre en évidence le bouton de l'onglet sélectionné
        const selectedButton = document.querySelector(`button[onclick="showTab('${tabName}')"]`);
        selectedButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        selectedButton.classList.add('border-indigo-500', 'text-indigo-600');
    }
</script>
@endpush
@endsection
