@extends('layouts.app')

@section('title', 'Gestion des soldes de congés')

@push('scripts')
<script>
    window.societeId = {{ auth()->user()->societe_id }};
</script>
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/force-reload-cp.js') }}"></script>
{{-- Références aux scripts JavaScript supprimées pour éviter les erreurs 404 --}}
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Gestion des soldes de congés</h1>
        <a href="{{ route('conges.index') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
            <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux congés
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6 flex items-center">
            <svg class="h-5 w-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-100">
        <h2 class="text-xl font-medium text-gray-800 mb-4">Sélectionnez un employé</h2>
        
        <div class="mb-6">
            <div class="relative">
                <input type="text" id="search" class="w-full border border-gray-300 rounded-lg py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:border-purple-500" placeholder="Rechercher un employé...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="employes-list">
            @forelse($employes as $employe)
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 hover:shadow-md transition-all duration-200 employe-card">
                    <div class="flex items-center mb-3">
                        <div class="h-8 w-8 rounded-md bg-purple-600 text-center flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($employe->prenom, 0, 1)) }}{{ strtoupper(substr($employe->nom, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <h3 class="text-gray-800 font-medium">{{ $employe->prenom }} {{ $employe->nom }}</h3>
                            <p class="text-gray-500 text-xs">{{ $employe->poste }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="text-center py-2 bg-blue-50 rounded-lg">
                            <span class="block text-xs text-gray-500 mb-1">CP</span>
                            <span class="block text-lg font-bold text-blue-600" data-employe-id="{{ $employe->id }}" data-solde-type="conges">{{ number_format($employe->solde_conges, 1) }}</span>
                        </div>
                        <div class="text-center py-2 bg-indigo-50 rounded-lg">
                            <span class="block text-xs text-gray-500 mb-1">RTT</span>
                            <span class="block text-lg font-bold text-indigo-600" data-employe-id="{{ $employe->id }}" data-solde-type="rtt">{{ number_format($employe->solde_rtt, 1) }}</span>
                        </div>
                        <div class="text-center py-2 bg-purple-50 rounded-lg">
                            <span class="block text-xs text-gray-500 mb-1">CE</span>
                            <span class="block text-lg font-bold text-purple-600" data-employe-id="{{ $employe->id }}" data-solde-type="exceptionnels">{{ number_format($employe->solde_conges_exceptionnels, 1) }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('solde.edit', $employe) }}" class="flex-1 py-2 text-center text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200 text-sm rounded-md">
                            <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Modifier
                        </a>
                        <a href="{{ route('solde.historique', $employe) }}" class="flex-1 py-2 text-center text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors duration-200 text-sm rounded-md">
                            <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Historique
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-gray-50 border border-gray-200 rounded-lg p-6 text-center text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-lg">Aucun employé trouvé</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const employeCards = document.querySelectorAll('.employe-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        employeCards.forEach(card => {
            const employeName = card.querySelector('h3').textContent.toLowerCase();
            const employePoste = card.querySelector('p').textContent.toLowerCase();
            
            if (employeName.includes(searchTerm) || employePoste.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
