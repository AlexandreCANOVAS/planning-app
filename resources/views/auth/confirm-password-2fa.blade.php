<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Cette action est sensible et nécessite la confirmation de votre mot de passe pour continuer.') }}
            </div>

            <form method="POST" action="{{ route('two-factor.confirm-password') }}">
                @csrf
                
                <input type="hidden" name="action" value="{{ $action }}">

                <div>
                    <x-input-label for="password" :value="__('Mot de passe')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end mt-4">
                    <x-primary-button class="ml-3">
                        {{ __('Confirmer') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
