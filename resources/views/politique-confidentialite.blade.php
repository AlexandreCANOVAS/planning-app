<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-6">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-white">
                    Politique de confidentialité
                </h1>
                <p class="mt-1 text-white text-opacity-80">
                    Comment nous protégeons vos données
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-gray-800">
                    <h2 class="text-xl font-semibold mb-4">Politique de confidentialité</h2>
                    <p class="mb-4">Dernière mise à jour : {{ date('d/m/Y') }}</p>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Introduction</h3>
                        <p>{{ auth()->user()->societe->nom ?? config('app.name', 'Planning App') }} s'engage à protéger la vie privée des utilisateurs de son application de gestion de planning. Cette politique de confidentialité explique comment nous collectons, utilisons, divulguons et protégeons vos informations personnelles.</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Collecte des données</h3>
                        <p>Nous collectons les informations suivantes :</p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Informations d'identification (nom, prénom, email)</li>
                            <li>Informations professionnelles (poste, type de contrat, horaires)</li>
                            <li>Informations personnelles (date de naissance, adresse, numéro de sécurité sociale)</li>
                            <li>Données de planification (horaires de travail, congés)</li>
                            <li>Données de connexion et d'utilisation de l'application</li>
                        </ul>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Utilisation des données</h3>
                        <p>Nous utilisons vos données pour :</p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Gérer les plannings et les horaires de travail</li>
                            <li>Traiter les demandes de congés</li>
                            <li>Gérer les informations administratives des employés</li>
                            <li>Améliorer notre service et l'expérience utilisateur</li>
                            <li>Assurer la sécurité de notre application</li>
                        </ul>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Conservation des données</h3>
                        <p>Nous conservons vos données personnelles aussi longtemps que nécessaire pour les finalités pour lesquelles elles ont été collectées, conformément à nos obligations légales et réglementaires.</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Partage des données</h3>
                        <p>Nous ne vendons pas vos données personnelles à des tiers. Nous pouvons partager vos informations avec :</p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Les membres autorisés de votre organisation</li>
                            <li>Nos fournisseurs de services qui nous aident à exploiter notre application</li>
                            <li>Les autorités légales lorsque la loi l'exige</li>
                        </ul>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Sécurité des données</h3>
                        <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données personnelles contre la perte, l'accès non autorisé, la divulgation, l'altération et la destruction.</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Vos droits</h3>
                        <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Droit d'accès à vos données personnelles</li>
                            <li>Droit de rectification de vos données inexactes</li>
                            <li>Droit à l'effacement de vos données</li>
                            <li>Droit à la limitation du traitement</li>
                            <li>Droit à la portabilité de vos données</li>
                            <li>Droit d'opposition au traitement</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium mb-2">Contact</h3>
                        <p>Pour toute question concernant cette politique de confidentialité ou pour exercer vos droits, veuillez nous contacter via la page de contact.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
