<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Statistiques des documents') }}
            </h2>
            <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Résumé global -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-purple-100 rounded-lg">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Total des documents</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $documents->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-green-100 rounded-lg">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Documents consultés</h3>
                            <p class="text-3xl font-bold text-green-600">
                                {{ $documents->sum('vus_count') }}
                                <span class="text-sm font-normal text-gray-500">sur {{ $documents->sum('employes_count') }} accès</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-blue-100 rounded-lg">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Lectures confirmées</h3>
                            <p class="text-3xl font-bold text-blue-600">
                                {{ $documents->sum('confirmes_count') }}
                                <span class="text-sm font-normal text-gray-500">sur {{ $documents->sum('vus_count') }} consultations</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique de consultation -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Taux de consultation par document</h3>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="consultationChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tableau détaillé -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Détails par document</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accès</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultations</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmations</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($documents as $document)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-purple-100 rounded-lg">
                                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $document->titre }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Ajouté le {{ $document->created_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $document->categorie }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($document->visible_pour_tous)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Tous
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                                {{ $document->employes_count }} employés
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $percentage = $document->employes_count > 0 ? ($document->vus_count / $document->employes_count) * 100 : 0;
                                                @endphp
                                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">{{ $document->vus_count }}/{{ $document->employes_count }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $confirmPercentage = $document->vus_count > 0 ? ($document->confirmes_count / $document->vus_count) * 100 : 0;
                                                @endphp
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $confirmPercentage }}%"></div>
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700">{{ $document->confirmes_count }}/{{ $document->vus_count }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.documents.show', $document->id) }}" class="text-purple-600 hover:text-purple-900">
                                            Détails
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @php
    $chartData = $documents->map(function($doc) {
        return [
            'titre' => \Illuminate\Support\Str::limit($doc->titre, 30),
            'employes_count' => $doc->employes_count,
            'vus_count' => $doc->vus_count,
            'confirmes_count' => $doc->confirmes_count
        ];
    });
    @endphp

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('consultationChart').getContext('2d');
            
            // Préparer les données pour le graphique
            const documents = @json($chartData);
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: documents.map(doc => doc.titre),
                    datasets: [
                        {
                            label: 'Accès',
                            data: documents.map(doc => doc.employes_count),
                            backgroundColor: 'rgba(209, 213, 219, 0.8)',
                            borderColor: 'rgba(209, 213, 219, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Consultations',
                            data: documents.map(doc => doc.vus_count),
                            backgroundColor: 'rgba(147, 51, 234, 0.8)',
                            borderColor: 'rgba(147, 51, 234, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Confirmations',
                            data: documents.map(doc => doc.confirmes_count),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45,
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
