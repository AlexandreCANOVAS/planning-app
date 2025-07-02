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
