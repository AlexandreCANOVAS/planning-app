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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Tableau de bord des statistiques -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tableau de bord des congés</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total des congés -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 shadow-sm flex items-center">
                            <div class="rounded-full bg-blue-500 p-3 mr-4">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Total des congés</p>
                                <p class="text-2xl font-bold text-blue-800">{{ $conges->count() }}</p>
                            </div>
                        </div>
                        
                        <!-- Congés en attente -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200 shadow-sm flex items-center">
                            <div class="rounded-full bg-yellow-500 p-3 mr-4">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">En attente</p>
                                <p class="text-2xl font-bold text-yellow-800">{{ $conges->where('statut', 'en_attente')->count() }}</p>
                            </div>
                        </div>
                        
                        <!-- Congés acceptés -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200 shadow-sm flex items-center">
                            <div class="rounded-full bg-green-500 p-3 mr-4">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Acceptés</p>
                                <p class="text-2xl font-bold text-green-800">{{ $conges->where('statut', 'accepte')->count() }}</p>
                            </div>
                        </div>
                        
                        <!-- Congés refusés -->
                        <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg border border-red-200 shadow-sm flex items-center">
                            <div class="rounded-full bg-red-500 p-3 mr-4">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-red-600 font-medium">Refusés</p>
                                <p class="text-2xl font-bold text-red-800">{{ $conges->where('statut', 'refuse')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique de répartition mensuelle -->
                    <div class="mt-8">
                        <h4 class="text-md font-medium text-gray-700 mb-3">Répartition mensuelle des congés</h4>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <canvas class="h-64" id="monthly-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Liste des congés -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtres et recherche avancée -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtres et recherche
                        </h4>
                        
                        <form action="{{ route('conges.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Filtre par statut -->
                            <div>
                                <label for="statut" class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                                <select id="statut" name="statut" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente" {{ isset($statut) && $statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="accepte" {{ isset($statut) && $statut == 'accepte' ? 'selected' : '' }}>Accepté</option>
                                    <option value="refuse" {{ isset($statut) && $statut == 'refuse' ? 'selected' : '' }}>Refusé</option>
                                </select>
                            </div>
                            
                            <!-- Filtre par période -->
                            <div>
                                <label for="periode" class="block text-xs font-medium text-gray-700 mb-1">Période</label>
                                <select id="periode" name="periode" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Toutes les périodes</option>
                                    <option value="mois_courant" {{ isset($periode) && $periode == 'mois_courant' ? 'selected' : '' }}>Mois courant</option>
                                    <option value="trimestre_courant" {{ isset($periode) && $periode == 'trimestre_courant' ? 'selected' : '' }}>Trimestre courant</option>
                                    <option value="annee_courante" {{ isset($periode) && $periode == 'annee_courante' ? 'selected' : '' }}>Année courante</option>
                                </select>
                            </div>
                            
                            <!-- Recherche par employé -->
                            <div>
                                <label for="employe" class="block text-xs font-medium text-gray-700 mb-1">Employé</label>
                                <input type="text" name="employe" id="employe" value="{{ $employe ?? '' }}" placeholder="Rechercher par nom..." class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            
                            <!-- Dates personnalisées -->
                            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="date_debut" class="block text-xs font-medium text-gray-700 mb-1">Date début</label>
                                    <input type="date" name="date_debut" id="date_debut" value="{{ $dateDebut ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label for="date_fin" class="block text-xs font-medium text-gray-700 mb-1">Date fin</label>
                                    <input type="date" name="date_fin" id="date_fin" value="{{ $dateFin ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <!-- Boutons -->
                            <div class="md:col-span-3 flex justify-end space-x-3 mt-2">
                                <a href="{{ route('conges.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Réinitialiser
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filtrer
                                </button>
                            </div>
                        </form>
                    </div>
                    
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
                                        <td class="px-6 py-4">
                                            @if($conge->employe)
                                                <div class="flex flex-col">
                                                    <div class="font-medium text-gray-900">{{ $conge->employe->nom }} {{ $conge->employe->prenom }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $conge->employe->solde_conges > 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            Solde: {{ number_format($conge->employe->solde_conges, 1) }} jours
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">Employé non trouvé</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <div>
                                                    Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                                    au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $conge->duree }} jour(s)
                                                </div>
                                                
                                                @php
                                                    $chevauchements = $conge->chevauchements();
                                                @endphp
                                                
                                                @if($chevauchements->count() > 0)
                                                    <div class="mt-2">
                                                        <button type="button" class="text-xs text-amber-600 hover:text-amber-800 flex items-center" 
                                                                onclick="document.getElementById('chevauchement-{{ $conge->id }}').classList.toggle('hidden')">
                                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                            {{ $chevauchements->count() }} chevauchement(s)
                                                        </button>
                                                        
                                                        <div id="chevauchement-{{ $conge->id }}" class="hidden mt-2 p-2 bg-amber-50 rounded-md border border-amber-200 text-xs">
                                                            <p class="font-medium text-amber-700 mb-1">Chevauchements avec :</p>
                                                            <ul class="list-disc list-inside space-y-1 text-amber-800">
                                                                @foreach($chevauchements as $autre)
                                                                    <li>{{ $autre->employe->nom }} {{ $autre->employe->prenom }} ({{ \Carbon\Carbon::parse($autre->date_debut)->format('d/m') }} - {{ \Carbon\Carbon::parse($autre->date_fin)->format('d/m') }})</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
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
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <div class="flex flex-col space-y-2">
                                                @if($conge->statut === 'en_attente' && $conge->employe)
                                                    <div class="flex space-x-2">
                                                        <button type="button" class="px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors duration-200 flex items-center text-xs"
                                                                onclick="document.getElementById('modal-accepter-{{ $conge->id }}').classList.remove('hidden')">
                                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Accepter
                                                        </button>
                                                        
                                                        <button type="button" class="px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200 flex items-center text-xs"
                                                                onclick="document.getElementById('modal-refuser-{{ $conge->id }}').classList.remove('hidden')">
                                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Refuser
                                                        </button>
                                                        
                                                        <!-- Modal Accepter -->
                                                        <div id="modal-accepter-{{ $conge->id }}" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                                                            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
                                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Accepter la demande de congé</h3>
                                                                <form action="{{ route('conges.update-status', $conge) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="statut" value="accepte">
                                                                    
                                                                    <div class="mb-4">
                                                                        <label for="commentaire-accepter-{{ $conge->id }}" class="block text-sm font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                                                                        <textarea id="commentaire-accepter-{{ $conge->id }}" name="commentaire" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                                                    </div>
                                                                    
                                                                    <div class="flex justify-end space-x-3">
                                                                        <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"
                                                                                onclick="document.getElementById('modal-accepter-{{ $conge->id }}').classList.add('hidden')">
                                                                            Annuler
                                                                        </button>
                                                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                                                            Confirmer
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Modal Refuser -->
                                                        <div id="modal-refuser-{{ $conge->id }}" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                                                            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
                                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Refuser la demande de congé</h3>
                                                                <form action="{{ route('conges.update-status', $conge) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="statut" value="refuse">
                                                                    
                                                                    <div class="mb-4">
                                                                        <label for="commentaire-refuser-{{ $conge->id }}" class="block text-sm font-medium text-gray-700 mb-1">Motif du refus</label>
                                                                        <textarea id="commentaire-refuser-{{ $conge->id }}" name="commentaire" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required></textarea>
                                                                    </div>
                                                                    
                                                                    <div class="flex justify-end space-x-3">
                                                                        <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"
                                                                                onclick="document.getElementById('modal-refuser-{{ $conge->id }}').classList.add('hidden')">
                                                                            Annuler
                                                                        </button>
                                                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                                                            Confirmer
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('conges.show', $conge) }}" class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors duration-200 flex items-center text-xs">
                                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Détails
                                                    </a>
                                                    
                                                    <button type="button" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-200 flex items-center text-xs"
                                                            onclick="document.getElementById('historique-{{ $conge->id }}').classList.toggle('hidden')">
                                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Historique
                                                    </button>
                                                </div>
                                                
                                                <!-- Historique des modifications -->
                                                <div id="historique-{{ $conge->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded-md border border-gray-200 text-xs">
                                                    <p class="font-medium text-gray-700 mb-1">Historique des modifications :</p>
                                                    @if($conge->historique->count() > 0)
                                                        <ul class="space-y-1 text-gray-600">
                                                            @foreach($conge->historique as $h)
                                                                <li class="flex items-start">
                                                                    <span class="inline-block w-3 h-3 rounded-full mr-1 mt-1 {{ $h->nouveau_statut === 'accepte' ? 'bg-green-500' : ($h->nouveau_statut === 'refuse' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                                                                    <div>
                                                                        <span class="font-medium">{{ $h->user->name }}</span> a changé le statut de 
                                                                        <span class="{{ $h->ancien_statut === 'accepte' ? 'text-green-600' : ($h->ancien_statut === 'refuse' ? 'text-red-600' : 'text-yellow-600') }}">{{ \App\Models\Conge::STATUTS[$h->ancien_statut] }}</span> à 
                                                                        <span class="{{ $h->nouveau_statut === 'accepte' ? 'text-green-600' : ($h->nouveau_statut === 'refuse' ? 'text-red-600' : 'text-yellow-600') }}">{{ \App\Models\Conge::STATUTS[$h->nouveau_statut] }}</span>
                                                                        <div class="text-gray-400 text-xs">{{ $h->created_at->format('d/m/Y H:i') }}</div>
                                                                        @if($h->commentaire)
                                                                            <div class="text-gray-500 italic mt-1">"{{ $h->commentaire }}"</div>
                                                                        @endif
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-gray-500 italic">Aucune modification enregistrée</p>
                                                    @endif
                                                </div>
                                            </div>
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
    <!-- Scripts pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Préparation des données pour le graphique mensuel
            const ctx = document.getElementById('monthly-chart').getContext('2d');
            
            // Données pour le graphique (à remplacer par des données dynamiques)
            const monthlyData = {
                labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                datasets: [
                    {
                        label: 'Congés acceptés',
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        data: @json($congesMensuels['accepte'] ?? array_fill(0, 12, 0)),
                    },
                    {
                        label: 'Congés en attente',
                        backgroundColor: 'rgba(234, 179, 8, 0.5)',
                        borderColor: 'rgb(234, 179, 8)',
                        borderWidth: 1,
                        data: @json($congesMensuels['en_attente'] ?? array_fill(0, 12, 0)),
                    },
                    {
                        label: 'Congés refusés',
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1,
                        data: @json($congesMensuels['refuse'] ?? array_fill(0, 12, 0)),
                    }
                ]
            };
            
            // Création du graphique
            new Chart(ctx, {
                type: 'bar',
                data: monthlyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Répartition mensuelle des congés'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de congés'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mois'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>