<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-6">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                <div class="w-32 h-32 rounded-full bg-white opacity-10"></div>
            </div>
            
            <div class="relative flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        {{ __('Modifier la fiche de paie') }}
                    </h2>
                    <p class="text-purple-100 text-sm">
                        @php
                            $dateParts = explode('-', $fichePaie->mois);
                            $annee = $dateParts[0];
                            $mois = intval($dateParts[1]);
                            $moisNom = \Carbon\Carbon::create()->month($mois)->locale('fr_FR')->monthName;
                        @endphp
                        {{ $fichePaie->employe->nom }} {{ $fichePaie->employe->prenom }} - {{ $moisNom }} {{ $annee }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('fiches-paie.show', $fichePaie->id) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm text-white transition-all duration-200 font-medium">
                        <i class="fas fa-eye mr-2"></i>
                        Voir la fiche
                    </a>
                    <a href="{{ route('fiches-paie.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm text-white transition-all duration-200 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form action="{{ route('fiches-paie.update', $fichePaie->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Sélection de l'employé -->
                        <div>
                            <label for="employe_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                            <select name="employe_id" id="employe_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('employe_id') border-red-500 @enderror" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->id }}" {{ old('employe_id', $fichePaie->employe_id) == $employe->id ? 'selected' : '' }}>
                                        {{ $employe->nom }} {{ $employe->prenom }} - {{ $employe->poste }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employe_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Période -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="mois" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                <select name="mois" id="mois" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('mois') border-red-500 @enderror" required>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ old('mois', $fichePaie->mois) == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->locale('fr_FR')->monthName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mois')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="annee" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                                <select name="annee" id="annee" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('annee') border-red-500 @enderror" required>
                                    @foreach(range(date('Y')-2, date('Y')+1) as $y)
                                        <option value="{{ $y }}" {{ old('annee', $fichePaie->annee) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('annee')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg mb-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                            <h3 class="text-lg font-semibold text-purple-800">Calcul automatique des heures</h3>
                        </div>
                        <p class="text-purple-700 mb-4">
                            Les heures travaillées seront calculées automatiquement à partir des plannings de l'employé pour le mois sélectionné.
                            Vous pourrez ajuster ces valeurs si nécessaire.
                        </p>
                        <div class="flex justify-end">
                            <button type="button" id="calculerHeures" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
                                <i class="fas fa-calculator mr-2"></i>
                                Recalculer les heures
                            </button>
                        </div>
                    </div>
                    
                    <!-- Heures travaillées -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Heures travaillées</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Heures normales -->
                            <div>
                                <label for="heures_normales" class="block text-sm font-medium text-gray-700 mb-1">Heures normales</label>
                                <input type="number" name="heures_normales" id="heures_normales" step="0.01" min="0" value="{{ old('heures_normales', $fichePaie->heures_normales) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_normales') border-red-500 @enderror" required>
                                @error('heures_normales')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Heures supplémentaires 25% -->
                            <div>
                                <label for="heures_sup_25" class="block text-sm font-medium text-gray-700 mb-1">Heures sup. (25%)</label>
                                <input type="number" name="heures_sup_25" id="heures_sup_25" step="0.01" min="0" value="{{ old('heures_sup_25', $fichePaie->heures_sup_25) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_sup_25') border-red-500 @enderror">
                                @error('heures_sup_25')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Heures supplémentaires 50% -->
                            <div>
                                <label for="heures_sup_50" class="block text-sm font-medium text-gray-700 mb-1">Heures sup. (50%)</label>
                                <input type="number" name="heures_sup_50" id="heures_sup_50" step="0.01" min="0" value="{{ old('heures_sup_50', $fichePaie->heures_sup_50) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_sup_50') border-red-500 @enderror">
                                @error('heures_sup_50')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Heures de nuit -->
                            <div>
                                <label for="heures_nuit" class="block text-sm font-medium text-gray-700 mb-1">Heures de nuit</label>
                                <input type="number" name="heures_nuit" id="heures_nuit" step="0.01" min="0" value="{{ old('heures_nuit', $fichePaie->heures_nuit) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_nuit') border-red-500 @enderror">
                                @error('heures_nuit')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Heures dimanche/jours fériés -->
                            <div>
                                <label for="heures_dimanche_ferie" class="block text-sm font-medium text-gray-700 mb-1">Heures dimanche/jours fériés</label>
                                <input type="number" name="heures_dimanche_ferie" id="heures_dimanche_ferie" step="0.01" min="0" value="{{ old('heures_dimanche_ferie', $fichePaie->heures_dimanche_ferie) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_dimanche_ferie') border-red-500 @enderror">
                                @error('heures_dimanche_ferie')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Primes et indemnités -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Primes et indemnités</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Prime de performance -->
                            <div>
                                <label for="prime_performance" class="block text-sm font-medium text-gray-700 mb-1">Prime de performance</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="prime_performance" id="prime_performance" step="0.01" min="0" value="{{ old('prime_performance', $fichePaie->prime_performance) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('prime_performance') border-red-500 @enderror">
                                </div>
                                @error('prime_performance')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Prime d'ancienneté -->
                            <div>
                                <label for="prime_anciennete" class="block text-sm font-medium text-gray-700 mb-1">Prime d'ancienneté</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="prime_anciennete" id="prime_anciennete" step="0.01" min="0" value="{{ old('prime_anciennete', $fichePaie->prime_anciennete) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('prime_anciennete') border-red-500 @enderror">
                                </div>
                                @error('prime_anciennete')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Indemnités de transport -->
                            <div>
                                <label for="indemnite_transport" class="block text-sm font-medium text-gray-700 mb-1">Indemnités de transport</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="indemnite_transport" id="indemnite_transport" step="0.01" min="0" value="{{ old('indemnite_transport', $fichePaie->indemnite_transport) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('indemnite_transport') border-red-500 @enderror">
                                </div>
                                @error('indemnite_transport')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Indemnités de repas -->
                            <div>
                                <label for="indemnite_repas" class="block text-sm font-medium text-gray-700 mb-1">Indemnités de repas</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="indemnite_repas" id="indemnite_repas" step="0.01" min="0" value="{{ old('indemnite_repas', $fichePaie->indemnite_repas) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('indemnite_repas') border-red-500 @enderror">
                                </div>
                                @error('indemnite_repas')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Commentaires -->
                    <div class="mb-6">
                        <label for="commentaires" class="block text-sm font-medium text-gray-700 mb-1">Commentaires</label>
                        <textarea name="commentaires" id="commentaires" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('commentaires') border-red-500 @enderror">{{ old('commentaires', $fichePaie->commentaires) }}</textarea>
                        @error('commentaires')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('fiches-paie.show', $fichePaie->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calculerHeuresBtn = document.getElementById('calculerHeures');
            
            calculerHeuresBtn.addEventListener('click', function() {
                const employeId = document.getElementById('employe_id').value;
                const mois = document.getElementById('mois').value;
                const annee = document.getElementById('annee').value;
                
                if (!employeId) {
                    alert('Veuillez sélectionner un employé');
                    return;
                }
                
                // Afficher un indicateur de chargement
                calculerHeuresBtn.disabled = true;
                calculerHeuresBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Calcul en cours...';
                
                // Appel AJAX pour calculer les heures
                fetch(`/api/calculer-heures/${employeId}/${mois}/${annee}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('heures_normales').value = data.heures_normales;
                        document.getElementById('heures_sup_25').value = data.heures_sup_25;
                        document.getElementById('heures_sup_50').value = data.heures_sup_50;
                        document.getElementById('heures_nuit').value = data.heures_nuit;
                        document.getElementById('heures_dimanche_ferie').value = data.heures_dimanche_ferie;
                        
                        // Réactiver le bouton
                        calculerHeuresBtn.disabled = false;
                        calculerHeuresBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i> Recalculer les heures';
                    })
                    .catch(error => {
                        console.error('Erreur lors du calcul des heures:', error);
                        alert('Une erreur est survenue lors du calcul des heures');
                        
                        // Réactiver le bouton
                        calculerHeuresBtn.disabled = false;
                        calculerHeuresBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i> Recalculer les heures';
                    });
            });
        });
    </script>
    @endpush
</x-app-layout>
