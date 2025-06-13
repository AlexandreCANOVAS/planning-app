@extends('layouts.app')

@section('content')
<!-- Débogage -->
<div class="p-4 bg-yellow-100 mb-4">
    <h3 class="font-bold">Débogage</h3>
    <p>Employe: {{ $employe->nom }} {{ $employe->prenom }} (ID: {{ $employe->id }})</p>
    <p>Mois: {{ $mois }}, Année: {{ $annee }}</p>
    <p>Nombre de jours avec plannings: {{ count($planningsByDate) }}</p>
    
    @if(session('debug_info'))
        <div class="mt-2 p-2 bg-white rounded">
            <h4 class="font-bold">Informations de débogage:</h4>
            <p>Employé ID: {{ session('debug_info')['employe_id'] }}</p>
            <p>Mois: {{ session('debug_info')['mois'] }}, Année: {{ session('debug_info')['annee'] }}</p>
            <p>Nombre de plannings trouvés: {{ session('debug_info')['count'] }}</p>
            @if(session('debug_info')['count'] > 0)
                <p>Dates des plannings:</p>
                <ul>
                    @foreach(session('debug_info')['plannings_dates'] as $date)
                        <li>{{ $date }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
    
    @if(count($planningsByDate) > 0)
        <p class="mt-2">Dates avec plannings:</p>
        <ul>
        @foreach($planningsByDate as $date => $planning)
            <li>{{ $date }}: 
                @if($planning['matin']) Matin @endif
                @if($planning['apres-midi']) Après-midi @endif
                @if($planning['journee']) Journée @endif
            </li>
        @endforeach
        </ul>
    @else
        <p class="mt-2 font-bold text-red-500">Aucun planning trouvé</p>
    @endif
</div>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(count($planningsByDate) == 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-4">
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun planning trouvé</p>
                <p>Il n'existe pas de planning pour {{ $employe->prenom }} {{ $employe->nom }} pour le mois de {{ date('F', mktime(0, 0, 0, $mois, 1)) }} {{ $annee }}.</p>
                <p class="mt-2">Vous pouvez créer un planning en utilisant le bouton "Créer" dans la page des plannings.</p>
            </div>
        </div>
        @endif
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
                            <div class="text-xs">
                                @if($dayPlannings['journee'])
                                    <div class="font-semibold text-gray-700 bg-blue-100 p-1 rounded">
                                        {{ optional($dayPlannings['journee']->lieu)->nom ?? 'Non défini' }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }} - 
                                        {{ Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}
                                    </div>
                                @endif

                                @if($dayPlannings['matin'])
                                    <div class="font-semibold text-gray-700 bg-green-100 p-1 rounded mt-1">
                                        {{ optional($dayPlannings['matin']->lieu)->nom ?? 'Non défini' }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }} - 
                                        {{ Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}
                                    </div>
                                @endif

                                @if($dayPlannings['apres-midi'])
                                    <div class="font-semibold text-gray-700 bg-yellow-100 p-1 rounded mt-1">
                                        {{ optional($dayPlannings['apres-midi']->lieu)->nom ?? 'Non défini' }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }} - 
                                        {{ Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}
                                    </div>
                                @endif
                            </div>
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
