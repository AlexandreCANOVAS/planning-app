<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Planning Mensuel - {{ $mois }} {{ $anneeActuelle }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Sélecteur de mois/année -->
                    <div class="mb-6">
                        <form action="{{ route('plannings.calendar') }}" method="GET" class="flex space-x-4">
                            <select name="mois" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $moisActuel ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="annee" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach(range($anneeActuelle - 1, $anneeActuelle + 1) as $a)
                                    <option value="{{ $a }}" {{ $a == $anneeActuelle ? 'selected' : '' }}>
                                        {{ $a }}
                                    </option>
                                @endforeach
                            </select>
                            <x-primary-button>Afficher</x-primary-button>
                        </form>
                    </div>

                    <!-- Calendrier -->
                    <div class="grid grid-cols-7 gap-2">
                        <!-- En-têtes des jours -->
                        @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $jour)
                            <div class="text-center font-semibold p-2 bg-gray-100">
                                {{ $jour }}
                            </div>
                        @endforeach

                        <!-- Jours du mois -->
                        @foreach($joursCalendrier as $jour)
                            <div class="min-h-[120px] border rounded-lg p-2 {{ $jour['aujourdhui'] ? 'bg-indigo-50' : ($jour['dansLeMois'] ? 'bg-white' : 'bg-gray-50') }}"
                                 data-date="{{ $jour['date'] }}">
                                <div class="flex justify-between items-start">
                                    <span class="font-medium {{ $jour['dansLeMois'] ? '' : 'text-gray-400' }}">
                                        {{ $jour['numero'] }}
                                    </span>
                                    @if($jour['dansLeMois'])
                                        <button onclick="ouvrirModalPlanning('{{ $jour['date'] }}')" 
                                                class="text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                @if($jour['dansLeMois'] && isset($plannings[$jour['date']]))
                                    <div class="mt-2 text-sm">
                                        @foreach($plannings[$jour['date']] as $planning)
                                            <div class="bg-indigo-100 text-indigo-800 rounded p-1 mb-1 text-xs">
                                                {{ $planning->employe->nom }} - {{ $planning->lieu->nom }}
                                                <br>{{ $planning->heure_debut }} - {{ $planning->heure_fin }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier un planning -->
    <div id="modalPlanning" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Planning du <span id="modalDate"></span>
                </h3>
                <form id="formPlanning" method="POST">
                    @csrf
                    <input type="hidden" name="date" id="inputDate">
                    
                    <div class="mb-4">
                        <x-input-label for="employe_id" value="Employé" />
                        <select name="employe_id" id="employe_id" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach($employes as $employe)
                                <option value="{{ $employe->id }}">{{ $employe->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="lieu_id" value="Lieu de travail" />
                        <select name="lieu_id" id="lieu_id" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach($lieux as $lieu)
                                <option value="{{ $lieu->id }}">{{ $lieu->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="heure_debut" value="Heure de début" />
                            <x-text-input type="time" name="heure_debut" id="heure_debut" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="heure_fin" value="Heure de fin" />
                            <x-text-input type="time" name="heure_fin" id="heure_fin" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <x-secondary-button type="button" onclick="fermerModalPlanning()">
                            Annuler
                        </x-secondary-button>
                        <x-primary-button>
                            Enregistrer
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function ouvrirModalPlanning(date) {
            document.getElementById('modalDate').textContent = date;
            document.getElementById('inputDate').value = date;
            document.getElementById('modalPlanning').classList.remove('hidden');
            document.getElementById('formPlanning').action = "{{ route('plannings.store') }}";
        }

        function fermerModalPlanning() {
            document.getElementById('modalPlanning').classList.add('hidden');
        }

        // Fermer le modal si on clique en dehors
        document.getElementById('modalPlanning').addEventListener('click', function(e) {
            if (e.target === this) {
                fermerModalPlanning();
            }
        });
    </script>
    @endpush
</x-app-layout>
