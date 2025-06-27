<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:pt-0">
        <div class="w-full max-w-md px-6 py-8 mx-auto overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800 sm:rounded-lg">

            <div class="flex justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <div class="mt-4 text-center">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ __('Vérifiez votre adresse e-mail') }}</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?') }}
                </p>
                 <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Si vous n\'avez pas reçu l\'e-mail, nous vous en enverrons un autre avec plaisir.') }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    <span class="font-medium">{{ __('Un nouveau lien de vérification a été envoyé !') }}</span>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-primary-button>
                        {{ __('Renvoyer l\'e-mail de vérification') }}
                    </x-primary-button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ __('Se déconnecter') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
