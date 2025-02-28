<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes Plannings') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('employe.plannings.calendar') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Vue Calendrier') }}
                </a>
                <a href="{{ route('employe.plannings.download-pdf') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    {{ __('Télécharger PDF') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('employe.plannings.index') }}" class="mb-6">
                        <div class="flex space-x-4">
                            <div>
                                <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                                <select name="mois" id="mois" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(range(1, 12) as $mois)
                                        <option value="{{ $mois }}" {{ $selectedMonth == $mois ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                                <select name="annee" id="annee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(range(now()->year - 2, now()->year + 2) as $annee)
                                        <option value="{{ $annee }}" {{ $selectedYear == $annee ? 'selected' : '' }}>
                                            {{ $annee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    {{ __('Filtrer') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Total des heures -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700">Total des heures travaillées : {{ $totalHeures }} heures</h3>
                    </div>

                    <!-- Liste des plannings -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($plannings as $planning)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($planning->date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $planning->lieu->nom ?? 'Non défini' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $planning->heures_travaillees }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $planning->commentaire }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Aucun planning trouvé pour cette période
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
