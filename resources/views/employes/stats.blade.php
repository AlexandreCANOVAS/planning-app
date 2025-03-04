<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Statistiques de') }} {{ $employe->prenom }} {{ $employe->nom }}
            </h2>
            <a href="{{ route('employes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Heures par lieu de travail -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition des heures par lieu de travail</h3>
                        @if(empty($chartData['locations']))
                            <p class="text-gray-500">Aucune donnée disponible.</p>
                        @else
                            <div class="aspect-w-16 aspect-h-9">
                                <canvas id="workByLocationChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Heures par mois -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Heures travaillées par mois</h3>
                        @if(empty($chartData['months']))
                            <p class="text-gray-500">Aucune donnée disponible.</p>
                        @else
                            <div class="aspect-w-16 aspect-h-9">
                                <canvas id="workByMonthChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Formations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">État des formations</h3>
                        @if($formations->isEmpty())
                            <p class="text-gray-500">Aucune formation enregistrée.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($formations as $formation)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-medium">{{ $formation['nom'] }}</h4>
                                            @if($formation['status'] === 'valid')
                                                <span class="px-2 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Valide
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-sm rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> Expirée
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <p>Obtenue le: {{ \Carbon\Carbon::parse($formation['date_obtention'])->format('d/m/Y') }}</p>
                                            @if($formation['date_recyclage'])
                                                <p>Recyclage prévu le: {{ \Carbon\Carbon::parse($formation['date_recyclage'])->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colors = [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ];

            // Graphique par lieu
            const locationData = @json($chartData['locations']);
            if (locationData && locationData.labels.length > 0) {
                new Chart(document.getElementById('workByLocationChart'), {
                    type: 'pie',
                    data: {
                        labels: locationData.labels,
                        datasets: [{
                            data: locationData.data,
                            backgroundColor: colors.slice(0, locationData.labels.length)
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
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Graphique par mois
            const monthData = @json($chartData['months']);
            if (monthData && monthData.labels.length > 0) {
                new Chart(document.getElementById('workByMonthChart'), {
                    type: 'bar',
                    data: {
                        labels: monthData.labels,
                        datasets: [{
                            label: 'Heures travaillées',
                            data: monthData.data,
                            backgroundColor: colors[0],
                            borderColor: colors[0],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Heures'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
