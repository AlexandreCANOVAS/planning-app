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
                        <div class="flex justify-end mb-4">
                            <a id="downloadPdfBtn" href="#" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Télécharger PDF
                            </a>
                            <a id="downloadExcelBtn" href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Télécharger Excel
                            </a>
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                        
                        <!-- Nouveau tableau pour les heures spéciales et absences -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Heures spéciales et absences</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                PÉRIODE
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                HEURES DE NUIT<br>(21H-06H)
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                HEURES DIMANCHE
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                HEURES JOURS FÉRIÉS
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                ABSENCES<br>(JOURS)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="heuresSpecialesBody" class="bg-white divide-y divide-gray-200">
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Total du mois</td>
                                            <td id="totalHeuresNuit" class="px-6 py-4 text-right text-sm font-medium heures-nuit"></td>
                                            <td id="totalHeuresDimanche" class="px-6 py-4 text-right text-sm font-medium heures-dimanche"></td>
                                            <td id="totalHeuresJoursFeries" class="px-6 py-4 text-right text-sm font-medium heures-jours-feries"></td>
                                            <td id="totalAbsences" class="px-6 py-4 text-right text-sm font-medium absences"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section des détails des jours spéciaux -->
                <div class="mt-8" id="detailsJoursSpeciaux" style="display: none;">
                    <div class="md:grid md:grid-cols-2 md:gap-6">
                        <!-- Détails des dimanches -->
                        <div class="mt-5 md:mt-0">
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-purple-100">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Détails des dimanches travaillés</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Majoration de 50%</p>
                                </div>
                                <div class="border-t border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailsDimanchesBody" class="bg-white divide-y divide-gray-200">
                                            <!-- Rempli par JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Détails des jours fériés -->
                        <div class="mt-5 md:mt-0">
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-rose-100">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Détails des jours fériés travaillés</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Majoration de 100%</p>
                                </div>
                                <div class="border-t border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour férié</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailsJoursFeriesBody" class="bg-white divide-y divide-gray-200">
                                            <!-- Rempli par JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détails des heures de nuit -->
                    <div class="mt-6">
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 bg-indigo-100">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Détails des heures de nuit</h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">Heures travaillées entre 21h et 6h - Majoration de 25%</p>
                            </div>
                            <div class="border-t border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailsHeuresNuitBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Rempli par JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section du graphique -->
                <div class="mt-8" id="graphiqueSection" style="display: none;">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 bg-gray-100">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Répartition des heures travaillées</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Visualisation graphique des différents types d'heures</p>
                        </div>
                        <div class="border-t border-gray-200 p-4">
                            <div style="height: 400px;">
                                <canvas id="heuresChart"></canvas>
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
        /* Styles pour les heures spéciales et absences */
        .heures-nuit {
            color: #6366f1; /* Indigo-500 de Tailwind */
            font-weight: 600;
        }
        .heures-dimanche {
            color: #8b5cf6; /* Violet-500 de Tailwind */
            font-weight: 600;
        }
        .heures-jours-feries {
            color: #e11d48; /* Rose-600 de Tailwind */
            font-weight: 600;
        }
        .absences {
            color: #0ea5e9; /* Sky-500 de Tailwind */
            font-weight: 600;
        }
    </style>

    <!-- Chart.js sera chargé avec les autres scripts -->

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            fetch('{{ route("comptabilite.calculer-heures") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                    // Ne pas définir Content-Type pour FormData
                },
                body: formData
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
                
                // Remplir le tableau des heures spéciales et absences
                const tbodySpecial = document.getElementById('heuresSpecialesBody');
                tbodySpecial.innerHTML = '';
                
                // Afficher les résultats par semaine pour les heures spéciales
                data.semaines.forEach(semaine => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${semaine.periode}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right ${parseFloat(semaine.heures_nuit || '0') > 0 ? 'heures-nuit' : ''}">${semaine.heures_nuit || '00:00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right ${parseFloat(semaine.heures_dimanche || '0') > 0 ? 'heures-dimanche' : ''}">${semaine.heures_dimanche || '00:00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right ${parseFloat(semaine.heures_jours_feries || '0') > 0 ? 'heures-jours-feries' : ''}">${semaine.heures_jours_feries || '00:00'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right ${parseFloat(semaine.absences || '0') > 0 ? 'absences' : ''}">${semaine.absences || '0'}</td>
                    `;
                    tbodySpecial.appendChild(row);
                });
                
                // Mettre à jour les totaux des heures spéciales
                const totalHeuresNuitElement = document.getElementById('totalHeuresNuit');
                const totalHeuresDimancheElement = document.getElementById('totalHeuresDimanche');
                const totalHeuresJoursFeriesElement = document.getElementById('totalHeuresJoursFeries');
                const totalAbsencesElement = document.getElementById('totalAbsences');
                
                totalHeuresNuitElement.textContent = data.total_mois.heures_nuit ? data.total_mois.heures_nuit + 'h' : '0h';
                totalHeuresDimancheElement.textContent = data.total_mois.heures_dimanche ? data.total_mois.heures_dimanche + 'h' : '0h';
                totalHeuresJoursFeriesElement.textContent = data.total_mois.heures_jours_feries ? data.total_mois.heures_jours_feries + 'h' : '0h';
                totalAbsencesElement.textContent = data.total_mois.absences ? data.total_mois.absences + ' jour(s)' : '0 jour';

                // Afficher le tableau des résultats
                document.getElementById('resultats').classList.remove('hidden');
                
                // Afficher les détails des jours spéciaux
                const detailsJoursSpeciaux = document.getElementById('detailsJoursSpeciaux');
                detailsJoursSpeciaux.style.display = 'block';
                
                // Remplir le tableau des dimanches
                const tbodyDimanches = document.getElementById('detailsDimanchesBody');
                tbodyDimanches.innerHTML = '';
                
                if (data.details.dimanches.length > 0) {
                    data.details.dimanches.forEach(dimanche => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${dimanche.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right heures-dimanche">${dimanche.heures}</td>
                        `;
                        tbodyDimanches.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucun dimanche travaillé ce mois-ci</td>
                    `;
                    tbodyDimanches.appendChild(row);
                }
                
                // Remplir le tableau des jours fériés
                const tbodyJoursFeries = document.getElementById('detailsJoursFeriesBody');
                tbodyJoursFeries.innerHTML = '';
                
                if (data.details.jours_feries.length > 0) {
                    data.details.jours_feries.forEach(jourFerie => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${jourFerie.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${jourFerie.nom}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right heures-jours-feries">${jourFerie.heures}</td>
                        `;
                        tbodyJoursFeries.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucun jour férié travaillé ce mois-ci</td>
                    `;
                    tbodyJoursFeries.appendChild(row);
                }
                
                // Remplir le tableau des heures de nuit
                const tbodyHeuresNuit = document.getElementById('detailsHeuresNuitBody');
                tbodyHeuresNuit.innerHTML = '';
                
                if (data.details.heures_nuit.length > 0) {
                    data.details.heures_nuit.forEach(nuit => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${nuit.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${nuit.jour}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right heures-nuit">${nuit.heures}</td>
                        `;
                        tbodyHeuresNuit.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucune heure de nuit travaillée ce mois-ci</td>
                    `;
                    tbodyHeuresNuit.appendChild(row);
                }
                
                // Afficher et initialiser le graphique
                const graphiqueSection = document.getElementById('graphiqueSection');
                graphiqueSection.style.display = 'block';
                
                // Détruire le graphique existant s'il y en a un
                if (window.heuresChart && typeof window.heuresChart.destroy === 'function') {
                    window.heuresChart.destroy();
                }
                // Réinitialiser la variable dans tous les cas
                window.heuresChart = null;
                
                // Créer le graphique
                const ctx = document.getElementById('heuresChart').getContext('2d');
                window.heuresChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.graphique.labels,
                        datasets: [{
                            data: data.graphique.data,
                            backgroundColor: data.graphique.backgroundColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value.toFixed(2)}h (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                
                // Mettre à jour les liens des boutons de téléchargement
                const downloadPdfBtn = document.getElementById('downloadPdfBtn');
                const downloadExcelBtn = document.getElementById('downloadExcelBtn');
                const employeId = document.getElementById('employe_id').value;
                const mois = document.getElementById('mois').value;
                
                // Configurer les liens de téléchargement
                downloadPdfBtn.href = `{{ route('export.comptabilite') }}?employe_id=${employeId}&mois=${mois}`;
                downloadExcelBtn.href = `{{ route('export.comptabilite.excel') }}?employe_id=${employeId}&mois=${mois}`;
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
