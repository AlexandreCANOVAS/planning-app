<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier la catégorie') }} : {{ $category->name }}
            </h2>
            <a href="{{ route('admin.document-categories.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.document-categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Type de catégorie -->
                            <div>
                                <label for="is_default" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type de catégorie</label>
                                <select name="is_default" id="is_default" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50" {{ $category->documents->count() > 0 ? 'disabled' : '' }}>
                                    <option value="0" {{ old('is_default', $category->is_default ? '1' : '0') == '0' ? 'selected' : '' }}>Personnalisée (pour ma société uniquement)</option>
                                    @if(Auth::user()->isAdmin())
                                    <option value="1" {{ old('is_default', $category->is_default ? '1' : '0') == '1' ? 'selected' : '' }}>Prédéfinie (pour toutes les sociétés)</option>
                                    @endif
                                </select>
                                @if($category->documents->count() > 0)
                                    <p class="mt-1 text-sm text-amber-600 dark:text-amber-400">Le type ne peut pas être modifié car cette catégorie est utilisée par des documents.</p>
                                    <input type="hidden" name="is_default" value="{{ $category->is_default ? '1' : '0' }}">
                                @endif
                                @error('is_default')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @if($category->documents->count() > 0)
                        <div class="mt-4">
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Documents associés</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cette catégorie est utilisée par {{ $category->documents->count() }} document(s).</p>
                        </div>
                        @endif
                        
                        <!-- Boutons d'action -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.document-categories.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 rounded-lg">
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
    </div>
</x-app-layout>
