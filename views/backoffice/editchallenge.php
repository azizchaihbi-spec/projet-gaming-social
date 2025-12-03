<?php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';
require_once '../../models/Challenge.php';

$challengeC = new ChallengeController();

if (!isset($_GET['id'])) {
    header("Location: ../../views/backoffice/index.php");
    exit;
}

$challenge = $challengeC->getOne($_GET['id']);

if (!$challenge) {
    die("Challenge introuvable !");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        // Validation des données
        $id_association = isset($_POST['id_association']) ? intval($_POST['id_association']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $objectif = isset($_POST['objectif']) ? floatval($_POST['objectif']) : 0;
        $recompense = isset($_POST['recompense']) ? trim($_POST['recompense']) : '';

        // Vérifications
        if ($id_association <= 0) {
            echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner une association']);
            exit;
        }

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Le nom du challenge est requis']);
            exit;
        }

        if ($objectif <= 0) {
            echo json_encode(['success' => false, 'message' => 'L\'objectif doit être supérieur à 0']);
            exit;
        }

        if (empty($recompense)) {
            echo json_encode(['success' => false, 'message' => 'La récompense est requise']);
            exit;
        }

        $challenge->setIdAssociation($id_association);
        $challenge->setName($name);
        $challenge->setObjectif($objectif);
        $challenge->setRecompense($recompense);

        $challengeC->update($challenge);

        echo json_encode([
            'success' => true, 
            'message' => "Challenge '{$name}' mis à jour avec succès !"
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
        exit;
    }
}

// Charger les associations pour le select
try {
    $associations = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $associations = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Challenge - Play2Help</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Space Mono', monospace; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        .card { 
            background: rgba(30, 41, 59, 0.9); 
            backdrop-filter: blur(15px); 
            border: 2px solid rgba(167, 139, 250, 0.4);
            box-shadow: 0 0 40px rgba(167, 139, 250, 0.2);
        }
        .neon { 
            text-shadow: 0 0 20px #a78bfa, 0 0 40px #a78bfa; 
        }
        .input-glow {
            transition: all 0.3s ease;
        }
        .input-glow:focus {
            box-shadow: 0 0 20px rgba(167, 139, 250, 0.5);
            border-color: #a78bfa;
        }
        .btn-primary {
            background: linear-gradient(135deg, #a78bfa 0%, #22d3ee 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(167, 139, 250, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(100, 116, 139, 0.4);
        }
        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #a78bfa, transparent);
            animation: scan 8s linear infinite;
            pointer-events: none;
            z-index: 9999;
        }
        @keyframes scan {
            0% { transform: translateY(0); opacity: 0.5; }
            50% { opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0.5; }
        }
        .error-shake {
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .success-pulse {
            animation: pulse 0.5s;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .label-required::after {
            content: ' *';
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>

<body class="relative">
    <div class="scanline"></div>

    <div class="container mx-auto px-4 py-12 max-w-4xl">
        
        <!-- HEADER AVEC RETOUR -->
        <div class="mb-8 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-purple-400 hover:text-purple-300 transition group">
                <i data-feather="arrow-left" class="group-hover:-translate-x-1 transition"></i>
                <span class="font-medium">Retour au Dashboard</span>
            </a>
            
            <div class="text-gray-400 text-sm">
                Challenge ID: <span class="text-purple-400 font-bold">#<?= $challenge->getIdChallenge() ?></span>
            </div>
        </div>

        <!-- TITRE PRINCIPAL -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-bold font-orbitron neon mb-3 animate-pulse">
                MODIFIER CHALLENGE
            </h1>
            <p class="text-purple-300 text-lg">✨ Optimisez votre mission gaming solidaire</p>
        </div>

        <!-- FORMULAIRE -->
        <div class="card rounded-3xl p-8 md:p-12">
            
            <!-- Message de succès/erreur -->
            <div id="messageContainer" class="mb-6 hidden"></div>

            <form id="editChallengeForm" class="space-y-6" novalidate>
                
                <!-- Association -->
                <div>
                    <label for="id_association" class="block text-lg font-semibold text-purple-300 mb-3 label-required">
                        🏢 Association
                    </label>
                    <select 
                        id="id_association" 
                        name="id_association" 
                        class="w-full px-6 py-4 bg-slate-800 text-white rounded-xl border-2 border-purple-500/30 focus:border-purple-500 input-glow outline-none text-lg"
                        required>
                        <option value="">Sélectionnez une association...</option>
                        <?php foreach ($associations as $assoc): ?>
                            <option value="<?= $assoc['id_association'] ?>" 
                                    <?= $assoc['id_association'] == $challenge->getIdAssociation() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($assoc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-red-400 text-sm mt-2 hidden" id="error-id_association">
                        <i data-feather="alert-circle" class="inline w-4 h-4"></i>
                        Veuillez sélectionner une association
                    </p>
                </div>

                <!-- Nom du Challenge -->
                <div>
                    <label for="name" class="block text-lg font-semibold text-purple-300 mb-3 label-required">
                        🎮 Nom du Challenge
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?= htmlspecialchars($challenge->getName()) ?>"
                        class="w-full px-6 py-4 bg-slate-800 text-white rounded-xl border-2 border-purple-500/30 focus:border-purple-500 input-glow outline-none text-lg"
                        placeholder="Ex: 10 kills Fortnite, Marathon 24h..."
                        required>
                    <p class="text-red-400 text-sm mt-2 hidden" id="error-name">
                        <i data-feather="alert-circle" class="inline w-4 h-4"></i>
                        Le nom du challenge est requis (min. 3 caractères)
                    </p>
                </div>

                <!-- Objectif -->
                <div>
                    <label for="objectif" class="block text-lg font-semibold text-purple-300 mb-3 label-required">
                        💰 Objectif de Dons (€)
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="objectif" 
                            name="objectif" 
                            value="<?= $challenge->getObjectif() ?>"
                            step="0.01" 
                            min="10"
                            class="w-full px-6 py-4 bg-slate-800 text-white rounded-xl border-2 border-purple-500/30 focus:border-purple-500 input-glow outline-none text-lg pr-12"
                            placeholder="100.00"
                            required>
                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-purple-400 text-xl font-bold">€</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2 flex items-center gap-2">
                        <i data-feather="info" class="w-4 h-4"></i>
                        Minimum 10€ pour lancer un challenge
                    </p>
                    <p class="text-red-400 text-sm mt-2 hidden" id="error-objectif">
                        <i data-feather="alert-circle" class="inline w-4 h-4"></i>
                        L'objectif doit être d'au moins 10€
                    </p>
                </div>

                <!-- Progression (lecture seule) -->
                <div>
                    <label class="block text-lg font-semibold text-cyan-300 mb-3">
                        📊 Progression Actuelle
                    </label>
                    <div class="bg-slate-800 p-6 rounded-xl border-2 border-cyan-500/30">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-yellow-400 font-bold text-2xl">
                                <?= number_format($challenge->getProgression(), 2) ?> €
                            </span>
                            <span class="text-gray-400 text-lg">
                                sur <?= number_format($challenge->getObjectif(), 2) ?> €
                            </span>
                        </div>
                        
                        <div class="w-full bg-gray-700 rounded-full h-3 mb-2">
                            <div class="bg-gradient-to-r from-purple-500 to-cyan-500 h-3 rounded-full transition-all duration-500" 
                                 style="width: <?= $challenge->getPourcentage() ?>%"></div>
                        </div>
                        
                        <div class="text-center">
                            <span class="text-emerald-400 font-bold text-xl"><?= $challenge->getPourcentage() ?>%</span>
                            <span class="text-gray-400"> complété</span>
                        </div>
                    </div>
                </div>

                <!-- Récompense -->
                <div>
                    <label for="recompense" class="block text-lg font-semibold text-purple-300 mb-3 label-required">
                        🏆 Récompense
                    </label>
                    <textarea 
                        id="recompense" 
                        name="recompense" 
                        rows="3"
                        class="w-full px-6 py-4 bg-slate-800 text-white rounded-xl border-2 border-purple-500/30 focus:border-purple-500 input-glow outline-none text-lg resize-none"
                        placeholder="Badge Épique, Shoutout, NFT Solidaire..."
                        required><?= htmlspecialchars($challenge->getRecompense()) ?></textarea>
                    <p class="text-red-400 text-sm mt-2 hidden" id="error-recompense">
                        <i data-feather="alert-circle" class="inline w-4 h-4"></i>
                        La récompense est requise (min. 5 caractères)
                    </p>
                </div>

                <!-- Note informative -->
                <div class="bg-blue-900/30 border-2 border-blue-500/50 rounded-xl p-5">
                    <div class="flex gap-3">
                        <i data-feather="info" class="text-blue-400 flex-shrink-0 mt-1"></i>
                        <div class="text-blue-200">
                            <p class="font-semibold mb-1">💡 Conseil Pro</p>
                            <p class="text-sm">Plus votre challenge est créatif et motivant, plus vous mobiliserez la communauté gaming !</p>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="flex-1 btn-primary text-white font-bold py-4 px-8 rounded-xl text-lg flex items-center justify-center gap-3">
                        <i data-feather="save" class="w-5 h-5"></i>
                        <span>Mettre à jour le Challenge</span>
                    </button>
                    
                    <a 
                        href="index.php" 
                        class="flex-1 btn-secondary text-white font-bold py-4 px-8 rounded-xl text-lg flex items-center justify-center gap-3 text-center">
                        <i data-feather="x-circle" class="w-5 h-5"></i>
                        <span>Annuler</span>
                    </a>
                </div>

            </form>
        </div>

        <!-- Stats rapides -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="card rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-cyan-400">
                    <?= number_format($challenge->getObjectif(), 0) ?> €
                </div>
                <div class="text-gray-400 mt-2">Objectif</div>
            </div>
            
            <div class="card rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-yellow-400">
                    <?= number_format($challenge->getProgression(), 0) ?> €
                </div>
                <div class="text-gray-400 mt-2">Progression</div>
            </div>
            
            <div class="card rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-emerald-400">
                    <?= $challenge->getPourcentage() ?>%
                </div>
                <div class="text-gray-400 mt-2">Complété</div>
            </div>
        </div>

    </div>

    <script>
        // Initialiser Feather Icons
        feather.replace();

        // Sélection des éléments
        const form = document.getElementById('editChallengeForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageContainer = document.getElementById('messageContainer');

        // Fonction de validation en temps réel
        function validateField(fieldId, validationFn, errorMsg) {
            const field = document.getElementById(fieldId);
            const errorEl = document.getElementById('error-' + fieldId);
            
            field.addEventListener('blur', function() {
                if (!validationFn(field.value)) {
                    field.classList.add('border-red-500', 'error-shake');
                    field.classList.remove('border-purple-500/30');
                    errorEl.classList.remove('hidden');
                    setTimeout(() => field.classList.remove('error-shake'), 500);
                } else {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-green-500');
                    errorEl.classList.add('hidden');
                }
            });

            field.addEventListener('input', function() {
                if (validationFn(field.value)) {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-green-500');
                    errorEl.classList.add('hidden');
                }
            });
        }

        // Validations spécifiques
        validateField('id_association', (val) => val !== '', 'Veuillez sélectionner une association');
        validateField('name', (val) => val.trim().length >= 3, 'Le nom doit contenir au moins 3 caractères');
        validateField('objectif', (val) => parseFloat(val) >= 10, 'L\'objectif doit être d\'au moins 10€');
        validateField('recompense', (val) => val.trim().length >= 5, 'La récompense doit contenir au moins 5 caractères');

        // Afficher un message
        function showMessage(type, message) {
            messageContainer.className = `mb-6 p-5 rounded-xl border-2 flex items-start gap-3 ${
                type === 'success' 
                    ? 'bg-green-900/30 border-green-500 text-green-200' 
                    : 'bg-red-900/30 border-red-500 text-red-200'
            }`;
            
            const icon = type === 'success' ? 'check-circle' : 'x-circle';
            messageContainer.innerHTML = `
                <i data-feather="${icon}" class="flex-shrink-0 mt-1"></i>
                <div class="flex-1">
                    <p class="font-bold mb-1">${type === 'success' ? 'Succès !' : 'Erreur !'}</p>
                    <p>${message}</p>
                </div>
            `;
            messageContainer.classList.remove('hidden');
            messageContainer.classList.add(type === 'success' ? 'success-pulse' : 'error-shake');
            
            feather.replace();
            
            // Scroll vers le message
            messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Validation complète du formulaire
        function validateForm() {
            const idAssociation = document.getElementById('id_association').value;
            const name = document.getElementById('name').value.trim();
            const objectif = parseFloat(document.getElementById('objectif').value);
            const recompense = document.getElementById('recompense').value.trim();

            const errors = [];

            if (!idAssociation) {
                errors.push('Veuillez sélectionner une association');
                document.getElementById('error-id_association').classList.remove('hidden');
            }

            if (name.length < 3) {
                errors.push('Le nom du challenge doit contenir au moins 3 caractères');
                document.getElementById('error-name').classList.remove('hidden');
            }

            if (objectif < 10 || isNaN(objectif)) {
                errors.push('L\'objectif doit être d\'au moins 10€');
                document.getElementById('error-objectif').classList.remove('hidden');
            }

            if (recompense.length < 5) {
                errors.push('La récompense doit contenir au moins 5 caractères');
                document.getElementById('error-recompense').classList.remove('hidden');
            }

            return errors;
        }

        // Soumission du formulaire
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validation
            const errors = validateForm();
            if (errors.length > 0) {
                showMessage('error', errors.join('<br>'));
                return;
            }

            // Désactiver le bouton
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Mise à jour en cours...</span>
            `;

            const formData = new FormData(form);

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('success', data.message);
                    
                    // Redirection après 2 secondes
                    setTimeout(() => {
                        window.location.href = 'index.php?success=challenge_updated';
                    }, 2000);
                } else {
                    showMessage('error', data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    feather.replace();
                }
            } catch (error) {
                console.error('Erreur:', error);
                showMessage('error', 'Erreur réseau. Veuillez réessayer.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                feather.replace();
            }
        });

        // Animation au chargement
        window.addEventListener('load', function() {
            document.querySelectorAll('.card').forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>

</body>
</html>