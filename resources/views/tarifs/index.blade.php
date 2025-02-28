@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des tarifs</h1>
        <a href="{{ route('tarifs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau tarif
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="min-w-full divide-y divide-gray-200">
            <div class="bg-gray-50">
                <div class="grid grid-cols-4 gap-4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div>Nom</div>
                    <div>Taux horaire</div>
                    <div>Description</div>
                    <div class="text-right">Actions</div>
                </div>
            </div>
            <div class="bg-white divide-y divide-gray-200">
                @forelse($tarifs as $tarif)
                    <div class="grid grid-cols-4 gap-4 px-6 py-4 text-sm">
                        <div class="text-gray-900 font-medium">{{ $tarif->nom }}</div>
                        <div class="text-gray-900">{{ number_format($tarif->taux_horaire, 2) }} €/h</div>
                        <div class="text-gray-500">{{ $tarif->description ?? '-' }}</div>
                        <div class="text-right space-x-2">
                            <a href="{{ route('tarifs.edit', $tarif) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('tarifs.destroy', $tarif) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce tarif ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500">
                        Aucun tarif n'a été créé. Cliquez sur "Nouveau tarif" pour commencer.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
