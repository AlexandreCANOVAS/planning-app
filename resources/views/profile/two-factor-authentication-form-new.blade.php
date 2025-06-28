<x-action-section>
    <x-slot name="title">
        {{ __('Authentification à deux facteurs') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ajoutez une sécurité supplémentaire à votre compte en utilisant l\'authentification à deux facteurs.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900">
            @if ($enabled)
                {{ __('Vous avez activé l\'authentification à deux facteurs.') }}
            @else
                {{ __('Vous n\'avez pas activé l\'authentification à deux facteurs.') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                {{ __('Lorsque l\'authentification à deux facteurs est activée, vous serez invité à fournir un jeton sécurisé et aléatoire lors de l\'authentification. Vous pouvez récupérer ce jeton depuis l\'application Google Authenticator de votre téléphone.') }}
            </p>
        </div>

        @if ($enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        {{ __('L\'authentification à deux facteurs est maintenant activée. Scannez le code QR suivant à l\'aide de l\'application d\'authentification de votre téléphone.') }}
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block bg-white">
                    {!! $qrCodeSvg !!}
                </div>
                
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        {{ __('Clé de configuration') }}: {{ $twoFactorSecret }}
                    </p>
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        {{ __('Stockez ces codes de récupération dans un gestionnaire de mots de passe sécurisé. Ils peuvent être utilisés pour récupérer l\'accès à votre compte si votre appareil d\'authentification à deux facteurs est perdu.') }}
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                    @foreach ($recoveryCodes as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $enabled)
                <!-- Bouton de redirection vers la confirmation de mot de passe pour l'activation 2FA -->
                <div id="two-factor-activation-container">
                    <a href="{{ route('two-factor.confirm-password', ['action' => 'enable']) }}" 
                       id="two-factor-activate-button"
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('ACTIVER 2FA') }}
                    </a>
                </div>
                
                <!-- Script pour déboguer le bouton d'activation -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('Script de débogage 2FA - Version ultra simplifiée');
                        
                        // Vérifier si le bouton est présent
                        const activateButton = document.getElementById('two-factor-activate-button');
                        if (activateButton) {
                            console.log('Bouton d\'activation trouvé - Version ultra simplifiée');
                            
                            // Ajouter un gestionnaire d'événement manuel
                            activateButton.addEventListener('click', function(e) {
                                console.log('Bouton d\'activation cliqué - Soumission du formulaire');
                            });
                        } else {
                            console.error('Bouton d\'activation NON TROUVÉ');
                            
                            // Afficher le contenu du conteneur pour débogage
                            const container = document.getElementById('two-factor-activation-container');
                            if (container) {
                                console.log('Contenu du conteneur:', container.innerHTML);
                            } else {
                                console.error('Conteneur d\'activation NON TROUVÉ');
                            }
                        }
                    });
                </script>
            @else
                <div class="flex items-center space-x-3">
                    @if (! $showingRecoveryCodes)
                        <a href="{{ route('two-factor.confirm-password', ['action' => 'recovery-codes']) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                            {{ __('Afficher les codes de récupération') }}
                        </a>
                    @else
                        <a href="{{ route('two-factor.confirm-password', ['action' => 'recovery-codes']) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                            {{ __('Régénérer les codes de récupération') }}
                        </a>
                    @endif

                    <a href="{{ route('two-factor.confirm-password', ['action' => 'disable']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Désactiver') }}
                    </a>
                </div>
            @endif
        </div>
    </x-slot>
</x-action-section>
