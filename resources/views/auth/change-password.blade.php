<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Changer votre mot de passe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('change-password.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="current_password" :value="__('Mot de passe actuel')" />
                            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Nouveau mot de passe')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmer le nouveau mot de passe')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Changer le mot de passe') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
