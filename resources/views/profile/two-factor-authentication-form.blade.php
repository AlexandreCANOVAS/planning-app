<x-action-section>
    <x-slot name="title">
        {{ __('Authentification à deux facteurs') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ajoutez une sécurité supplémentaire à votre compte en utilisant l\'authentification à deux facteurs.') }}
    </x-slot>

    <x-slot name="content">
        <div>
            <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                <p>
                    {{ __('L\'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte en exigeant l\'accès à votre téléphone en plus de votre mot de passe.') }}
                </p>
            </div>

            @if ($this->enabled)
                @if ($showingQrCode)
                    <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                        <p class="font-semibold">
                            @if ($showingConfirmation)
                                {{ __('Pour terminer l\'activation de l\'authentification à deux facteurs, scannez le code QR suivant à l\'aide de l\'application d\'authentification de votre téléphone ou saisissez la clé de configuration et fournissez le code OTP généré.') }}
                            @else
                                {{ __('L\'authentification à deux facteurs est maintenant activée. Scannez le code QR suivant à l\'aide de l\'application d\'authentification de votre téléphone ou saisissez la clé de configuration.') }}
                            @endif
                        </p>
                    </div>

                    <div class="mt-4 p-2 inline-block bg-white">
                        {!! $this->user->twoFactorQrCodeSvg() !!}
                    </div>

                    <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                        <p class="font-semibold">
                            {{ __('Clé de configuration') }}: {{ decrypt($this->user->two_factor_secret) }}
                        </p>
                    </div>

                    @if ($showingConfirmation)
                        <div class="mt-4">
                            <x-label for="code" value="{{ __('Code') }}" />

                            <x-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                                wire:model.defer="code"
                                wire:keydown.enter="confirmTwoFactorAuthentication" />

                            <x-input-error for="code" class="mt-2" />
                        </div>
                    @endif
                @endif

                <div class="mt-4">
                    @if ($showingRecoveryCodes)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Stockez ces codes de récupération dans un gestionnaire de mots de passe sécurisé. Ils peuvent être utilisés pour récupérer l\'accès à votre compte si votre dispositif d\'authentification à deux facteurs est perdu.') }}
                        </p>

                        <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 rounded-lg">
                            @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                                <div>{{ $code }}</div>
                            @endforeach
                                <div class="p-6">
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Confirmer le mot de passe') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Pour votre sécurité, veuillez confirmer votre mot de passe pour continuer.') }}
                                    </p>
                                    <div class="mt-4">
                                        <x-text-input type="password" class="mt-1 block w-3/4" placeholder="{{ __('Mot de passe') }}" x-ref="password" wire:model.defer="password" @keydown.enter="$wire.regenerateRecoveryCodes().then(() => { confirming = false; });" />
                                        <x-input-error for="password" class="mt-2" />
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button @click="confirming = false">
                                            {{ __('Annuler') }}
                                        </x-secondary-button>
                                        <x-primary-button class="ml-3" @click="$wire.regenerateRecoveryCodes().then(() => { confirming = false; });" wire:loading.attr="disabled">
                                            {{ __('Confirmer') }}
                                        </x-primary-button>
                                    </div>
                                </div>
                            </x-modal>
                        </div>
                    @else
                         <div x-data="{ confirming: false }">
                            <button @click="confirming = true" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Afficher les codes de récupération') }}
                            </button>
                            <x-modal x-show="confirming" @close="confirming = false">
                                <div class="p-6">
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Confirmer le mot de passe') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Pour votre sécurité, veuillez confirmer votre mot de passe pour continuer.') }}
                                    </p>
                                    <div class="mt-4">
                                        <x-text-input type="password" class="mt-1 block w-3/4" placeholder="{{ __('Mot de passe') }}" x-ref="password" wire:model.defer="password" @keydown.enter="$wire.showRecoveryCodes().then(() => { confirming = false; });" />
                                        <x-input-error for="password" class="mt-2" />
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button @click="confirming = false">
                                            {{ __('Annuler') }}
                                        </x-secondary-button>
                                        <x-primary-button class="ml-3" @click="$wire.showRecoveryCodes().then(() => { confirming = false; });" wire:loading.attr="disabled">
                                            {{ __('Confirmer') }}
                                        </x-primary-button>
                                    </div>
                                </div>
                            </x-modal>
                        </div>
                    @endif
    
                    <div x-data="{ confirming: false }">
                        <button @click="confirming = true" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Désactiver') }}
                        </button>
                        <x-modal x-show="confirming" @close="confirming = false">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Confirmer le mot de passe') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Pour votre sécurité, veuillez confirmer votre mot de passe pour continuer.') }}
                                </p>
                                <div class="mt-4">
                                    <x-text-input type="password" class="mt-1 block w-3/4" placeholder="{{ __('Mot de passe') }}" x-ref="password" wire:model.defer="password" @keydown.enter="$wire.disableTwoFactorAuthentication().then(() => { confirming = false; });" />
                                    <x-input-error for="password" class="mt-2" />
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button @click="confirming = false">
                                        {{ __('Annuler') }}
                                    </x-secondary-button>
                                    <x-primary-button class="ml-3" @click="$wire.disableTwoFactorAuthentication().then(() => { confirming = false; });" wire:loading.attr="disabled">
                                        {{ __('Confirmer') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </x-modal>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>
</x-action-section>
