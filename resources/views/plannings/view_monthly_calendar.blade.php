@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Planning de {{ $employe->nom }} {{ $employe->prenom }} - {{ Carbon\Carbon::create(null, $mois, 1)->locale('fr')->monthName }} {{ $annee }}</h2>
                <a href="{{ route('plannings.calendar') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Retour au calendrier
                </a>
            </div>

            <!-- Calendrier -->
            <div class="grid grid-cols-7 gap-4">
                <!-- En-têtes des jours -->
                <div class="text-center font-semibold">Lun</div>
                <div class="text-center font-semibold">Mar</div>
                <div class="text-center font-semibold">Mer</div>
                <div class="text-center font-semibold">Jeu</div>
                <div class="text-center font-semibold">Ven</div>
                <div class="text-center font-semibold">Sam</div>
                <div class="text-center font-semibold">Dim</div>

                <!-- Jours du calendrier -->
                @php
                    $currentDate = $debutPeriode->copy();
                @endphp

                @while($currentDate <= $finPeriode)
                    @php
                        $isCurrentMonth = $currentDate->month === intval($mois);
                        $currentDateStr = $currentDate->format('Y-m-d');
                        $dayPlannings = $planningsByDate[$currentDateStr] ?? null;
                    @endphp

                    <div class="min-h-[120px] p-2 border rounded-lg {{ !$isCurrentMonth ? 'bg-gray-100' : '' }} {{ $dayPlannings ? 'bg-blue-50' : '' }}">
                        <div class="text-right mb-2 {{ !$isCurrentMonth ? 'text-gray-400' : '' }}">
                            {{ $currentDate->format('d') }}
                        </div>
                        
                        @if($dayPlannings)
                            @if($dayPlannings['journee'])
                                <div class="text-xs">
                                    <div class="font-semibold text-gray-700">
                                        {{ $dayPlannings['journee']->lieuTravail->nom ?? 'Non défini' }}
                                    </div>
                                    @if($dayPlannings['journee']->lieuTravail && !in_array($dayPlannings['journee']->lieuTravail->nom, ['RH', 'CP']))
                                        <div class="text-gray-600">
                                            {{ Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }} - 
                                            {{ Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-xs">
                                    @if($dayPlannings['matin'])
                                        <div class="font-semibold text-gray-700">
                                            {{ $dayPlannings['matin']->lieuTravail->nom ?? 'Non défini' }}
                                        </div>
                                        @if($dayPlannings['matin']->lieuTravail && !in_array($dayPlannings['matin']->lieuTravail->nom, ['RH', 'CP']))
                                            <div class="text-gray-600">
                                                {{ Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }} - 
                                                {{ Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}
                                            </div>
                                        @endif
                                    @endif

                                    @if($dayPlannings['apres-midi'])
                                        @if($dayPlannings['matin'])
                                            <div class="mt-1 border-t border-gray-200 pt-1"></div>
                                        @endif
                                        <div class="font-semibold text-gray-700">
                                            {{ $dayPlannings['apres-midi']->lieuTravail->nom ?? 'Non défini' }}
                                        </div>
                                        @if($dayPlannings['apres-midi']->lieuTravail && !in_array($dayPlannings['apres-midi']->lieuTravail->nom, ['RH', 'CP']))
                                            <div class="text-gray-600">
                                                {{ Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }} - 
                                                {{ Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>

                    @php
                        $currentDate->addDay();
                    @endphp
                @endwhile
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .calendar-cell {
        min-height: 100px;
        position: relative;
    }
    .planning-details {
        font-size: 0.75rem;
        line-height: 1rem;
    }
</style>
@endpush
@endsection
