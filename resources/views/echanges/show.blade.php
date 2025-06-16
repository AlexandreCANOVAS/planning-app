@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center">
        <a href="{{ route('employe.echanges.index') }}" class="text-indigo-400 hover:text-indigo-300 mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-semibold text-indigo-300">Détails de la demande d'échange</h1>
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

    <div class="border border-gray-700 rounded-lg overflow-hidden backdrop-blur-sm bg-gray-800 bg-opacity-50 max-w-3xl">
        <div class="bg-gray-800 bg-opacity-70 px-4 py-3 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-medium text-indigo-300">Demande #{{ $echange->id }}</h2>
            <span class="px-3 py-1 rounded-full text-sm 
                @if($echange->status === 'pending') bg-yellow-500 bg-opacity-20 text-yellow-500 border border-yellow-500
                @elseif($echange->status === 'accepted') bg-green-500 bg-opacity-20 text-green-500 border border-green-500
                @else bg-red-500 bg-opacity-20 text-red-500 border border-red-500
                @endif">
                @if($echange->status === 'pending')
                    En attente
                @elseif($echange->status === 'accepted')
                    Accepté
                @else
                    Refusé
                @endif
            </span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="border border-gray-700 rounded-md p-4 bg-gray-800 bg-opacity-50">
                    <h3 class="text-indigo-400 font-medium mb-3">Demandeur</h3>
                    <p class="text-white mb-2">{{ $echange->employe->prenom }} {{ $echange->employe->nom }}</p>
                    <p class="text-gray-400 text-sm">{{ $echange->employe->email }}</p>
                    <div class="mt-4">
                        <h4 class="text-indigo-400 text-sm mb-1">Date proposée</h4>
                        <p class="text-white">{{ \Carbon\Carbon::parse($echange->date)->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <div class="border border-gray-700 rounded-md p-4 bg-gray-800 bg-opacity-50">
                    <h3 class="text-indigo-400 font-medium mb-3">Destinataire</h3>
                    <p class="text-white mb-2">{{ $echange->targetEmploye->prenom }} {{ $echange->targetEmploye->nom }}</p>
                    <p class="text-gray-400 text-sm">{{ $echange->targetEmploye->email }}</p>
                    <div class="mt-4">
                        <h4 class="text-indigo-400 text-sm mb-1">Date demandée</h4>
                        <p class="text-white">{{ \Carbon\Carbon::parse($echange->target_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            
            @if($echange->commentaire)
                <div class="border border-gray-700 rounded-md p-4 bg-gray-800 bg-opacity-50 mb-6">
                    <h3 class="text-indigo-400 font-medium mb-2">Commentaire</h3>
                    <p class="text-white">{{ $echange->commentaire }}</p>
                </div>
            @endif
            
            <div class="border border-gray-700 rounded-md p-4 bg-gray-800 bg-opacity-50 mb-6">
                <h3 class="text-indigo-400 font-medium mb-2">Informations complémentaires</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Date de la demande</p>
                        <p class="text-white">{{ $echange->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($echange->status !== 'pending')
                        <div>
                            <p class="text-gray-400 text-sm">Date de réponse</p>
                            <p class="text-white">{{ $echange->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($echange->status === 'pending')
                <div class="flex justify-end space-x-3">
                    @if(Auth::user()->employe->id === $echange->employe_id)
                        <form action="{{ route('employe.echanges.annuler', $echange->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition">
                                Annuler la demande
                            </button>
                        </form>
                    @elseif(Auth::user()->employe->id === $echange->target_employe_id)
                        <div class="flex space-x-3">
                            <form action="{{ route('employe.echanges.repondre', $echange->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="response" value="reject">
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition">
                                    Refuser
                                </button>
                            </form>
                            <form action="{{ route('employe.echanges.repondre', $echange->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="response" value="accept">
                                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                                    Accepter
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
