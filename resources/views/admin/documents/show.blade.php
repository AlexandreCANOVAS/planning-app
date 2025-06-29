<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du document') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.documents.edit', $document->id) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Informations du document -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-16 w-16 flex items-center justify-center bg-purple-100 rounded-lg">
                            <svg class="h-10 w-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-medium text-gray-900">{{ $document->titre }}</h3>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $document->categorie }}
                                </span>
                                @if($document->visible_pour_tous)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Visible pour tous
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Accès restreint
                                    </span>
                                @endif
                                @if($document->date_expiration)
                                    @if($document->isExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Expiré le {{ \Carbon\Carbon::parse($document->date_expiration)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Expire le {{ \Carbon\Carbon::parse($document->date_expiration)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Détails du document -->
                <div class="p-6 border-b border-gray-200">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Détails</h4>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->description ?: 'Aucune description' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type de fichier</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($document->type_fichier) }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Société</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->societe->nom }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ajouté par</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->uploadedBy->name }} {{ $document->uploadedBy->prenom }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'ajout</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>
                
                <!-- Aperçu et téléchargement -->
                <div class="p-6 border-b border-gray-200">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Fichier</h4>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">{{ basename($document->fichier_path) }}</p>
                        </div>
                        <div class="mt-3 sm:mt-0 flex space-x-2">
                            <a href="{{ $document->download_url }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg" target="_blank">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Visualiser
                            </a>
                            <a href="{{ $document->download_url }}?download=1" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Télécharger
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Gestion des accès -->
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Accès des employés</h4>
                    
                    @if($document->visible_pour_tous)
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        Ce document est visible par tous les employés de la société {{ $document->societe->nom }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($document->employes->count() > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Ce document est accessible à {{ $document->employes->count() }} employé(s) spécifique(s).</p>
                            </div>
                            
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
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $employe->user->name }} {{ $employe->user->prenom }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($employe->pivot->consulte_le)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ \Carbon\Carbon::parse($employe->pivot->consulte_le)->setTimezone('Europe/Paris')->format('d/m/Y à H:i') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Non consulté
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($employe->pivot->confirme_lecture)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Oui
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Non
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-6">
                                <button type="button" id="manage-employes-btn" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                                    Gérer les accès
                                </button>
                            </div>
                        @else
                            <div class="bg-amber-50 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-amber-800">
                                            Aucun employé n'a accès à ce document. Ajoutez des employés pour permettre l'accès.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="button" id="manage-employes-btn" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                                    Ajouter des employés
                                </button>
                            </div>
                        @endif
                        
                        <!-- Modal pour gérer les accès -->
                        <div id="manage-employes-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
                                <div class="p-6 border-b border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-medium text-gray-900">Gérer les accès des employés</h3>
                                        <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.documents.manage-employes', $document->id) }}" method="POST">
                                    @csrf
                                    
                                    <div class="p-6">
                                        <div class="mb-4">
                                            <input type="text" id="search-employes-modal" placeholder="Rechercher un employé..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                                        </div>
                                        
                                        <div class="max-h-60 overflow-y-auto">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                @foreach(\App\Models\Employe::with('user')->where('societe_id', $document->societe_id)->get() as $employe)
                                                    <div class="employe-item-modal flex items-center p-2 rounded-md hover:bg-gray-50">
                                                        <input id="modal-employe-{{ $employe->id }}" name="employes[]" value="{{ $employe->id }}" type="checkbox" {{ $document->employes->contains($employe->id) ? 'checked' : '' }} class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded">
                                                        <label for="modal-employe-{{ $employe->id }}" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                                            {{ $employe->user->name }} {{ $employe->user->prenom }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="px-6 py-4 bg-gray-50 text-right rounded-b-lg">
                                        <button type="button" id="cancel-modal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg mr-2">
                                            Annuler
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                                            Enregistrer
                                        </button>
                                    </div>
                                </form>
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
            // Gestion du modal pour les accès employés
            const manageEmployesBtn = document.getElementById('manage-employes-btn');
            const manageEmployesModal = document.getElementById('manage-employes-modal');
            const closeModal = document.getElementById('close-modal');
            const cancelModal = document.getElementById('cancel-modal');
            
            if (manageEmployesBtn && manageEmployesModal) {
                manageEmployesBtn.addEventListener('click', function() {
                    manageEmployesModal.classList.remove('hidden');
                });
                
                if (closeModal) {
                    closeModal.addEventListener('click', function() {
                        manageEmployesModal.classList.add('hidden');
                    });
                }
                
                if (cancelModal) {
                    cancelModal.addEventListener('click', function() {
                        manageEmployesModal.classList.add('hidden');
                    });
                }
                
                // Recherche d'employés dans le modal
                const searchEmployesModal = document.getElementById('search-employes-modal');
                const employeItemsModal = document.querySelectorAll('.employe-item-modal');
                
                if (searchEmployesModal) {
                    searchEmployesModal.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        
                        employeItemsModal.forEach(function(item) {
                            const employeName = item.querySelector('label').textContent.toLowerCase();
                            if (employeName.includes(searchTerm)) {
                                item.style.display = 'flex';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
