<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-6">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                <div class="w-32 h-32 rounded-full bg-white opacity-10"></div>
            </div>
            
            <div class="relative flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        {{ __('Fiches de paie') }}
                    </h2>
                    <p class="text-purple-100 text-sm">
                        Gestion des fiches de paie des employés
                    </p>
                </div>
                <div>
                    <a href="{{ route('fiches-paie.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm text-white transition-all duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Créer une fiche de paie
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtres</h3>
                <form action="{{ route('fiches-paie.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="employe_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                        <select name="employe_id" id="employe_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            <option value="">Tous les employés</option>
                            @foreach($employes as $employe)
                                <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>
                                    {{ $employe->nom }} {{ $employe->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="mois" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                        <select name="mois" id="mois" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            <option value="">Tous les mois</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('fr_FR')->monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="annee" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                        <select name="annee" id="annee" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            <option value="">Toutes les années</option>
                            @foreach(range(date('Y')-2, date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ request('annee') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            <option value="">Tous les statuts</option>
                            <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="validé" {{ request('statut') == 'validé' ? 'selected' : '' }}>Validé</option>
                            <option value="publié" {{ request('statut') == 'publié' ? 'selected' : '' }}>Publié</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('fiches-paie.index') }}" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Liste des fiches de paie -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Liste des fiches de paie</h3>
                </div>
                
                @if($fichesPaie->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Employé
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Période
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Salaire brut
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Salaire net
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date de création
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($fichesPaie as $fichePaie)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($fichePaie->employe->photo)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $fichePaie->employe->photo) }}" alt="{{ $fichePaie->employe->nom }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                            <span class="text-purple-800 font-semibold text-sm">
                                                                {{ substr($fichePaie->employe->prenom, 0, 1) }}{{ substr($fichePaie->employe->nom, 0, 1) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $fichePaie->employe->nom }} {{ $fichePaie->employe->prenom }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $fichePaie->employe->poste }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @php
                                                    $dateParts = explode('-', $fichePaie->mois);
                                                    $annee = $dateParts[0];
                                                    $mois = intval($dateParts[1]);
                                                    $moisNom = \Carbon\Carbon::create()->month($mois)->locale('fr_FR')->monthName;
                                                @endphp
                                                {{ $moisNom }} {{ $annee }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($fichePaie->salaire_brut, 2, ',', ' ') }} €
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($fichePaie->salaire_net, 2, ',', ' ') }} €
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($fichePaie->statut === 'brouillon')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Brouillon
                                                </span>
                                            @elseif($fichePaie->statut === 'validé')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Validé
                                                </span>
                                            @elseif($fichePaie->statut === 'publié')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Publié
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $fichePaie->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('fiches-paie.show', $fichePaie->id) }}" class="text-purple-600 hover:text-purple-900" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($fichePaie->statut === 'brouillon')
                                                    <a href="{{ route('fiches-paie.edit', $fichePaie->id) }}" class="text-blue-600 hover:text-blue-900" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('fiches-paie.valider', $fichePaie->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Valider">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($fichePaie->statut === 'validé')
                                                    <form action="{{ route('fiches-paie.publier', $fichePaie->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Publier">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <a href="{{ route('fiches-paie.export-pdf', $fichePaie->id) }}" class="text-red-600 hover:text-red-900" title="Exporter en PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                
                                                @if($fichePaie->statut === 'brouillon')
                                                    <form action="{{ route('fiches-paie.destroy', $fichePaie->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette fiche de paie ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
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
                    
                    <div class="p-4">
                        {{ $fichesPaie->links() }}
                    </div>
                @else
                    <div class="p-6 text-center">
                        <div class="py-8">
                            <div class="mb-4">
                                <i class="fas fa-file-invoice-dollar text-gray-300 text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Aucune fiche de paie trouvée</h3>
                            <p class="text-gray-500 mb-6">Aucune fiche de paie ne correspond à vos critères de recherche.</p>
                            <a href="{{ route('fiches-paie.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Créer une fiche de paie
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
