@extends('layouts.app')

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
                        onclick="creerPlanning()"
                        class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors duration-200"
                    >
                        Modifier le planning
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

            <!-- Bouton de création -->
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="creerPlanning()" 
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
                alert('Aucun planning à enregistrer');
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
                    alert('Planning modifié avec succès');
                    window.location.href = '{{ route('plannings.calendar') }}';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'enregistrement');
            });
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
</style>
@endpush

@endsection