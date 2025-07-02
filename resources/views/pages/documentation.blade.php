@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Documentation
            </h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                Guides complets et ressources pour utiliser efficacement notre plateforme
            </p>
        </div>

        <div class="mt-12 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-purple-100">
            <div class="p-6">
                <!-- Navigation de la documentation -->
                <div class="flex flex-col md:flex-row">
                    <!-- Sidebar de navigation -->
                    <div class="w-full md:w-64 flex-shrink-0 mb-6 md:mb-0 md:mr-6">
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-purple-100">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Catégories</h3>
                            <nav class="space-y-1">
                                <a href="#introduction" class="flex items-center px-3 py-2 text-sm font-medium rounded-md bg-purple-50 text-purple-700">
                                    <svg class="mr-3 h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Introduction
                                </a>
                                <a href="#plannings" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-purple-700">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Plannings
                                </a>
                                <a href="#conges" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-purple-700">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Congés
                                </a>
                                <a href="#comptabilite" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-purple-700">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Comptabilité
                                </a>
                                <a href="#api" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-purple-700">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                    API
                                </a>
                            </nav>
                        </div>
                    </div>

                    <!-- Contenu principal -->
                    <div class="flex-1">
                        <div class="prose max-w-none">
                            <!-- Introduction -->
                            <section id="introduction" class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900">Introduction</h2>
                                <div class="mt-4 bg-white rounded-lg shadow p-6 border border-purple-100">
                                    <p class="mb-4">
                                        Bienvenue dans la documentation de Planify, votre solution complète pour la gestion des plannings, congés et comptabilité de votre entreprise.
                                    </p>
                                    <p class="mb-4">
                                        Cette documentation vous guidera à travers les différentes fonctionnalités de notre plateforme et vous aidera à tirer le meilleur parti de nos outils.
                                    </p>
                                    <p>
                                        Que vous soyez employeur ou employé, vous trouverez ici toutes les informations nécessaires pour utiliser efficacement notre solution.
                                    </p>
                                </div>
                            </section>

                            <!-- Plannings -->
                            <section id="plannings" class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900">Plannings</h2>
                                <div class="mt-4 bg-white rounded-lg shadow p-6 border border-purple-100">
                                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Création de plannings</h3>
                                    <p class="mb-4">
                                        La création de plannings est simple et intuitive. Accédez à la section "Plannings" depuis votre tableau de bord et suivez ces étapes :
                                    </p>
                                    <ol class="list-decimal pl-6 mb-4">
                                        <li class="mb-2">Sélectionnez le mois et l'année pour votre planning</li>
                                        <li class="mb-2">Choisissez les employés à inclure dans le planning</li>
                                        <li class="mb-2">Définissez les horaires de travail pour chaque jour</li>
                                        <li class="mb-2">Attribuez les lieux de travail si nécessaire</li>
                                        <li>Enregistrez et publiez le planning</li>
                                    </ol>
                                    <p>
                                        Une fois le planning publié, les employés concernés recevront une notification et pourront consulter leurs horaires depuis leur espace personnel.
                                    </p>
                                </div>
                            </section>

                            <!-- Congés -->
                            <section id="conges" class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900">Congés</h2>
                                <div class="mt-4 bg-white rounded-lg shadow p-6 border border-purple-100">
                                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Gestion des congés</h3>
                                    <p class="mb-4">
                                        Notre module de gestion des congés permet de :
                                    </p>
                                    <ul class="list-disc pl-6 mb-4">
                                        <li class="mb-2">Suivre les soldes de congés payés de chaque employé</li>
                                        <li class="mb-2">Traiter les demandes de congés (approbation ou refus)</li>
                                        <li class="mb-2">Visualiser un calendrier global des absences</li>
                                        <li>Générer des rapports sur les congés pris</li>
                                    </ul>
                                    <p>
                                        Les employés peuvent soumettre leurs demandes de congés directement depuis leur espace, ce qui facilite le processus pour tous.
                                    </p>
                                </div>
                            </section>

                            <!-- Comptabilité -->
                            <section id="comptabilite" class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900">Comptabilité</h2>
                                <div class="mt-4 bg-white rounded-lg shadow p-6 border border-purple-100">
                                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Suivi du temps et comptabilité</h3>
                                    <p class="mb-4">
                                        Notre module de comptabilité vous permet de :
                                    </p>
                                    <ul class="list-disc pl-6 mb-4">
                                        <li class="mb-2">Suivre les heures travaillées par employé</li>
                                        <li class="mb-2">Calculer automatiquement les heures supplémentaires</li>
                                        <li class="mb-2">Générer des fiches de paie</li>
                                        <li class="mb-2">Exporter les données comptables au format PDF ou Excel</li>
                                        <li>Suivre les heures de nuit et jours fériés</li>
                                    </ul>
                                    <p>
                                        Toutes ces fonctionnalités sont accessibles depuis la section "Comptabilité" de votre tableau de bord.
                                    </p>
                                </div>
                            </section>

                            <!-- API -->
                            <section id="api">
                                <h2 class="text-2xl font-bold text-gray-900">API</h2>
                                <div class="mt-4 bg-white rounded-lg shadow p-6 border border-purple-100">
                                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Documentation de l'API</h3>
                                    <p class="mb-4">
                                        Notre API REST vous permet d'intégrer les données de Planify dans vos propres systèmes. Voici quelques endpoints disponibles :
                                    </p>
                                    <div class="overflow-x-auto mb-4">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead>
                                                <tr>
                                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Endpoint</th>
                                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Méthode</th>
                                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">/api/lieux</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">GET</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">Récupérer la liste des lieux de travail</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">/api/plannings</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">GET</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">Récupérer les plannings</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">/api/employes</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">GET</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">Récupérer la liste des employés</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p>
                                        Pour accéder à notre API, vous devez générer une clé API depuis votre espace administrateur. Pour plus d'informations, contactez notre équipe de support.
                                    </p>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
