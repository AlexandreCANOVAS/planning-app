<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Planning Mensuel - {{ \Carbon\Carbon::create(null, $mois, 1)->locale('fr')->monthName }} {{ $annee }}
            </h2>
            <div class="flex space-x-4">
                <select id="moisSelect" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $m == $mois ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->monthName }}
                        </option>
                    @endforeach
                </select>
                <select id="anneeSelect" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @foreach(range(now()->year - 1, now()->year + 2) as $a)
                        <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>
                            {{ $a }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">
                        Planning - {{ Carbon\Carbon::create(null, $moisActuel, 1)->locale('fr')->monthName }} {{ $anneeActuelle }}
                    </h2>
                    
                    <!-- Navigation entre les mois -->
                    <div class="flex space-x-4">
                        <a href="{{ route('plannings.calendar', ['mois' => Carbon\Carbon::create($anneeActuelle, $moisActuel)->subMonth()->month, 'annee' => Carbon\Carbon::create($anneeActuelle, $moisActuel)->subMonth()->year]) }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Mois précédent
                        </a>
                        <a href="{{ route('plannings.calendar', ['mois' => Carbon\Carbon::create($anneeActuelle, $moisActuel)->addMonth()->month, 'annee' => Carbon\Carbon::create($anneeActuelle, $moisActuel)->addMonth()->year]) }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Mois suivant
                        </a>
                    </div>
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
                            $isCurrentMonth = $currentDate->month === intval($moisActuel);
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
                                                {{ Carbon\Carbon::parse($dayPlannings['journee']->heure_debut)->format('H:i') }} 
                                                - 
                                                {{ Carbon\Carbon::parse($dayPlannings['journee']->heure_fin)->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($dayPlannings['matin'])
                                        <div class="text-xs mb-1">
                                            <div class="font-semibold text-gray-700">
                                                {{ $dayPlannings['matin']->lieuTravail->nom ?? 'Non défini' }}
                                            </div>
                                            @if($dayPlannings['matin']->lieuTravail && !in_array($dayPlannings['matin']->lieuTravail->nom, ['RH', 'CP']))
                                                <div class="text-gray-600">
                                                    {{ Carbon\Carbon::parse($dayPlannings['matin']->heure_debut)->format('H:i') }} 
                                                    - 
                                                    {{ Carbon\Carbon::parse($dayPlannings['matin']->heure_fin)->format('H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($dayPlannings['apres-midi'])
                                        <div class="text-xs">
                                            <div class="font-semibold text-gray-700">
                                                {{ $dayPlannings['apres-midi']->lieuTravail->nom ?? 'Non défini' }}
                                            </div>
                                            @if($dayPlannings['apres-midi']->lieuTravail && !in_array($dayPlannings['apres-midi']->lieuTravail->nom, ['RH', 'CP']))
                                                <div class="text-gray-600">
                                                    {{ Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_debut)->format('H:i') }} 
                                                    - 
                                                    {{ Carbon\Carbon::parse($dayPlannings['apres-midi']->heure_fin)->format('H:i') }}
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
        </div>
    </div>

    @push('scripts')
    <script>
        // Mise à jour du calendrier quand on change le mois ou l'année
        document.getElementById('moisSelect').addEventListener('change', updateCalendar);
        document.getElementById('anneeSelect').addEventListener('change', updateCalendar);

        function updateCalendar() {
            const mois = document.getElementById('moisSelect').value;
            const annee = document.getElementById('anneeSelect').value;
            window.location.href = `{{ route('plannings.calendar') }}?mois=${mois}&annee=${annee}`;
        }
    </script>
    @endpush
</x-app-layout>
