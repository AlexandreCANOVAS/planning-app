<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consultation du document') }}
            </h2>
            <a href="{{ route('employe.documents.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            
            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif
            <!-- Informations du document -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-blue-100 rounded-lg">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $document->titre }}</h3>
                            <p class="text-sm text-gray-500">
                                Ajouté le {{ $document->created_at->format('d/m/Y') }} 
                                @if($document->date_expiration)
                                    · Expire le {{ \Carbon\Carbon::parse($document->date_expiration)->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-1">Catégorie</h4>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $document->categorie }}
                        </span>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-1">Description</h4>
                        <p class="text-gray-900">{{ $document->description }}</p>
                    </div>
                    
                    @if($document->societe)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Société</h4>
                            <p class="text-gray-900">{{ $document->societe->nom }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Aperçu du document -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Aperçu du document</h3>
                </div>
                
                <div class="p-6">
                    @php
                        $extension = pathinfo($document->fichier_path, PATHINFO_EXTENSION);
                    @endphp
                    
                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                        <div class="flex justify-center">
                            <img src="{{ route('employe.documents.preview', $document->id) }}" alt="{{ $document->titre }}" class="max-w-full h-auto rounded-lg dark:border dark:border-gray-700">
                        </div>
                    @elseif(strtolower($extension) === 'pdf')
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ route('employe.documents.preview', $document->id) }}" class="w-full h-96 border-0 rounded-lg dark:border dark:border-gray-700"></iframe>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-600">L'aperçu n'est pas disponible pour ce type de fichier.</p>
                            <p class="text-sm text-gray-500 mt-1">Veuillez télécharger le document pour le consulter.</p>
                        </div>
                    @endif
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-500">{{ strtoupper($extension) }} · {{ number_format($document->taille / 1024, 2) }} KB</span>
                    </div>
                    <div>
                        <a href="{{ route('employe.documents.download', $document->id) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:bg-purple-700 dark:hover:bg-purple-600">
                            <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Confirmation de lecture -->
            @if(!$document->pivot || !$document->pivot->confirme_lecture)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Confirmation de lecture</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Confirmation requise</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Veuillez confirmer que vous avez lu et compris ce document.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('employe.documents.confirm', $document->id) }}" method="POST">
                            @csrf
                            <div class="flex items-start mb-4">
                                <div class="flex items-center h-5">
                                    <input id="confirmation" name="confirmation" type="checkbox" required class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="confirmation" class="font-medium text-gray-700">Je confirme avoir lu et compris ce document</label>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:bg-purple-700 dark:hover:bg-purple-600">
                                    Confirmer la lecture
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Lecture confirmée</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Vous avez confirmé avoir lu et compris ce document le {{ \Carbon\Carbon::parse($document->pivot->lecture_confirmee)->setTimezone('Europe/Paris')->format('d/m/Y à H:i') }}.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
