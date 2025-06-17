<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-8">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                <div class="w-32 h-32 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="relative">
                <h2 class="text-3xl font-bold text-white mb-2">
                    {{ __('Créer votre entreprise') }}
                </h2>
                <p class="text-purple-100 text-lg">
                    Commencez à gérer vos plannings en quelques étapes simples
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-8">
                    <form method="POST" action="{{ route('societes.store') }}" class="space-y-8">
                        @csrf
                        
                        <!-- Section Informations Principales -->
                        <div class="space-y-8">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-building mr-3 text-[rgb(75,20,140)]"></i>
                                    Informations principales
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nom de l'entreprise -->
                                    <div class="col-span-2 md:col-span-1">
                                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'entreprise</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-briefcase text-gray-400"></i>
                                            </div>
                                            <input type="text" id="nom" name="nom" 
                                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[rgb(75,20,140)] focus:border-transparent"
                                                placeholder="Entrez le nom de votre entreprise"
                                                value="{{ old('nom') }}" required>
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                                    </div>

                                    <!-- SIRET -->
                                    <div class="col-span-2 md:col-span-1">
                                        <label for="siret" class="block text-sm font-medium text-gray-700 mb-2">Numéro SIRET</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-fingerprint text-gray-400"></i>
                                            </div>
                                            <input type="text" id="siret" name="siret"
                                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[rgb(75,20,140)] focus:border-transparent"
                                                placeholder="14 chiffres"
                                                value="{{ old('siret') }}" required
                                                maxlength="19">
                                            <input type="hidden" id="siret_hidden" name="siret" value="{{ old('siret') }}">
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('siret')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section Structure -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-sitemap mr-3 text-[rgb(75,20,140)]"></i>
                                    Structure juridique
                                </h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Forme Juridique -->
                                    <div>
                                        <label for="forme_juridique" class="block text-sm font-medium text-gray-700 mb-2">Forme Juridique</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-balance-scale text-gray-400"></i>
                                            </div>
                                            <select id="forme_juridique" name="forme_juridique" 
                                                class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[rgb(75,20,140)] focus:border-transparent appearance-none bg-white"
                                                required>
                                                <option value="">Sélectionnez une forme juridique</option>
                                                <option value="SARL" {{ old('forme_juridique') == 'SARL' ? 'selected' : '' }}>SARL - Société à Responsabilité Limitée</option>
                                                <option value="EURL" {{ old('forme_juridique') == 'EURL' ? 'selected' : '' }}>EURL - Entreprise Unipersonnelle à Responsabilité Limitée</option>
                                                <option value="SAS" {{ old('forme_juridique') == 'SAS' ? 'selected' : '' }}>SAS - Société par Actions Simplifiée</option>
                                                <option value="SASU" {{ old('forme_juridique') == 'SASU' ? 'selected' : '' }}>SASU - Société par Actions Simplifiée Unipersonnelle</option>
                                                <option value="SA" {{ old('forme_juridique') == 'SA' ? 'selected' : '' }}>SA - Société Anonyme</option>
                                                <option value="SCI" {{ old('forme_juridique') == 'SCI' ? 'selected' : '' }}>SCI - Société Civile Immobilière</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('forme_juridique')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section Contact -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-address-card mr-3 text-[rgb(75,20,140)]"></i>
                                    Informations de contact
                                </h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Adresse -->
                                    <div>
                                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse complète</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                            </div>
                                            <textarea id="adresse" name="adresse" rows="3"
                                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[rgb(75,20,140)] focus:border-transparent"
                                                placeholder="Numéro, rue, code postal, ville"
                                                required>{{ old('adresse') }}</textarea>
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                                    </div>

                                    <!-- Téléphone -->
                                    <div>
                                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-phone text-gray-400"></i>
                                            </div>
                                            <input type="tel" id="telephone" name="telephone"
                                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[rgb(75,20,140)] focus:border-transparent"
                                                placeholder="01 23 45 67 89"
                                                value="{{ old('telephone') }}">
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="pt-6">
                            <button type="submit" class="w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] text-white font-medium rounded-lg shadow-lg hover:from-[rgb(85,30,150)] hover:to-[rgb(65,20,120)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                <i class="fas fa-rocket mr-2"></i>
                                Créer mon entreprise
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Animation du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('form > div > div');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    section.style.transition = 'all 0.5s ease-out';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });
        });

        // Formattage automatique du SIRET
        const siretInput = document.getElementById('siret');
        const siretHidden = document.getElementById('siret_hidden');

        siretInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);
            
            // Mise à jour du champ visible avec espaces
            siretInput.value = value.replace(/(\d{3})(?=\d)/g, '$1 ').trim();
            
            // Mise à jour du champ caché sans espaces
            siretHidden.value = value;
        });

        // Formattage automatique du téléphone
        document.getElementById('telephone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            e.target.value = value.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
        });

        // S'assurer que le formulaire envoie la bonne valeur du SIRET
        document.querySelector('form').addEventListener('submit', function(e) {
            const siretValue = siretHidden.value;
            if (siretValue.length !== 14) {
                e.preventDefault();
                alert('Le numéro SIRET doit contenir exactement 14 chiffres.');
            }
        });
    </script>
    @endpush
</x-app-layout>