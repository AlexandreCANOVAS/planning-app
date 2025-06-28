@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <!-- En-tête avec bouton retour -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('plannings.view-monthly-calendar', [Auth::id(), date('m'), date('Y')]) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-white/20 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Retour
                            </a>
                            <h2 class="text-2xl font-bold text-white">
                                Planning de {{ $employe->prenom }} {{ $employe->nom }}
                            </h2>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('employe.plannings.comparer', $employe->id) }}?mois={{ $selectedMonth }}&annee={{ $selectedYear }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Comparer les plannings
                            </a>
                            <button type="button" onclick="openExchangeModal()" 
                                class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-white/20 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Proposer un échange
                            </button>
                            <div class="text-white text-lg font-semibold px-4 py-2 rounded-lg bg-white/10 backdrop-blur-sm">
                                {{ $totalHeures }}h <span class="text-sm font-normal">ce mois</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-lg text-white/90">
                        {{ $firstDay->locale('fr')->monthName }} {{ $selectedYear }}
                    </div>
                </div>

                <!-- Calendrier -->
                <div class="p-6">
                    <!-- Jours de la semaine -->
                    <div class="grid grid-cols-7 mb-4">
                        @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                            <div class="text-center font-semibold text-gray-600 text-sm py-2">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Grille des jours -->
                    <div class="grid grid-cols-7 gap-2">
                        @php
                            $currentDay = $firstDay->copy()->startOfWeek(Carbon\Carbon::MONDAY);
                            $endDay = $lastDay->copy()->endOfWeek(Carbon\Carbon::SUNDAY);
                        @endphp

                        @while($currentDay <= $endDay)
                            @php
                                $isCurrentMonth = $currentDay->month === $selectedMonth;
                                $isToday = $currentDay->isToday();
                                $hasPlanning = isset($plannings[$currentDay->format('Y-m-d')]);
                                $planning = $hasPlanning ? $plannings[$currentDay->format('Y-m-d')] : null;
                            @endphp

                            <div class="min-h-[120px] relative group">
                                <!-- Fond et bordures -->
                                <div class="absolute inset-0 rounded-lg border transition-all duration-200 
                                    {{ $isCurrentMonth 
                                        ? ($isToday 
                                            ? 'border-indigo-500 bg-indigo-50' 
                                            : 'border-gray-200 bg-white hover:border-indigo-200 hover:shadow-md') 
                                        : 'border-gray-100 bg-gray-50' }}">
                                </div>

                                <!-- Contenu -->
                                <div class="relative p-2">
                                    <!-- Numéro du jour -->
                                    <div class="flex justify-end">
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full 
                                            {{ $isToday 
                                                ? 'bg-indigo-500 text-white' 
                                                : ($isCurrentMonth ? 'text-gray-900' : 'text-gray-400') }}">
                                            {{ $currentDay->format('j') }}
                                        </span>
                                    </div>

                                    <!-- Planning du jour -->
                                    @if($hasPlanning && $isCurrentMonth)
                                        <div class="mt-2 space-y-2">
                                            @php
                                                // Regrouper les plannings par lieu
                                                $planningsByLieu = collect($plannings[$currentDay->format('Y-m-d')])
                                                    ->groupBy('lieu_id')
                                                    ->map(function($items) {
                                                        return [
                                                            'lieu' => $items->first()->lieu,
                                                            'horaires' => $items->map(function($item) {
                                                                return [
                                                                    'debut' => Carbon\Carbon::parse($item->heure_debut)->format('H:i'),
                                                                    'fin' => Carbon\Carbon::parse($item->heure_fin)->format('H:i')
                                                                ];
                                                            })
                                                        ];
                                                    });
                                            @endphp

                                            @foreach($planningsByLieu as $planning)
                                                <div class="bg-gradient-to-br from-indigo-500/10 to-purple-500/10 backdrop-blur-sm 
                                                            rounded-lg p-3 transform transition-all duration-200 
                                                            group-hover:scale-[1.02] group-hover:shadow-md">
                                                    <div class="font-medium text-indigo-700">{{ $planning['lieu']->nom }}</div>
                                                    <div class="space-y-1">
                                                        @foreach($planning['horaires'] as $horaire)
                                                            <div class="text-indigo-600 flex items-center text-sm">
                                                                @if($loop->first)
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                @else
                                                                    <div class="w-4 h-4 mr-1"></div>
                                                                @endif
                                                                {{ $horaire['debut'] }} - {{ $horaire['fin'] }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @php
                                $currentDay->addDay();
                            @endphp
                        @endwhile
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'échange de jours -->
    <div id="exchange-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="exchange-form" action="{{ route('employe.plannings.proposer-echange') }}" method="POST">
                    @csrf
                    <input type="hidden" name="collegue_id" value="{{ $employe->id }}">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Proposer un échange de jours
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Sélectionnez un jour de votre planning et un jour du planning de {{ $employe->prenom }} que vous souhaitez échanger.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 space-y-4">
                            <!-- Sélection de votre jour -->
                            <div>
                                <label for="your_day" class="block text-sm font-medium text-gray-700">Votre jour</label>
                                <div class="mt-1">
                                    <input type="date" name="your_day" id="your_day" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Sélectionnez un jour où vous travaillez</p>
                            </div>
                            
                            <!-- Sélection du jour du collègue -->
                            <div>
                                <label for="collegue_day" class="block text-sm font-medium text-gray-700">Jour de {{ $employe->prenom }}</label>
                                <div class="mt-1">
                                    <input type="date" name="collegue_day" id="collegue_day" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Sélectionnez un jour où votre collègue travaille</p>
                            </div>
                            
                            <!-- Motif de l'échange -->
                            <div>
                                <label for="motif" class="block text-sm font-medium text-gray-700">Motif de l'échange</label>
                                <div class="mt-1">
                                    <textarea id="motif" name="motif" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Expliquez pourquoi vous souhaitez échanger ces jours" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Proposer l'échange
                        </button>
                        <button type="button" onclick="closeExchangeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fonctions pour gérer le modal d'échange de jours
        function openExchangeModal() {
            document.getElementById('exchange-modal').classList.remove('hidden');
        }
        
        function closeExchangeModal() {
            document.getElementById('exchange-modal').classList.add('hidden');
        }
        
        // Fermer le modal si on clique en dehors
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('exchange-modal');
            const modalContent = document.querySelector('#exchange-modal .inline-block');
            
            if (modal && !modal.classList.contains('hidden') && !modalContent.contains(event.target) && !event.target.closest('button[onclick="openExchangeModal()"]')) {
                closeExchangeModal();
            }
        });
        
        // Pré-remplir les dates avec le mois actuel
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const year = {{ $selectedYear }};
            const month = {{ $selectedMonth }} - 1; // JavaScript months are 0-indexed
            
            const yourDayInput = document.getElementById('your_day');
            const collegueDayInput = document.getElementById('collegue_day');
            
            // Définir la date minimale au premier jour du mois
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            // Formater les dates pour l'attribut min/max
            const formatDate = (date) => {
                const d = new Date(date);
                const month = '' + (d.getMonth() + 1);
                const day = '' + d.getDate();
                const year = d.getFullYear();
                
                return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
            };
            
            yourDayInput.min = formatDate(firstDay);
            yourDayInput.max = formatDate(lastDay);
            collegueDayInput.min = formatDate(firstDay);
            collegueDayInput.max = formatDate(lastDay);
        });
    </script>
    @endpush
@endsection
