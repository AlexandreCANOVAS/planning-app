<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Créer un Planning Mensuel
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('plannings.create_monthly_calendar') }}" method="GET" class="space-y-6">
                        <div>
                            <label for="employe_id" class="block text-sm font-medium text-gray-700">Employé</label>
                            <select id="employe_id" name="employe_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->id }}">{{ $employe->nom }} {{ $employe->prenom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                                <select id="mois" name="mois" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(null, $m, 1)->locale('fr')->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                                <select id="annee" name="annee" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Continuer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
