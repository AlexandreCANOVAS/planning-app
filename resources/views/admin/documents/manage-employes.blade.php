<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des accès au document') }}
            </h2>
            <a href="{{ route('documents.show', $document->id) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour au document
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-purple-100 rounded-lg">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $document->titre }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $document->description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Visibilité du document</h3>
                            <form action="{{ route('documents.update', $document->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="update_visibility" value="1">
                                <input type="hidden" name="visible_pour_tous" value="{{ $document->visible_pour_tous ? '0' : '1' }}">
                                <button type="submit" class="px-4 py-2 {{ $document->visible_pour_tous ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }} rounded-lg text-sm font-medium">
                                    {{ $document->visible_pour_tous ? 'Rendre spécifique' : 'Rendre visible pour tous' }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($document->visible_pour_tous)
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-gray-700">Ce document est visible par tous les employés</span>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span class="text-gray-700">Ce document est visible uniquement par les employés sélectionnés</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!$document->visible_pour_tous)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employés ayant accès</h3>
                            
                            <form action="{{ route('documents.manage-employes', $document->id) }}" method="POST" class="mb-6">
                                @csrf
                                <div class="mb-4">
                                    <label for="employe_search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un employé à ajouter</label>
                                    <div class="flex">
                                        <input type="text" id="employe_search" class="flex-1 rounded-l-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" placeholder="Nom, prénom ou email...">
                                        <button type="button" id="search_button" class="px-4 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                            Rechercher
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="search_results" class="hidden mb-4 max-h-60 overflow-y-auto border border-gray-200 rounded-lg"></div>
                                
                                <div id="selected_employes" class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Employés sélectionnés</label>
                                    <div class="flex flex-wrap gap-2" id="selected_employes_list">
                                        @foreach($document->employes as $employe)
                                            <div class="flex items-center bg-purple-100 text-purple-800 rounded-full px-3 py-1 text-sm" data-employe-id="{{ $employe->id }}">
                                                <span>{{ $employe->prenom }} {{ $employe->nom }}</span>
                                                <button type="button" class="ml-2 text-purple-600 hover:text-purple-800 remove-employe" data-employe-id="{{ $employe->id }}">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                                <input type="hidden" name="employes[]" value="{{ $employe->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                        Mettre à jour les accès
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques de consultation</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consulté le</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lecture confirmée</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($document->employes as $employe)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 rounded-full">
                                                            <span class="text-gray-500 font-medium">{{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}</span>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $employe->prenom }} {{ $employe->nom }}</div>
                                                            <div class="text-sm text-gray-500">{{ $employe->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($employe->pivot->consulte_le)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ \Carbon\Carbon::parse($employe->pivot->consulte_le)->format('d/m/Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Non consulté
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($employe->pivot->confirme_lecture)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                            {{ \Carbon\Carbon::parse($employe->pivot->confirme_le)->format('d/m/Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Non confirmée
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('employe_search');
            const searchButton = document.getElementById('search_button');
            const searchResults = document.getElementById('search_results');
            const selectedEmployesList = document.getElementById('selected_employes_list');
            
            // Fonction pour rechercher des employés
            function searchEmployes() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm.length < 2) {
                    searchResults.innerHTML = '<div class="p-4 text-gray-500">Veuillez saisir au moins 2 caractères</div>';
                    searchResults.classList.remove('hidden');
                    return;
                }
                
                // Simuler une recherche AJAX (à remplacer par un vrai appel AJAX)
                fetch(`/api/employes/search?q=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="p-4 text-gray-500">Aucun employé trouvé</div>';
                        } else {
                            searchResults.innerHTML = '';
                            data.forEach(employe => {
                                // Vérifier si l'employé est déjà sélectionné
                                const isAlreadySelected = document.querySelector(`#selected_employes_list [data-employe-id="${employe.id}"]`);
                                if (!isAlreadySelected) {
                                    const employeElement = document.createElement('div');
                                    employeElement.className = 'p-3 hover:bg-gray-100 cursor-pointer flex items-center justify-between';
                                    employeElement.innerHTML = `
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">${employe.prenom} ${employe.nom}</div>
                                            <div class="text-xs text-gray-500">${employe.email}</div>
                                        </div>
                                        <button type="button" class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Ajouter</button>
                                    `;
                                    employeElement.addEventListener('click', function() {
                                        addEmploye(employe);
                                    });
                                    searchResults.appendChild(employeElement);
                                }
                            });
                        }
                        searchResults.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                        searchResults.innerHTML = '<div class="p-4 text-red-500">Erreur lors de la recherche</div>';
                        searchResults.classList.remove('hidden');
                    });
            }
            
            // Fonction pour ajouter un employé à la liste des sélectionnés
            function addEmploye(employe) {
                const employeElement = document.createElement('div');
                employeElement.className = 'flex items-center bg-purple-100 text-purple-800 rounded-full px-3 py-1 text-sm';
                employeElement.dataset.employeId = employe.id;
                employeElement.innerHTML = `
                    <span>${employe.prenom} ${employe.nom}</span>
                    <button type="button" class="ml-2 text-purple-600 hover:text-purple-800 remove-employe" data-employe-id="${employe.id}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <input type="hidden" name="employes[]" value="${employe.id}">
                `;
                selectedEmployesList.appendChild(employeElement);
                
                // Ajouter l'événement de suppression
                employeElement.querySelector('.remove-employe').addEventListener('click', function() {
                    employeElement.remove();
                });
                
                // Cacher les résultats de recherche et vider le champ
                searchResults.classList.add('hidden');
                searchInput.value = '';
            }
            
            // Événements
            searchButton.addEventListener('click', searchEmployes);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchEmployes();
                }
            });
            
            // Ajouter les événements de suppression aux employés déjà sélectionnés
            document.querySelectorAll('.remove-employe').forEach(button => {
                button.addEventListener('click', function() {
                    const employeId = this.dataset.employeId;
                    document.querySelector(`#selected_employes_list [data-employe-id="${employeId}"]`).remove();
                });
            });
            
            // Cacher les résultats de recherche quand on clique ailleurs
            document.addEventListener('click', function(e) {
                if (!searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchButton) {
                    searchResults.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
