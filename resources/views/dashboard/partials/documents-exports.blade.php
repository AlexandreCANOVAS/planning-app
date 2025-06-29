<!-- Documents et Exports -->
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">
        <i class="fas fa-file-export text-blue-500 mr-2"></i>
        Documents et Exports
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Planning mensuel -->
        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                <i class="fas fa-calendar text-blue-600 text-xl"></i>
            </div>
            <h4 class="text-gray-900 font-medium mb-2">Planning mensuel</h4>
            <p class="text-gray-500 text-sm mb-4">Exportez le planning de vos employés au format PDF</p>
            
            <form action="{{ route('export.plannings') }}" method="GET" class="space-y-3">
                <div class="space-y-2">
                    <label class="text-sm text-gray-600 block">Employé :</label>
                    <select name="employe_id" class="text-sm rounded-lg border-gray-200 w-full focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les employés</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">{{ $employe->nom }} {{ $employe->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm text-gray-600 block">Mois :</label>
                    <input type="month" name="mois" 
                           class="text-sm rounded-lg border-gray-200 w-full focus:ring-2 focus:ring-blue-500"
                           value="{{ now()->format('Y-m') }}"
                           onchange="updateDates(this.value)">
                </div>
                <input type="hidden" name="date_debut" id="date_debut">
                <input type="hidden" name="date_fin" id="date_fin">
                <button type="submit" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg px-4 py-2 text-sm font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-download mr-2"></i>
                    Télécharger
                </button>
            </form>
        </div>

        <!-- Documents comptables -->
        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                <i class="fas fa-calculator text-green-600 text-xl"></i>
            </div>
            <h4 class="text-gray-900 font-medium mb-2">Documents comptables</h4>
            <p class="text-gray-500 text-sm mb-4">Générez les documents comptables par employé</p>
            
            <form action="{{ route('export.comptabilite') }}" method="GET" class="space-y-3">
                <div class="space-y-2">
                    <label class="text-sm text-gray-600 block">Employé :</label>
                    <select name="employe_id" class="text-sm rounded-lg border-gray-200 w-full focus:ring-2 focus:ring-green-500">
                        <option value="">Tous les employés</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">{{ $employe->nom }} {{ $employe->prenom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm text-gray-600 block">Mois :</label>
                    <input type="month" name="mois" 
                           class="text-sm rounded-lg border-gray-200 w-full focus:ring-2 focus:ring-green-500"
                           value="{{ now()->format('Y-m') }}">
                </div>
                <button type="submit" class="w-full bg-green-50 hover:bg-green-100 text-green-600 rounded-lg px-4 py-2 text-sm font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-download mr-2"></i>
                    Télécharger
                </button>
            </form>
        </div>

        <!-- Fiches de paie (à venir) -->
        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 opacity-60">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mb-4">
                <i class="fas fa-file-invoice-dollar text-purple-600 text-xl"></i>
            </div>
            <h4 class="text-gray-900 font-medium mb-2">Fiches de paie</h4>
            <p class="text-gray-500 text-sm mb-4">Générez les fiches de paie de vos employés</p>
            
            <div class="mt-auto">
                <button disabled class="w-full bg-gray-100 text-gray-400 rounded-lg px-4 py-2 text-sm font-medium cursor-not-allowed flex items-center justify-center">
                    <i class="fas fa-clock mr-2"></i>
                    Bientôt disponible
                </button>
            </div>
        </div>

        <!-- Rapports d'activité (à venir) -->
        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 opacity-60">
            <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg mb-4">
                <i class="fas fa-chart-line text-orange-600 text-xl"></i>
            </div>
            <h4 class="text-gray-900 font-medium mb-2">Rapports d'activité</h4>
            <p class="text-gray-500 text-sm mb-4">Analysez l'activité de votre entreprise</p>
            
            <div class="mt-auto">
                <button disabled class="w-full bg-gray-100 text-gray-400 rounded-lg px-4 py-2 text-sm font-medium cursor-not-allowed flex items-center justify-center">
                    <i class="fas fa-clock mr-2"></i>
                    Bientôt disponible
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les dates au chargement
    const defaultMonth = document.querySelector('input[name="mois"]').value;
    updateDates(defaultMonth);
});

function updateDates(monthValue) {
    if (!monthValue) return;
    
    const [year, month] = monthValue.split('-');
    const startDate = new Date(year, month - 1, 1);
    const endDate = new Date(year, month, 0);
    
    document.getElementById('date_debut').value = startDate.toISOString().split('T')[0];
    document.getElementById('date_fin').value = endDate.toISOString().split('T')[0];
}
</script>
@endpush
