@extends('layouts.app')

@section('title', 'Centre d\'aide')

@section('content')
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Centre d'aide
            </h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                Trouvez des réponses à vos questions et apprenez à utiliser notre plateforme
            </p>
        </div>

        <div class="mt-12 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-purple-100">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Section FAQ -->
                    <div class="bg-purple-50 p-6 rounded-lg shadow-sm border border-purple-100">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="ml-3 text-lg font-medium text-gray-900">FAQ</h3>
                        </div>
                        <p class="text-gray-600">Consultez notre liste de questions fréquemment posées pour trouver rapidement des réponses.</p>
                        <div class="mt-4">
                            <a href="{{ route('faq') }}" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800">
                                Voir la FAQ
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Section Tutoriels -->
                    <div class="bg-purple-50 p-6 rounded-lg shadow-sm border border-purple-100">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <h3 class="ml-3 text-lg font-medium text-gray-900">Tutoriels vidéo</h3>
                        </div>
                        <p class="text-gray-600">Apprenez à utiliser notre plateforme avec nos tutoriels vidéo détaillés.</p>
                        <div class="mt-4">
                            <a href="#" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800">
                                Voir les tutoriels
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Section Contact Support -->
                    <div class="bg-purple-50 p-6 rounded-lg shadow-sm border border-purple-100">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="ml-3 text-lg font-medium text-gray-900">Contacter le support</h3>
                        </div>
                        <p class="text-gray-600">Vous ne trouvez pas de réponse ? Notre équipe de support est là pour vous aider.</p>
                        <div class="mt-4">
                            <a href="{{ route('contact') }}" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800">
                                Contacter le support
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section FAQ -->
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Questions fréquemment posées</h2>
                    
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-1')">
                                <span class="text-lg font-medium text-gray-900">Comment créer un planning ?</span>
                                <svg id="icon-faq-1" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-1" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour créer un planning, connectez-vous à votre compte, accédez à la section "Plannings" depuis le tableau de bord, puis cliquez sur le bouton "Créer un planning". Suivez les instructions à l'écran pour sélectionner les employés, les horaires et les lieux de travail.
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-4">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-2')">
                                <span class="text-lg font-medium text-gray-900">Comment gérer les congés des employés ?</span>
                                <svg id="icon-faq-2" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-2" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour gérer les congés, accédez à la section "Congés" depuis le tableau de bord. Vous pouvez y voir les demandes en attente, approuver ou refuser des congés, et consulter le calendrier des absences. Les employés peuvent soumettre leurs demandes depuis leur propre espace.
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-4">
                            <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-3')">
                                <span class="text-lg font-medium text-gray-900">Comment exporter les données de comptabilité ?</span>
                                <svg id="icon-faq-3" class="h-5 w-5 text-purple-600 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="faq-3" class="mt-4 hidden">
                                <p class="text-gray-600">
                                    Pour exporter les données de comptabilité, accédez à la section "Comptabilité", sélectionnez la période souhaitée, puis cliquez sur le bouton "Exporter". Vous pouvez choisir entre différents formats (PDF, Excel) selon vos besoins.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleFaq(id) {
        const element = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            element.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
@endpush
@endsection
