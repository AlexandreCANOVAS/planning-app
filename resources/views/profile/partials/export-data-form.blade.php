<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Exporter les données') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Téléchargez une archive de toutes les données que nous avons sur vous.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('gdpr.export') }}">
        @csrf
        <x-primary-button>
            {{ __('Télécharger mes données') }}
        </x-primary-button>
    </form>
</section>
