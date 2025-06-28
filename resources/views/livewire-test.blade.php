<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Livewire</title>
    @livewireStyles
</head>
<body>
    <div class="container">
        <h1>Test de Livewire</h1>
        
        <div>
            <button onclick="testLivewireConnection()">Tester la connexion Livewire</button>
            <div id="result"></div>
        </div>
    </div>

    @livewireScripts
    
    <script>
        function testLivewireConnection() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = 'Test en cours...';
            
            // Vérifier si Livewire est chargé
            if (window.Livewire) {
                resultDiv.innerHTML += '<br>Livewire est chargé correctement.';
                
                // Vérifier la configuration de Livewire
                resultDiv.innerHTML += '<br>URL de base Livewire: ' + window.livewireScriptConfig.url;
                
                // Tester une requête AJAX vers la route Livewire
                fetch('/livewire/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Livewire': true
                    },
                    body: JSON.stringify({
                        fingerprint: {
                            id: 'test',
                            name: 'test',
                            method: 'GET',
                            path: window.location.pathname,
                            locale: 'fr'
                        },
                        serverMemo: {},
                        updates: []
                    })
                })
                .then(response => {
                    if (response.ok) {
                        resultDiv.innerHTML += '<br>✅ Connexion à /livewire/update réussie!';
                        return response.json();
                    } else {
                        resultDiv.innerHTML += '<br>❌ Erreur de connexion à /livewire/update: ' + response.status;
                        throw new Error('Erreur de connexion');
                    }
                })
                .then(data => {
                    resultDiv.innerHTML += '<br>Réponse reçue: ' + JSON.stringify(data);
                })
                .catch(error => {
                    resultDiv.innerHTML += '<br>Erreur: ' + error.message;
                    resultDiv.innerHTML += '<br><br>Vérifiez les points suivants:';
                    resultDiv.innerHTML += '<br>1. Le service provider Livewire est-il enregistré?';
                    resultDiv.innerHTML += '<br>2. Les routes Livewire sont-elles correctement enregistrées?';
                    resultDiv.innerHTML += '<br>3. Y a-t-il des erreurs dans la console du navigateur?';
                });
            } else {
                resultDiv.innerHTML = '❌ Livewire n\'est pas chargé correctement.';
            }
        }
    </script>
</body>
</html>
