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
                        {{ __('Fiche de paie') }}
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
                    <a href="{{ route('fiches-paie.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm text-white transition-all duration-200 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à la liste
                    </a>
                    <a href="{{ route('fiches-paie.export-pdf', $fichePaie->id) }}" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-sm text-white transition-all duration-200 font-medium">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Exporter en PDF
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statut et actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="text-lg font-semibold">Statut:</div>
                        @if($fichePaie->statut === 'brouillon')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-pencil-alt mr-2"></i> Brouillon
                            </span>
                        @elseif($fichePaie->statut === 'validé')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-check-circle mr-2"></i> Validé
                            </span>
                        @elseif($fichePaie->statut === 'publié')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-paper-plane mr-2"></i> Publié
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex space-x-2">
                        @if($fichePaie->statut === 'brouillon')
                            <a href="{{ route('fiches-paie.edit', $fichePaie->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </a>
                            
                            <form action="{{ route('fiches-paie.valider', $fichePaie->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md transition-colors duration-200">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Valider
                                </button>
                            </form>
                            
                            <form action="{{ route('fiches-paie.destroy', $fichePaie->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette fiche de paie ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-200">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer
                                </button>
                            </form>
                        @endif
                        
                        @if($fichePaie->statut === 'validé')
                            <form action="{{ route('fiches-paie.publier', $fichePaie->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Publier
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Informations générales -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Informations employé -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations employé</h3>
                    <div class="flex items-center mb-4">
                        @if($fichePaie->employe->photo)
                            <img class="h-16 w-16 rounded-full object-cover mr-4" src="{{ asset('storage/' . $fichePaie->employe->photo) }}" alt="{{ $fichePaie->employe->nom }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                <span class="text-purple-800 font-semibold text-xl">
                                    {{ substr($fichePaie->employe->prenom, 0, 1) }}{{ substr($fichePaie->employe->nom, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">{{ $fichePaie->employe->nom }} {{ $fichePaie->employe->prenom }}</h4>
                            <p class="text-gray-600">{{ $fichePaie->employe->poste }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Matricule:</span>
                            <span class="font-medium text-gray-900">{{ $fichePaie->employe->matricule ?? 'Non défini' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type de contrat:</span>
                            <span class="font-medium text-gray-900">{{ $fichePaie->employe->type_contrat ?? 'Non défini' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date d'embauche:</span>
                            <span class="font-medium text-gray-900">{{ $fichePaie->employe->date_embauche ? $fichePaie->employe->date_embauche->format('d/m/Y') : 'Non définie' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Salaire de base:</span>
                            <span class="font-medium text-gray-900">{{ number_format($fichePaie->employe->salaire_base, 2, ',', ' ') }} € / mois</span>
                        </div>
                    </div>
                </div>
                
                <!-- Informations période -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations période</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mois:</span>
                            <span class="font-medium text-gray-900">{{ $moisNom }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Année:</span>
                            <span class="font-medium text-gray-900">{{ $fichePaie->annee }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date de création:</span>
                            <span class="font-medium text-gray-900">{{ $fichePaie->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($fichePaie->date_validation)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date de validation:</span>
                                <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($fichePaie->date_validation)->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if($fichePaie->date_publication)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date de publication:</span>
                                <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($fichePaie->date_publication)->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Récapitulatif -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Récapitulatif</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Salaire brut:</span>
                            <span class="font-semibold text-lg text-gray-900">{{ number_format($fichePaie->salaire_brut, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Total cotisations:</span>
                            <span class="font-medium text-red-600">-{{ number_format($fichePaie->total_cotisations, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-800 font-semibold">Salaire net:</span>
                            <span class="font-bold text-xl text-green-600">{{ number_format($fichePaie->salaire_net, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
            </div>
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
<!-- Détail des cotisations -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Détail des cotisations</h3>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cotisation
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Base
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Taux
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Part salariale
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Part patronale
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Sécurité sociale - Maladie -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Sécurité sociale - Maladie
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->salaire_brut, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        0,75%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->cotisation_maladie, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        13,00%
                    </td>
                </tr>
                
                <!-- Assurance vieillesse -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Assurance vieillesse
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->salaire_brut, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        6,90%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->cotisation_vieillesse, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        8,55%
                    </td>
                </tr>
                
                <!-- Assurance chômage -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Assurance chômage
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->salaire_brut, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        2,40%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->cotisation_chomage, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        4,05%
                    </td>
                </tr>
                
                <!-- Retraite complémentaire -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Retraite complémentaire
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->salaire_brut, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        3,15%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->cotisation_retraite, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        4,72%
                    </td>
                </tr>
                
                <!-- CSG/CRDS -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        CSG/CRDS
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->salaire_brut * 0.9825, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        9,20%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($fichePaie->cotisation_csg_crds, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        0,00%
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                        Total des cotisations:
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                        {{ number_format($fichePaie->total_cotisations, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        -
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

        </div>
    </div>
</x-app-layout>
