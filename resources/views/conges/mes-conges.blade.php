<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Mes demandes de congés
                </h2>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
                    {{ $conges->count() }} demande(s)
                </span>
            </div>
            <a href="{{ route('employe.conges.calendar') }}" 
               class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:from-indigo-700 hover:via-indigo-800 hover:to-indigo-900 transition-all duration-300 ease-in-out shadow-lg hover:shadow-2xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2 transform group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Voir le calendrier
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Formulaire de demande de congé -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl transition-all duration-300 hover:shadow-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Nouvelle demande de congé</h3>
                    </div>
                    
                    <form action="{{ route('employe.conges.demande') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <x-input-label for="date_debut" value="Date de début" class="text-sm font-semibold text-gray-700"/>
                                <div class="relative group">
                                    <x-text-input id="date_debut" type="date" name="date_debut" 
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-300 group-hover:border-indigo-400" required />
                                    <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <x-input-label for="date_fin" value="Date de fin" class="text-sm font-semibold text-gray-700"/>
                                <div class="relative group">
                                    <x-text-input id="date_fin" type="date" name="date_fin" 
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-300 group-hover:border-indigo-400" required />
                                    <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-3">
                                <x-input-label for="motif" value="Motif" class="text-sm font-semibold text-gray-700"/>
                                <div class="relative group">
                                    <x-text-input id="motif" type="text" name="motif" 
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-300 group-hover:border-indigo-400" 
                                        placeholder="Décrivez brièvement le motif de votre demande" required />
                                    <x-input-error :messages="$errors->get('motif')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <x-primary-button class="group bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-700 hover:via-indigo-800 hover:to-indigo-900 transition-all duration-300 ease-in-out transform hover:-translate-y-0.5 shadow-lg hover:shadow-2xl rounded-xl px-8">
                                <svg class="w-5 h-5 mr-2 transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Soumettre la demande
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des congés -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl transition-all duration-300 hover:shadow-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center mb-8">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Historique des demandes</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 rounded-xl overflow-hidden">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($conges as $conge)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($conge->date_debut)->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $conge->motif }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-4 py-1.5 inline-flex text-sm font-semibold rounded-full 
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
                                                    <button type="submit" 
                                                        class="group text-red-600 hover:text-red-900 transition-colors duration-200 flex items-center">
                                                        <svg class="w-4 h-4 mr-2 transform group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Annuler
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <div class="flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4">
                                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </div>
                                                <span class="text-xl font-medium mb-2">Aucune demande de congé</span>
                                                <p class="text-sm text-gray-400">Vos futures demandes apparaîtront ici</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Après la section "Liste des congés" et avant la fin du div principal -->

<!-- Congés des autres employés -->
<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl transition-all duration-300 hover:shadow-2xl border border-gray-100">
    <div class="p-8">
        <div class="flex items-center mb-8">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold text-gray-900">Congés de l'équipe</h3>
                <p class="text-sm text-gray-500 mt-1">Visualisez les absences de vos collègues</p>
            </div>
            <div class="flex space-x-2">
                <select id="mois" class="rounded-lg border-gray-300 text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Tous les mois</option>
                    <option value="1">Janvier</option>
                    <option value="2">Février</option>
                    <!-- ... autres mois ... -->
                </select>
                <select id="annee" class="rounded-lg border-gray-300 text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Employé</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durée</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($autresConges ?? [] as $conge)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-purple-700">
                                            {{ substr($conge->employe->nom, 0, 1) }}{{ substr($conge->employe->prenom, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $conge->employe->prenom }} {{ $conge->employe->nom }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $conge->employe->service }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($conge->date_debut)->diffInDays(\Carbon\Carbon::parse($conge->date_fin)) + 1 }} jours
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-4 py-1.5 inline-flex text-sm font-semibold rounded-full 
                                    {{ $conge->statut === 'accepte' ? 'bg-green-100 text-green-800' : 
                                       ($conge->statut === 'refuse' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($conge->statut) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-lg font-medium">Aucun congé pour le moment</span>
                                    <p class="text-sm text-gray-400">Il n'y a pas de congés prévus pour vos collègues</p>
                                </div>
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