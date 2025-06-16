@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-indigo-300">Échanges de planning</h1>
        <a href="{{ route('employe.echanges.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Nouvelle demande
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 border border-green-500 bg-green-100 bg-opacity-20 text-green-500 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 border border-red-500 bg-red-100 bg-opacity-20 text-red-500 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Demandes envoyées -->
        <div class="border border-gray-700 rounded-lg overflow-hidden backdrop-blur-sm bg-gray-800 bg-opacity-50">
            <div class="bg-gray-800 bg-opacity-70 px-4 py-3 border-b border-gray-700">
                <h2 class="text-lg font-medium text-indigo-300">Demandes envoyées</h2>
            </div>
            <div class="p-4">
                @if($demandesEnvoyees->isEmpty())
                    <p class="text-gray-400 text-center py-4">Aucune demande envoyée</p>
                @else
                    <div class="space-y-4">
                        @foreach($demandesEnvoyees as $demande)
                            <div class="border border-gray-700 rounded-md p-4 bg-gray-800 bg-opacity-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-white">
                                            Échange avec {{ $demande->targetEmploye->prenom }} {{ $demande->targetEmploye->nom }}
                                        </h3>
                                        <div class="mt-2 text-sm text-gray-300">
                                            <p>
                                                <span class="text-indigo-400">Votre date:</span> 
                                                {{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}
                                            </p>
                                            <p>
                                                <span class="text-indigo-400">Date demandée:</span> 
                                                {{ \Carbon\Carbon::parse($demande->target_date)->format('d/m/Y') }}
                                            </p>
                                            <p class="mt-2">
                                                <span class="text-indigo-400">Statut:</span>
                                                @if($demande->status === 'pending')
                                                    <span class="text-yellow-500">En attente</span>
                                                @elseif($demande->status === 'accepted')
                                                    <span class="text-green-500">Accepté</span>
                                                @else
                                                    <span class="text-red-500">Refusé</span>
                                                @endif
                                            </p>
                                            @if($demande->commentaire)
                                                <p class="mt-2">
                                                    <span class="text-indigo-400">Commentaire:</span> 
                                                    {{ $demande->commentaire }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('employe.echanges.show', $demande->id) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($demande->status === 'pending')
                                            <form action="{{ route('employe.echanges.annuler', $demande->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Demandes reçues -->
        <div class="border border-gray-700 rounded-lg overflow-hidden backdrop-blur-sm bg-gray-800 bg-opacity-50">
            <div class="bg-gray-800 bg-opacity-70 px-4 py-3 border-b border-gray-700">
                <h2 class="text-lg font-medium text-indigo-300">Demandes reçues</h2>
            </div>
            <div class="p-4">
                @if($demandesRecues->isEmpty())
                    <p class="text-gray-400 text-center py-4">Aucune demande reçue</p>
                @else
                    <div class="space-y-4">
                        @foreach($demandesRecues as $demande)
                            <div class="border border-gray-700 rounded-md p-4 bg-gray-800 bg-opacity-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-white">
                                            Demande de {{ $demande->employe->prenom }} {{ $demande->employe->nom }}
                                        </h3>
                                        <div class="mt-2 text-sm text-gray-300">
                                            <p>
                                                <span class="text-indigo-400">Sa date:</span> 
                                                {{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}
                                            </p>
                                            <p>
                                                <span class="text-indigo-400">Votre date:</span> 
                                                {{ \Carbon\Carbon::parse($demande->target_date)->format('d/m/Y') }}
                                            </p>
                                            <p class="mt-2">
                                                <span class="text-indigo-400">Statut:</span>
                                                @if($demande->status === 'pending')
                                                    <span class="text-yellow-500">En attente</span>
                                                @elseif($demande->status === 'accepted')
                                                    <span class="text-green-500">Accepté</span>
                                                @else
                                                    <span class="text-red-500">Refusé</span>
                                                @endif
                                            </p>
                                            @if($demande->commentaire)
                                                <p class="mt-2">
                                                    <span class="text-indigo-400">Commentaire:</span> 
                                                    {{ $demande->commentaire }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('employe.echanges.show', $demande->id) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($demande->status === 'pending')
                                            <div class="flex space-x-1">
                                                <form action="{{ route('employe.echanges.repondre', $demande->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="response" value="accept">
                                                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('employe.echanges.repondre', $demande->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="response" value="reject">
                                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
