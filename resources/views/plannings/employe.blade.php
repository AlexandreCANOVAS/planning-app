@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filtres et actions -->
            <div class="bg-white bg-white shadow-sm rounded-lg border border-gray-200 border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 text-gray-800">Mon Planning</h2>
                        
                        <!-- Sélecteurs de période -->
                        <div class="flex items-center space-x-4">
                            <select name="mois" id="mois" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" form="filter-form">
                                @foreach(range(1, 12) as $mois)
                                    <option value="{{ $mois }}" {{ $selectedMonth == $mois ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="annee" id="annee" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" form="filter-form">
                                @foreach(range(now()->year - 2, now()->year + 2) as $annee)
                                    <option value="{{ $annee }}" {{ $selectedYear == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('employe.plannings.calendar') }}?mois={{ $selectedMonth }}&annee={{ $selectedYear }}" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 text-gray-700 bg-white bg-white hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Consulter mon planning
                        </a>
                        <a href="{{ route('employe.plannings.download-pdf') }}?mois={{ $selectedMonth }}&annee={{ $selectedYear }}" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Télécharger en PDF
                        </a>
                        <button type="button" onclick="openModificationModal()" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Demander une modification
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Vue Calendrier et Statistiques -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Calendrier -->
                <div class="lg:col-span-2 bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-800 text-gray-800 mb-4">Calendrier du mois</h3>
                        <div id="planning-calendar" class="calendar-container">
                            <div class="grid grid-cols-7 gap-1 mb-2 text-center">
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Lun</div>
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Mar</div>
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Mer</div>
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Jeu</div>
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Ven</div>
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Sam</div>
                                <div class="text-xs font-medium text-gray-500 text-gray-500">Dim</div>
                            </div>
                            <div class="grid grid-cols-7 gap-2" id="calendar-container">
                                <!-- Les jours du calendrier seront générés par JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="bg-white bg-white shadow-sm rounded-lg border border-gray-200 border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-800 text-gray-800 mb-4">Statistiques du mois</h3>
                        <div class="space-y-4">
                            <!-- Total des heures -->
                            <div class="flex justify-between items-center p-3 bg-blue-100 rounded-lg border border-blue-200">
                                <span class="text-sm font-medium text-blue-800">Total des heures</span>
                                <span class="text-lg font-bold text-blue-800">{{ $totalHeures ?? '0' }}h</span>
                            </div>
                            
                            <!-- Heures supplémentaires -->
                            <div class="flex justify-between items-center p-3 bg-indigo-100 rounded-lg border border-indigo-200">
                                <span class="text-sm font-medium text-indigo-800">Heures supplémentaires</span>
                                <span class="text-lg font-bold text-indigo-800">{{ $heuresSupplementaires ?? '0' }}h</span>
                            </div>
                            
                            <!-- Heures de nuit -->
                            <div class="flex justify-between items-center p-3 bg-purple-100 rounded-lg border border-purple-200">
                                <span class="text-sm font-medium text-purple-800">Heures de nuit</span>
                                <span class="text-lg font-bold text-purple-800">{{ $heuresNuit ?? '0' }}h</span>
                            </div>
                            
                            <!-- Jours fériés travaillés -->
                            <div class="flex justify-between items-center p-3 bg-violet-100 rounded-lg border border-violet-200">
                                <span class="text-sm font-medium text-violet-800">Jours fériés travaillés</span>
                                <span class="text-lg font-bold text-violet-800">{{ $joursFeries ?? '0' }}</span>
                            </div>
                            
                            <!-- Dimanches travaillés -->
                            <div class="flex justify-between items-center p-3 bg-pink-100 rounded-lg border border-pink-200">
                                <span class="text-sm font-medium text-pink-800">Dimanches travaillés</span>
                                <span class="text-lg font-bold text-pink-800">{{ $dimanchesTravailles ?? '0' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphique et Notifications -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Graphique d'évolution -->
                <div class="bg-white bg-white shadow-sm rounded-lg border border-gray-200 border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-800 text-gray-800 mb-4">Évolution des heures travaillées</h3>
                        <div class="h-64">
                            <canvas id="heuresChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications de changements -->
                <div class="bg-white bg-white shadow-sm rounded-lg border border-gray-200 border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-800 text-gray-800 mb-4">Modifications récentes</h3>
                        <div class="space-y-4" id="notifications-container">
                            @if(isset($modifications) && count($modifications) > 0)
                                @foreach($modifications as $modification)
                                    <div class="flex items-start p-3 bg-gray-50 bg-white rounded-lg">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                @php
                                                    $iconClass = 'text-blue-600 dark:text-blue-300';
                                                    $iconPath = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                                    
                                                    if ($modification->type_modification === 'horaires') {
                                                        $iconClass = 'text-purple-600 dark:text-purple-300';
                                                        $iconPath = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                                    } elseif ($modification->type_modification === 'lieu') {
                                                        $iconClass = 'text-green-600 dark:text-green-300';
                                                        $iconPath = 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z';
                                                    } elseif ($modification->type_modification === 'absence') {
                                                        $iconClass = 'text-red-600 dark:text-red-300';
                                                        $iconPath = 'M6 18L18 6M6 6l12 12';
                                                    }
                                                @endphp
                                                <svg class="w-4 h-4 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 text-gray-800">
                                                @if($modification->type_modification === 'horaires')
                                                    Demande de changement d'horaires
                                                @elseif($modification->type_modification === 'lieu')
                                                    Demande de changement de lieu
                                                @elseif($modification->type_modification === 'absence')
                                                    Demande d'absence
                                                @else
                                                    Demande de modification
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 text-gray-500">{{ $modification->motif }}</p>
                                            <div class="flex items-center mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $modification->statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : ($modification->statut === 'accepte' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300') }}">
                                                    {{ $modification->statut === 'en_attente' ? 'En attente' : ($modification->statut === 'accepte' ? 'Acceptée' : 'Refusée') }}
                                                </span>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 ml-2">{{ $modification->date_demande->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center justify-center p-6 text-gray-500 text-gray-500">
                                    <p>Aucune modification récente</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal de demande de modification -->
            <div id="modification-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-gray-900" id="modal-title">
                                        Demande de modification de planning
                                    </h3>
                                    <div class="mt-4">
                                        <form id="modification-form" action="{{ route('employe.plannings.demande-modification') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label for="date_demande" class="block text-sm font-medium text-gray-700 text-gray-700">Date concernée</label>
                                                <input type="date" name="date_demande" id="date_demande" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            </div>
                                            <div>
                                                <label for="type_modification" class="block text-sm font-medium text-gray-700 text-gray-700">Type de modification</label>
                                                <select name="type_modification" id="type_modification" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                                    <option value="">Sélectionner un type</option>
                                                    <option value="horaires">Changement d'horaires</option>
                                                    <option value="lieu">Changement de lieu</option>
                                                    <option value="absence">Demande d'absence</option>
                                                    <option value="autre">Autre</option>
                                                </select>
                                            </div>
                                            <div id="horaires-container" class="hidden space-y-4">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="heure_debut" class="block text-sm font-medium text-gray-700 text-gray-700">Heure de début</label>
                                                        <input type="time" name="heure_debut" id="heure_debut" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label for="heure_fin" class="block text-sm font-medium text-gray-700 text-gray-700">Heure de fin</label>
                                                        <input type="time" name="heure_fin" id="heure_fin" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="lieu-container" class="hidden">
                                                <label for="lieu_id" class="block text-sm font-medium text-gray-700 text-gray-700">Nouveau lieu</label>
                                                <select name="lieu_id" id="lieu_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="">Sélectionner un lieu</option>
                                                    @foreach(App\Models\Lieu::where('societe_id', auth()->user()->societe_id)->orderBy('nom')->get() as $lieu)
                                                        <option value="{{ $lieu->id }}">{{ $lieu->nom }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label for="motif" class="block text-sm font-medium text-gray-700 text-gray-700">Motif de la demande</label>
                                                <textarea name="motif" id="motif" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                            </div>
                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                <button type="submit" form="modification-form" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Envoyer la demande
                                                </button>
                                                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm close-modal">
                                                    Annuler
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" onclick="submitModificationForm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Soumettre la demande
                            </button>
                            <button type="button" onclick="closeModificationModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white bg-white text-base font-medium text-gray-700 text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form id="filter-form" method="GET" action="{{ route('plannings.view-monthly-calendar', [Auth::id(), date('m'), date('Y')]) }}" class="hidden"></form>

            <!-- Planning Collègue -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Planning Collègue</h2>
                
                <div class="space-y-6">
                    <div class="space-y-6">
                        <!-- Sélection du collègue et période -->
                        <div class="flex items-center space-x-4">
                            <select id="employe_id" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Sélectionner un collègue</option>
                                @foreach(App\Models\Employe::where('societe_id', auth()->user()->societe_id)
                                        ->where('id', '!=', auth()->user()->employe->id)
                                        ->orderBy('nom')
                                        ->get() as $collegue)
                                    <option value="{{ $collegue->id }}">{{ $collegue->nom }} {{ $collegue->prenom }}</option>
                                @endforeach
                            </select>
                            <select id="mois_collegue" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach(range(1, 12) as $mois)
                                    <option value="{{ $mois }}" {{ now()->month == $mois ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName }}
                                    </option>
                                @endforeach
                            </select>
                            <select id="annee_collegue" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach(range(now()->year - 2, now()->year + 2) as $annee)
                                    <option value="{{ $annee }}" {{ now()->year == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bouton de consultation -->
                        <div>
                            <button type="button" onclick="voirPlanningCollegue()" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Voir le planning
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function voirPlanningCollegue() {
                        const employeId = document.getElementById('employe_id').value;
                        if (!employeId) {
                            alert('Veuillez sélectionner un collègue');
                            return;
                        }
                        const mois = document.getElementById('mois_collegue').value;
                        const annee = document.getElementById('annee_collegue').value;
                        
                        // Construire l'URL avec le paramètre employe dans le chemin
                        const url = "{{ route('employe.plannings.collegue', ['employe' => ':employe']) }}".replace(':employe', employeId) + 
                            '?mois=' + mois + '&annee=' + annee;
                        
                        window.location.href = url;
                    }
                </script>
            </div>
        </div>
    </div>

    <!-- Chargement de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Débogage - Vérifier si le script est chargé
        console.log('Script chargé');
        
        // Mise à jour automatique des paramètres dans les liens quand les sélecteurs changent
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, initialisation du calendrier et du graphique');
            
            // Déboguer les données brutes reçues du contrôleur
            console.log('Données du planning brutes:', @json($planningData ?? []));
            console.log('Données du graphique brutes:', @json($donneesGraphique ?? []));
            console.log('Statistiques:', {
                totalHeures: @json($totalHeures ?? 0),
                heuresSupplementaires: @json($heuresSupplementaires ?? 0),
                heuresNuit: @json($heuresNuit ?? 0),
                joursFeries: @json($joursFeries ?? 0),
                dimanchesTravailles: @json($dimanchesTravailles ?? 0)
            });
            
            const moisSelect = document.getElementById('mois');
            const anneeSelect = document.getElementById('annee');
            
            if (moisSelect) {
                moisSelect.addEventListener('change', updateLinks);
                console.log('Event listener ajouté pour mois');
            } else {
                console.error('Element mois non trouvé');
            }
            
            if (anneeSelect) {
                anneeSelect.addEventListener('change', updateLinks);
                console.log('Event listener ajouté pour annee');
            } else {
                console.error('Element annee non trouvé');
            }
            
            // Tenter de générer le calendrier et le graphique
            try {
                console.log('Tentative de génération du calendrier...');
                generateCalendar();
                console.log('Calendrier généré');
            } catch (e) {
                console.error('Erreur lors de la génération du calendrier:', e);
            }
            
            try {
                console.log('Tentative d\'initialisation du graphique...');
                initChart();
                console.log('Graphique initialisé');
            } catch (e) {
                console.error('Erreur lors de l\'initialisation du graphique:', e);
            }
            
            try {
                setupModalHandlers();
                console.log('Gestionnaires de modal configurés');
            } catch (e) {
                console.error('Erreur lors de la configuration des gestionnaires de modal:', e);
            }
        });

        function updateLinks() {
            console.log('updateLinks appelé');
            const mois = document.getElementById('mois').value;
            const annee = document.getElementById('annee').value;
            
            // Mettre à jour les liens avec les nouveaux paramètres
            const calendarLink = document.querySelector('a[href*="plannings.calendar"]');
            if (calendarLink) {
                calendarLink.href = "{{ route('employe.plannings.calendar') }}?mois=" + mois + "&annee=" + annee;
            } else {
                console.error('Lien calendar non trouvé');
            }
            
            const pdfLink = document.querySelector('a[href*="plannings.download-pdf"]');
            if (pdfLink) {
                pdfLink.href = "{{ route('employe.plannings.download-pdf') }}?mois=" + mois + "&annee=" + annee;
            } else {
                console.error('Lien download-pdf non trouvé');
            }

            // Soumettre le formulaire pour mettre à jour le total des heures
            const filterForm = document.getElementById('filter-form');
            if (filterForm) {
                filterForm.submit();
            } else {
                console.error('Formulaire filter-form non trouvé');
            }
        }

        function generateCalendar() {
            const calendarContainer = document.getElementById('calendar-container');
            if (!calendarContainer) {
                console.error('Élément calendar-container non trouvé');
                return;
            }
            
            // Récupérer les données du planning depuis le contrôleur
            let planningData;
            try {
                planningData = @json($planningData ?? []);
                console.log('Données du planning (brut):', planningData);
            } catch (e) {
                console.error('Erreur lors de la récupération des données du planning:', e);
                planningData = {};
            }
            
            // Vider le conteneur
            calendarContainer.innerHTML = '';
            calendarContainer.className = 'grid grid-cols-7 gap-2';
            
            // Obtenir le premier jour du mois et le nombre de jours
            const year = {{ $selectedYear ?? now()->year }};
            const month = {{ $selectedMonth ?? now()->month }};
            
            // Déterminer le premier jour du mois et le nombre de jours dans le mois
            const firstDay = new Date(year, month - 1, 1);
            const lastDay = new Date(year, month, 0);
            const daysInMonth = lastDay.getDate();
            
            // Déterminer le décalage pour le premier jour du mois (0 = dimanche, 1 = lundi, etc.)
            let startOffset = firstDay.getDay() - 1; // Commencer par lundi
            if (startOffset < 0) startOffset = 6; // Si c'est dimanche, mettre à la fin
            
            // Ajouter des cellules vides pour les jours avant le début du mois
            for (let i = 0; i < startOffset; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'h-24 border border-gray-200 rounded-lg';
                calendarContainer.appendChild(emptyCell);
            }
            
            // Ajouter les jours du mois
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayData = planningData[dateStr];
                
                // Créer l'élément de jour
                const dayCell = document.createElement('div');
                dayCell.className = 'h-24 border border-gray-200 rounded-lg overflow-hidden';
                
                // Ajouter une classe selon le type de jour
                if (dayData) {
                    if (dayData.type) {
                        if (dayData.type === 'weekend') {
                            dayCell.classList.add('bg-gray-100');
                        } else if (dayData.type === 'conge') {
                            dayCell.classList.add('bg-green-50');
                        } else if (dayData.type === 'formation') {
                            dayCell.classList.add('bg-blue-50');
                        } else if (dayData.type === 'ferie') {
                            dayCell.classList.add('bg-purple-50');
                        } else {
                            // Type normal ou autre
                            dayCell.classList.add('bg-white');
                        }
                    } else {
                        // Déterminer si c'est un weekend
                        const currentDate = new Date(year, month - 1, day);
                        const isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6;
                        
                        if (isWeekend) {
                            dayCell.classList.add('bg-gray-100');
                        } else {
                            dayCell.classList.add('bg-white');
                        }
                    }
                } else {
                    // Pas de données pour ce jour
                    const currentDate = new Date(year, month - 1, day);
                    const isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6;
                    
                    if (isWeekend) {
                        dayCell.classList.add('bg-gray-100');
                    } else {
                        dayCell.classList.add('bg-white');
                    }
                }
                
                // Contenu du jour - style épuré comme dans l'exemple
                const dayContent = document.createElement('div');
                dayContent.className = 'p-2 h-full';
                
                // Numéro du jour en haut à gauche
                const dayNumber = document.createElement('div');
                dayNumber.className = 'text-sm font-bold mb-1';
                dayNumber.textContent = day;
                dayContent.appendChild(dayNumber);
                
                // Informations du planning si disponibles
                if (dayData && dayData.planning) {
                    try {
                        // Lieu de travail
                        if (dayData.planning.lieu) {
                            const locationInfo = document.createElement('div');
                            locationInfo.className = 'text-sm font-medium';
                            
                            // Vérifier si lieu est un objet ou une chaîne
                            if (typeof dayData.planning.lieu === 'object' && dayData.planning.lieu !== null) {
                                locationInfo.textContent = dayData.planning.lieu.nom || '';
                            } else {
                                locationInfo.textContent = dayData.planning.lieu || '';
                            }
                            
                            if (locationInfo.textContent) {
                                dayContent.appendChild(locationInfo);
                            }
                        }
                        
                        // Horaires
                        if (dayData.planning.heure_debut && dayData.planning.heure_fin) {
                            const hoursInfo = document.createElement('div');
                            hoursInfo.className = 'text-sm text-gray-600';
                            hoursInfo.textContent = `${dayData.planning.heure_debut} - ${dayData.planning.heure_fin}`;
                            dayContent.appendChild(hoursInfo);
                        }
                    } catch (e) {
                        console.error(`Erreur lors de l'affichage des données pour le jour ${dateStr}:`, e);
                    }
                }
                
                // Ajouter une interaction au clic sur la cellule
                dayCell.style.cursor = 'pointer';
                dayCell.addEventListener('click', function() {
                    // Préremplir la date dans le formulaire de demande de modification
                    const dateDemande = document.getElementById('date_demande');
                    if (dateDemande) {
                        dateDemande.value = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    }
                    
                    // Afficher le modal de demande de modification
                    const modal = document.getElementById('modification-modal');
                    if (modal) {
                        modal.classList.remove('hidden');
                    }
                });
                
                dayCell.appendChild(dayContent);
                calendarContainer.appendChild(dayCell);
            }
            
            // Ajouter les cellules vides pour les jours après la fin du mois
            const totalCells = startOffset + daysInMonth;
            const remainingCells = 7 - (totalCells % 7);
            if (remainingCells < 7) {
                for (let i = 0; i < remainingCells; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'h-24 border border-gray-200 border-gray-200 rounded-lg';
                    calendarContainer.appendChild(emptyCell);
                }
            }
        }

        function initChart() {
            const ctx = document.getElementById('heuresChart');
            if (!ctx) {
                console.error('Élément heuresChart non trouvé');
                return;
            }
            
            // Récupérer les données du graphique depuis le contrôleur
            let donneesGraphique;
            try {
                donneesGraphique = @json($donneesGraphique ?? []);
                console.log('Données du graphique:', donneesGraphique);
                
                // Déboguer les données du graphique
                if (donneesGraphique.length === 0) {
                    console.warn('Aucune donnée de graphique disponible!');
                } else {
                    console.log('Structure des données:', donneesGraphique[0]);
                }
            } catch (e) {
                console.error('Erreur lors de la récupération des données du graphique:', e);
                // Données de secours si les données du contrôleur ne sont pas disponibles
                donneesGraphique = [
                    { mois: 'Jan', heures: 150 },
                    { mois: 'Fév', heures: 160 },
                    { mois: 'Mar', heures: 155 },
                    { mois: 'Avr', heures: 165 },
                    { mois: 'Mai', heures: 170 },
                    { mois: 'Juin', heures: 168 }
                ];
            }
            
            // S'assurer que les données sont valides et complètes
            if (!Array.isArray(donneesGraphique) || donneesGraphique.length === 0) {
                console.warn('Données du graphique invalides ou vides, utilisation des données de secours');
                donneesGraphique = [
                    { mois: 'Jan', heures: 150 },
                    { mois: 'Fév', heures: 160 },
                    { mois: 'Mar', heures: 155 },
                    { mois: 'Avr', heures: 165 },
                    { mois: 'Mai', heures: 170 },
                    { mois: 'Juin', heures: 168 }
                ];
            }
            
            // Vérifier que chaque objet a les propriétés requises
            donneesGraphique = donneesGraphique.filter(item => {
                return item && typeof item === 'object' && 'mois' in item && 'heures' in item;
            });
            
            // S'assurer que les heures sont des nombres
            donneesGraphique = donneesGraphique.map(item => ({
                mois: item.mois,
                heures: parseFloat(item.heures) || 0
            }));
            
            // Préparer les données pour Chart.js
            const chartData = {
                labels: donneesGraphique.map(item => item.mois),
                datasets: [{
                    label: 'Heures travaillées',
                    data: donneesGraphique.map(item => item.heures),
                    backgroundColor: 'rgba(99, 102, 241, 0.3)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true
                }]
            };
            
            // Configuration du graphique
            const chartConfig = {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: Math.max(...donneesGraphique.map(item => item.heures)) * 1.1, // Ajoute 10% d'espace au-dessus
                            ticks: {
                                callback: function(value) {
                                    return value + 'h';
                                },
                                font: {
                                    weight: 'bold',
                                    size: 11
                                },
                                color: '#4B5563' // text-gray-600
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                borderDash: [3, 3]
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold',
                                    size: 11
                                },
                                color: '#4B5563' // text-gray-600
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1f2937',
                            bodyColor: '#4b5563',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            borderColor: 'rgba(99, 102, 241, 0.5)',
                            borderWidth: 1,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' heures';
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            };
            
            // Créer le graphique
            new Chart(ctx, chartConfig);
        }

        // Gestion du modal de demande de modification
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour fermer le modal
            function closeModal() {
                const modal = document.getElementById('modification-modal');
                if (modal) {
                    modal.classList.add('hidden');
                }
            }
            
            // Ajouter des gestionnaires d'événements pour fermer le modal
            const closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });
            
            // Fermer le modal en cliquant sur l'arrière-plan
            const modalBackground = document.querySelector('#modification-modal .fixed.inset-0');
            if (modalBackground) {
                modalBackground.addEventListener('click', closeModal);
            }
            
            // Empêcher la propagation du clic depuis le contenu du modal
            const modalContent = document.querySelector('#modification-modal .bg-white.rounded-lg');
            if (modalContent) {
                modalContent.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
        function setupModalHandlers() {
            // Gérer l'affichage des champs en fonction du type de modification
            document.getElementById('type_modification').addEventListener('change', function() {
                const horairesContainer = document.getElementById('horaires-container');
                const lieuContainer = document.getElementById('lieu-container');
                
                horairesContainer.classList.add('hidden');
                lieuContainer.classList.add('hidden');
                
                if (this.value === 'horaires') {
                    horairesContainer.classList.remove('hidden');
                } else if (this.value === 'lieu') {
                    lieuContainer.classList.remove('hidden');
                }
            });
        }

        function openModificationModal() {
            document.getElementById('modification-modal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModificationModal() {
            document.getElementById('modification-modal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function submitModificationForm() {
            document.getElementById('modification-form').submit();
        }
    </script>
@endsection
