@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Créer un planning mensuel') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('plannings.mensuel.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="employe_id" class="block text-sm font-medium text-gray-700">Employé</label>
                                <select id="employe_id" name="employe_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionnez un employé</option>
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->id }}" {{ old('employe_id') == $employe->id ? 'selected' : '' }}>
                                            {{ $employe->nom }} {{ $employe->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employe_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="lieu_id" class="block text-sm font-medium text-gray-700">Lieu de travail</label>
                                <select id="lieu_id" name="lieu_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionnez un lieu</option>
                                    <option value="cp">CP (Congé Payé)</option>
                                    @foreach($lieux as $lieu)
                                        <option value="{{ $lieu->id }}" {{ old('lieu_id') == $lieu->id ? 'selected' : '' }}>
                                            {{ $lieu->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lieu_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                                <input type="month" id="mois" name="mois" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ old('mois') }}" required>
                                @error('mois')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jours de travail</label>
                                <div class="mt-2 grid grid-cols-4 gap-4">
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(1, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Lundi</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="2" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(2, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Mardi</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="3" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(3, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Mercredi</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="4" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(4, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Jeudi</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="5" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(5, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Vendredi</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="6" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(6, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Samedi</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="jours[]" value="0" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ in_array(0, old('jours', [])) ? 'checked' : '' }}>
                                            <span class="ml-2">Dimanche</span>
                                        </label>
                                    </div>
                                </div>
                                @error('jours')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="heure_debut" class="block text-sm font-medium text-gray-700">Heure de début</label>
                                    <input type="time" id="heure_debut" name="heure_debut" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ old('heure_debut') }}" required>
                                    @error('heure_debut')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="heure_fin" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                                    <input type="time" id="heure_fin" name="heure_fin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ old('heure_fin') }}" required>
                                    @error('heure_fin')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" id="fillReposButton" class="mb-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Remplir les jours non sélectionnés avec des repos
                                </button>
                            </div>

                            <input type="hidden" name="jours_repos" id="jours_repos" value="">

                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Créer le planning mensuel') }}
                            </button>
                            <a href="{{ route('plannings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('lieu_id').addEventListener('change', function() {
        const heureDebut = document.getElementById('heure_debut');
        const heureFin = document.getElementById('heure_fin');
        
        if (this.value === 'cp') {
            heureDebut.value = '00:00';
            heureFin.value = '00:00';
            heureDebut.readOnly = true;
            heureFin.readOnly = true;
        } else {
            heureDebut.readOnly = false;
            heureFin.readOnly = false;
            if (heureDebut.value === '00:00' && heureFin.value === '00:00') {
                heureDebut.value = '';
                heureFin.value = '';
            }
        }
    });

    document.getElementById('fillReposButton').addEventListener('click', function() {
        const mois = document.getElementById('mois').value;
        if (!mois) {
            alert('Veuillez sélectionner un mois avant de remplir les jours de repos.');
            return;
        }

        const joursSelectionnes = Array.from(document.querySelectorAll('input[name="jours[]"]:checked')).map(cb => parseInt(cb.value));
        const joursRepos = [];
        
        // Créer un objet Date pour le premier jour du mois
        const date = new Date(mois + '-01');
        const dernierJour = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

        // Parcourir tous les jours du mois
        for (let jour = 1; jour <= dernierJour; jour++) {
            date.setDate(jour);
            const jourSemaine = date.getDay() || 7; // Convertir 0 (dimanche) en 7
            
            // Si ce n'est pas un jour travaillé (et pas un dimanche), ajouter aux jours de repos
            if (!joursSelectionnes.includes(jourSemaine) && jourSemaine !== 7) {
                joursRepos.push(date.toISOString().split('T')[0]); // Format YYYY-MM-DD
            }
        }

        // Stocker les jours de repos dans le champ caché
        document.getElementById('jours_repos').value = JSON.stringify(joursRepos);
        
        alert(`${joursRepos.length} jours de repos seront ajoutés au planning.`);
    });
</script>
@endpush
