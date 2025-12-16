<?php
require_once '../../config/config.php';
require_once '../../controllers/ChallengeController.php';

$challengeC = new ChallengeController();

// Récupérer toutes les associations pour le filtre
$associations = config::getConnexion()->query("SELECT * FROM association ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer l'association sélectionnée
$selectedAssoc = isset($_GET['id_association']) ? intval($_GET['id_association']) : null;

// Récupérer les challenges de l'association sélectionnée
$challenges = [];
$assocDetails = null;

if ($selectedAssoc) {
    // Détails de l'association
    $stmt = config::getConnexion()->prepare("SELECT * FROM association WHERE id_association = ?");
    $stmt->execute([$selectedAssoc]);
    $assocDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Challenges avec jointure
    $challenges = $challengeC->getChallengesByAssociation($selectedAssoc);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Challenges - Play to Help</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #22d3ee, transparent);
            animation: scan 8s linear infinite;
            pointer-events: none;
            z-index: 9999;
        }
        @keyframes scan {
            0% { transform: translateY(0); opacity: 0.5; }
            50% { opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0.5; }
        }
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .glow-card:hover {
            box-shadow: 0 0 50px rgba(167, 139, 250, 0.5);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .challenge-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .challenge-item:hover {
            transform: scale(1.02);
            background: rgba(167, 139, 250, 0.1);
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .stats-badge {
            background: linear-gradient(135deg, rgba(167, 139, 250, 0.2), rgba(34, 211, 238, 0.2));
            border: 2px solid rgba(167, 139, 250, 0.5);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="relative">
    <div class="scanline"></div>

    <div class="container mx-auto px-4 py-12 max-w-7xl">
        
        <!-- HEADER -->
        <div class="mb-8 flex items-center justify-between">
            <a href="indexsinda.php" class="flex items-center gap-2 text-purple-400 hover:text-purple-300 transition group">
                <i data-feather="arrow-left" class="group-hover:-translate-x-1 transition"></i>
                <span class="font-medium">Retour au Dashboard</span>
            </a>
        </div>

        <!-- TITRE PRINCIPAL -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-bold font-orbitron neon animate-pulse mb-3">
                🎮 ANALYTICS CHALLENGES
            </h1>
            <p class="text-purple-300 text-xl">Explorez les performances par association</p>
        </div>

        <!-- FORMULAIRE DE SÉLECTION -->
        <div class="card rounded-3xl p-8 mb-12 glow-card">
            <form method="GET" action="" class="flex flex-col md:flex-row gap-6 items-end">
                <div class="flex-1">
                    <label for="id_association" class="block text-lg font-semibold text-purple-300 mb-3">
                        🏢 Sélectionnez une Association
                    </label>
                    <select 
                        id="id_association" 
                        name="id_association" 
                        class="w-full px-6 py-4 bg-slate-800 text-white rounded-xl border-2 border-purple-500/30 focus:border-purple-500 outline-none text-lg"
                        onchange="this.form.submit()">
                        <option value="">-- Choisir une association --</option>
                        <?php foreach ($associations as $assoc): ?>
                            <option value="<?= $assoc['id_association'] ?>" 
                                    <?= $selectedAssoc == $assoc['id_association'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($assoc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-purple-500 to-cyan-500 text-white font-bold rounded-xl hover:scale-105 transition">
                    <i data-feather="search" class="inline w-5 h-5 mr-2"></i>
                    Analyser
                </button>
            </form>
        </div>

        <?php if ($selectedAssoc && $assocDetails): ?>
            
            <!-- DÉTAILS DE L'ASSOCIATION -->
            <div class="card rounded-3xl p-8 mb-12 glow-card">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-cyan-500 flex items-center justify-center text-6xl">
                        🏆
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-4xl font-bold text-cyan-400 mb-2"><?= htmlspecialchars($assocDetails['name']) ?></h2>
                        <p class="text-gray-300 text-lg"><?= htmlspecialchars($assocDetails['description'] ?? 'Association humanitaire') ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($challenges)): ?>
                
                <!-- STATISTIQUES GLOBALES -->
                <?php
                $totalObjectif = array_sum(array_column($challenges, 'objectif'));
                $totalProgression = array_sum(array_column($challenges, 'progression'));
                $nbChallenges = count($challenges);
                $avgPourcentage = $nbChallenges > 0 ? round(($totalProgression / $totalObjectif) * 100, 1) : 0;
                ?>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                    <div class="stats-badge rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-cyan-400"><?= $nbChallenges ?></div>
                        <div class="text-gray-300 mt-2">Challenges Actifs</div>
                    </div>
                    
                    <div class="stats-badge rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-yellow-400"><?= number_format($totalObjectif, 0) ?> €</div>
                        <div class="text-gray-300 mt-2">Objectif Total</div>
                    </div>
                    
                    <div class="stats-badge rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-emerald-400"><?= number_format($totalProgression, 0) ?> €</div>
                        <div class="text-gray-300 mt-2">Collecté</div>
                    </div>
                    
                    <div class="stats-badge rounded-2xl p-6 text-center">
                        <div class="text-4xl font-bold text-purple-400"><?= $avgPourcentage ?>%</div>
                        <div class="text-gray-300 mt-2">Moyenne</div>
                    </div>
                </div>

                <!-- GRAPHIQUE -->
                <div class="card rounded-3xl p-8 mb-12">
                    <h3 class="text-2xl font-bold text-center text-purple-300 mb-8">
                        📊 Progression des Challenges
                    </h3>
                    <canvas id="challengeChart" height="80"></canvas>
                </div>

                <!-- LISTE DES CHALLENGES -->
                <div class="card rounded-3xl p-8">
                    <h3 class="text-3xl font-bold text-center text-purple-300 mb-8 neon">
                        🎯 Liste Complète des Challenges
                    </h3>
                    
                    <div class="space-y-6">
                        <?php foreach ($challenges as $challenge): ?>
                            <?php
                            $objectif = floatval($challenge['objectif']);
                            $progression = floatval($challenge['progression']);
                            $pourcentage = $objectif > 0 ? min(100, round(($progression / $objectif) * 100, 2)) : 0;
                            ?>
                            
                            <div class="challenge-item bg-slate-800/50 rounded-2xl p-6 border-2 border-purple-500/30">
                                <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                                    
                                    <!-- Info Challenge -->
                                    <div class="flex-1">
                                        <h4 class="text-2xl font-bold text-cyan-400 mb-2">
                                            <?= htmlspecialchars($challenge['name']) ?>
                                        </h4>
                                        <p class="text-gray-300 mb-3">
                                            🏆 <strong>Récompense:</strong> <?= htmlspecialchars($challenge['recompense']) ?>
                                        </p>
                                        <div class="flex flex-wrap gap-4 text-sm">
                                            <span class="px-3 py-1 bg-purple-900/50 rounded-full text-purple-300">
                                                ID: #<?= $challenge['id_challenge'] ?>
                                            </span>
                                            <span class="px-3 py-1 bg-cyan-900/50 rounded-full text-cyan-300">
                                                Objectif: <?= number_format($objectif, 2) ?> €
                                            </span>
                                            <span class="px-3 py-1 bg-emerald-900/50 rounded-full text-emerald-300">
                                                Collecté: <?= number_format($progression, 2) ?> €
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Progression Circulaire -->
                                    <div class="relative w-32 h-32 flex-shrink-0">
                                        <svg class="w-full h-full progress-ring" viewBox="0 0 120 120">
                                            <!-- Background circle -->
                                            <circle cx="60" cy="60" r="50" 
                                                    fill="none" 
                                                    stroke="rgba(100, 116, 139, 0.3)" 
                                                    stroke-width="10"/>
                                            <!-- Progress circle -->
                                            <circle cx="60" cy="60" r="50" 
                                                    fill="none" 
                                                    stroke="url(#gradient<?= $challenge['id_challenge'] ?>)" 
                                                    stroke-width="10"
                                                    stroke-linecap="round"
                                                    stroke-dasharray="<?= 314 * ($pourcentage / 100) ?>, 314"/>
                                            <defs>
                                                <linearGradient id="gradient<?= $challenge['id_challenge'] ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#a78bfa;stop-opacity:1" />
                                                    <stop offset="100%" style="stop-color:#22d3ee;stop-opacity:1" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-3xl font-bold text-white"><?= $pourcentage ?>%</span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-3">
                                        <a href="editchallenge.php?id=<?= $challenge['id_challenge'] ?>" 
                                           class="px-4 py-2 bg-yellow-600 hover:bg-yellow-500 text-white rounded-lg transition">
                                            <i data-feather="edit-2" class="inline w-4 h-4"></i>
                                        </a>
                                        <a href="deletechallenge.php?id=<?= $challenge['id_challenge'] ?>" 
                                           onclick="return confirm('Supprimer ce challenge ?')"
                                           class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition">
                                            <i data-feather="trash-2" class="inline w-4 h-4"></i>
                                        </a>
                                    </div>

                                </div>

                                <!-- Barre de progression linéaire -->
                                <div class="mt-4">
                                    <div class="w-full bg-gray-700 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-purple-500 to-cyan-500 h-3 rounded-full transition-all duration-500" 
                                             style="width: <?= $pourcentage ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SCRIPT GRAPHIQUE -->
                <script>
                    const challengeData = {
                        labels: <?= json_encode(array_column($challenges, 'name')) ?>,
                        objectifs: <?= json_encode(array_column($challenges, 'objectif')) ?>,
                        progressions: <?= json_encode(array_column($challenges, 'progression')) ?>
                    };

                    new Chart(document.getElementById('challengeChart'), {
                        type: 'bar',
                        data: {
                            labels: challengeData.labels,
                            datasets: [
                                {
                                    label: 'Objectif (€)',
                                    data: challengeData.objectifs,
                                    backgroundColor: 'rgba(167, 139, 250, 0.5)',
                                    borderColor: '#a78bfa',
                                    borderWidth: 2
                                },
                                {
                                    label: 'Progression (€)',
                                    data: challengeData.progressions,
                                    backgroundColor: 'rgba(34, 211, 238, 0.5)',
                                    borderColor: '#22d3ee',
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#e2e8f0',
                                        font: { size: 14, family: 'Space Mono' }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#e2e8f0',
                                        callback: function(value) {
                                            return value + ' €';
                                        }
                                    },
                                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                                },
                                x: {
                                    ticks: { color: '#e2e8f0' },
                                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                                }
                            }
                        }
                    });
                </script>

            <?php else: ?>
                
                <!-- AUCUN CHALLENGE -->
                <div class="card rounded-3xl p-16 text-center">
                    <div class="text-8xl mb-6">🎮</div>
                    <h3 class="text-3xl font-bold text-purple-300 mb-4">Aucun challenge pour cette association</h3>
                    <p class="text-gray-400 mb-8">Créez le premier challenge et mobilisez la communauté !</p>
                    <a href="../frontoffice/don.php#modalChallenge" 
                       class="inline-block px-8 py-4 bg-gradient-to-r from-purple-500 to-cyan-500 text-white font-bold rounded-xl hover:scale-105 transition">
                        <i data-feather="plus-circle" class="inline w-5 h-5 mr-2"></i>
                        Créer un Challenge
                    </a>
                </div>

            <?php endif; ?>

        <?php else: ?>
            
            <!-- MESSAGE INITIAL -->
            <div class="card rounded-3xl p-16 text-center">
                <div class="text-8xl mb-6">🔍</div>
                <h3 class="text-3xl font-bold text-purple-300 mb-4">Explorez les Challenges par Association</h3>
                <p class="text-gray-400 text-lg">Sélectionnez une association ci-dessus pour voir ses challenges et statistiques</p>
            </div>

        <?php endif; ?>

    </div>

    <script>
        feather.replace();
    </script>

</body>
</html>