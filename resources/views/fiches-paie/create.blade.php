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
                        {{ __('Créer une fiche de paie') }}
                    </h2>
                    <p class="text-purple-100 text-sm">
                        Création d'une nouvelle fiche de paie pour un employé
                    </p>
                </div>
                <div>
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
                <form action="{{ route('fiches-paie.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Sélection de l'employé -->
                        <div>
                            <label for="employe_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                            <select name="employe_id" id="employe_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('employe_id') border-red-500 @enderror" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->id }}" {{ old('employe_id') == $employe->id ? 'selected' : '' }}>
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
                                <label for="mois_select" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                                <select id="mois_select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" required>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ old('mois_select', date('n')) == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->locale('fr_FR')->monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="annee_select" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                                <select id="annee_select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" required>
                                    @foreach(range(date('Y')-2, date('Y')+1) as $y)
                                        <option value="{{ $y }}" {{ old('annee_select', date('Y')) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Champ caché pour stocker la valeur au format Y-m -->
                            <input type="hidden" name="mois" id="mois" value="{{ old('mois', date('Y-m')) }}">
                            @error('mois')
                                <div class="col-span-2">
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                </div>
                            @enderror
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
                                Calculer les heures
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
                                <input type="number" name="heures_normales" id="heures_normales" step="0.01" min="0" value="{{ old('heures_normales', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_normales') border-red-500 @enderror" required>
                                @error('heures_normales')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Heures supplémentaires 25% -->
                            <div>
                                <label for="heures_sup_25" class="block text-sm font-medium text-gray-700 mb-1">Heures sup. (25%)</label>
                                <input type="number" name="heures_sup_25" id="heures_sup_25" step="0.01" min="0" value="{{ old('heures_sup_25', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_sup_25') border-red-500 @enderror">
                                @error('heures_sup_25')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Heures supplémentaires 50% -->
                            <div>
                                <label for="heures_sup_50" class="block text-sm font-medium text-gray-700 mb-1">Heures sup. (50%)</label>
                                <input type="number" name="heures_sup_50" id="heures_sup_50" step="0.01" min="0" value="{{ old('heures_sup_50', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_sup_50') border-red-500 @enderror">
                                @error('heures_sup_50')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Heures de nuit -->
                            <div>
                                <label for="heures_nuit" class="block text-sm font-medium text-gray-700 mb-1">Heures de nuit</label>
                                <input type="number" name="heures_nuit" id="heures_nuit" step="0.01" min="0" value="{{ old('heures_nuit', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_nuit') border-red-500 @enderror">
                                @error('heures_nuit')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Heures dimanche/jours fériés -->
                            <div>
                                <label for="heures_dimanche_ferie" class="block text-sm font-medium text-gray-700 mb-1">Heures dimanche/jours fériés</label>
                                <input type="number" name="heures_dimanche_ferie" id="heures_dimanche_ferie" step="0.01" min="0" value="{{ old('heures_dimanche_ferie', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('heures_dimanche_ferie') border-red-500 @enderror">
                                @error('heures_dimanche_ferie')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Primes et indemnités -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Primes et indemnités</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Prime de transport -->
                            <div>
                                <label for="prime_transport" class="block text-sm font-medium text-gray-700 mb-1">Prime de transport</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="prime_transport" id="prime_transport" step="0.01" min="0" value="{{ old('prime_transport', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('prime_transport') border-red-500 @enderror">
                                </div>
                                @error('prime_transport')
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
                                    <input type="number" name="prime_anciennete" id="prime_anciennete" step="0.01" min="0" value="{{ old('prime_anciennete', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('prime_anciennete') border-red-500 @enderror">
                                </div>
                                @error('prime_anciennete')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Prime de performance -->
                            <div>
                                <label for="prime_performance" class="block text-sm font-medium text-gray-700 mb-1">Prime de performance</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="prime_performance" id="prime_performance" step="0.01" min="0" value="{{ old('prime_performance', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('prime_performance') border-red-500 @enderror">
                                </div>
                                @error('prime_performance')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Autres primes -->
                            <div>
                                <label for="autres_primes" class="block text-sm font-medium text-gray-700 mb-1">Autres primes</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="autres_primes" id="autres_primes" step="0.01" min="0" value="{{ old('autres_primes', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('autres_primes') border-red-500 @enderror">
                                </div>
                                @error('autres_primes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Indemnités repas -->
                            <div>
                                <label for="indemnites_repas" class="block text-sm font-medium text-gray-700 mb-1">Indemnités repas</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="indemnites_repas" id="indemnites_repas" step="0.01" min="0" value="{{ old('indemnites_repas', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('indemnites_repas') border-red-500 @enderror">
                                </div>
                                @error('indemnites_repas')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations salariales -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations salariales</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Salaire de base -->
                            <div>
                                <label for="salaire_base" class="block text-sm font-medium text-gray-700 mb-1">Salaire de base</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="salaire_base" id="salaire_base" step="0.01" min="0" value="{{ old('salaire_base', 1600) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('salaire_base') border-red-500 @enderror" required>
                                </div>
                                @error('salaire_base')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Cotisations salariales -->
                            <div>
                                <label for="cotisations_salariales" class="block text-sm font-medium text-gray-700 mb-1">Cotisations salariales</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="cotisations_salariales" id="cotisations_salariales" step="0.01" min="0" value="{{ old('cotisations_salariales', 350) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('cotisations_salariales') border-red-500 @enderror" required>
                                </div>
                                @error('cotisations_salariales')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Cotisations patronales -->
                            <div>
                                <label for="cotisations_patronales" class="block text-sm font-medium text-gray-700 mb-1">Cotisations patronales</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="cotisations_patronales" id="cotisations_patronales" step="0.01" min="0" value="{{ old('cotisations_patronales', 800) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('cotisations_patronales') border-red-500 @enderror" required>
                                </div>
                                @error('cotisations_patronales')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Impôt sur le revenu -->
                            <div>
                                <label for="impot_revenu" class="block text-sm font-medium text-gray-700 mb-1">Impôt sur le revenu</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="impot_revenu" id="impot_revenu" step="0.01" min="0" value="{{ old('impot_revenu', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('impot_revenu') border-red-500 @enderror">
                                </div>
                                @error('impot_revenu')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Taux horaire -->
                            <div>
                                <label for="taux_horaire" class="block text-sm font-medium text-gray-700 mb-1">Taux horaire</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="taux_horaire" id="taux_horaire" step="0.01" min="0" value="{{ old('taux_horaire', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('taux_horaire') border-red-500 @enderror">
                                </div>
                                @error('taux_horaire')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Avantages en nature -->
                            <div>
                                <label for="avantages_nature" class="block text-sm font-medium text-gray-700 mb-1">Avantages en nature</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="avantages_nature" id="avantages_nature" step="0.01" min="0" value="{{ old('avantages_nature', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('avantages_nature') border-red-500 @enderror">
                                </div>
                                @error('avantages_nature')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Absences et congés -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Absences et congés</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Jours de congés payés -->
                            <div>
                                <label for="jours_cp" class="block text-sm font-medium text-gray-700 mb-1">Jours de congés payés</label>
                                <input type="number" name="jours_cp" id="jours_cp" step="0.5" min="0" value="{{ old('jours_cp', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('jours_cp') border-red-500 @enderror">
                                @error('jours_cp')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Jours d'absence maladie -->
                            <div>
                                <label for="jours_maladie" class="block text-sm font-medium text-gray-700 mb-1">Jours d'absence maladie</label>
                                <input type="number" name="jours_maladie" id="jours_maladie" step="0.5" min="0" value="{{ old('jours_maladie', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('jours_maladie') border-red-500 @enderror">
                                @error('jours_maladie')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Jours d'absence non rémunérés -->
                            <div>
                                <label for="jours_absence_non_remuneres" class="block text-sm font-medium text-gray-700 mb-1">Jours d'absence non rémunérés</label>
                                <input type="number" name="jours_absence_non_remuneres" id="jours_absence_non_remuneres" step="0.5" min="0" value="{{ old('jours_absence_non_remuneres', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('jours_absence_non_remuneres') border-red-500 @enderror">
                                @error('jours_absence_non_remuneres')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Indemnités maladie -->
                            <div>
                                <label for="indemnites_maladie" class="block text-sm font-medium text-gray-700 mb-1">Indemnités maladie</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="indemnites_maladie" id="indemnites_maladie" step="0.01" min="0" value="{{ old('indemnites_maladie', 0) }}" class="calcul-element w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('indemnites_maladie') border-red-500 @enderror">
                                </div>
                                @error('indemnites_maladie')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Retenues diverses -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Retenues diverses</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Avance sur salaire -->
                            <div>
                                <label for="avance_salaire" class="block text-sm font-medium text-gray-700 mb-1">Avance sur salaire</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="avance_salaire" id="avance_salaire" step="0.01" min="0" value="{{ old('avance_salaire', 0) }}" class="calcul-element w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('avance_salaire') border-red-500 @enderror">
                                </div>
                                @error('avance_salaire')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Saisie sur salaire -->
                            <div>
                                <label for="saisie_salaire" class="block text-sm font-medium text-gray-700 mb-1">Saisie sur salaire</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="saisie_salaire" id="saisie_salaire" step="0.01" min="0" value="{{ old('saisie_salaire', 0) }}" class="calcul-element w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('saisie_salaire') border-red-500 @enderror">
                                </div>
                                @error('saisie_salaire')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Autres retenues -->
                            <div>
                                <label for="autres_retenues" class="block text-sm font-medium text-gray-700 mb-1">Autres retenues</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                    <input type="number" name="autres_retenues" id="autres_retenues" step="0.01" min="0" value="{{ old('autres_retenues', 0) }}" class="calcul-element w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('autres_retenues') border-red-500 @enderror">
                                </div>
                                @error('autres_retenues')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Récapitulatif et totaux -->
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border border-purple-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-purple-800 mb-4">Récapitulatif et totaux</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-2 border-b border-purple-200">
                                    <span class="text-sm font-medium text-gray-700">Salaire brut</span>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">€</span>
                                        </div>
                                        <input type="number" name="salaire_brut" id="salaire_brut" step="0.01" min="0" value="{{ old('salaire_brut', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('salaire_brut') border-red-500 @enderror" readonly>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center pb-2 border-b border-purple-200">
                                    <span class="text-sm font-medium text-gray-700">Total des cotisations</span>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">€</span>
                                        </div>
                                        <input type="number" name="total_cotisations" id="total_cotisations" step="0.01" min="0" value="{{ old('total_cotisations', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" readonly>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center pb-2 border-b border-purple-200">
                                    <span class="text-sm font-medium text-gray-700">Total des retenues</span>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">€</span>
                                        </div>
                                        <input type="number" name="total_retenues" id="total_retenues" step="0.01" min="0" value="{{ old('total_retenues', 0) }}" class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-800">Salaire net à payer</span>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">€</span>
                                        </div>
                                        <input type="number" name="salaire_net" id="salaire_net" step="0.01" min="0" value="{{ old('salaire_net', 0) }}" class="w-full pl-8 text-lg font-bold rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('salaire_net') border-red-500 @enderror" readonly>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Le salaire net est calculé automatiquement à partir des informations saisies ci-dessus.</p>
                                <button type="button" id="calculerTotaux" class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Calculer les totaux
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Éléments personnalisables -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
                            <h3 class="text-lg font-semibold text-gray-800">Éléments personnalisables</h3>
                            <div class="flex flex-wrap gap-2">
                                <div class="dropdown relative">
                                    <button type="button" class="dropdown-toggle inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-md transition-colors duration-200">
                                        <i class="fas fa-list mr-1"></i>
                                        Ajouter un modèle
                                    </button>
                                    <div class="dropdown-menu hidden absolute right-0 mt-1 z-10 w-64 bg-white rounded-md shadow-lg py-1 border border-gray-200">
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Prime d'assiduité" data-type="montant" data-valeur="50">
                                            Prime d'assiduité
                                        </button>
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Prime de responsabilité" data-type="montant" data-valeur="100">
                                            Prime de responsabilité
                                        </button>
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Heures de formation" data-type="heures" data-valeur="7">
                                            Heures de formation
                                        </button>
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Indemnité de télétravail" data-type="montant" data-valeur="30">
                                            Indemnité de télétravail
                                        </button>
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Participation aux bénéfices" data-type="pourcentage" data-valeur="2">
                                            Participation aux bénéfices
                                        </button>
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Prime de fin d'année" data-type="montant" data-valeur="200">
                                            Prime de fin d'année
                                        </button>
                                        <button type="button" class="modele-element block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50" 
                                            data-nom="Remboursement frais" data-type="montant" data-valeur="0">
                                            Remboursement frais
                                        </button>
                                    </div>
                                </div>
                                <button type="button" id="ajouterElement" class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-md transition-colors duration-200">
                                    <i class="fas fa-plus mr-1"></i>
                                    Ajouter un élément
                                </button>
                            </div>
                        </div>
                        
                        <div id="elementsPersonnalises" class="space-y-4">
                            <!-- Les éléments personnalisés seront ajoutés ici dynamiquement -->
                            @if(old('elements_personnalises_nom') && count(old('elements_personnalises_nom')) > 0)
                                @foreach(old('elements_personnalises_nom') as $index => $nom)
                                    <div class="element-personnalise grid grid-cols-1 md:grid-cols-12 gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="md:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'élément</label>
                                            <input type="text" name="elements_personnalises_nom[]" value="{{ $nom }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" placeholder="Ex: Prime exceptionnelle" required>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                            <select name="elements_personnalises_type[]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                                <option value="montant" {{ old('elements_personnalises_type')[$index] == 'montant' ? 'selected' : '' }}>Montant (€)</option>
                                                <option value="pourcentage" {{ old('elements_personnalises_type')[$index] == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                                <option value="heures" {{ old('elements_personnalises_type')[$index] == 'heures' ? 'selected' : '' }}>Heures</option>
                                                <option value="jours" {{ old('elements_personnalises_type')[$index] == 'jours' ? 'selected' : '' }}>Jours</option>
                                                <option value="texte" {{ old('elements_personnalises_type')[$index] == 'texte' ? 'selected' : '' }}>Texte</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Valeur</label>
                                            <input type="text" name="elements_personnalises_valeur[]" value="{{ old('elements_personnalises_valeur')[$index] }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="md:col-span-2 flex items-end">
                                            <button type="button" class="supprimer-element w-full md:w-auto px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition-colors duration-200">
                                                <i class="fas fa-trash-alt"></i>
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div id="aucunElement" class="{{ old('elements_personnalises_nom') && count(old('elements_personnalises_nom')) > 0 ? 'hidden' : '' }} text-center py-6 text-gray-500 italic">
                            Aucun élément personnalisé ajouté. Cliquez sur "Ajouter un élément" pour en créer un.
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="mb-6">
                        <label for="commentaires" class="block text-sm font-medium text-gray-700 mb-1">Commentaires</label>
                        <textarea name="commentaires" id="commentaires" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('commentaires') border-red-500 @enderror">{{ old('commentaires') }}</textarea>
                        @error('commentaires')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('fiches-paie.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour convertir le format HH:MM en nombre décimal
            function convertirHHMMEnDecimal(hhmmStr) {
                if (!hhmmStr || hhmmStr === '00:00') return 0;
                
                const [heures, minutes] = hhmmStr.split(':').map(Number);
                return heures + (minutes / 60);
            }
            
            // Fonction pour mettre à jour le champ caché mois au format Y-m
            function updateMoisField() {
                const mois = document.getElementById('mois_select').value.padStart(2, '0');
                const annee = document.getElementById('annee_select').value;
                document.getElementById('mois').value = `${annee}-${mois}`;
            }
            
            // Mettre à jour le champ mois lors du chargement de la page
            updateMoisField();
            
            // Ajouter des écouteurs d'événements pour les changements de mois et d'année
            document.getElementById('mois_select').addEventListener('change', updateMoisField);
            document.getElementById('annee_select').addEventListener('change', updateMoisField);
            
            const calculerHeuresBtn = document.getElementById('calculerHeures');
            
            calculerHeuresBtn.addEventListener('click', function() {
                const employeId = document.getElementById('employe_id').value;
                const mois = document.getElementById('mois_select').value;
                const annee = document.getElementById('annee_select').value;
                
                if (!employeId) {
                    alert('Veuillez sélectionner un employé');
                    return;
                }
                
                // Afficher un indicateur de chargement
                calculerHeuresBtn.disabled = true;
                calculerHeuresBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Calcul en cours...';
                
                // Appel AJAX pour calculer les heures
                fetch(`/api/calculer-heures/${employeId}/${mois}/${annee}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues:', data);
                        
                        if (data.total_mois) {
                            // Convertir les heures du format HH:MM en décimal
                            const totalHeures = convertirHHMMEnDecimal(data.total_mois.heures);
                            const heuresSup25 = convertirHHMMEnDecimal(data.total_mois.heures_sup_25);
                            const heuresSup50 = convertirHHMMEnDecimal(data.total_mois.heures_sup_50);
                            const heuresNuit = convertirHHMMEnDecimal(data.total_mois.heures_nuit);
                            const heuresDimanche = convertirHHMMEnDecimal(data.total_mois.heures_dimanche);
                            const heuresJoursFeries = convertirHHMMEnDecimal(data.total_mois.heures_jours_feries);
                            
                            // Calculer les heures normales (total - toutes les heures spéciales)
                            const heuresNormales = totalHeures - heuresSup25 - heuresSup50 - heuresNuit - heuresDimanche - heuresJoursFeries;
                            
                            document.getElementById('heures_normales').value = heuresNormales.toFixed(2);
                            document.getElementById('heures_sup_25').value = heuresSup25.toFixed(2);
                            document.getElementById('heures_sup_50').value = heuresSup50.toFixed(2);
                            document.getElementById('heures_nuit').value = heuresNuit.toFixed(2);
                            
                            // Combiner les heures de dimanche et jours fériés
                            const heuresDimancheFerie = heuresDimanche + heuresJoursFeries;
                            document.getElementById('heures_dimanche_ferie').value = heuresDimancheFerie.toFixed(2);
                        }
                        
                        // Réactiver le bouton
                        calculerHeuresBtn.disabled = false;
                        calculerHeuresBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i> Calculer les heures';
                    })
                    .catch(error => {
                        console.error('Erreur lors du calcul des heures:', error);
                        alert('Une erreur est survenue lors du calcul des heures. Veuillez réessayer.');
                        
                        // Réactiver le bouton
                        calculerHeuresBtn.disabled = false;
                        calculerHeuresBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i> Calculer les heures';
                    });
            });

            // Gestion des éléments personnalisables
            const ajouterElementBtn = document.getElementById('ajouterElement');
            const elementsContainer = document.getElementById('elementsPersonnalises');
            const aucunElementDiv = document.getElementById('aucunElement');
            let elementCounter = document.querySelectorAll('.element-personnalise').length;
            
            // Gestion du menu déroulant des modèles
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            
            // Ouvrir/fermer le menu déroulant
            dropdownToggle.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
            });
            
            // Fermer le menu déroulant en cliquant ailleurs sur la page
            document.addEventListener('click', function(event) {
                if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
            
            // Ajouter des écouteurs d'événements pour les modèles
            document.querySelectorAll('.modele-element').forEach(function(button) {
                button.addEventListener('click', function() {
                    const nom = this.getAttribute('data-nom');
                    const type = this.getAttribute('data-type');
                    const valeur = this.getAttribute('data-valeur');
                    
                    ajouterElementPersonnalise(nom, type, valeur);
                    dropdownMenu.classList.add('hidden');
                });
            });

            // Fonction pour ajouter un nouvel élément personnalisé
            function ajouterElementPersonnalise(nomPredefini = '', typePredefini = 'montant', valeurPredefinie = '') {
                // Incrémenter le compteur
                elementCounter++;
                
                // Créer un nouvel élément
                const nouvelElement = document.createElement('div');
                nouvelElement.className = 'element-personnalise grid grid-cols-1 md:grid-cols-12 gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200';
                
                nouvelElement.innerHTML = `
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'élément</label>
                        <input type="text" name="elements_personnalises_nom[]" value="${nomPredefini}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" placeholder="Ex: Prime exceptionnelle" required>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="elements_personnalises_type[]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            <option value="montant" ${typePredefini === 'montant' ? 'selected' : ''}>Montant (€)</option>
                            <option value="pourcentage" ${typePredefini === 'pourcentage' ? 'selected' : ''}>Pourcentage (%)</option>
                            <option value="heures" ${typePredefini === 'heures' ? 'selected' : ''}>Heures</option>
                            <option value="jours" ${typePredefini === 'jours' ? 'selected' : ''}>Jours</option>
                            <option value="texte" ${typePredefini === 'texte' ? 'selected' : ''}>Texte</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valeur</label>
                        <input type="text" name="elements_personnalises_valeur[]" value="${valeurPredefinie}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="button" class="supprimer-element w-full md:w-auto px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-trash-alt"></i>
                            Supprimer
                        </button>
                    </div>
                `;
                
                // Ajouter l'élément au conteneur
                elementsContainer.appendChild(nouvelElement);
                
                // Masquer le message "Aucun élément"
                aucunElementDiv.classList.add('hidden');
                
                // Ajouter l'écouteur d'événement pour le bouton de suppression
                const supprimerBtn = nouvelElement.querySelector('.supprimer-element');
                supprimerBtn.addEventListener('click', function() {
                    supprimerElementPersonnalise(nouvelElement);
                });
                
                // Mettre le focus sur le champ nom si aucun nom n'est prédéfini
                if (!nomPredefini) {
                    nouvelElement.querySelector('input[name="elements_personnalises_nom[]"]').focus();
                }
            }

            // Fonction pour supprimer un élément personnalisé
            function supprimerElementPersonnalise(element) {
                // Supprimer l'élément
                element.remove();
                
                // Décrémenter le compteur
                elementCounter--;
                
                // Afficher le message "Aucun élément" si nécessaire
                if (elementCounter === 0) {
                    aucunElementDiv.classList.remove('hidden');
                }
            }
            
            // Ajouter un écouteur d'événement pour le bouton d'ajout
            ajouterElementBtn.addEventListener('click', ajouterElementPersonnalise);
            
            // Calcul des totaux
            const calculerTotauxBtn = document.getElementById('calculerTotaux');
            
            calculerTotauxBtn.addEventListener('click', function() {
                // Récupérer les valeurs des champs
                const tauxHoraire = parseFloat(document.getElementById('taux_horaire').value) || 0;
                const heuresTravaillees = parseFloat(document.getElementById('heures_travaillees').value) || 0;
                const heuresSupplementaires = parseFloat(document.getElementById('heures_supplementaires').value) || 0;
                const primeRendement = parseFloat(document.getElementById('prime_rendement').value) || 0;
                const primeAnciennete = parseFloat(document.getElementById('prime_anciennete').value) || 0;
                const primeTransport = parseFloat(document.getElementById('prime_transport').value) || 0;
                const avantagesNature = parseFloat(document.getElementById('avantages_nature').value) || 0;
                const indemnitesMaladie = parseFloat(document.getElementById('indemnites_maladie').value) || 0;
                
                // Retenues
                const avanceSalaire = parseFloat(document.getElementById('avance_salaire').value) || 0;
                const saisieSalaire = parseFloat(document.getElementById('saisie_salaire').value) || 0;
                const autresRetenues = parseFloat(document.getElementById('autres_retenues').value) || 0;
                
                // Calcul du salaire brut de base
                const salaireBase = tauxHoraire * heuresTravaillees;
                
                // Calcul du salaire brut total (base + heures supp + primes)
                const salaireBrut = salaireBase + 
                                    (tauxHoraire * 1.25 * heuresSupplementaires) + 
                                    primeRendement + 
                                    primeAnciennete + 
                                    primeTransport + 
                                    avantagesNature + 
                                    indemnitesMaladie;
                
                // Calcul des cotisations (estimation simplifiée à 23% du salaire brut)
                const totalCotisations = salaireBrut * 0.23;
                
                // Calcul du total des retenues
                const totalRetenues = avanceSalaire + saisieSalaire + autresRetenues;
                
                // Calcul du salaire net
                const salaireNet = salaireBrut - totalCotisations - totalRetenues;
                
                // Ajouter les valeurs des éléments personnalisés (si de type montant)
                const elementsPersonnalises = document.querySelectorAll('.element-personnalise');
                let totalElementsPersonnalises = 0;
                
                elementsPersonnalises.forEach(function(element) {
                    const typeElement = element.querySelector('select[name="elements_personnalises_type[]"]').value;
                    const valeurElement = parseFloat(element.querySelector('input[name="elements_personnalises_valeur[]"]').value) || 0;
                    
                    if (typeElement === 'montant') {
                        totalElementsPersonnalises += valeurElement;
                    } else if (typeElement === 'pourcentage') {
                        totalElementsPersonnalises += (salaireBrut * valeurElement / 100);
                    }
                });
                
                // Mise à jour des champs de totaux
                document.getElementById('salaire_brut').value = (salaireBrut + totalElementsPersonnalises).toFixed(2);
                document.getElementById('total_cotisations').value = totalCotisations.toFixed(2);
                document.getElementById('total_retenues').value = totalRetenues.toFixed(2);
                document.getElementById('salaire_net').value = (salaireNet + totalElementsPersonnalises).toFixed(2);
                
                // Animation pour mettre en évidence les résultats
                const champsResultats = [document.getElementById('salaire_brut'), 
                                        document.getElementById('total_cotisations'),
                                        document.getElementById('total_retenues'),
                                        document.getElementById('salaire_net')];
                
                champsResultats.forEach(function(champ) {
                    champ.classList.add('bg-yellow-50');
                    setTimeout(function() {
                        champ.classList.remove('bg-yellow-50');
                    }, 1500);
                });
            });

            // Ajouter des écouteurs d'événements pour les boutons de suppression existants
            document.querySelectorAll('.supprimer-element').forEach(function(button) {
                button.addEventListener('click', function() {
                    const element = button.closest('.element-personnalise');
                    supprimerElementPersonnalise(element);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
