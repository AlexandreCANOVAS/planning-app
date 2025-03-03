@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Mon Planning -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Mon Planning</h2>
                
                <!-- Filtres et actions -->
                <div class="space-y-6">
                    <!-- Sélecteurs de période -->
                    <div class="flex items-center space-x-4">
                        <select name="mois" id="mois" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" form="filter-form">
                            @foreach(range(1, 12) as $mois)
                                <option value="{{ $mois }}" {{ $selectedMonth == $mois ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName }}
                                </option>
                            @endforeach
                        </select>
                        <select name="annee" id="annee" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" form="filter-form">
                            @foreach(range(now()->year - 2, now()->year + 2) as $annee)
                                <option value="{{ $annee }}" {{ $selectedYear == $annee ? 'selected' : '' }}>
                                    {{ $annee }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Total des heures -->
                    <div class="text-sm text-gray-600">
                        Total des heures pour {{ \Carbon\Carbon::create()->month($selectedMonth)->locale('fr')->monthName }} {{ $selectedYear }} : 
                        <span class="font-semibold text-gray-900">{{ $totalHeures }}h</span>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex space-x-4">
                        <a href="{{ route('employe.plannings.calendar') }}?mois={{ $selectedMonth }}&annee={{ $selectedYear }}" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Consulter mon planning
                        </a>
                        <a href="{{ route('employe.plannings.download-pdf') }}?mois={{ $selectedMonth }}&annee={{ $selectedYear }}" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Télécharger en PDF
                        </a>
                    </div>
                </div>

                <form id="filter-form" method="GET" action="{{ route('employe.plannings.index') }}" class="hidden"></form>
            </div>

            <!-- Planning Collègue -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Planning Collègue</h2>
                
                <div class="space-y-6">
                    <div class="space-y-6">
                        <!-- Sélection du collègue et période -->
                        <div class="flex items-center space-x-4">
                            <select id="employe_id" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Sélectionner un collègue</option>
                                @foreach(App\Models\Employe::where('societe_id', auth()->user()->societe_id)
                                        ->where('id', '!=', auth()->user()->employe->id)
                                        ->orderBy('nom')
                                        ->get() as $collegue)
                                    <option value="{{ $collegue->id }}">{{ $collegue->nom }} {{ $collegue->prenom }}</option>
                                @endforeach
                            </select>
                            <select id="mois_collegue" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach(range(1, 12) as $mois)
                                    <option value="{{ $mois }}" {{ now()->month == $mois ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName }}
                                    </option>
                                @endforeach
                            </select>
                            <select id="annee_collegue" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach(range(now()->year - 2, now()->year + 2) as $annee)
                                    <option value="{{ $annee }}" {{ now()->year == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bouton de consultation -->
                        <div>
                            <button type="button" onclick="voirPlanningCollegue()" 
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Voir le planning
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function voirPlanningCollegue() {
                        const employeId = document.getElementById('employe_id').value;
                        if (!employeId) {
                            alert('Veuillez sélectionner un collègue');
                            return;
                        }
                        const mois = document.getElementById('mois_collegue').value;
                        const annee = document.getElementById('annee_collegue').value;
                        
                        // Construire l'URL avec le paramètre employe dans le chemin
                        const url = "{{ route('employe.plannings.collegue', ['employe' => ':employe']) }}".replace(':employe', employeId) + 
                            '?mois=' + mois + '&annee=' + annee;
                        
                        window.location.href = url;
                    }
                </script>
            </div>
        </div>
    </div>

    <script>
        // Mise à jour automatique des paramètres dans les liens quand les sélecteurs changent
        document.getElementById('mois').addEventListener('change', updateLinks);
        document.getElementById('annee').addEventListener('change', updateLinks);

        function updateLinks() {
            const mois = document.getElementById('mois').value;
            const annee = document.getElementById('annee').value;
            
            // Mettre à jour les liens avec les nouveaux paramètres
            document.querySelector('a[href*="plannings.calendar"]').href = 
                "{{ route('employe.plannings.calendar') }}?mois=" + mois + "&annee=" + annee;
            
            document.querySelector('a[href*="plannings.download-pdf"]').href = 
                "{{ route('employe.plannings.download-pdf') }}?mois=" + mois + "&annee=" + annee;

            // Soumettre le formulaire pour mettre à jour le total des heures
            document.getElementById('filter-form').submit();
        }
    </script>
@endsection
