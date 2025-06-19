@extends('layouts.app')

@section('content')
<div class="relative flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Cercle d'erreur animé -->
        <div class="mx-auto">
            <div class="relative">
                <div class="animate-pulse bg-red-100 dark:bg-red-900/30 rounded-full h-32 w-32 mx-auto flex items-center justify-center">
                    <span class="text-5xl font-bold text-red-600 dark:text-red-400">500</span>
                </div>
                <div class="absolute -top-2 -right-2">
                    <div class="animate-ping bg-red-400 rounded-full h-4 w-4"></div>
                </div>
            </div>
        </div>
        
        <!-- Message d'erreur -->
        <div class="mt-8">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                Erreur serveur
            </h1>
            <p class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-lg">
                Oups ! Une erreur s'est produite sur notre serveur. Nos équipes techniques ont été informées.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 dark:border-gray-700 text-base font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-home mr-2"></i> Accueil
                </a>
            </div>
        </div>
        
        <!-- Suggestions -->
        <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Que faire ?</h2>
            <ul class="mt-4 list-disc list-inside text-sm text-gray-500 dark:text-gray-400 space-y-2">
                <li>Rafraîchissez la page et réessayez</li>
                <li>Revenez plus tard, le problème est peut-être temporaire</li>
                <li>Si le problème persiste, contactez le support technique</li>
            </ul>
        </div>
    </div>
</div>
@endsection
