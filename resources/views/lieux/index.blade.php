<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lieux de travail') }}
            </h2>
            <a href="{{ route('lieux.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Ajouter un lieu
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Lieux de travail</h2>
                        <div class="flex space-x-2">
                            <div class="dropdown relative">
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded inline-flex items-center">
                                    <i class="fas fa-file-export mr-1"></i> Exporter
                                    <i class="fas fa-chevron-down ml-2"></i>
                                </button>
                                <div class="dropdown-menu absolute hidden right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10">
                                    <a href="{{ route('lieux.export.pdf') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-file-pdf mr-2 text-red-500"></i> Export PDF
                                    </a>
                                    <a href="{{ route('lieux.export.excel') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-file-excel mr-2 text-green-500"></i> Export Excel
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('lieux.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded inline-flex items-center">
                                <i class="fas fa-plus mr-1"></i> Ajouter un lieu
                            </a>
                        </div>
                    </div>
                    
                    <script>
                        // Script pour le menu déroulant d'export
                        document.addEventListener('DOMContentLoaded', function() {
                            const dropdownButton = document.querySelector('.dropdown button');
                            const dropdownMenu = document.querySelector('.dropdown-menu');
                            
                            dropdownButton.addEventListener('click', function() {
                                dropdownMenu.classList.toggle('hidden');
                            });
                            
                            // Fermer le menu si on clique ailleurs
                            document.addEventListener('click', function(event) {
                                if (!event.target.closest('.dropdown')) {
                                    dropdownMenu.classList.add('hidden');
                                }
                            });
                        });
                    </script>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    


                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($lieux as $lieu)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex justify-center">
                                                <div class="h-8 w-8 rounded-full border-2 border-gray-200" style="background-color: {{ $lieu->couleur }}"></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="pl-2 border-l-4 font-medium" style="border-color: {{ $lieu->couleur }}">
                                                    {{ $lieu->nom }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $lieu->adresse }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('lieux.edit', ['lieu' => $lieu->id]) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
                                                    <i class="fas fa-edit mr-1"></i> Modifier
                                                </a>
                                                
                                                <a href="{{ route('lieux.plannings', ['lieu' => $lieu->id]) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                                    <i class="fas fa-calendar-alt mr-1"></i> Plannings
                                                </a>
                                                
                                                <a href="{{ route('lieux.duplicate', ['lieu' => $lieu->id]) }}" class="text-green-600 hover:text-green-900 inline-flex items-center">
                                                    <i class="fas fa-copy mr-1"></i> Dupliquer
                                                </a>
                                                
                                                @if(!in_array($lieu->nom, ['RH', 'CP']))
                                                    <form action="{{ route('lieux.destroy', ['lieu' => $lieu->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce lieu ?')">
                                                            <i class="fas fa-trash-alt mr-1"></i> Supprimer
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $lieux->links() }}
                    </div>
                    
                    <!-- Section des statistiques d'utilisation -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques d'utilisation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($lieux as $lieu)
                                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                                    <div class="border-l-4 px-4 py-3 flex justify-between items-center" style="border-color: {{ $lieu->couleur }}">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full mr-3" style="background-color: {{ $lieu->couleur }}"></div>
                                            <div>
                                                <h4 class="text-lg font-medium">{{ $lieu->nom }}</h4>
                                                <p class="text-sm text-gray-600">{{ $lieu->adresse }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('lieux.edit', ['lieu' => $lieu->id]) }}" class="text-gray-400 hover:text-gray-500">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                    <!-- Informations de contact -->
                                    <div class="px-4 py-2 border-t border-gray-100">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="flex items-center">
                                                <div class="text-blue-500 mr-2"><i class="fas fa-phone"></i></div>
                                                <div>
                                                    <div class="text-xs text-gray-500">Téléphone</div>
                                                    <div class="text-sm">{{ $lieu->telephone ?: 'Non renseigné' }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="text-blue-500 mr-2"><i class="fas fa-clock"></i></div>
                                                <div>
                                                    <div class="text-xs text-gray-500">Horaires</div>
                                                    <div class="text-sm">{{ $lieu->horaires ?: 'Non renseignés' }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="text-blue-500 mr-2"><i class="fas fa-user-tie"></i></div>
                                                <div>
                                                    <div class="text-xs text-gray-500">Contact principal</div>
                                                    <div class="text-sm">{{ $lieu->contact_principal ?: 'Non renseigné' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Statistiques d'utilisation -->
                                    <div class="px-4 py-3 bg-gray-50 flex justify-between border-t border-gray-100">
                                        <div class="text-center px-4 py-2">
                                            <div class="text-xs text-gray-500 uppercase mb-1 flex items-center justify-center">
                                                <i class="fas fa-users mr-1"></i> Employés aujourd'hui
                                            </div>
                                            <div class="text-2xl font-bold {{ $lieu->employes_aujourdhui > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                                {{ $lieu->employes_aujourdhui }}
                                            </div>
                                        </div>
                                        <div class="text-center px-4 py-2 border-l border-gray-200">
                                            <div class="text-xs text-gray-500 uppercase mb-1 flex items-center justify-center">
                                                <i class="fas fa-chart-line mr-1"></i> Heures ce mois
                                            </div>
                                            <div class="text-2xl font-bold {{ $lieu->heures_mois > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ number_format($lieu->heures_mois, 1) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Carte des lieux de travail (déplacée en bas) -->
                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Carte des lieux de travail</h3>
                        @include('lieux.partials.map')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(session('lieu_id'))
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmation de suppression</h3>
            <p class="text-gray-700 mb-6">Ce lieu est utilisé dans des plannings. Êtes-vous sûr de vouloir le supprimer quand même ?</p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('confirmationModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                <form action="{{ route('lieux.forceDestroy', ['lieu' => session('lieu_id')]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer quand même</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>