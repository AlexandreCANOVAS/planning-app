@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-indigo-300">Nouvelle demande d'échange</h1>
        <p class="text-gray-400 mt-1">Proposez un échange de jour de planning avec un collègue</p>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 border border-red-500 bg-red-100 bg-opacity-20 text-red-500 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="border border-gray-700 rounded-lg overflow-hidden backdrop-blur-sm bg-gray-800 bg-opacity-50 max-w-3xl">
        <div class="bg-gray-800 bg-opacity-70 px-4 py-3 border-b border-gray-700">
            <h2 class="text-lg font-medium text-indigo-300">Formulaire de demande</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('employe.echanges.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="target_employe_id" class="block text-sm font-medium text-gray-300 mb-2">Collègue</label>
                    <select id="target_employe_id" name="target_employe_id" class="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sélectionnez un collègue</option>
                        @foreach($collegues as $collegue)
                            <option value="{{ $collegue->id }}" {{ old('target_employe_id') == $collegue->id ? 'selected' : '' }}>
                                {{ $collegue->prenom }} {{ $collegue->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('target_employe_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-300 mb-2">Votre date à échanger</label>
                        <select id="date" name="date" class="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sélectionnez une date</option>
                            @foreach($plannings as $date => $planning)
                                <option value="{{ $date }}" {{ old('date') == $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} - 
                                    @if(count($planning) > 0 && $planning[0]->lieu)
                                        {{ $planning[0]->lieu->nom }}
                                    @else
                                        Lieu non défini
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="target_date" class="block text-sm font-medium text-gray-300 mb-2">Date souhaitée</label>
                        <input type="date" id="target_date" name="target_date" value="{{ old('target_date') }}" 
                            class="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('target_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="commentaire" class="block text-sm font-medium text-gray-300 mb-2">Commentaire (optionnel)</label>
                    <textarea id="commentaire" name="commentaire" rows="3" 
                        class="w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('employe.echanges.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition">
                        Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des sélecteurs avec des options améliorées
        const targetEmployeSelect = document.getElementById('target_employe_id');
        const dateSelect = document.getElementById('date');
        
        // Vous pouvez ajouter ici du code pour améliorer l'expérience utilisateur
        // Par exemple, utiliser Select2 pour des sélecteurs plus ergonomiques
    });
</script>
@endsection
