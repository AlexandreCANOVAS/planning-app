<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Mot de passe oublié ?
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Pas de problème. Indiquez-nous votre adresse email et nous vous enverrons un lien de réinitialisation.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="rounded-md shadow-sm">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Adresse email
                        </label>
                        <input id="email" name="email" type="email" required
                            class="appearance-none relative block w-full px-3 py-2 mt-1 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            value="{{ old('email') }}"
                            placeholder="exemple@email.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Envoyer le lien
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Retour à la connexion
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
