@extends('layouts.app')

@section('title', 'Statut du système')

@section('content')
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Statut du système
            </h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                Consultez l'état actuel de nos services et infrastructures
            </p>
        </div>

        <div class="mt-12 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-purple-100">
            <div class="p-6">
                <!-- Statut global -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="ml-3 text-xl font-medium text-gray-900">Tous les systèmes sont opérationnels</h2>
                    </div>
                    <p class="mt-2 text-gray-600">Dernière mise à jour : {{ now()->format('d/m/Y H:i') }}</p>
                </div>

                <!-- Tableau des services -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">État des services</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disponibilité</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($services as $service)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $service['name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $service['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $service['uptime'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Historique des incidents -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des incidents récents</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4">
                            <p class="text-gray-600">Aucun incident n'a été signalé au cours des 30 derniers jours.</p>
                        </div>
                        
                        <div class="hidden">
                            <!-- Ce bloc sera affiché s'il y a des incidents -->
                            <div class="divide-y divide-gray-200">
                                <div class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">Maintenance planifiée</h4>
                                            <p class="mt-1 text-sm text-gray-500">15/06/2025 - 02:00 à 04:00</p>
                                        </div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Résolu
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Maintenance planifiée pour mise à jour du système. Service indisponible pendant 2 heures.
                                    </p>
                                </div>
                                
                                <div class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">Problème de connexion</h4>
                                            <p class="mt-1 text-sm text-gray-500">10/06/2025 - 14:30 à 15:45</p>
                                        </div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Résolu
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Certains utilisateurs ont rencontré des difficultés de connexion. Le problème a été identifié et résolu.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abonnement aux notifications -->
                <div class="mt-8 bg-purple-50 border border-purple-100 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Restez informé</h3>
                    <p class="text-gray-600 mb-4">
                        Abonnez-vous pour recevoir des notifications en cas d'incident ou de maintenance planifiée.
                    </p>
                    <form class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-grow">
                            <input type="email" name="email" id="email" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Votre adresse email">
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            S'abonner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
