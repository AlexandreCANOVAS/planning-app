<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mon abonnement') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                Premium
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notifications -->
            @if (session('success'))
                <div class="mb-6 bg-white border-l-4 border-green-500 rounded-lg shadow-md overflow-hidden">
                    <div class="flex items-center p-4">
                        <div class="flex-shrink-0 bg-green-500 rounded-full p-1">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Succès</p>
                            <p class="text-sm text-gray-600">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="ml-auto bg-white rounded-md p-1.5 text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-white border-l-4 border-red-500 rounded-lg shadow-md overflow-hidden">
                    <div class="flex items-center p-4">
                        <div class="flex-shrink-0 bg-red-500 rounded-full p-1">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Erreur</p>
                            <p class="text-sm text-gray-600">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="ml-auto bg-white rounded-md p-1.5 text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Dashboard principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                <!-- Colonne de gauche: Informations de l'abonnement -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <!-- En-tête avec badge de statut -->
                        <div class="bg-gradient-to-r from-purple-700 to-purple-500 p-6 relative overflow-hidden">
                            <!-- Cercles décoratifs -->
                            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 rounded-full bg-white opacity-10"></div>
                            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 rounded-full bg-white opacity-10"></div>
                            
                            <div class="relative z-10">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-white">Abonnement Premium</h2>
                                    @if($subscription->canceled())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Annulé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mt-6 flex items-baseline">
                                    <span class="text-3xl font-extrabold text-white">99,99€</span>
                                    <span class="ml-1 text-xl font-medium text-purple-100">/mois</span>
                                </div>
                                
                                <p class="mt-2 text-purple-100">Accès illimité à toutes les fonctionnalités</p>
                            </div>
                        </div>
                    
                    <!-- Corps de la carte avec détails -->                    
                    <div class="p-6">
                        <!-- Informations sur l'abonnement -->                        
                        <div class="space-y-6">
                            <!-- Statut et période -->                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Statut avec icône -->                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($subscription->canceled())
                                                <div class="p-2 bg-orange-100 rounded-full">
                                                    <svg class="h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="p-2 bg-green-100 rounded-full">
                                                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Statut</p>
                                            <p class="text-sm text-gray-700">
                                                @if($subscription->canceled())
                                                    Abonnement annulé
                                                @else
                                                    Abonnement actif
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Période avec icône -->                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="p-2 bg-purple-100 rounded-full">
                                                <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Période</p>
                                            <p class="text-sm text-gray-700">
                                                @if($subscription->canceled())
                                                    Se termine le {{ $subscription->ends_at->format('d/m/Y') }}
                                                @else
                                                    Renouvellement le {{ \Carbon\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->format('d/m/Y') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Méthode de paiement -->                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="p-2 bg-blue-100 rounded-full">
                                                <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Méthode de paiement</p>
                                            <p class="text-sm text-gray-700">
                                                @if($paymentMethod)
                                                    {{ ucfirst($paymentMethod->card->brand) }} •••• {{ $paymentMethod->card->last4 }}
                                                    <span class="text-xs text-gray-500 ml-1">Exp. {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}</span>
                                                @else
                                                    Aucune méthode de paiement enregistrée
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <a href="#" class="text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors">
                                        Mettre à jour
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Actions -->                            
                            <div class="pt-4">
                                @if($subscription->canceled())
                                    <a href="{{ route('subscription.resume') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Reprendre mon abonnement
                                    </a>
                                @else
                                    <a href="{{ route('subscription.cancel') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                        <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Annuler mon abonnement
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Factures -->
                <div class="mt-6">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="border-b border-gray-100 p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Historique des factures</h3>
                                <span class="text-sm text-gray-500">{{ count($invoices) }} facture(s)</span>
                            </div>
                        </div>
                        
                        <div class="overflow-hidden">
                            @if(count($invoices) > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($invoices as $invoice)
                                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $invoice->date()->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $invoice->total() }}€
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($invoice->status == 'paid')
                                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Payée
                                                            </span>
                                                        @else
                                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                {{ ucfirst($invoice->status) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ $invoice->hosted_invoice_url }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-800 mr-3">
                                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Voir
                                                        </a>
                                                        <a href="{{ $invoice->invoice_pdf }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-800">
                                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            PDF
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-8 px-6 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune facture</h3>
                                    <p class="mt-1 text-sm text-gray-500">Vos factures apparaîtront ici après votre premier paiement.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Colonne de droite: Informations complémentaires -->
            <div class="lg:col-span-1">
                <!-- Carte d'informations sur l'abonnement -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-6">
                    <div class="p-6 bg-gradient-to-br from-purple-50 to-white">
                        <h3 class="font-semibold text-gray-900 mb-4">Informations</h3>
                        
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-sm text-gray-700">Accès illimité à toutes les fonctionnalités</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-sm text-gray-700">Facturation mensuelle automatique</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-sm text-gray-700">Annulation possible à tout moment</p>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Carte d'aide -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Besoin d'aide ?</h3>
                        
                        <div class="space-y-4">
                            <a href="{{ url('/faq') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-2">
                                        <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-sm font-medium text-gray-900">FAQ</h4>
                                        <p class="text-xs text-gray-500">Questions fréquentes sur les abonnements</p>
                                    </div>
                                </div>
                            </a>
                            
                            <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-2">
                                        <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-sm font-medium text-gray-900">Contact</h4>
                                        <p class="text-xs text-gray-500">Nous contacter pour toute question</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
