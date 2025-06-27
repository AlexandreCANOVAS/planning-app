@php
    // Nous ne voulons pas afficher la bannière sur la page de la politique des cookies elle-même.
    $isCookiePage = request()->routeIs('pages.cookies');
@endphp

@unless($isCookiePage)
<div x-data="{
        showBanner: false,
        showSettingsModal: false,
        preferences: {
            necessary: true,
            analytics: false,
            marketing: false,
        },
        init() {
            this.$nextTick(() => {
                if (!localStorage.getItem('cookie_consent_given')) {
                    this.showBanner = true;
                }
            });
        },
        loadPreferences() {
            const consent = localStorage.getItem('cookie_consent_given');
            if (!consent) return;
            try {
                const parsed = JSON.parse(consent);
                this.preferences.analytics = !!parsed.analytics;
                this.preferences.marketing = !!parsed.marketing;
            } catch (e) {
                this.preferences.analytics = consent === 'accepted';
                this.preferences.marketing = consent === 'accepted';
            }
        },
        openSettings() {
            this.loadPreferences();
            this.showBanner = false;
            this.showSettingsModal = true;
        },
        acceptAll() {
            this.preferences.analytics = true;
            this.preferences.marketing = true;
            this.saveAndClose();
        },
        refuseAll() {
            this.preferences.analytics = false;
            this.preferences.marketing = false;
            this.saveAndClose();
        },
        savePreferences() {
            this.saveAndClose();
        },
        saveAndClose() {
            localStorage.setItem('cookie_consent_given', JSON.stringify(this.preferences));
            this.showSettingsModal = false;
            this.showBanner = false;
        }
    }" 
    x-init="init()" 
    @keydown.escape.window="showSettingsModal = false">

    <!-- Banner -->
    <div
        x-show="showBanner && !showSettingsModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform translate-y-0"
        x-transition:leave-end="transform translate-y-full"
        class="fixed bottom-0 inset-x-0 z-50"
        style="display: none;"
    >
        <div class="w-full bg-white dark:bg-slate-900 border-t border-black/10 dark:border-white/10">
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-y-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex-grow md:pr-6">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-semibold text-gray-900 dark:text-white">Mais d'abord, les cookies 🍪</span>
                            <span class="ml-1">Nous utilisons des cookies pour assurer le bon fonctionnement du site et améliorer votre expérience. Pour en savoir plus, consultez notre <a href="{{ route('pages.cookies') }}" class="font-medium text-purple-600 dark:text-purple-400 hover:underline">politique de cookies</a>.</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-x-3 flex-shrink-0">
                        <button type="button" @click="openSettings()" class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-800 dark:text-gray-300 bg-gray-200 dark:bg-slate-800 hover:bg-gray-300 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">Gérer</button>
                        <button type="button" @click="refuseAll()" class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-800 dark:text-gray-300 bg-gray-200 dark:bg-slate-800 hover:bg-gray-300 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">Refuser tout</button>
                        <button type="button" @click="acceptAll()" class="px-3 py-1.5 rounded-md text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">Accepter tout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div x-show="showSettingsModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[999]" style="display: none;">
        <div @click.away="showSettingsModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg p-6 md:p-8 m-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Gérer vos préférences</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Vous pouvez choisir ci-dessous les types de cookies que vous autorisez. Les cookies essentiels ne peuvent pas être désactivés.</p>
            
            <div class="space-y-4">
                <!-- Essentiels -->
                <div class="p-4 border rounded-lg dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Cookies Essentiels</h3>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Toujours actifs</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Ces cookies sont indispensables au bon fonctionnement du site. Ils garantissent les fonctionnalités de base et de sécurité, de manière anonyme.</p>
                </div>

                <!-- Analyse -->
                <div class="p-4 border rounded-lg dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Cookies d'Analyse</h3>
                        <label for="analytics_cookies" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="analytics_cookies" class="sr-only peer" x-model="preferences.analytics">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:bg-purple-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Ces cookies nous aident à comprendre comment les visiteurs interagissent avec le site, en collectant des informations de manière anonyme.</p>
                </div>

                <!-- Marketing -->
                <div class="p-4 border rounded-lg dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Cookies de Publicité</h3>
                        <label for="marketing_cookies" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="marketing_cookies" class="sr-only peer" x-model="preferences.marketing">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:bg-purple-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Ces cookies sont utilisés pour vous proposer des publicités pertinentes en fonction de vos centres d'intérêt, sur ce site ou sur d'autres.</p>
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-4">
                <div class="flex items-center gap-x-2 w-full sm:w-auto">
                    <button type="button" @click="preferences.analytics = false; preferences.marketing = false" class="w-full px-4 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Tout refuser
                    </button>
                    <button type="button" @click="preferences.analytics = true; preferences.marketing = true" class="w-full px-4 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Tout accepter
                    </button>
                </div>
                <button @click="savePreferences()" class="w-full sm:w-auto px-6 py-2 rounded-md font-semibold text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Sauvegarder mes choix
                </button>
            </div>
        </div>
    </div>
</div>
@endunless
