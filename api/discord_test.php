<?php
// api/discord_test.php - Page de test des webhooks Discord

require_once __DIR__ . '/../controllers/DiscordController.php';

// Vérifier si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $webhook = $_POST['webhook_url'] ?? ''; // Récupérer l'URL du webhook
    $result = false;
    
    switch ($action) {
        case 'test_connection':
            $result = DiscordController::testWebhook($webhook);
            break;
            
        case 'test_event':
            if (!empty($webhook)) {
                $event = [
                    'titre' => 'CS:GO Charity Tournament',
                    'theme' => 'Tournoi solidaire pour les enfants',
                    'date_debut' => date('Y-m-d H:i:s', strtotime('+1 day')),
                    'objectif' => '5000 DT pour l\'éducation'
                ];
                $embed = [
                    'title' => '🎮 Nouvel Événement Créé !',
                    'description' => "**{$event['titre']}**\n\n{$event['theme']}",
                    'color' => 0xEC6090,
                    'fields' => [
                        ['name' => '📅 Date de début', 'value' => date('d/m/Y à H:i', strtotime($event['date_debut'])), 'inline' => true],
                        ['name' => '🎯 Objectif', 'value' => $event['objectif'], 'inline' => true]
                    ],
                    'footer' => ['text' => 'Play to Help'],
                    'timestamp' => date('c')
                ];
                $result = DiscordController::sendEmbed($webhook, $embed);
            }
            break;
            
        case 'test_stream_live':
            if (!empty($webhook)) {
                $embed = [
                    'title' => '🔴 STREAM EN DIRECT !',
                    'description' => "**Marathon Gaming 24h** vient de démarrer !",
                    'url' => 'https://twitch.tv/example',
                    'color' => 0x9146FF,
                    'fields' => [
                        ['name' => '🎮 Plateforme', 'value' => 'Twitch', 'inline' => true],
                        ['name' => '💰 Objectif Dons', 'value' => '3000 DT', 'inline' => true]
                    ],
                    'footer' => ['text' => 'Rejoignez maintenant !'],
                    'timestamp' => date('c')
                ];
                $result = DiscordController::sendEmbed($webhook, $embed);
            }
            break;
            
        case 'test_donation':
            if (!empty($webhook)) {
                $embed = [
                    'title' => '💚 Nouveau Don Reçu !',
                    'description' => "**Ahmed Ben Ali** vient de donner **50 DT** !",
                    'color' => 0x28A745,
                    'fields' => [
                        ['name' => '🎯 Association', 'value' => 'Croissant Rouge Tunisien', 'inline' => true],
                        ['name' => '📝 Message', 'value' => 'Bravo pour cette initiative ! 💪', 'inline' => false]
                    ],
                    'footer' => ['text' => 'Merci pour votre générosité ! 🙏'],
                    'timestamp' => date('c')
                ];
                $result = DiscordController::sendEmbed($webhook, $embed);
            }
            break;
            
        case 'test_goal':
            if (!empty($webhook)) {
                $embed = [
                    'title' => '🎉 OBJECTIF ATTEINT ! 🎉',
                    'description' => "L'objectif de **5000 DT** a été atteint pour **CS:GO Tournament** !\n\nMerci à tous les donateurs ! 💪",
                    'color' => 0x00FF00,
                    'footer' => ['text' => 'Play to Help - Ensemble on fait la différence !'],
                    'timestamp' => date('c')
                ];
                $result = DiscordController::sendEmbed($webhook, $embed);
            }
            break;
            
        case 'test_stream_end':
            if (!empty($webhook)) {
                $embed = [
                    'title' => '✅ Stream Terminé',
                    'description' => "Le stream **Marathon Gaming 24h** s'est terminé !",
                    'color' => 0x28A745,
                    'fields' => [
                        ['name' => '💰 Total collecté', 'value' => '3450 DT', 'inline' => true],
                        ['name' => '👥 Donateurs', 'value' => '87 personnes', 'inline' => true],
                        ['name' => '⏱️ Durée', 'value' => '6h 32min', 'inline' => true]
                    ],
                    'footer' => ['text' => 'Merci à tous ! ❤️'],
                    'timestamp' => date('c')
                ];
                $result = DiscordController::sendEmbed($webhook, $embed);
            }
            break;
            
        case 'test_reminder':
            if (!empty($webhook)) {
                $embed = [
                    'title' => '⏰ Rappel : Événement demain !',
                    'description' => "N'oubliez pas ! **Valorant Tournament** commence demain à 20:00",
                    'color' => 0xFFA500,
                    'fields' => [
                        ['name' => '📍 Inscriptions', 'value' => 'Inscrivez-vous maintenant !', 'inline' => false]
                    ],
                    'footer' => ['text' => 'Play to Help'],
                    'timestamp' => date('c')
                ];
                $result = DiscordController::sendEmbed($webhook, $embed);
            }
            break;
    }
    
    $message = $result ? '✅ Notification envoyée avec succès !' : '❌ Échec de l\'envoi. Vérifiez l\'URL du webhook.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Discord Webhooks - Play to Help</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #1f2122 0%, #27292a 100%);
            color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #27292a;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        h1 {
            color: #ec6090;
            margin-bottom: 10px;
            font-size: 2em;
        }
        .subtitle {
            color: #999;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-success {
            background: #28a745;
            color: #fff;
        }
        .alert-error {
            background: #dc3545;
            color: #fff;
        }
        .setup-box {
            background: #1f2122;
            border-left: 4px solid #ec6090;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .setup-box h3 {
            color: #ec6090;
            margin-bottom: 15px;
        }
        .setup-box ol {
            margin-left: 20px;
            line-height: 1.8;
        }
        .setup-box code {
            background: #27292a;
            padding: 2px 8px;
            border-radius: 4px;
            color: #ec6090;
            font-family: 'Courier New', monospace;
        }
        .test-section {
            background: #1f2122;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .test-section h3 {
            color: #ec6090;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
            font-weight: 500;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            background: #27292a;
            border: 1px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #ec6090;
        }
        .btn {
            background: #ec6090;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background: #d84a7a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(236,96,144,0.4);
        }
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Test Discord Webhooks</h1>
        <p class="subtitle">Play to Help - Configuration & Tests</p>
        
        <?php if (isset($message)): ?>
            <div class="alert <?= $result ? 'alert-success' : 'alert-error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div class="setup-box">
            <h3>📋 Configuration Webhook</h3>
            <ol>
                <li>Ouvre ton serveur Discord</li>
                <li>Va dans <code>Paramètres du serveur</code> → <code>Intégrations</code> → <code>Webhooks</code></li>
                <li>Clique sur <code>Nouveau Webhook</code></li>
                <li>Donne-lui un nom (ex: "Play to Help Events")</li>
                <li>Choisis le canal où poster</li>
                <li>Copie l'URL du webhook</li>
                <li>Colle l'URL dans <code>config/discord.php</code></li>
            </ol>
        </div>
        
        <!-- Test connexion -->
        <div class="test-section">
            <h3>🔌 Test de Connexion</h3>
            <form method="POST" id="mainForm">
                <input type="hidden" name="action" value="test_connection" id="actionInput">
                <div class="form-group">
                    <label>URL du Webhook Discord</label>
                    <input type="text" name="webhook_url" id="webhookInput" placeholder="https://discord.com/api/webhooks/..." required>
                    <small style="color:#999;display:block;margin-top:5px;">💡 Cette URL sera utilisée pour tous les tests ci-dessous</small>
                </div>
                <button type="submit" class="btn">
                    <span class="icon">🔌</span> Tester la Connexion
                </button>
            </form>
        </div>
        
        <!-- Tests notifications -->
        <div class="test-section">
            <h3>🧪 Tests des Notifications</h3>
            <p style="color:#999;margin-bottom:15px;">⚠️ Entrez d'abord votre webhook ci-dessus !</p>
            <div class="btn-group">
                <button onclick="testAction('test_event')" class="btn">
                    <span class="icon">🎮</span> Nouvel Événement
                </button>
                
                <button onclick="testAction('test_stream_live')" class="btn">
                    <span class="icon">🔴</span> Stream Live
                </button>
                
                <button onclick="testAction('test_donation')" class="btn">
                    <span class="icon">💚</span> Nouveau Don
                </button>
                
                <button onclick="testAction('test_goal')" class="btn">
                    <span class="icon">🎉</span> Objectif Atteint
                </button>
                
                <button onclick="testAction('test_stream_end')" class="btn">
                    <span class="icon">✅</span> Stream Terminé
                </button>
                
                <button onclick="testAction('test_reminder')" class="btn">
                    <span class="icon">⏰</span> Rappel Événement
                </button>
            </div>
        </div>
    </div>
    
    <script>
    function testAction(action) {
        const webhook = document.getElementById('webhookInput').value;
        if (!webhook) {
            alert('⚠️ Veuillez d\'abord entrer l\'URL de votre webhook Discord !');
            return;
        }
        
        // Créer un formulaire caché et le soumettre
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="${action}">
            <input type="hidden" name="webhook_url" value="${webhook}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
    </script>
</body>
</html>
