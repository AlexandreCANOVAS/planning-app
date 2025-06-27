<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                 <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-4xl">P</span>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-center text-gray-800">Finaliser votre inscription</h2>
            <p class="text-center text-gray-600 mt-2 mb-6">Créez votre mot de passe pour activer votre compte.</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />
            
            <!-- Affichage des erreurs -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Erreurs:</strong>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('employee.invitation.process') }}" class="mt-6 space-y-6" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $invitation->token }}" />

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" value="Mot de passe" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Terms and Conditions -->
                <div class="mt-6">
                    <label for="terms" class="inline-flex items-center">
                        <input id="terms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="terms" required>
                        <span class="ml-2 text-sm text-gray-600">J'accepte les <a href="#" class="underline hover:text-gray-900">termes et conditions</a> et la <a href="{{ url('/politique-de-confidentialite') }}" class="underline hover:text-gray-900">politique de confidentialité</a>.</span>
                    </label>
                     <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Créer mon compte') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        if (form) {
            console.log('Formulaire trouvé');
            console.log('Action du formulaire:', form.action);
            console.log('Méthode du formulaire:', form.method);
            
            // Vérifier le token CSRF
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) {
                console.log('Token CSRF trouvé');
            } else {
                console.error('Token CSRF non trouvé!');
            }
            
            // Vérifier le token d'invitation
            const invitationToken = document.querySelector('input[name="token"]');
            if (invitationToken) {
                console.log('Token d\'invitation trouvé:', invitationToken.value);
            } else {
                console.error('Token d\'invitation non trouvé!');
            }
            
            // Ajouter un gestionnaire d'événement pour le formulaire
            form.addEventListener('submit', function(event) {
                console.log('Formulaire soumis');
                
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Traitement en cours...';
                }
                
                // Laisser le formulaire se soumettre normalement
                return true;
            });
        } else {
            console.error('Formulaire non trouvé');
        }
    });
</script>
</x-guest-layout>
