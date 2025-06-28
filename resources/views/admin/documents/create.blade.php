<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ajouter un document') }}
            </h2>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    
                    <!-- Informations générales -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Informations générales</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Titre -->
                            <div>
                                <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                                <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                @error('titre')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Catégorie -->
                            <div>
                                <label for="categorie" class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                                <div class="flex">
                                    <select name="categorie" id="categorie" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categories as $categorie)
                                            <option value="{{ $categorie }}" {{ old('categorie') == $categorie ? 'selected' : '' }}>{{ $categorie }}</option>
                                        @endforeach
                                        <option value="autre">Autre...</option>
                                    </select>
                                </div>
                                <div id="autre-categorie" class="mt-2 hidden">
                                    <input type="text" name="nouvelle_categorie" id="nouvelle_categorie" placeholder="Nouvelle catégorie" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                </div>
                                @error('categorie')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Fichier -->
                        <div class="mt-4">
                            <label for="fichier" class="block text-sm font-medium text-gray-700 mb-1">Fichier <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="fichier" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                            <span>Téléverser un fichier</span>
                                            <input id="fichier" name="fichier" type="file" class="sr-only" required>
                                        </label>
                                        <p class="pl-1">ou glisser-déposer</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, DOCX, XLSX, PPTX, JPG, PNG jusqu'à 10MB
                                    </p>
                                </div>
                            </div>
                            <div id="selected-file" class="mt-2 text-sm text-gray-600 hidden">
                                Fichier sélectionné: <span id="file-name" class="font-medium"></span>
                            </div>
                            @error('fichier')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Date d'expiration -->
                        <div class="mt-4">
                            <label for="date_expiration" class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                            <input type="date" name="date_expiration" id="date_expiration" value="{{ old('date_expiration') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                            <p class="mt-1 text-xs text-gray-500">Laissez vide si le document n'expire pas</p>
                            @error('date_expiration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Visibilité et accès -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Visibilité et accès</h3>
                        
                        <!-- Société -->
                        <div>
                            <label for="societe_id" class="block text-sm font-medium text-gray-700 mb-1">Société <span class="text-red-500">*</span></label>
                            <select name="societe_id" id="societe_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                <option value="">Sélectionner une société</option>
                                @foreach($societes as $societe)
                                    <option value="{{ $societe->id }}" {{ old('societe_id') == $societe->id ? 'selected' : '' }}>{{ $societe->nom }}</option>
                                @endforeach
                            </select>
                            @error('societe_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Visibilité -->
                        <div class="mt-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="visible_pour_tous" name="visible_pour_tous" type="checkbox" {{ old('visible_pour_tous') ? 'checked' : '' }} class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="visible_pour_tous" class="font-medium text-gray-700">Visible pour tous les employés</label>
                                    <p class="text-gray-500">Si cette option est désactivée, vous pourrez sélectionner des employés spécifiques.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sélection des employés -->
                        <div id="selection-employes" class="mt-4 {{ old('visible_pour_tous') ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionner les employés <span class="text-red-500">*</span></label>
                            <div class="mt-1 p-4 border border-gray-300 rounded-md max-h-60 overflow-y-auto">
                                <div class="mb-2">
                                    <input type="text" id="search-employes" placeholder="Rechercher un employé..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($employes as $employe)
                                        <div class="employe-item flex items-center p-2 rounded-md hover:bg-gray-50">
                                            <input id="employe-{{ $employe->id }}" name="employes[]" value="{{ $employe->id }}" type="checkbox" {{ in_array($employe->id, old('employes', [])) ? 'checked' : '' }} class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded">
                                            <label for="employe-{{ $employe->id }}" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                                {{ $employe->user->name }} {{ $employe->user->prenom }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('employes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg">
                            Annuler
                        </a>
                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de la catégorie "Autre"
            const categorieSelect = document.getElementById('categorie');
            const autreCategorieDiv = document.getElementById('autre-categorie');
            
            categorieSelect.addEventListener('change', function() {
                if (this.value === 'autre') {
                    autreCategorieDiv.classList.remove('hidden');
                } else {
                    autreCategorieDiv.classList.add('hidden');
                }
            });
            
            // Gestion de la visibilité pour tous
            const visiblePourTous = document.getElementById('visible_pour_tous');
            const selectionEmployes = document.getElementById('selection-employes');
            
            visiblePourTous.addEventListener('change', function() {
                if (this.checked) {
                    selectionEmployes.classList.add('hidden');
                } else {
                    selectionEmployes.classList.remove('hidden');
                }
            });
            
            // Affichage du nom du fichier sélectionné
            const fichierInput = document.getElementById('fichier');
            const selectedFile = document.getElementById('selected-file');
            const fileName = document.getElementById('file-name');
            
            fichierInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                    selectedFile.classList.remove('hidden');
                } else {
                    selectedFile.classList.add('hidden');
                }
            });
            
            // Recherche d'employés
            const searchEmployes = document.getElementById('search-employes');
            const employeItems = document.querySelectorAll('.employe-item');
            
            searchEmployes.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                employeItems.forEach(function(item) {
                    const employeName = item.querySelector('label').textContent.toLowerCase();
                    if (employeName.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
