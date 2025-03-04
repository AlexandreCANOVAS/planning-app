<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Plannings') }}
            </h2>
            <div class="flex gap-4">
                <a href="{{ route('plannings.create-monthly') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    Planning mensuel
                </a>
                <a href="{{ route('plannings.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Créer un planning
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($plannings->isEmpty())
                        <p class="text-gray-500 text-center">Aucun planning n'a été créé pour le moment.</p>
                    @else
                        <div class="space-y-8">
                            @foreach($plannings as $yearMonth => $employeePlannings)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="bg-gray-100 px-4 py-3 border-b">
                                        <h3 class="text-lg font-semibold">
                                            {{ Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->locale('fr')->isoFormat('MMMM YYYY') }}
                                        </h3>
                                    </div>
                                    
                                    <div class="divide-y">
                                        @foreach($employeePlannings as $employeeId => $planningGroup)
                                            @php
                                                $employe = $planningGroup->first()->employe;
                                                $totalHeures = $planningGroup->sum('heures_travaillees');
                                            @endphp
                                            <div class="p-4 hover:bg-gray-50">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <h4 class="font-medium">{{ $employe->nom }} {{ $employe->prenom }}</h4>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $planningGroup->count() }} jours planifiés
                                                            <span class="mx-2">•</span>
                                                            {{ number_format($totalHeures, 1) }} heures
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <a href="{{ route('plannings.export-pdf', [
                                                            'employe_id' => $employeeId,
                                                            'mois' => Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('m'),
                                                            'annee' => Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('Y')
                                                        ]) }}" 
                                                        class="text-indigo-600 hover:text-indigo-800"
                                                        title="Télécharger le planning en PDF">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                        </a>
                                                        <a href="{{ route('plannings.calendar', [
                                                            'employe_id' => $employeeId,
                                                            'mois' => Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('m'),
                                                            'annee' => Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('Y')
                                                        ]) }}" 
                                                        class="text-indigo-600 hover:text-indigo-800">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('plannings.destroy-monthly', [
                                                            'employe_id' => $employeeId,
                                                            'yearMonth' => $yearMonth
                                                        ]) }}" 
                                                        method="POST" 
                                                        class="inline-block"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce planning ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 px-4 py-3 bg-gray-50 text-right sm:px-6">
        <form action="{{ route('plannings.export-pdf', ['employe_id' => ':employe_id', 'mois' => ':mois', 'annee' => ':annee']) }}" method="GET" class="inline-block" id="exportForm">
            <div class="flex items-center justify-end gap-4">
                <select name="employe_id" class="mt-1 block w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required onchange="updateExportUrl()">
                    <option value="">Sélectionner un employé</option>
                    @foreach($employes as $employe)
                        <option value="{{ $employe->id }}">{{ $employe->nom }} {{ $employe->prenom }}</option>
                    @endforeach
                </select>

                <select name="mois" class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required onchange="updateExportUrl()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>

                <select name="annee" class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required onchange="updateExportUrl()">
                    @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger PDF
                </button>
            </div>
        </form>
    </div>

    <script>
    function updateExportUrl() {
        const form = document.getElementById('exportForm');
        const employe = form.querySelector('[name="employe_id"]').value;
        const mois = form.querySelector('[name="mois"]').value;
        const annee = form.querySelector('[name="annee"]').value;
        
        if (employe && mois && annee) {
            const url = form.action
                .replace(':employe_id', employe)
                .replace(':mois', mois)
                .replace(':annee', annee);
            form.action = url;
        }
    }
    </script>
</x-app-layout>