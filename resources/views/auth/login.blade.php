<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Card Container -->
            <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8">
                <!-- Logo et Titre -->
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 rounded-2xl flex items-center justify-center transform transition-all duration-300 hover:rotate-12 hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a2 2 0 11-4 0 2 2 0 014 0zM7 7a2 2 0 11-4 0 2 2 0 014 0zM15 7a2 2 0 11-4 0 2 2 0 014 0z M21 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75M3 21v-2a4 4 0 013.5-3.94m0-4.12a4 4 0 010-7.75" />
                        </svg>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                        Connexion
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Ou
                        <a href="{{ route('register') }}" class="font-medium text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-80 transition-opacity duration-200">
                            créez un nouveau compte
                        </a>
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-5">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                    class="pl-10 block w-full px-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out"
                                    placeholder="exemple@email.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Mot de passe -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Mot de passe
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" required
                                    class="pl-10 block w-full px-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out"
                                    placeholder="Votre mot de passe">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Options de connexion -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition duration-150 ease-in-out">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                    Se souvenir de moi
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="font-medium text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-80 transition-opacity duration-200">
                                        Mot de passe oublié ?
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Bouton de connexion -->
                        <div>
                            <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all duration-200 hover:scale-[1.02] hover:shadow-lg">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-indigo-300 group-hover:text-indigo-200 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </span>
                                Se connecter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
