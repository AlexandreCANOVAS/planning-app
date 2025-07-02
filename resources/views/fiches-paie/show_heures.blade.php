<!-- Détail des heures travaillées -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Détail des heures travaillées</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <!-- Heures normales -->
        <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
            <div class="text-sm text-gray-500 mb-1">Heures normales</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($fichePaie->heures_normales, 2, ',', ' ') }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ number_format($fichePaie->montant_heures_normales, 2, ',', ' ') }} €</div>
        </div>
        
        <!-- Heures supplémentaires 25% -->
        <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-200">
            <div class="text-sm text-blue-700 mb-1">Heures sup. (25%)</div>
            <div class="text-2xl font-bold text-blue-800">{{ number_format($fichePaie->heures_sup_25, 2, ',', ' ') }}</div>
            <div class="text-xs text-blue-700 mt-1">{{ number_format($fichePaie->montant_heures_sup_25, 2, ',', ' ') }} €</div>
        </div>
        
        <!-- Heures supplémentaires 50% -->
        <div class="bg-indigo-50 rounded-lg p-4 text-center border border-indigo-200">
            <div class="text-sm text-indigo-700 mb-1">Heures sup. (50%)</div>
            <div class="text-2xl font-bold text-indigo-800">{{ number_format($fichePaie->heures_sup_50, 2, ',', ' ') }}</div>
            <div class="text-xs text-indigo-700 mt-1">{{ number_format($fichePaie->montant_heures_sup_50, 2, ',', ' ') }} €</div>
        </div>
        
        <!-- Heures de nuit -->
        <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-200">
            <div class="text-sm text-purple-700 mb-1">Heures de nuit</div>
            <div class="text-2xl font-bold text-purple-800">{{ number_format($fichePaie->heures_nuit, 2, ',', ' ') }}</div>
            <div class="text-xs text-purple-700 mt-1">{{ number_format($fichePaie->montant_heures_nuit, 2, ',', ' ') }} €</div>
        </div>
        
        <!-- Heures dimanche/jours fériés -->
        <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200">
            <div class="text-sm text-red-700 mb-1">Dim./Jours fériés</div>
            <div class="text-2xl font-bold text-red-800">{{ number_format($fichePaie->heures_dimanche_ferie, 2, ',', ' ') }}</div>
            <div class="text-xs text-red-700 mt-1">{{ number_format($fichePaie->montant_heures_dimanche_ferie, 2, ',', ' ') }} €</div>
        </div>
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
        <div class="flex justify-between items-center">
            <span class="font-medium text-gray-700">Total des heures travaillées:</span>
            <span class="font-bold text-lg text-gray-900">
                {{ number_format($fichePaie->heures_normales + $fichePaie->heures_sup_25 + $fichePaie->heures_sup_50 + $fichePaie->heures_nuit + $fichePaie->heures_dimanche_ferie, 2, ',', ' ') }} heures
            </span>
        </div>
    </div>
    
    <div class="text-sm text-gray-500">
        <p><i class="fas fa-info-circle mr-1"></i> Les heures de nuit sont comptabilisées entre 21h et 6h avec une majoration de 20%.</p>
        <p><i class="fas fa-info-circle mr-1"></i> Les heures travaillées les dimanches et jours fériés sont majorées de 50%.</p>
    </div>
</div>
