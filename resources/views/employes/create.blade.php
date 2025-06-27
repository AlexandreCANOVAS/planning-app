<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ __('Inviter un nouvel employé') }}
                </h2>
                <p class="text-gray-600 mt-1">
                    Envoyez une invitation par e-mail pour que votre employé puisse créer son compte.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Veuillez corriger les {{ $errors->count() }} erreur(s) ci-dessous.
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul role="list" class="list-disc pl-5 space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employes.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom -->
                                <div>
                                    <x-input-label for="nom" :value="__('Nom')" />
                                    <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                                </div>

                                <!-- Prénom -->
                                <div>
                                    <x-input-label for="prenom" :value="__('Prénom')" />
                                    <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" :value="old('prenom')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                                </div>

                                <!-- Email -->
                                <div class="col-span-2">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Poste / Fonction -->
                                <div class="col-span-2">
                                    <x-input-label for="poste" :value="__('Poste / Fonction')" />
                                    <x-text-input id="poste" name="poste" type="text" class="mt-1 block w-full" :value="old('poste')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('poste')" />
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                            <a href="{{ route('employes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-6 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Annuler
                            </a>
                            <x-primary-button>
                                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                {{ __('Envoyer l\'invitation') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
