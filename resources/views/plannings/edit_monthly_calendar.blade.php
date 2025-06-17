@extends('layouts.app')

@push('styles')
<style>
    .calendar-cell {
        min-height: 100px;
        transition: background-color 0.2s ease;
    }
    .calendar-cell:hover {
        cursor: pointer;
    }
    .calendar-cell.selected {
        background-color: #fef08a !important; /* yellow-200 */
        border: 2px solid #eab308 !important; /* yellow-500 */
    }
    .calendar-cell.has-planning {
        background-color: #dbeafe !important; /* blue-100 */
    }
    .calendar-cell.modified {
        background-color: #fee2e2 !important; /* red-100 */
        border: 2px solid #ef4444 !important; /* red-500 */
    }
    .modified-text {
        color: #dc2626 !important; /* red-600 */
        font-weight: bold;
    }
</style>
@endpush

@section('content')
@php
use App\Models\Lieu;
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">
                    Modification du Planning - {{ $employe->nom }} {{ $employe->prenom }}
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
                    <button 
                        type="button" 
                        id="exportPdfBtn"
                        onclick="exportPdfWithModifications()"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200"
                    >
                        Exporter PDF avec modifications
                    </button>
                    <a href="{{ route('plannings.calendar') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Retour au calendrier
                    </a>
                </div>
            </div>

            <h3 class="text-lg mb-4">{{ \Carbon\Carbon::create(null, $mois, 1)->locale('fr')->monthName }} {{ $annee }}</h3>

            <!-- Calendrier -->
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

            <!-- Boutons d'action -->
            <div class="mt-4 flex justify-end items-center">
                <!-- Bouton de création -->
                <button type="button" onclick="creerPlanningWrapper()" 
                    class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    id="btnCreerPlanning"
                >
                    <i class="fas fa-save mr-2"></i> 
                    @if(request()->has('from_modification'))
                        Modifier le planning
                    @else
                        Modifier le planning
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour le téléchargement du PDF -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center h-full w-full z-50" style="display: none;">
    <div class="relative mx-auto p-6 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium">Planning modifié avec succès</h3>
                <button onclick="closeConfirmationModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="text-center">Voulez-vous télécharger le PDF du planning modifié ?</p>
            
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="exportPdfWithModifications(); closeConfirmationModal();" 
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200">
                    Oui
                </button>
                <button onclick="closeConfirmationModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition-colors duration-200">
                    Non
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
    // Variables globales
    let selectedDates = [];
    let employeId = {{ $employe->id }};
    let selectedLieuId = null;
    let temporaryPlannings = {};
    let modifiedPlanningIds = []; // Pour suivre les IDs des plannings modifiés
    let allPlanningIds = []; // Pour stocker tous les IDs des plannings existants
    let originalPlannings = {}; // Pour stocker les plannings originaux avant modification
    
    // Fonctions globales pour être accessibles depuis les attributs onclick
    window.toggleDateSelection = function(cell) {
        console.log('toggleDateSelection called', cell);
        
        // Vérifier si la cellule a déjà un planning
        if (cell.classList.contains('has-planning')) {
            console.log('Cell has planning, allowing selection');
            // Permettre la sélection sans confirmation
        }
        
        const date = cell.dataset.date;
        console.log('Cell date:', date);
        console.log('Current classes:', [...cell.classList]);
        
        // Si la cellule est déjà sélectionnée, la désélectionner
        if (cell.classList.contains('selected')) {
            console.log('Deselecting cell');
            cell.classList.remove('selected');
            cell.style.backgroundColor = '#ffffff'; // blanc
            cell.style.border = '1px solid #e5e7eb'; // gris clair
            
            // Retirer la date de la liste des dates sélectionnées
            const index = selectedDates.indexOf(date);
            if (index > -1) {
                selectedDates.splice(index, 1);
            }
        } else {
            // Sélectionner la cellule
            console.log('Selecting cell');
            cell.classList.add('selected');
            cell.style.backgroundColor = '#fef08a'; // jaune
            cell.style.border = '2px solid #eab308'; // jaune foncé
            
            // Ajouter la date à la liste des dates sélectionnées
            if (!selectedDates.includes(date)) {
                selectedDates.push(date);
            }
        }
        
        console.log('Selected dates:', selectedDates);
        
        // Si au moins une date est sélectionnée, activer le bouton pour remplir les plannings
        document.querySelector('button[onclick="showForm()"]').disabled = selectedDates.length === 0;
        console.log('Button enabled:', selectedDates.length > 0);
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
    
    window.toggleHoraires = function() {
        const isCompose = document.querySelector('input[name="type_horaire"]:checked').value === 'compose';
        document.getElementById('horaires_simples').classList.toggle('hidden', isCompose);
        document.getElementById('horaires_composes').classList.toggle('hidden', !isCompose);
    };
    
    document.addEventListener('DOMContentLoaded', function() {

        // Initialiser les plannings existants et collecter tous les IDs
        @foreach($planningsByDate as $date => $dayPlannings)
            @if($dayPlannings['journee'])
                allPlanningIds.push({{ $dayPlannings['journee']->id }});
                // Stocker le planning original pour comparaison ultérieure
                originalPlannings['{{ $date }}'] = {
                    id: {{ $dayPlannings['journee']->id }},
                    lieu_id: {{ $dayPlannings['journee']->lieu_id }},
                    type_horaire: 'simple',
                    horaires: {
                        debut: '{{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }}',
                        fin: '{{ \Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}'
                    }
                };
            @endif
            @if($dayPlannings['matin'] && $dayPlannings['apres-midi'])
                allPlanningIds.push({{ $dayPlannings['matin']->id }});
                allPlanningIds.push({{ $dayPlannings['apres-midi']->id }});
                // Stocker le planning original pour comparaison ultérieure
                originalPlannings['{{ $date }}'] = {
                    matin_id: {{ $dayPlannings['matin']->id }},
                    aprem_id: {{ $dayPlannings['apres-midi']->id }},
                    lieu_id: {{ $dayPlannings['matin']->lieu_id }},
                    type_horaire: 'compose',
                    horaires: {
                        debut_matin: '{{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }}',
                        fin_matin: '{{ \Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}',
                        debut_aprem: '{{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }}',
                        fin_aprem: '{{ \Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}'
                    }
                };
            @elseif($dayPlannings['matin'])
                allPlanningIds.push({{ $dayPlannings['matin']->id }});
            @elseif($dayPlannings['apres-midi'])
                allPlanningIds.push({{ $dayPlannings['apres-midi']->id }});
            @endif
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

        // Initialisation des événements au chargement de la page
        document.getElementById('lieu_id').addEventListener('change', function() {
            selectedLieuId = this.value;
        });
        
        // Initialiser les horaires
        toggleHoraires();
        
        // Désactiver le bouton d'export PDF au chargement
        document.getElementById('exportPdfBtn').classList.add('opacity-50');
        document.getElementById('exportPdfBtn').disabled = true;

        // Fonction pour exporter le PDF avec les modifications en rouge
        // Fonction pour vérifier si un planning a été réellement modifié
        function isPlanningReallyModified(date, newPlanning) {
            // Si le planning n'existait pas avant, c'est une modification
            if (!originalPlannings[date]) {
                return true;
            }
            
            const original = originalPlannings[date];
            
            // Vérifier si le lieu a changé
            if (original.lieu_id != newPlanning.lieu_id) {
                return true;
            }
            
            // Vérifier si le type d'horaire a changé
            if (original.type_horaire !== newPlanning.type_horaire) {
                return true;
            }
            
            // Vérifier si les horaires ont changé
            if (original.type_horaire === 'simple' && newPlanning.type_horaire === 'simple') {
                if (original.horaires.debut !== newPlanning.horaires.debut || 
                    original.horaires.fin !== newPlanning.horaires.fin) {
                    return true;
                }
            } else if (original.type_horaire === 'compose' && newPlanning.type_horaire === 'compose') {
                if (original.horaires.debut_matin !== newPlanning.horaires.debut_matin || 
                    original.horaires.fin_matin !== newPlanning.horaires.fin_matin || 
                    original.horaires.debut_aprem !== newPlanning.horaires.debut_aprem || 
                    original.horaires.fin_aprem !== newPlanning.horaires.fin_aprem) {
                    return true;
                }
            }
            
            // Si on arrive ici, c'est que rien n'a changé
            return false;
        }
        
        window.exportPdfWithModifications = function() {
            console.log('exportPdfWithModifications called');
            console.log('modifiedPlanningIds:', modifiedPlanningIds);
            console.log('temporaryPlannings:', temporaryPlannings);
            console.log('allPlanningIds:', allPlanningIds);
            console.log('originalPlannings:', originalPlannings);
            
            // Filtrer les plannings temporaires pour ne garder que ceux qui sont réellement modifiés
            const reallyModifiedTemporaryPlannings = {};
            for (const [date, planning] of Object.entries(temporaryPlannings)) {
                if (isPlanningReallyModified(date, planning)) {
                    reallyModifiedTemporaryPlannings[date] = planning;
                    // Si ce planning avait un ID original, l'ajouter à modifiedPlanningIds
                    if (originalPlannings[date]) {
                        if (originalPlannings[date].id) {
                            modifiedPlanningIds.push(originalPlannings[date].id);
                        }
                        if (originalPlannings[date].matin_id) {
                            modifiedPlanningIds.push(originalPlannings[date].matin_id);
                        }
                        if (originalPlannings[date].aprem_id) {
                            modifiedPlanningIds.push(originalPlannings[date].aprem_id);
                        }
                    }
                }
            }
            
            // Vérifier s'il y a des modifications réelles
            if (modifiedPlanningIds.length === 0 && Object.keys(reallyModifiedTemporaryPlannings).length === 0) {
                alert('Aucune modification réelle n\'a été détectée');
                return;
            }
            
            // S'assurer que modifiedPlanningIds ne contient que des valeurs uniques
            // et uniquement les plannings qui ont été réellement modifiés
            const uniqueModifiedPlanningIds = [...new Set(modifiedPlanningIds)];
            console.log('uniqueModifiedPlanningIds:', uniqueModifiedPlanningIds);
            console.log('reallyModifiedTemporaryPlannings:', reallyModifiedTemporaryPlannings);
            
            // Créer un formulaire temporaire pour soumettre les données
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("plannings.export-pdf-with-modifications") }}';
            form.target = '_blank'; // Ouvrir dans un nouvel onglet
            
            // Ajouter le token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Ajouter l'ID de l'employé
            const employeIdInput = document.createElement('input');
            employeIdInput.type = 'hidden';
            employeIdInput.name = 'employe_id';
            employeIdInput.value = employeId;
            form.appendChild(employeIdInput);
            
            // Ajouter le mois et l'année
            const moisInput = document.createElement('input');
            moisInput.type = 'hidden';
            moisInput.name = 'mois';
            moisInput.value = {{ $mois }};
            form.appendChild(moisInput);
            
            const anneeInput = document.createElement('input');
            anneeInput.type = 'hidden';
            anneeInput.name = 'annee';
            anneeInput.value = {{ $annee }};
            form.appendChild(anneeInput);
            
            // Ajouter les IDs des plannings modifiés (uniquement les valeurs uniques)
            const modifiedPlanningsInput = document.createElement('input');
            modifiedPlanningsInput.type = 'hidden';
            modifiedPlanningsInput.name = 'modified_plannings';
            modifiedPlanningsInput.value = JSON.stringify(uniqueModifiedPlanningIds);
            form.appendChild(modifiedPlanningsInput);
            
            // Ajouter les plannings temporaires (pour les jours de repos RH)
            // N'envoyer que les plannings temporaires réellement modifiés
            const temporaryPlanningsInput = document.createElement('input');
            temporaryPlanningsInput.type = 'hidden';
            temporaryPlanningsInput.name = 'temporary_plannings';
            temporaryPlanningsInput.value = JSON.stringify(reallyModifiedTemporaryPlannings);
            form.appendChild(temporaryPlanningsInput);
            
            // Ajouter le formulaire au document et le soumettre
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        };
        
        window.savePlanning = function() {
            console.log('savePlanning called');
            
            // Vérifier qu'un lieu est sélectionné
            if (!selectedLieuId) {
                alert('Veuillez sélectionner un lieu');
                return;
            }
            
            // Réinitialiser modifiedPlanningIds pour ne garder que les plannings réellement modifiés
            modifiedPlanningIds = [];

            // Vérifier si des dates sont sélectionnées
            if (selectedDates.length === 0) {
                // Essayer de récupérer les dates sélectionnées directement depuis le DOM
                const selectedCells = document.querySelectorAll('.calendar-cell.selected');
                console.log('Selected cells found in DOM:', selectedCells.length);
                
                if (selectedCells.length > 0) {
                    // Réinitialiser selectedDates
                    selectedDates = [];
                    
                    // Ajouter les dates des cellules sélectionnées
                    selectedCells.forEach(cell => {
                        const date = cell.dataset.date;
                        if (date && !selectedDates.includes(date)) {
                            selectedDates.push(date);
                        }
                    });
                    
                    console.log('Reconstructed selectedDates:', selectedDates);
                }
                
                // Vérifier à nouveau si des dates sont sélectionnées
                if (selectedDates.length === 0) {
                    alert('Veuillez sélectionner au moins un jour');
                    return;
                }
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
                
                // Récupérer l'ID du planning existant s'il y en a un et l'ajouter à modifiedPlanningIds
                // car ce planning a été modifié
                @foreach($planningsByDate as $dateStr => $dayPlannings)
                    if ('{{ $dateStr }}' === date) {
                        @if($dayPlannings['journee'])
                            if (!modifiedPlanningIds.includes({{ $dayPlannings['journee']->id }})) {
                                modifiedPlanningIds.push({{ $dayPlannings['journee']->id }});
                                console.log('Planning journee modifié:', {{ $dayPlannings['journee']->id }});
                            }
                        @endif
                        @if($dayPlannings['matin'])
                            if (!modifiedPlanningIds.includes({{ $dayPlannings['matin']->id }})) {
                                modifiedPlanningIds.push({{ $dayPlannings['matin']->id }});
                                console.log('Planning matin modifié:', {{ $dayPlannings['matin']->id }});
                            }
                        @endif
                        @if($dayPlannings['apres-midi'])
                            if (!modifiedPlanningIds.includes({{ $dayPlannings['apres-midi']->id }})) {
                                modifiedPlanningIds.push({{ $dayPlannings['apres-midi']->id }});
                                console.log('Planning après-midi modifié:', {{ $dayPlannings['apres-midi']->id }});
                            }
                        @endif
                    }
                @endforeach

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
                            <div class="font-semibold modified-text">${lieuNom}</div>
                            <div class="modified-text">${horaireText}</div>
                        </div>
                    `;
                    cell.classList.remove('selected');
                    cell.classList.remove('bg-blue-50');
                    cell.classList.add('modified');
                    
                    // Activer le bouton d'export PDF si des modifications ont été faites
                    document.getElementById('exportPdfBtn').classList.remove('opacity-50');
                    document.getElementById('exportPdfBtn').disabled = false;
                }
            });

            selectedDates = [];
            closeForm();
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
                    // Vérifier si un planning existe déjà pour cette date
                    const planningId = cell.dataset.planningId;
                    
                    // Si un planning existe, ajouter son ID à la liste des plannings modifiés
                    if (planningId) {
                        if (!modifiedPlanningIds.includes(parseInt(planningId))) {
                            modifiedPlanningIds.push(parseInt(planningId));
                        }
                    }
                    
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
                    cell.classList.add('modified'); // Marquer comme modifié
                    cell.classList.remove('selected');
                    
                    // Activer le bouton d'export PDF si des modifications ont été faites
                    document.getElementById('exportPdfBtn').classList.remove('opacity-50');
                    document.getElementById('exportPdfBtn').disabled = false;
                    
                    // Forcer l'ajout d'au moins un élément dans modifiedPlanningIds pour permettre l'export PDF
                    if (modifiedPlanningIds.length === 0) {
                        modifiedPlanningIds.push(-1); // Valeur fictive pour indiquer qu'il y a des modifications
                    }
                }
            });
            
            console.log('modifiedPlanningIds après ajout RH:', modifiedPlanningIds);
            
            // Réinitialiser la sélection
            selectedDates = [];
        };
        
        window.creerPlanning = function() {
            console.log('creerPlanning appelé');
            console.log('temporaryPlannings:', temporaryPlannings);
            
            // Vérifier si temporaryPlannings est défini et non vide
            if (!temporaryPlannings || Object.keys(temporaryPlannings).length === 0) {
                alert('Aucun planning à enregistrer. Veuillez sélectionner au moins un jour et ajouter un planning.');
                return;
            }

            const data = {
                employe_id: employeId,
                mois: {{ $mois }},
                annee: {{ $annee }},
                plannings: temporaryPlannings
            };

            fetch('{{ route('plannings.update-monthly-calendar') }}', {
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
                    alert(data.error);
                } else {
                    // Activer le bouton d'export PDF
                    document.getElementById('exportPdfBtn').classList.remove('opacity-50');
                    document.getElementById('exportPdfBtn').disabled = false;
                    
                    // Afficher la boîte de dialogue modale de confirmation
                    document.getElementById('confirmationModal').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'enregistrement');
            });
        };
        
        window.closeConfirmationModal = function() {
            document.getElementById('confirmationModal').style.display = 'none';
        };
        
        // Fonction wrapper pour déboguer l'appel à creerPlanning()
        window.creerPlanningWrapper = function() {
            console.log('creerPlanningWrapper appelé');
            try {
                creerPlanning();
            } catch (error) {
                console.error('Erreur lors de l\'appel à creerPlanning():', error);
                alert('Une erreur est survenue lors de la modification du planning. Veuillez consulter la console pour plus de détails.');
            }
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
    // Fonction pour afficher un toast de notification
    function showToast(message, type = 'success') {
        // Supprimer les toasts existants
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = `toast fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-opacity duration-300 flex items-center ${
            type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
            type === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' :
            'bg-blue-100 border-l-4 border-blue-500 text-blue-700'
        }`;
        
        // Ajouter l'icône
        const icon = document.createElement('div');
        icon.className = 'mr-3';
        icon.innerHTML = type === 'success' 
            ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
            : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
        toast.appendChild(icon);
        
        // Ajouter le message
        const messageEl = document.createElement('div');
        messageEl.textContent = message;
        toast.appendChild(messageEl);
        
        // Ajouter le bouton de fermeture
        const closeBtn = document.createElement('button');
        closeBtn.className = 'ml-auto text-gray-400 hover:text-gray-600';
        closeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        closeBtn.onclick = function() {
            toast.remove();
        };
        toast.appendChild(closeBtn);
        
        // Ajouter le toast au document
        document.body.appendChild(toast);
        
        // Faire disparaître le toast après 5 secondes
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
    
    // Fonction pour afficher un toast de confirmation avec un bouton d'action
    function showConfirmationToast(message, buttonText, buttonAction) {
        // Supprimer les toasts existants
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = 'toast fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-opacity duration-300 bg-green-100 border-l-4 border-green-500 text-green-700';
        
        // Créer un conteneur flex pour le contenu
        const container = document.createElement('div');
        container.className = 'flex items-center';
        toast.appendChild(container);
        
        // Ajouter l'icône
        const icon = document.createElement('div');
        icon.className = 'mr-3';
        icon.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
        container.appendChild(icon);
        
        // Ajouter le message
        const messageEl = document.createElement('div');
        messageEl.textContent = message;
        container.appendChild(messageEl);
        
        // Ajouter le bouton de fermeture
        const closeBtn = document.createElement('button');
        closeBtn.className = 'ml-auto text-gray-400 hover:text-gray-600';
        closeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        closeBtn.onclick = function() {
            toast.remove();
        };
        container.appendChild(closeBtn);
        
        // Ajouter un séparateur
        const separator = document.createElement('div');
        separator.className = 'border-t border-green-200 my-2';
        toast.appendChild(separator);
        
        // Ajouter le bouton d'action
        const actionBtn = document.createElement('button');
        actionBtn.className = 'w-full mt-2 px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors flex items-center justify-center';
        actionBtn.innerHTML = `<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> ${buttonText}`;
        actionBtn.onclick = function() {
            buttonAction();
            toast.remove();
        };
        toast.appendChild(actionBtn);
        
        // Ajouter le toast au document
        document.body.appendChild(toast);
        
        // Faire disparaître le toast après 10 secondes
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 10000);
    }
</script>
@endpush

@push('styles')
<style>
    .calendar-cell {
        min-height: 100px;
        transition: all 0.3s ease;
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
    
    /* Styles pour les toasts de notification */
    .toast {
        opacity: 1;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        max-width: 400px;
    }
    
    .toast.opacity-0 {
        opacity: 0;
    }
    
    /* Styles pour les toasts selon le thème */
    .toast.bg-green-100 {
        background-color: rgba(220, 252, 231, 0.9) !important;
    }
    
    .toast.bg-red-100 {
        background-color: rgba(254, 226, 226, 0.9) !important;
    }
    
    .toast.bg-blue-100 {
        background-color: rgba(219, 234, 254, 0.9) !important;
    }
</style>
@endpush

@endsection