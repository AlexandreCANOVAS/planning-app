<x-two-factor-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-md">
                    <span class="text-white font-bold text-3xl">P</span>
                </div>
            </div>
            
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Veuillez confirmer l\'accès à votre compte en saisissant le code d\'authentification fourni par votre application d\'authentification.') }}
            </div>

            @if ($errors->any())
                <div class="mb-4">
                    <div class="font-medium text-red-600">{{ __('Oups! Quelque chose s\'est mal passé.') }}</div>

                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf

                <div>
                    <x-input-label for="code" value="{{ __('Code') }}" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus autocomplete="one-time-code" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div class="flex justify-end mt-4">
                    <x-primary-button class="ml-4">
                        {{ __('Confirmer') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            {{ __('Ou') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Si vous avez perdu l\'accès à votre application d\'authentification, vous pouvez utiliser un code de récupération.') }}
                </div>

                <form method="POST" action="{{ route('two-factor.verify-recovery') }}">
                    @csrf

                    <div>
                        <x-input-label for="recovery_code" value="{{ __('Code de récupération') }}" />
                        <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" autocomplete="one-time-code" />
                        <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
                    </div>

                    <div class="flex justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Utiliser un code de récupération') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-two-factor-layout>
