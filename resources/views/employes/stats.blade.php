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
                        @if(empty($workByLocation))
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
                        @if(empty($workByMonth))
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
            // Configuration des couleurs
            const colors = [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ];

            <!-- Debug Info -->
                @if(app()->environment('local'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Debug Information</h3>
                        <pre class="bg-gray-100 p-4 rounded">
                            Plannings Count: {{ $debug['plannings_count'] }}
                            Has Location Data: {{ $debug['has_locations'] ? 'Yes' : 'No' }}
                            Has Monthly Data: {{ $debug['has_months'] ? 'Yes' : 'No' }}
                            
                            Work by Location:
                            @json($workByLocation, JSON_PRETTY_PRINT)
                            
                            Work by Month:
                            @json($workByMonth, JSON_PRETTY_PRINT)
                        </pre>
                    </div>
                </div>
                @endif

            // Graphique par lieu
            @if(!empty($workByLocation))
            const workByLocationData = @json($workByLocation);
            new Chart(document.getElementById('workByLocationChart'), {
                type: 'pie',
                data: {
                    labels: Object.keys(workByLocationData),
                    datasets: [{
                        data: Object.values(workByLocationData),
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            @endif

            // Graphique par mois
            @if(!empty($workByMonth))
            const workByMonthData = @json($workByMonth);
            new Chart(document.getElementById('workByMonthChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(workByMonthData).map(date => {
                        const [year, month] = date.split('-');
                        return new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Heures travaillées',
                        data: Object.values(workByMonthData),
                        backgroundColor: colors[0]
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
                    }
                }
            });
            @endif
        });
    </script>
    @endpush
</x-app-layout>
