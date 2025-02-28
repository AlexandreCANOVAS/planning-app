<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le lieu de travail') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('lieux.update', $lieu) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="nom" :value="__('Nom du lieu')" />
                                <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $lieu->nom)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                            </div>

                            <div>
                                <x-input-label for="adresse" :value="__('Adresse')" />
                                <x-text-area id="adresse" name="adresse" class="mt-1 block w-full" required>{{ old('adresse', $lieu->adresse) }}</x-text-area>
                                <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="code_postal" :value="__('Code postal')" />
                                    <x-text-input id="code_postal" name="code_postal" type="text" class="mt-1 block w-full" :value="old('code_postal', $lieu->code_postal)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('code_postal')" />
                                </div>

                                <div>
                                    <x-input-label for="ville" :value="__('Ville')" />
                                    <x-text-input id="ville" name="ville" type="text" class="mt-1 block w-full" :value="old('ville', $lieu->ville)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('ville')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Mettre à jour') }}</x-primary-button>
                            <a href="{{ route('lieux.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>