<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comptabilité') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="searchForm" class="mb-6">
                        @csrf
                        <div class="mb-6">
                            <label for="employe_id" class="block text-sm font-medium text-gray-700">Sélectionner un employé</label>
                            <select id="employe_id" name="employe_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Choisir un employé</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->id }}">{{ $employe->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                            <input type="month" id="mois" name="mois" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ $moisActuel }}" required>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Calculer
                            </button>
                        </div>
                    </form>

                    <div id="resultats" class="hidden">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Heures supplémentaires par semaine</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                PÉRIODE
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                TOTAL<br>HEURES
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                HEURES SUP.<br>25%<br>(36H À 43H)
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                HEURES SUP.<br>50%<br>(>44H)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultatsBody" class="bg-white divide-y divide-gray-200">
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Total du mois</td>
                                            <td id="totalHeures" class="px-6 py-4 text-right text-sm font-medium"></td>
                                            <td id="totalHS25" class="px-6 py-4 text-right text-sm font-medium"></td>
                                            <td id="totalHS50" class="px-6 py-4 text-right text-sm font-medium"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .heures-sup-25 {
            color: #f97316; /* Orange-500 de Tailwind */
            font-weight: 600;
        }
        .heures-sup-50 {
            color: #dc2626; /* Red-600 de Tailwind */
            font-weight: 600;
        }
        .total-sup-25 {
            color: #f97316;
            font-weight: 700;
        }
        .total-sup-50 {
            color: #dc2626;
            font-weight: 700;
        }
    </style>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                employe_id: document.getElementById('employe_id').value,
                mois: document.getElementById('mois').value,
                _token: document.querySelector('input[name="_token"]').value
            };

            fetch('{{ route("comptabilite.calculer-heures") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const tbody = document.getElementById('resultatsBody');
                tbody.innerHTML = '';

                // Afficher les résultats par semaine
                data.semaines.forEach(semaine => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${semaine.periode}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">${semaine.total_heures}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right ${parseFloat(semaine.heures_sup_25.replace(':', '.')) > 0 ? 'text-orange-600' : ''}">${semaine.heures_sup_25}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right ${parseFloat(semaine.heures_sup_50.replace(':', '.')) > 0 ? 'text-red-600' : ''}">${semaine.heures_sup_50}</td>
                    `;
                    tbody.appendChild(row);
                });

                // Mettre à jour les totaux avec les classes CSS appropriées
                const totalHeuresElement = document.getElementById('totalHeures');
                const totalHS25Element = document.getElementById('totalHS25');
                const totalHS50Element = document.getElementById('totalHS50');

                totalHeuresElement.textContent = data.total_mois.heures + 'h';
                totalHS25Element.textContent = data.total_mois.heures_sup_25 + 'h';
                totalHS50Element.textContent = data.total_mois.heures_sup_50 + 'h';

                // Ajouter les classes pour la coloration des heures supplémentaires
                totalHS25Element.className = parseFloat(data.total_mois.heures_sup_25) > 0 ? 'px-6 py-4 text-right text-sm font-medium total-sup-25' : 'px-6 py-4 text-right text-sm font-medium';
                totalHS50Element.className = parseFloat(data.total_mois.heures_sup_50) > 0 ? 'px-6 py-4 text-right text-sm font-medium total-sup-50' : 'px-6 py-4 text-right text-sm font-medium';

                // Afficher le tableau des résultats
                document.getElementById('resultats').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors du calcul des heures');
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
