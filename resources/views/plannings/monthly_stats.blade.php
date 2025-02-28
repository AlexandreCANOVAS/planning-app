@extends('layouts.app')

@section('content')
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
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jours Planifiés</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures Totales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employeStats as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $stat['employe']->nom }} {{ $stat['employe']->prenom }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $stat['nombre_jours'] }} jours</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($stat['heures_totales'], 1) }} heures</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('plannings.calendar.edit', ['employe' => $stat['employe']->id]) }}" 
                                   class="text-blue-600 hover:text-blue-900">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
@endsection
