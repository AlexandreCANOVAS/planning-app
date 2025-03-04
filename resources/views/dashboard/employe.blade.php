<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Heures travaillées -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Heures cette semaine</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['heures_semaine'], 2) }}h</div>
                    </div>
                </div>

                <!-- Congés restants -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Congés restants</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['conges_restants'] }} jours</div>
                    </div>
                </div>

                <!-- Plannings actifs -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Prochain service</div>
                        <div class="text-xl font-bold text-gray-900">
                            @if($stats['prochain_planning'])
                                {{ \Carbon\Carbon::parse($stats['prochain_planning']->date)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lieu de travail -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Lieu actuel</div>
                        <div class="text-xl font-bold text-gray-900">{{ $stats['prochain_planning']->lieu->nom ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides et Activité récente -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Actions rapides -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('employe.plannings.index') }}" class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4 hover:bg-gray-50 transition">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Voir mon planning</div>
                                <div class="text-sm text-gray-500">Consultez vos horaires</div>
                            </div>
                        </a>

                        <a href="{{ route('employe.conges.index') }}" class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4 hover:bg-gray-50 transition">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Demander un congé</div>
                                <div class="text-sm text-gray-500">Nouvelle demande</div>
                            </div>
                        </a>

                        <div class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Mes documents</div>
                                <div class="text-sm text-gray-500">Bientôt disponible</div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4">
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Mon profil</div>
                                <div class="text-sm text-gray-500">Bientôt disponible</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activité récente -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Activité récente</h2>
                    <div class="bg-white rounded-xl shadow-sm divide-y divide-gray-200">
                        @forelse($plannings->take(5) as $planning)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Service prévu le {{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}
                                            à {{ $planning->lieu->nom }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Confirmé
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-sm text-gray-500 italic">
                                Aucune activité récente
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Documents et Exports -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Documents et Exports</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Planning mensuel -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="mb-4">
                            <div class="p-3 bg-blue-100 rounded-lg w-fit">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Planning mensuel</h3>
                        <p class="text-sm text-gray-500 mb-4">Exportez votre planning au format PDF</p>
                        <form action="{{ route('employe.plannings.download-pdf') }}" method="GET" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mois :</label>
                                <select name="mois" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->monthName }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Année :</label>
                                <select name="annee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Télécharger
                            </button>
                        </form>
                    </div>

                    <!-- Rapports d'activité -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="mb-4">
                            <div class="p-3 bg-yellow-100 rounded-lg w-fit">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Rapports d'activité</h3>
                        <p class="text-sm text-gray-500 mb-4">Suivez votre activité</p>
                        <div class="text-sm text-center text-gray-500 italic">
                            Bientôt disponible
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>