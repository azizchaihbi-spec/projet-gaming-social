<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/ChallengeController.php';

$id = intval($_GET['id'] ?? 0);
$nom = $_GET['nom'] ?? '';
$progression = floatval($_GET['progression'] ?? 0);
$objectif = floatval($_GET['objectif'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$pourcentage = $objectif > 0 ? min(100, round(($progression / $objectif) * 100, 2)) : 0;

// Traitement du formulaire
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProgression = floatval($_POST['progression']);
    
    if ($newProgression < 0) {
        $message = 'La progression doit être supérieure ou égale à 0 €';
        $messageType = 'error';
    } else {
        $challengeC = new ChallengeController();
        $result = $challengeC->updateProgression($id, $newProgression);
        
        if ($result) {
            $message = 'Progression mise à jour avec succès !';
            $messageType = 'success';
            $progression = $newProgression;
            $pourcentage = $objectif > 0 ? min(100, round(($progression / $objectif) * 100, 2)) : 0;
            
            // Redirection après 2 secondes
            header("refresh:2;url=index.php");
        } else {
            $message = 'Erreur lors de la mise à jour';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la progression - <?php echo htmlspecialchars($nom); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Space Mono', monospace;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(167, 139, 250, 0.5);
            border-radius: 30px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 0 60px rgba(167, 139, 250, 0.4);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .neon {
            text-shadow: 0 0 20px #a78bfa, 0 0 40px #a78bfa;
        }
        .progress-bar-bg {
            width: 100%;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 20px;
            position: relative;
            transition: width 0.6s ease;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.8);
        }
        .input-field {
            background: rgba(167, 139, 250, 0.1);
            border: 2px solid rgba(167, 139, 250, 0.4);
            color: white;
            padding: 15px 20px;
            border-radius: 15px;
            font-size: 1.5rem;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #a78bfa;
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.6);
            background: rgba(167, 139, 250, 0.2);
        }
        .btn {
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-save {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        }
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6);
        }
        .btn-cancel {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 2px solid #ef4444;
        }
        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.3);
            transform: translateY(-3px);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            font-weight: 600;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 2px solid #10b981;
            color: #10b981;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 2px solid #ef4444;
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="text-3xl font-bold text-center mb-2 neon text-purple-300">
            Modifier la Progression
        </h1>
        <p class="text-center text-gray-400 mb-6">
            <?php echo htmlspecialchars($nom); ?>
        </p>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $messageType === 'success' ? '✅' : '❌'; ?> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-300">Objectif :</span>
                <span class="text-2xl font-bold text-emerald-400"><?php echo number_format($objectif, 2); ?> €</span>
            </div>
            
            <div class="progress-bar-bg">
                <div class="progress-fill" style="width: <?php echo $pourcentage; ?>%"></div>
            </div>
            
            <div class="flex justify-between items-center mt-2">
                <span class="text-gray-400">Progression actuelle</span>
                <span class="text-lg font-bold text-yellow-400"><?php echo $pourcentage; ?>%</span>
            </div>
        </div>
        
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-center text-gray-300 mb-3 text-lg">
                    Nouvelle progression (€)
                </label>
                <input type="number" 
                       name="progression" 
                       step="0.01" 
                       min="0" 
                       value="<?php echo $progression; ?>"
                       class="input-field"
                       required
                       autofocus>
                <p class="text-center text-gray-500 text-sm mt-2">
                    Entrez le montant total collecté pour ce challenge
                </p>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="btn btn-save flex-1">
                    💾 Sauvegarder
                </button>
                <a href="index.php" class="btn btn-cancel flex-1 text-center">
                    ❌ Annuler
                </a>
            </div>
        </form>
        
        <div class="mt-6 p-4 bg-purple-900/20 rounded-lg border border-purple-500/30">
            <p class="text-sm text-gray-400 text-center">
                💡 <strong>Astuce :</strong> La progression sera automatiquement mise à jour dans le dashboard
            </p>
        </div>
    </div>
</body>
</html>
