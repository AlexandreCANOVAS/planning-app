<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Politique de Confidentialité') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-bold mb-4">Politique de Confidentialité de Planify</h3>

                    <p class="mb-2"><strong>Dernière mise à jour :</strong> {{ date('d/m/Y') }}</p>

                    <p class="mb-4">Votre vie privée est importante pour nous. Cette politique de confidentialité explique quelles données personnelles nous collectons auprès de vous et comment nous les utilisons.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">1. Données que nous collectons</h4>
                    <p class="mb-4">Nous collectons des données pour fonctionner efficacement et vous fournir les meilleures expériences avec nos services. Vous fournissez certaines de ces données directement, par exemple lorsque vous créez un compte, ou lorsque votre employeur crée un compte pour vous. Nous en obtenons certaines en enregistrant la façon dont vous interagissez avec nos services. Les données que nous collectons peuvent inclure les suivantes :</p>
                    <ul class="list-disc list-inside mb-4">
                        <li><strong>Données d'identification :</strong> Nom, prénom, adresse e-mail, numéro de téléphone, photo de profil.</li>
                        <li><strong>Données professionnelles :</strong> Poste, type de contrat, dates de contrat, date d'embauche, plannings de travail, formations.</li>
                        <li><strong>Données personnelles :</strong> Date de naissance, adresse postale, situation familiale, nombre d'enfants, contact d'urgence.</li>
                        <li><strong>Données sensibles :</strong> Numéro de sécurité sociale (collecté uniquement lorsque la loi l'exige pour les déclarations sociales).</li>
                        <li><strong>Données de connexion et techniques :</strong> Mots de passe (hashés), adresses IP, logs d'activité.</li>
                    </ul>

                    <h4 class="text-md font-bold mt-6 mb-2">2. Comment nous utilisons les données personnelles</h4>
                    <p class="mb-4">Nous utilisons les données que nous collectons pour les finalités suivantes :</p>
                    <ul class="list-disc list-inside mb-4">
                        <li>Fournir et gérer nos services, notamment la gestion des plannings, des congés et des formations.</li>
                        <li>Assurer la gestion administrative de votre contrat de travail.</li>
                        <li>Communiquer avec vous concernant votre compte ou nos services.</li>
                        <li>Assurer la sécurité de nos services.</li>
                        <li>Respecter nos obligations légales (par exemple, les déclarations sociales).</li>
                    </ul>

                    <h4 class="text-md font-bold mt-6 mb-2">3. Partage des données personnelles</h4>
                    <p class="mb-4">Nous ne partageons pas vos données personnelles avec des tiers, à l'exception des cas suivants :</p>
                     <ul class="list-disc list-inside mb-4">
                        <li>Avec votre employeur, dans le cadre de la gestion de votre contrat de travail.</li>
                        <li>Si la loi l'exige ou pour répondre à une procédure judiciaire.</li>
                        <li>Pour protéger nos clients, par exemple pour empêcher le spam ou les tentatives de fraude.</li>
                    </ul>

                    <h4 class="text-md font-bold mt-6 mb-2">4. Vos droits</h4>
                    <p class="mb-4">Conformément au RGPD, vous disposez des droits suivants concernant vos données personnelles :</p>
                    <ul class="list-disc list-inside mb-4">
                        <li><strong>Droit d'accès :</strong> Vous avez le droit de savoir si nous détenons des données vous concernant et d'en obtenir une copie.</li>
                        <li><strong>Droit de rectification :</strong> Vous avez le droit de faire corriger des données inexactes.</li>
                        <li><strong>Droit à l'effacement (ou "droit à l'oubli") :</strong> Vous avez le droit de demander la suppression de vos données, sous réserve de nos obligations légales de conservation.</li>
                        <li><strong>Droit à la limitation du traitement :</strong> Vous avez le droit de demander la suspension du traitement de vos données dans certains cas.</li>
                        <li><strong>Droit à la portabilité :</strong> Vous avez le droit de recevoir vos données dans un format structuré et lisible par machine.</li>
                    </ul>
                    <p class="mb-4">Pour exercer ces droits, vous pouvez nous contacter à l'adresse dpo@planify.app ou contacter directement votre employeur.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">5. Sécurité des données</h4>
                    <p class="mb-4">Nous nous engageons à protéger la sécurité de vos données personnelles. Nous utilisons diverses technologies et procédures de sécurité pour aider à protéger vos données personnelles contre l'accès, l'utilisation ou la divulgation non autorisés. Par exemple, nous stockons les données sensibles comme le numéro de sécurité sociale sous forme chiffrée.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">6. Durée de conservation</h4>
                    <p class="mb-4">Nous conservons les données personnelles aussi longtemps que nécessaire pour fournir les services et effectuer les transactions que vous avez demandées, ou à d'autres fins essentielles telles que le respect de nos obligations légales, la résolution des litiges et l'application de nos accords. Par exemple, les données liées à la paie sont conservées pendant la durée légale requise.</p>

                    <h4 class="text-md font-bold mt-6 mb-2">7. Contact</h4>
                    <p class="mb-4">Si vous avez une question ou une préoccupation concernant la confidentialité, veuillez nous contacter à dpo@planify.app.</p>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
