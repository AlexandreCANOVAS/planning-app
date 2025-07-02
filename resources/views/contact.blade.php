<x-guest-layout>
    <div class="bg-gradient-to-b from-indigo-50 to-white min-h-screen">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight mb-4">
                        Contactez-nous
                    </h1>
                    <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto">
                        Notre équipe est à votre disposition pour répondre à toutes vos questions
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" required
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                    placeholder="Votre nom">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" required
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                    placeholder="vous@exemple.com">
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
                            <div class="mt-1">
                                <input type="text" name="subject" id="subject" required
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                    placeholder="Le sujet de votre message">
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <div class="mt-1">
                                <textarea name="message" id="message" rows="6" required
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out resize-none"
                                    placeholder="Votre message..."></textarea>
                            </div>
                        </div>

                        <div>
                            <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="lg:mt-6">
                    <div class="space-y-12">
                        <!-- Other Contact Methods -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-8">Autres moyens de nous contacter</h3>
                            <dl class="space-y-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-100">
                                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <dt class="text-lg font-medium text-gray-900">Email</dt>
                                        <dd class="mt-1 text-gray-500">
                                            <a href="mailto:support@planningapp.com" class="hover:text-indigo-600 transition duration-150 ease-in-out">
                                                support@planningapp.com
                                            </a>
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-100">
                                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <dt class="text-lg font-medium text-gray-900">Téléphone</dt>
                                        <dd class="mt-1 text-gray-500">
                                            <a href="tel:0123456789" class="hover:text-indigo-600 transition duration-150 ease-in-out">
                                                01 23 45 67 89
                                            </a>
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>

                        <!-- Office Hours -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-8">Horaires d'ouverture</h3>
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                                <dl class="space-y-4">
                                    <div class="flex justify-between">
                                        <dt class="font-medium">Lundi - Vendredi</dt>
                                        <dd>9h00 - 18h00</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="font-medium">Samedi</dt>
                                        <dd>9h00 - 12h00</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="font-medium">Dimanche</dt>
                                        <dd>Fermé</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-cookie-consent />
</x-guest-layout>
