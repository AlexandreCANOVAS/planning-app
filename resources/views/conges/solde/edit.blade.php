@extends('layouts.app')

@section('title', 'Modifier les soldes de congés')

@section('content')
@push('scripts')
<script src="{{ asset('js/toast.js') }}"></script>
@endpush
<div class="container mx-auto px-3 py-4 max-w-5xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
        <div>
            <h1 class="text-xl font-bold text-gray-800 mb-1">
                <i class="fas fa-edit mr-1 text-purple-600"></i>Modification des soldes
            </h1>
            <p class="text-gray-600 text-sm">
                <i class="fas fa-info-circle mr-1"></i>{{ $employe->prenom }} {{ $employe->nom }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('employes.show', $employe->id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-purple-500">
                <i class="fas fa-user mr-1"></i>
                Profil
            </a>
            <a href="{{ route('conges.index') }}" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-purple-500">
                <i class="fas fa-calendar-alt mr-1"></i>
                Congés
            </a>
            <a href="{{ route('solde.historique', $employe) }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                <i class="fas fa-history mr-1"></i>
                Historique
            </a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Informations employé -->
            <div class="md:col-span-1">
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-user-circle text-purple-600 text-lg mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informations</h3>
                    </div>
                    <div class="space-y-2 text-gray-600 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Nom:</span> {{ $employe->nom }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Prénom:</span> {{ $employe->prenom }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-briefcase text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Poste:</span> {{ $employe->poste }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Embauche:</span> {{ $employe->date_embauche ? $employe->date_embauche->format('d/m/Y') : 'Non définie' }}
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-file-contract text-gray-400 w-5"></i>
                            <span class="font-medium mr-1">Contrat:</span> {{ $employe->type_contrat }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Soldes actuels -->
            <div class="md:col-span-3">
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-calculator text-purple-600 text-lg mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Soldes actuels</h3>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg text-center">
                            <div class="flex justify-center mb-1">
                                <i class="fas fa-umbrella-beach text-blue-500 text-xl"></i>
                            </div>
                            <span class="block text-xs font-medium text-blue-600">Congés payés</span>
                            <span id="current-solde-conges" class="block text-2xl font-bold text-blue-800">{{ number_format($employe->solde_conges, 1) }}</span>
                            <span class="text-blue-600 text-xs">jours</span>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg text-center">
                            <div class="flex justify-center mb-1">
                                <i class="fas fa-clock text-green-500 text-xl"></i>
                            </div>
                            <span class="block text-xs font-medium text-green-600">RTT</span>
                            <span id="current-solde-rtt" class="block text-2xl font-bold text-green-800">{{ number_format($employe->solde_rtt, 1) }}</span>
                            <span class="text-green-600 text-xs">jours</span>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-lg text-center">
                            <div class="flex justify-center mb-1">
                                <i class="fas fa-star text-purple-500 text-xl"></i>
                            </div>
                            <span class="block text-xs font-medium text-purple-600">Congés exceptionnels</span>
                            <span id="current-solde-exceptionnels" class="block text-2xl font-bold text-purple-800">{{ number_format($employe->solde_conges_exceptionnels, 1) }}</span>
                            <span class="text-purple-600 text-xs">jours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="solde-form" action="{{ route('solde.update', $employe) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-edit text-purple-600 text-lg mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Modifier les soldes</h3>
                </div>
                
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <!-- Congés payés -->
                    <div class="form-group">
                        <div class="relative">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-umbrella-beach text-blue-500 mr-1 text-sm"></i>
                                <label for="solde_conges" class="block text-xs font-medium text-gray-700">Congés payés</label>
                            </div>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="solde_conges" id="solde_conges" 
                                    class="block w-full pr-10 pl-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                    value="{{ old('solde_conges', $employe->solde_conges) }}" 
                                    step="0.5" min="0" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <span class="text-gray-500 text-xs">jours</span>
                                </div>
                            </div>
                            @error('solde_conges')
                                <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- RTT -->
                    <div class="form-group">
                        <div class="relative">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-clock text-green-500 mr-1 text-sm"></i>
                                <label for="solde_rtt" class="block text-xs font-medium text-gray-700">RTT</label>
                            </div>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="solde_rtt" id="solde_rtt" 
                                    class="block w-full pr-10 pl-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                    value="{{ old('solde_rtt', $employe->solde_rtt) }}" 
                                    step="0.5" min="0" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <span class="text-gray-500 text-xs">jours</span>
                                </div>
                            </div>
                            @error('solde_rtt')
                                <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Congés exceptionnels -->
                    <div class="form-group">
                        <div class="relative">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-star text-purple-500 mr-1 text-sm"></i>
                                <label for="solde_conges_exceptionnels" class="block text-xs font-medium text-gray-700">Congés exceptionnels</label>
                            </div>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="solde_conges_exceptionnels" id="solde_conges_exceptionnels" 
                                    class="block w-full pr-10 pl-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                    value="{{ old('solde_conges_exceptionnels', $employe->solde_conges_exceptionnels) }}" 
                                    step="0.5" min="0" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <span class="text-gray-500 text-xs">jours</span>
                                </div>
                            </div>
                            @error('solde_conges_exceptionnels')
                                <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Commentaire -->
                <div class="form-group mb-3">
                    <div class="flex items-center mb-1">
                        <i class="fas fa-comment-alt text-gray-500 mr-1 text-sm"></i>
                        <label for="commentaire" class="block text-xs font-medium text-gray-700">Commentaire (raison de la modification)</label>
                    </div>
                    <div class="mt-1">
                        <textarea name="commentaire" id="commentaire" 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                            rows="2">{{ old('commentaire') }}</textarea>
                    </div>
                    @error('commentaire')
                        <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
