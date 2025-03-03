@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <!-- En-tête avec bouton retour -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('employe.plannings.index') }}" 
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
                        <div class="text-white text-lg font-semibold px-4 py-2 rounded-lg bg-white/10 backdrop-blur-sm">
                            {{ $totalHeures }}h <span class="text-sm font-normal">ce mois</span>
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
@endsection
