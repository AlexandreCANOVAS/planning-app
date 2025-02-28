<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un employé') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Erreurs:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('_previous'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
                            <p>Données soumises:</p>
                            <pre>{{ print_r(old(), true) }}</pre>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employes.store') }}" class="space-y-6" id="employeForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nom" :value="__('Nom')" />
                                <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                            </div>

                            <div>
                                <x-input-label for="prenom" :value="__('Prénom')" />
                                <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" :value="old('prenom')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="telephone" :value="__('Téléphone')" />
                                <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full" :value="old('telephone')" />
                                <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                            </div>

                            <div>
                                <x-input-label for="adresse" :value="__('Adresse')" />
                                <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full" :value="old('adresse')" />
                                <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                            </div>

                            <div>
                                <x-input-label for="date_naissance" :value="__('Date de naissance')" />
                                <x-text-input id="date_naissance" name="date_naissance" type="date" class="mt-1 block w-full" :value="old('date_naissance')" />
                                <x-input-error class="mt-2" :messages="$errors->get('date_naissance')" />
                            </div>

                            <div>
                                <x-input-label for="date_embauche" :value="__('Date d\'embauche')" />
                                <x-text-input id="date_embauche" name="date_embauche" type="date" class="mt-1 block w-full" :value="old('date_embauche')" />
                                <x-input-error class="mt-2" :messages="$errors->get('date_embauche')" />
                            </div>

                            <div>
                                <x-input-label for="numero_securite_sociale" :value="__('Numéro de sécurité sociale')" />
                                <x-text-input id="numero_securite_sociale" name="numero_securite_sociale" type="text" class="mt-1 block w-full" :value="old('numero_securite_sociale')" />
                                <x-input-error class="mt-2" :messages="$errors->get('numero_securite_sociale')" />
                            </div>

                            <input type="hidden" name="role" value="employe">
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Ajouter') }}
                            </button>
                            <a href="{{ route('employes.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Annuler') }}</a>
                        </div>
                    </form>

                    <script>
                        document.getElementById('employeForm').addEventListener('submit', function(e) {
                            // e.preventDefault(); // Décommenter pour tester
                            console.log('Form submitted');
                            const formData = new FormData(this);
                            for (let [key, value] of formData.entries()) {
                                console.log(key + ': ' + value);
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>