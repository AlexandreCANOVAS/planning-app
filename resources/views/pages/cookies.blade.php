<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Politique de Gestion des Cookies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-bold mb-4">Politique de Gestion des Cookies de Planify</h3>

                    <p class="mb-2"><strong>Dernière mise à jour :</strong> {{ date('d/m/Y') }}</p>

                    <p class="mb-4">Ce site utilise des cookies pour améliorer l'expérience utilisateur, assurer la sécurité et le bon fonctionnement du site. Cette politique explique ce que sont les cookies, comment nous les utilisons, et comment vous pouvez les gérer.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">1. Qu'est-ce qu'un cookie ?</h4>
                    <p class="mb-4">Un cookie est un petit fichier texte stocké sur votre ordinateur ou appareil mobile par un site web. Il permet au site de se souvenir de vos actions et préférences (comme la connexion, la langue, la taille de la police et d'autres préférences d'affichage) sur une période donnée, afin que vous n'ayez pas à les ressaisir chaque fois que vous revenez sur le site ou naviguez d'une page à une autre.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">2. Les cookies que nous utilisons</h4>
                    <p class="mb-4">Nous utilisons principalement les types de cookies suivants :</p>
                    <ul class="list-disc list-inside mb-4">
                        <li><strong>Cookies strictement nécessaires :</strong> Ces cookies sont essentiels au fonctionnement de notre site. Ils vous permettent de naviguer sur le site et d'utiliser ses fonctionnalités, comme l'accès aux zones sécurisées. Sans ces cookies, des services comme l'authentification ne pourraient pas être fournis.</li>
                        <ul class="list-disc list-inside ml-6">
                            <li><code>planify_session</code>: Cookie de session Laravel, nécessaire pour identifier votre session sur le serveur.</li>
                            <li><code>XSRF-TOKEN</code>: Utilisé pour la protection contre les attaques de type Cross-Site Request Forgery (CSRF).</li>
                        </ul>
                        <li><strong>Cookies de fonctionnalité :</strong> Ces cookies permettent au site de se souvenir des choix que vous avez faits (par exemple, votre nom d'utilisateur) pour fournir des fonctionnalités améliorées et plus personnelles.</li>
                        <li><strong>Cookies de performance et d'analyse :</strong> Nous n'utilisons pas de cookies de suivi ou d'analyse tiers (comme Google Analytics) pour le moment.</li>
                    </ul>

                     <h4 class="text-md font-bold mt-6 mb-2">3. Comment gérer les cookies</h4>
                    <p class="mb-4">La plupart des navigateurs web vous permettent de contrôler les cookies via les paramètres de votre navigateur. Vous pouvez configurer votre navigateur pour qu'il vous avertisse avant d'accepter les cookies, ou vous pouvez le configurer pour qu'il les refuse complètement. Veuillez noter que si vous désactivez les cookies nécessaires, certaines parties de notre site pourraient ne pas fonctionner correctement.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">4. Contact</h4>
                    <p class="mb-4">Si vous avez des questions sur notre utilisation des cookies, veuillez nous contacter à <a href="mailto:dpo@planify.app" class="underline">dpo@planify.app</a>.</p>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
