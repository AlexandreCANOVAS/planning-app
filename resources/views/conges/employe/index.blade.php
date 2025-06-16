@extends('layouts.app')

@section('title', 'Mes congés')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Mes congés</h1>
        <a href="{{ route('employe.conges.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> Demander un congé
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Mes congés -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Mes demandes de congés</h2>
                </div>
                <div class="card-body">
                    @if ($conges->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="px-4 py-2 text-left">Période</th>
                                        <th class="px-4 py-2 text-left">Motif</th>
                                        <th class="px-4 py-2 text-left">Statut</th>
                                        <th class="px-4 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($conges as $conge)
                                        <tr class="border-b dark:border-gray-600">
                                            <td class="px-4 py-3">
                                                Du {{ $conge->date_debut->format('d/m/Y') }} au {{ $conge->date_fin->format('d/m/Y') }}
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $conge->duree }} jour(s)
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">{{ $conge->motif }}</td>
                                            <td class="px-4 py-3">
                                                @if ($conge->statut === 'en_attente')
                                                    <span class="badge-warning">En attente</span>
                                                @elseif ($conge->statut === 'accepte')
                                                    <span class="badge-success">Accepté</span>
                                                @elseif ($conge->statut === 'refuse')
                                                    <span class="badge-danger">Refusé</span>
                                                    @if ($conge->commentaire)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            Motif: {{ $conge->commentaire }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('employe.conges.show', $conge) }}" class="btn-icon btn-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if ($conge->statut === 'en_attente')
                                                        <a href="{{ route('employe.conges.edit', $conge) }}" class="btn-icon btn-warning" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('employe.conges.destroy', $conge) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-icon btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande de congé ?')" title="Annuler">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400">Vous n'avez pas encore fait de demande de congé.</p>
                            <a href="{{ route('employe.conges.create') }}" class="btn-primary mt-2">
                                Faire une demande
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Congés des collègues -->
        <div class="lg:col-span-1">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Congés des collègues</h2>
                </div>
                <div class="card-body">
                    @if ($congesCollegues->count() > 0)
                        <div class="space-y-3">
                            @foreach ($congesCollegues as $congeCollegue)
                                <div class="p-3 border rounded-lg dark:border-gray-600">
                                    <div class="font-medium">{{ $congeCollegue->employe->prenom }} {{ $congeCollegue->employe->nom }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Du {{ $congeCollegue->date_debut->format('d/m/Y') }} au {{ $congeCollegue->date_fin->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                            Aucun collègue n'a de congé prévu prochainement.
                        </p>
                    @endif
                </div>
            </div>

            <div class="card mt-6">
                <div class="card-header">
                    <h2 class="text-lg font-medium">Calendrier des congés</h2>
                </div>
                <div class="card-body">
                    <a href="{{ route('employe.conges.calendar') }}" class="btn-secondary w-full text-center">
                        <i class="far fa-calendar-alt mr-2"></i> Voir le calendrier
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
