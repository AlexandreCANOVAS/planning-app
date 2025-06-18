<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mon Planning - Vue Calendrier') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('employe.plannings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Vue Liste') }}
                </a>
                <a href="{{ route('employe.plannings.download-pdf') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    {{ __('Télécharger PDF') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('employe.plannings.calendar') }}" class="mb-6">
                        <div class="flex space-x-4">
                            <div>
                                <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                                <select name="mois" id="mois" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(range(1, 12) as $mois)
                                        <option value="{{ $mois }}" {{ $selectedMonth == $mois ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                                <select name="annee" id="annee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(range(now()->year - 2, now()->year + 2) as $annee)
                                        <option value="{{ $annee }}" {{ $selectedYear == $annee ? 'selected' : '' }}>
                                            {{ $annee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    {{ __('Filtrer') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Calendrier -->
                    <div class="grid grid-cols-7 gap-2">
                        @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $jour)
                            <div class="text-center text-sm font-medium text-gray-500 py-2">
                                {{ $jour }}
                            </div>
                        @endforeach

                        @php
                            $firstDay = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1);
                            $lastDay = $firstDay->copy()->endOfMonth();
                            $daysToSkip = $firstDay->dayOfWeek == 0 ? 6 : $firstDay->dayOfWeek - 1;
                        @endphp

                        @for($i = 0; $i < $daysToSkip; $i++)
                            <div class="h-32 bg-gray-50 rounded-lg"></div>
                        @endfor

                        @for($day = 1; $day <= $lastDay->day; $day++)
                            @php
                                $currentDate = \Carbon\Carbon::create($selectedYear, $selectedMonth, $day);
                                $currentDateStr = $currentDate->format('Y-m-d');
                                $planning = $plannings->get($currentDateStr, collect([]));
                                $isWeekend = in_array($currentDate->dayOfWeek, [0, 6]);
                            @endphp
                            
                            <div class="h-32 rounded-lg border {{ !$planning->isEmpty() ? 'border-blue-200' : 'border-gray-200' }} p-2 overflow-hidden">
                                <div class="font-medium text-sm {{ $isWeekend ? 'text-gray-400' : 'text-gray-700' }}">
                                    {{ $day }}
                                </div>
                                @if(!$planning->isEmpty())
                                    <div class="mt-1 space-y-1">
                                        @foreach($planning as $p)
                                            <div class="rounded border border-blue-300 bg-blue-50 p-1 text-xs">
                                                <div class="font-semibold text-blue-700 truncate">{{ $p['lieu'] }}</div>
                                                <div class="text-gray-600">{{ $p['heure_debut'] }} - {{ $p['heure_fin'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
