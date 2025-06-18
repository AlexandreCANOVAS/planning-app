<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-6">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-white">
                    Mentions légales
                </h1>
                <p class="mt-1 text-white text-opacity-80">
                    Informations légales concernant notre application
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-gray-800">
                    <h2 class="text-xl font-semibold mb-4">Informations légales</h2>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Éditeur du site</h3>
                        <p>{{ auth()->user()->societe->nom ?? config('app.name', 'Planning App') }}</p>
                        <p>Adresse : [Adresse de l'entreprise]</p>
                        <p>Téléphone : [Numéro de téléphone]</p>
                        <p>Email : [Email de contact]</p>
                        <p>SIRET : [Numéro SIRET]</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Directeur de la publication</h3>
                        <p>[Nom du directeur de publication]</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Hébergement</h3>
                        <p>[Nom de l'hébergeur]</p>
                        <p>Adresse : [Adresse de l'hébergeur]</p>
                        <p>Téléphone : [Numéro de téléphone de l'hébergeur]</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Propriété intellectuelle</h3>
                        <p>L'ensemble du contenu de ce site (textes, images, vidéos, etc.) est protégé par le droit d'auteur. Toute reproduction ou représentation, intégrale ou partielle, faite sans le consentement de l'auteur ou de ses ayants droit est illicite.</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Données personnelles</h3>
                        <p>Conformément à la loi « Informatique et Libertés » du 6 janvier 1978 modifiée et au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition aux données personnelles vous concernant. Pour exercer ces droits, veuillez nous contacter via la page de contact.</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Cookies</h3>
                        <p>Ce site utilise des cookies pour améliorer l'expérience utilisateur. En naviguant sur ce site, vous acceptez l'utilisation de cookies conformément à notre politique de confidentialité.</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium mb-2">Loi applicable</h3>
                        <p>Le présent site est soumis à la loi française. En cas de litige, les tribunaux français seront seuls compétents.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
