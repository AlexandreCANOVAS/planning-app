<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des employés') }}
            </h2>
            <a href="{{ route('employes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                <i class="fas fa-user-plus mr-2"></i>
                {{ __('Ajouter un employé') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                    <p class="font-bold">Succès!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Grille des employés -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($employes as $employe)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-2xl font-bold text-blue-600">
                                            {{ strtoupper(substr($employe['nom'], 0, 1)) }}{{ strtoupper(substr($employe['prenom'], 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        {{ $employe['nom'] }} {{ $employe['prenom'] }}
                                    </h3>
                                    <div class="flex flex-col space-y-1">
                                        <p class="text-sm text-gray-500 flex items-center">
                                            <i class="fas fa-envelope mr-2"></i>
                                            {{ $employe['email'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 flex items-center">
                                            <i class="fas fa-phone mr-2"></i>
                                            {{ $employe['telephone'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 flex items-center">
                                            <i class="fas fa-graduation-cap mr-2"></i>
                                            {{ $employe['formations_count'] }} formations
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <a href="{{ route('employes.stats', $employe['id']) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors duration-150">
                                    <i class="fas fa-chart-bar mr-1.5"></i>
                                    Stats
                                </a>
                                <a href="{{ route('employes.formations', ['employe' => $employe['id']]) }}" class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors duration-150">
                                    <i class="fas fa-graduation-cap mr-1.5"></i>
                                    Formations
                                </a>
                                <a href="{{ route('employes.edit', $employe['id']) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-200 transition-colors duration-150">
                                    <i class="fas fa-edit mr-1.5"></i>
                                    Éditer
                                </a>
                                <form action="{{ route('employes.destroy', $employe['id']) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors duration-150" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?')">
                                        <i class="fas fa-trash-alt mr-1.5"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3">
                        <div class="bg-gray-50 rounded-xl p-8 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg">Aucun employé n'a été ajouté pour le moment.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>