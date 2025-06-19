@extends('layouts.app')

@section('content')
@php
use App\Models\Lieu;
@endphp

<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-8 lg:px-8">
        <!-- Modal de notification centrée -->
        <div id="notification-container" class="fixed inset-0 flex items-center justify-center z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="hideNotification()"></div>
            <div id="notification" class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div id="notification-icon" class="mr-4 flex-shrink-0"></div>
                        <h3 id="notification-title" class="text-lg font-medium">Notification</h3>
                    </div>
                    <div class="mb-5">
                        <p id="notification-message" class="text-base"></p>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button onclick="hideNotification()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Fermer
                        </button>
                    </div>
                </div>
                <button onclick="hideNotification()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">
                    @if(request()->is('*/create-monthly-calendar') && !request()->has('from_modification'))
                        Création du Planning - {{ $employe->nom }} {{ $employe->prenom }}
                    @else
                        Modification du Planning - {{ $employe->nom }} {{ $employe->prenom }}
                    @endif
                </h2>
                <div class="flex gap-4">
                    <button 
                        type="button" 
                        onclick="showForm()"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-200"
                    >
                        Remplir sélection multiple
                    </button>
                    <button 
                        type="button" 
                        onclick="remplirJoursRepos()"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                    >
                        Remplir les jours non travaillés en repos
                    </button>
                    <button 
                        type="button" 
                        onclick="ajouterConge()"
                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        Ajouter un congé payé
                    </button>
                    <button 
                        type="button" 
                        onclick="ajouterReposSelection()"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-200"
                    >
                        Ajouter les jours de repos (RH)
                    </button>
                    <a href="{{ route('plannings.calendar') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Retour au calendrier
                    </a>
                </div>
            </div>

            <!-- Calendrier du mois actuel (à remplir) -->
            <div class="max-w-5xl mx-auto current-calendar mb-8">
                <div class="flex items-center mb-4">
                    <h3 class="text-lg font-semibold">Mois actuel : {{ Carbon\Carbon::create($annee, $mois, 1)->locale('fr')->monthName }} {{ $annee }}</h3>
                    <span class="text-sm text-blue-500 ml-2 px-2 py-1 border border-blue-300 rounded-md">(En cours de création)</span>
                </div>
                <div class="grid grid-cols-7 gap-1 mb-6">
                    <div class="text-center font-semibold">Lun</div>
                    <div class="text-center font-semibold">Mar</div>
                    <div class="text-center font-semibold">Mer</div>
                    <div class="text-center font-semibold">Jeu</div>
                    <div class="text-center font-semibold">Ven</div>
                    <div class="text-center font-semibold">Sam</div>
                    <div class="text-center font-semibold">Dim</div>

                @php
                    $currentDate = $debutPeriode->copy();
                @endphp

                @while($currentDate <= $finPeriode)
                    @php
                        $isCurrentMonth = $currentDate->month == $mois;
                        $currentDateStr = $currentDate->format('Y-m-d');
                        $dayPlannings = $planningsByDate[$currentDateStr] ?? null;
                        
                        $bgClass = '';
                        if ($dayPlannings) {
                            $bgClass = 'bg-blue-50';
                        }
                        if (!$isCurrentMonth) {
                            $bgClass = 'bg-gray-50';
                        }
                    @endphp

                    <div class="calendar-cell border p-2 {{ $bgClass }}" 
                         data-date="{{ $currentDateStr }}"
                         @if($isCurrentMonth) onclick="toggleDateSelection(this)" @endif>
                        <div class="text-right mb-2">{{ $currentDate->format('d') }}</div>
                        
                        @if($dayPlannings)
                            @if($dayPlannings['journee'])
                                <div class="planning-details text-xs">
                                    <div class="font-semibold">{{ $dayPlannings['journee']->lieu->nom ?? 'Non défini' }}</div>
                                    @if($dayPlannings['journee']->lieu && !in_array($dayPlannings['journee']->lieu->nom, ['RH', 'CP']))
                                        <div>
                                            {{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if($dayPlannings['matin'])
                                    <div class="planning-details text-xs">
                                        <div class="font-semibold">{{ $dayPlannings['matin']->lieu->nom ?? 'Non défini' }}</div>
                                        @if($dayPlannings['matin']->lieu && !in_array($dayPlannings['matin']->lieu->nom, ['RH', 'CP']))
                                            <div>
                                                {{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($dayPlannings['apres-midi'])
                                    @if($dayPlannings['matin'])
                                        <div class="mt-1 border-t border-gray-200 pt-1"></div>
                                    @endif
                                    <div class="planning-details text-xs">
                                        <div class="font-semibold">{{ $dayPlannings['apres-midi']->lieu->nom ?? 'Non défini' }}</div>
                                        @if($dayPlannings['apres-midi']->lieu && !in_array($dayPlannings['apres-midi']->lieu->nom, ['RH', 'CP']))
                                            <div>
                                                {{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                    @php
                        $currentDate->addDay();
                    @endphp
                @endwhile
            </div>
            </div>

            <!-- Calendrier du mois précédent (référence) -->
            <div class="max-w-5xl mx-auto reference-calendar mt-6 mb-8">
                <div class="flex items-center mb-4">
                    <h3 class="text-lg font-semibold">Mois précédent : {{ Carbon\Carbon::create($anneePrecedente, $moisPrecedent, 1)->locale('fr')->monthName }} {{ $anneePrecedente }}</h3>
                    <span class="text-sm text-gray-500 ml-2 px-2 py-1 border border-gray-300 rounded-md">(Référence)</span>
                    <div class="ml-auto text-sm text-gray-600">Consultez ce planning pour vous aider à créer celui du mois actuel</div>
                </div>
                <div class="grid grid-cols-7 gap-1 mb-6">
                    <div class="text-center font-semibold">Lun</div>
                    <div class="text-center font-semibold">Mar</div>
                    <div class="text-center font-semibold">Mer</div>
                    <div class="text-center font-semibold">Jeu</div>
                    <div class="text-center font-semibold">Ven</div>
                    <div class="text-center font-semibold">Sam</div>
                    <div class="text-center font-semibold">Dim</div>

                    @php
                        $currentDate = $debutPeriodePrecedent->copy();
                    @endphp

                    @while($currentDate <= $finPeriodePrecedent)
                        @php
                            $isCurrentMonth = $currentDate->month == $moisPrecedent;
                            $currentDateStr = $currentDate->format('Y-m-d');
                            $dayPlannings = $planningsByDatePrecedent[$currentDateStr] ?? null;
                            
                            $bgClass = '';
                            if ($dayPlannings) {
                                $bgClass = 'bg-blue-50';
                            }
                            if (!$isCurrentMonth) {
                                $bgClass = 'bg-gray-50';
                            }
                        @endphp

                        <div class="calendar-cell-prev border p-2 {{ $bgClass }}" data-date="{{ $currentDateStr }}">
                            <div class="text-right mb-2">{{ $currentDate->format('d') }}</div>
                            
                            @if($dayPlannings)
                                @if($dayPlannings['journee'])
                                    <div class="planning-details text-xs">
                                        <div class="font-semibold">{{ $dayPlannings['journee']->lieu->nom ?? 'Non défini' }}</div>
                                        @if($dayPlannings['journee']->lieu && !in_array($dayPlannings['journee']->lieu->nom, ['RH', 'CP']))
                                            <div>
                                                {{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($dayPlannings['matin'])
                                        <div class="planning-details text-xs">
                                            <div class="font-semibold">{{ $dayPlannings['matin']->lieu->nom ?? 'Non défini' }}</div>
                                            @if($dayPlannings['matin']->lieu && !in_array($dayPlannings['matin']->lieu->nom, ['RH', 'CP']))
                                                <div>
                                                    {{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($dayPlannings['apres-midi'])
                                        @if($dayPlannings['matin'])
                                            <div class="mt-1 border-t border-gray-200 pt-1"></div>
                                        @endif
                                        <div class="planning-details text-xs">
                                            <div class="font-semibold">{{ $dayPlannings['apres-midi']->lieu->nom ?? 'Non défini' }}</div>
                                            @if($dayPlannings['apres-midi']->lieu && !in_array($dayPlannings['apres-midi']->lieu->nom, ['RH', 'CP']))
                                                <div>
                                                    {{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" onclick="creerPlanning()" 
                    class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    id="btnCreerPlanning"
                >
                    @if(request()->has('from_modification'))
                        <span>Modifier le planning</span>
                    @else
                        <span>Créer le planning</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Formulaire de planning -->
<div id="planningForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium">Remplir les plannings</h3>
                <button onclick="closeForm()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Lieu de travail -->
            <div>
                <label for="lieu_id" class="block text-sm font-medium text-gray-700">Lieu de travail</label>
                <select id="lieu_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Sélectionnez un lieu</option>
                    @foreach($lieux->where('is_special', false) as $lieu)
                        <option value="{{ $lieu->id }}" data-color="{{ $lieu->couleur }}">{{ $lieu->nom }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type d'horaires -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Type d'horaires</label>
                <div class="mt-2 space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="type_horaire" value="simple" class="form-radio" checked onchange="toggleHoraires()">
                        <span class="ml-2">Simple</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="type_horaire" value="compose" class="form-radio" onchange="toggleHoraires()">
                        <span class="ml-2">Composé</span>
                    </label>
                </div>
            </div>

            <!-- Horaires simples -->
            <div id="horaires_simples">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="heure_debut_simple" class="block text-sm font-medium text-gray-700">Heure de début</label>
                        <input type="time" id="heure_debut_simple" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="heure_fin_simple" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                        <input type="time" id="heure_fin_simple" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Horaires composés -->
            <div id="horaires_composes" class="hidden">
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Matin</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="heure_debut_matin" class="block text-sm font-medium text-gray-700">Début</label>
                            <input type="time" id="heure_debut_matin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="heure_fin_matin" class="block text-sm font-medium text-gray-700">Fin</label>
                            <input type="time" id="heure_fin_matin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Après-midi</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="heure_debut_aprem" class="block text-sm font-medium text-gray-700">Début</label>
                            <input type="time" id="heure_debut_aprem" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="heure_fin_aprem" class="block text-sm font-medium text-gray-700">Fin</label>
                            <input type="time" id="heure_fin_aprem" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeForm()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button type="button" onclick="savePlanning()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fonctions pour gérer les notifications modales
    window.showNotification = function(type, message) {
        const container = document.getElementById('notification-container');
        const notification = document.getElementById('notification');
        const notificationIcon = document.getElementById('notification-icon');
        const notificationMessage = document.getElementById('notification-message');
        const notificationTitle = document.getElementById('notification-title');
        
        // Définir le message
        notificationMessage.textContent = message;
        
        // Configurer le type de notification
        if (type === 'success') {
            notificationTitle.textContent = 'Succès';
            notification.className = 'relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all border-t-4 border-green-500';
            notificationIcon.innerHTML = `
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            `;
        } else if (type === 'error') {
            notificationTitle.textContent = 'Erreur';
            notification.className = 'relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all border-t-4 border-red-500';
            notificationIcon.innerHTML = `
                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            `;
        } else if (type === 'info') {
            notificationTitle.textContent = 'Information';
            notification.className = 'relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all border-t-4 border-blue-500';
            notificationIcon.innerHTML = `
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            `;
        }
        
        // Afficher la modal
        document.body.classList.add('overflow-hidden'); // Empêcher le défilement
        container.classList.remove('hidden');
        
        // Animation d'entrée
        notification.classList.add('animate-fade-in-scale');
        
        // Masquer automatiquement après 8 secondes pour les succès
        if (type === 'success') {
            setTimeout(hideNotification, 8000);
        }
    };
    
    window.hideNotification = function() {
        const container = document.getElementById('notification-container');
        const notification = document.getElementById('notification');
        
        // Animation de sortie
        notification.classList.remove('animate-fade-in-scale');
        notification.classList.add('animate-fade-out-scale');
        
        // Masquer après l'animation
        setTimeout(() => {
            container.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            notification.classList.remove('animate-fade-out-scale');
        }, 300);
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        let selectedDates = [];
        let employeId = {{ $employe->id }};
        let selectedLieuId = null;
        let temporaryPlannings = {};

        // Initialiser les plannings existants
        @foreach($planningsByDate as $date => $dayPlannings)
            @if($dayPlannings['journee'] || ($dayPlannings['matin'] && $dayPlannings['apres-midi']))
                temporaryPlannings['{{ $date }}'] = {
                    date: '{{ $date }}',
                    @if($dayPlannings['journee'])
                        lieu_id: {{ $dayPlannings['journee']->lieu_id }},
                        lieu_nom: '{{ $dayPlannings['journee']->lieu->nom ?? "Non défini" }}',
                        type_horaire: 'simple',
                        horaires: {
                            debut: '{{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }}',
                            fin: '{{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}'
                        }
                    @elseif($dayPlannings['matin'] && $dayPlannings['apres-midi'])
                        lieu_id: {{ $dayPlannings['matin']->lieu_id }},
                        lieu_nom: '{{ $dayPlannings['matin']->lieu->nom ?? "Non défini" }}',
                        type_horaire: 'compose',
                        horaires: {
                            debut_matin: '{{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }}',
                            fin_matin: '{{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}',
                            debut_aprem: '{{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }}',
                            fin_aprem: '{{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}'
                        }
                    @endif
                };
                
                // Marquer visuellement les cellules avec des plannings existants
                setTimeout(() => {
                    const cell = document.querySelector(`.calendar-cell[data-date="{{ $date }}"]`);
                    if (cell) {
                        cell.classList.add('has-planning');
                    }
                }, 100);
            @endif
        @endforeach

        window.toggleHoraires = function() {
            const isCompose = document.querySelector('input[name="type_horaire"]:checked').value === 'compose';
            document.getElementById('horaires_simples').classList.toggle('hidden', isCompose);
            document.getElementById('horaires_composes').classList.toggle('hidden', !isCompose);
        };

        window.showForm = function() {
            document.getElementById('planningForm').classList.remove('hidden');
        };

        window.closeForm = function() {
            document.getElementById('planningForm').classList.add('hidden');
            document.getElementById('lieu_id').value = '';
            document.querySelector('input[name="type_horaire"][value="simple"]').checked = true;
            toggleHoraires();
        };

        window.toggleDateSelection = function(cell) {
            const date = cell.dataset.date;
            if (selectedDates.includes(date)) {
                selectedDates = selectedDates.filter(d => d !== date);
                cell.classList.remove('selected', 'bg-blue-200');
            } else {
                selectedDates.push(date);
                cell.classList.add('selected', 'bg-blue-200');
            }

            document.querySelector('button[onclick="showForm()"]').disabled = selectedDates.length === 0;
        };

        document.getElementById('lieu_id').addEventListener('change', function() {
            selectedLieuId = this.value;
        });

        window.savePlanning = function() {
            if (!selectedLieuId) {
                alert('Veuillez sélectionner un lieu de travail');
                return;
            }

            if (selectedDates.length === 0) {
                alert('Veuillez sélectionner au moins un jour');
                return;
            }

            const typeHoraire = document.querySelector('input[name="type_horaire"]:checked').value;
            const lieuNom = document.querySelector('#lieu_id option:checked').text;
            
            let horaires = {};
            if (typeHoraire === 'simple') {
                horaires = {
                    debut: document.getElementById('heure_debut_simple').value,
                    fin: document.getElementById('heure_fin_simple').value
                };
            } else {
                horaires = {
                    debut_matin: document.getElementById('heure_debut_matin').value,
                    fin_matin: document.getElementById('heure_fin_matin').value,
                    debut_aprem: document.getElementById('heure_debut_aprem').value,
                    fin_aprem: document.getElementById('heure_fin_aprem').value
                };
            }

            selectedDates.forEach(date => {
                temporaryPlannings[date] = {
                    lieu_id: selectedLieuId,
                    lieu_nom: lieuNom,
                    type_horaire: typeHoraire,
                    horaires: horaires
                };

                const cell = document.querySelector(`[data-date="${date}"]`);
                if (cell) {
                    let horaireText = '';
                    if (typeHoraire === 'simple') {
                        horaireText = `${horaires.debut} - ${horaires.fin}`;
                    } else {
                        horaireText = `${horaires.debut_matin} - ${horaires.fin_matin}<br>${horaires.debut_aprem} - ${horaires.fin_aprem}`;
                    }

                    cell.innerHTML = `
                        <div class="text-right mb-2">${new Date(date).getDate()}</div>
                        <div class="planning-details text-xs">
                            <div class="font-semibold">${lieuNom}</div>
                            <div>${horaireText}</div>
                        </div>
                    `;
                    cell.classList.add('bg-blue-50');
                    cell.classList.remove('selected');
                }
            });

            selectedDates = [];
            closeForm();
        };

        window.creerPlanning = function() {
            if (Object.keys(temporaryPlannings).length === 0) {
                showNotification('error', 'Aucun planning à enregistrer');
                return;
            }

            // Désactiver le bouton pendant le traitement
            const btnCreerPlanning = document.getElementById('btnCreerPlanning');
            btnCreerPlanning.disabled = true;
            btnCreerPlanning.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Traitement en cours...';

            const data = {
                employe_id: employeId,
                plannings: temporaryPlannings
            };

            fetch('{{ route('plannings.store-monthly-calendar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showNotification('error', data.error);
                    btnCreerPlanning.disabled = false;
                    btnCreerPlanning.innerHTML = '{{ request()->has("from_modification") ? "Modifier le planning" : "Créer le planning" }}';
                } else {
                    const employeNomPrenom = '{{ $employe->nom }} {{ $employe->prenom }}';
                    const moisNom = '{{ Carbon\Carbon::create($annee, $mois, 1)->locale("fr")->monthName }}';
                    
                    showNotification('success', `Le planning de ${employeNomPrenom} pour le mois de ${moisNom} a été créé avec succès. Un e-mail a été envoyé à ${employeNomPrenom}.`);
                    
                    // Rediriger après un court délai pour permettre à l'utilisateur de voir le message
                    setTimeout(() => {
                        window.location.href = '{{ route('plannings.calendar') }}';
                    }, 2500);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('error', 'Une erreur est survenue lors de l\'enregistrement');
                btnCreerPlanning.disabled = false;
                btnCreerPlanning.innerHTML = '{{ request()->has("from_modification") ? "Modifier le planning" : "Créer le planning" }}';
            });
        };

        window.ajouterReposSelection = function() {
            if (selectedDates.length === 0) {
                alert('Veuillez sélectionner au moins un jour');
                return;
            }
            
            const rhId = {{ Lieu::where('nom', 'RH')->where('is_special', true)->first()->id ?? 'null' }};
            
            selectedDates.forEach(date => {
                const cell = document.querySelector(`.calendar-cell[data-date="${date}"]`);
                if (cell) {
                    temporaryPlannings[date] = {
                        lieu_id: rhId,
                        lieu_nom: 'RH',
                        type_horaire: 'simple',
                        horaires: {
                            debut: '00:00',
                            fin: '00:00'
                        }
                    };

                    cell.innerHTML = `
                        <div class="text-right mb-2">${date.split('-')[2]}</div>
                        <div class="planning-details text-xs">
                            <div class="font-semibold">RH</div>
                            <div>00:00-00:00</div>
                        </div>
                    `;
                    cell.classList.add('bg-gray-50');
                    cell.classList.remove('selected');
                }
            });
            
            // Réinitialiser la sélection
            selectedDates = [];
        };
        
        window.remplirJoursRepos = function() {
            const rhId = {{ Lieu::where('nom', 'RH')->where('is_special', true)->first()->id ?? 'null' }};

            document.querySelectorAll('.calendar-cell').forEach(cell => {
                const date = cell.dataset.date;
                if (date && date.startsWith('{{ $annee }}-{{ str_pad($mois, 2, '0', STR_PAD_LEFT) }}')) {
                    if (!cell.querySelector('.planning-details')) {
                        temporaryPlannings[date] = {
                            lieu_id: rhId,
                            lieu_nom: 'RH',
                            type_horaire: 'simple',
                            horaires: {
                                debut: '00:00',
                                fin: '00:00'
                            }
                        };

                        cell.innerHTML = `
                            <div class="text-right mb-2">${date.split('-')[2]}</div>
                            <div class="planning-details text-xs">
                                <div class="font-semibold">RH</div>
                                <div>00:00-00:00</div>
                            </div>
                        `;
                        cell.classList.add('bg-gray-50');
                    }
                }
            });
        };

        window.ajouterConge = function() {
            if (selectedDates.length === 0) {
                alert('Veuillez sélectionner au moins un jour');
                return;
            }

            const cpId = {{ Lieu::where('nom', 'CP')->where('is_special', true)->first()->id ?? 'null' }};

            selectedDates.forEach(date => {
                const cell = document.querySelector(`[data-date="${date}"]`);
                if (cell) {
                    temporaryPlannings[date] = {
                        lieu_id: cpId,
                        lieu_nom: 'CP',
                        type_horaire: 'simple',
                        horaires: {
                            debut: '00:00',
                            fin: '00:00'
                        }
                    };

                    cell.innerHTML = `
                        <div class="text-right mb-2">${date.split('-')[2]}</div>
                        <div class="planning-details text-xs">
                            <div class="font-semibold">CP</div>
                            <div>00:00-00:00</div>
                        </div>
                    `;
                    cell.classList.add('bg-green-50');
                    cell.classList.remove('selected', 'bg-blue-200');
                }
            });

            selectedDates = [];
            document.querySelector('button[onclick="showForm()"]').disabled = true;
        };
    });
</script>
@endpush

@push('styles')
<style>
    /* Styles pour les notifications modales */
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes fadeOutScale {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }
    
    .animate-fade-in-scale {
        animation: fadeInScale 0.3s ease-out forwards;
    }
    
    .animate-fade-out-scale {
        animation: fadeOutScale 0.3s ease-in forwards;
    }
    
    /* Style pour le fond semi-transparent */
    #notification-container .bg-black {
        backdrop-filter: blur(4px);
    }
    
    /* Styles pour les calendriers */
    .calendar-cell, .calendar-cell-prev {
        min-height: 60px;
        transition: all 0.3s ease;
        position: relative;
        border-radius: 0.25rem;
        border: 1px solid rgba(160, 174, 192, 0.3);
        background-color: rgba(30, 41, 59, 0.02);
    }
    
    .calendar-cell:hover, .calendar-cell-prev:hover {
        background-color: rgba(30, 41, 59, 0.05);
    }
    
    .reference-calendar {
        border-radius: 0.375rem;
        padding: 0.5rem;
        margin-top: 0.5rem;
        border: 1px solid rgba(160, 174, 192, 0.2);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    
    .current-calendar {
        border-radius: 0.375rem;
        padding: 0.5rem;
        border: 1px solid rgba(66, 153, 225, 0.2);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    
    .calendar-cell:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .calendar-cell.selected {
        border: 2px solid #3b82f6;
        background-color: #eff6ff !important;
    }

    .calendar-cell.planned {
        background-color: #e0f2fe !important;
    }
    
    .calendar-cell.has-planning {
        background-color: #e0f2fe !important;
        border: 2px solid #7dd3fc;
    }

    .planning-details {
        opacity: 0.9;
        font-size: 0.875rem;
    }

    .planning-details:hover {
        opacity: 1;
    }

    #planningForm {
        z-index: 9999;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1), 0 -2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    #planningForm.show {
        transform: translateY(0);
    }

    .transition-transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>
@endpush

@endsection