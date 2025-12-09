<?php
require_once '../../config/db.php';
require_once '../../controllers/DonController.php';
require_once '../../controllers/ChallengeController.php';

// ---- DON DATA ---- //
$donC = new DonController();
$dons = $donC->list();

// Stats générales
$totalCollecte = array_sum(array_column($dons, 'montant'));
$nombreDons    = count($dons);
$associations  = count(array_unique(array_column($dons, 'id_association')));

// Répartition par association (doughnut)
$statsAssoc = [];
foreach ($dons as $d) {
    $nom = $d['association_nom'] ?? 'Autre';
    $statsAssoc[$nom] = ($statsAssoc[$nom] ?? 0) + $d['montant'];
}

// Évolution mensuelle (line chart)
$monthly = array_fill(0, 12, 0);
foreach ($dons as $d) {
    $mois = date('n', strtotime($d['date_don'])) - 1;
    $monthly[$mois] += $d['montant'];
}

// ---- CHALLENGE DATA ---- //
$challengeC = new ChallengeController();
$challenges = $challengeC->list();

// Liste des associations pour le filtre
$assocQuery = $conn->query("SELECT id_association as id, name as nom FROM association ORDER BY name");
$associationsList = [];
while ($row = $assocQuery->fetch_assoc()) {
    $associationsList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Backoffice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Space Mono', monospace; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
        }
        .card { 
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(34, 211, 238, 0.3); 
        }
        .neon { 
            text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; 
        }
        .glow:hover { 
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); 
        }
        .scanline { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 4px; 
            background: linear-gradient(90deg, transparent, #22d3ee, transparent); 
            animation: scan 6s linear infinite; 
        }
        @keyframes scan { 
            0% { transform: translateY(-100%); } 
            100% { transform: translateY(100vh); } 
        }
        .font-orbitron { 
            font-family: 'Orbitron', sans-serif; 
        }
        
        /* Styles pour l'édition inline */
        .editable-cell {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .editable-cell:hover {
            background: rgba(167, 139, 250, 0.1);
            border: 1px solid rgba(167, 139, 250, 0.3);
        }
        .edit-input {
            background: rgba(30, 41, 59, 0.9);
            border: 2px solid #a78bfa;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            width: 120px;
            font-size: 16px;
            outline: none;
        }
        .edit-input:focus {
            box-shadow: 0 0 20px rgba(167, 139, 250, 0.5);
        }
        .save-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .save-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }
        .cancel-btn {
            background: rgba(239, 68, 68, 0.8);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }
        
        /* Navigation Buttons */
        .nav-btn {
            background: linear-gradient(135deg, rgba(167, 139, 250, 0.2), rgba(34, 211, 238, 0.2));
            border: 2px solid rgba(167, 139, 250, 0.5);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .nav-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(167, 139, 250, 0.6);
            border-color: #a78bfa;
        }
        
        /* Dropdown Tri - Design Gaming Créatif */
        .sort-dropdown {
            position: relative;
        }
        .sort-button {
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.15), rgba(6, 182, 212, 0.15));
            border: 2px solid rgba(34, 211, 238, 0.5);
            color: #22d3ee;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 600;
        }
        .sort-button:hover {
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.25), rgba(6, 182, 212, 0.25));
            box-shadow: 0 0 25px rgba(34, 211, 238, 0.5);
            transform: translateY(-2px);
        }
        .sort-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(30, 41, 59, 0.95);
            border: 2px solid rgba(34, 211, 238, 0.4);
            border-radius: 10px;
            margin-top: 5px;
            display: none;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        .sort-menu.active {
            display: block;
        }
        .sort-option {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #e2e8f0;
        }
        .sort-option:hover {
            background: rgba(34, 211, 238, 0.2);
            color: #22d3ee;
        }
        
        /* Dropdown Challenges - Design Gaming Créatif */
        .sort-button-challenges {
            background: linear-gradient(135deg, rgba(167, 139, 250, 0.15), rgba(139, 92, 246, 0.15));
            border-color: rgba(167, 139, 250, 0.5);
            color: #a78bfa;
        }
        .sort-button-challenges:hover {
            background: linear-gradient(135deg, rgba(167, 139, 250, 0.25), rgba(139, 92, 246, 0.25));
            box-shadow: 0 0 25px rgba(167, 139, 250, 0.5);
        }
        .sort-menu-challenges {
            border-color: rgba(167, 139, 250, 0.5);
        }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">
    <div class="scanline"></div>
    
    <main class="container mx-auto px-6 py-12 max-w-7xl">
        <!-- TITRE FUTURISTE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">PLAY2HELP</h1>
            <p class="text-cyan-400 text-xl mt-4">⚡ Live Dashboard</p>
        </div>

        <!-- NAVIGATION VERS NOUVELLES PAGES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12 max-w-4xl mx-auto">
            <!-- Analytics Challenges -->
            <a href="challenge_analytics.php" class="nav-btn rounded-2xl p-8 text-center block group">
                <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">📊</div>
                <h3 class="text-2xl font-bold text-purple-300 mb-2">Analytics Challenges</h3>
                <p class="text-gray-400">Explorez les performances par association</p>
                <div class="mt-4 text-cyan-400 font-semibold">Voir les stats <i data-feather="arrow-right" class="inline w-4 h-4 ml-1"></i></div>
            </a>
            
            <!-- Retour Front -->
            <a href="../frontoffice/don.php" class="nav-btn rounded-2xl p-8 text-center block group">
                <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">🎮</div>
                <h3 class="text-2xl font-bold text-emerald-300 mb-2">Espace Public</h3>
                <p class="text-gray-400">Retour à l'interface utilisateur</p>
                <div class="mt-4 text-cyan-400 font-semibold">Accéder <i data-feather="arrow-right" class="inline w-4 h-4 ml-1"></i></div>
            </a>
        </div>

        <!-- 4 CARTES DE STATS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-cyan-400 neon"><?= number_format($totalCollecte, 0, '', ' ') ?> €</h3>
                <p class="text-gray-300 mt-3 text-lg">Total collecté</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-emerald-400"><?= $nombreDons ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Dons reçus</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-purple-400"><?= $associations ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Associations</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-yellow-400 animate-pulse">LIVE</h3>
                <p class="text-gray-300 mt-3 text-lg">En direct</p>
            </div>
        </div>

        <!-- GRAPHIQUES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Répartition des dons</h2>
                <canvas id="donutChart"></canvas>
            </div>
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Progression 2025</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <!-- TABLEAU DES DONS AVEC FILTRAGE ET RECHERCHE -->
        <div class="card rounded-3xl p-10 glow mb-20">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Tous les dons • <?= $nombreDons ?></h2>
                <a href="../frontoffice/don.php" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    ➕ Nouveau don
                </a>
            </div>
            
            <!-- Filtres et Recherche - Design Gaming Créatif -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm text-cyan-300 mb-2 font-bold">🔍 Recherche Rapide</label>
                    <input type="text" id="searchInput" placeholder="Nom, email, association..." 
                           class="w-full bg-gradient-to-r from-cyan-900/30 to-blue-900/30 border-2 border-cyan-500/50 rounded-xl px-4 py-3 focus:border-cyan-400 focus:outline-none text-white font-medium transition-all hover:shadow-lg hover:shadow-cyan-500/30">
                </div>
                
                <div>
                    <label class="block text-sm text-cyan-300 mb-2 font-bold">🏢 Association</label>
                    <select id="filterAssociation" class="w-full bg-gradient-to-r from-emerald-900/30 to-teal-900/30 border-2 border-emerald-500/50 rounded-xl px-4 py-3 focus:border-emerald-400 focus:outline-none text-white font-medium transition-all hover:shadow-lg hover:shadow-emerald-500/30">
                        <option value="">🌍 Toutes</option>
                        <?php foreach ($associationsList as $assoc): ?>
                            <option value="<?php echo $assoc['id']; ?>"><?php echo htmlspecialchars($assoc['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm text-cyan-300 mb-2 font-bold">⚡ Tri Rapide</label>
                    <div class="sort-dropdown">
                        <button class="sort-button" onclick="toggleSortMenu(event)">
                            <span>⚡</span>
                            <span id="sortLabel">Trier...</span>
                            <span>▼</span>
                        </button>
                        <div class="sort-menu" id="sortMenu">
                            <div class="sort-option" onclick="sortDons('date_desc')">📅 Date (↓ récent)</div>
                            <div class="sort-option" onclick="sortDons('date_asc')">📅 Date (↑ ancien)</div>
                            <div class="sort-option" onclick="sortDons('montant_desc')">💰 Montant (↓)</div>
                            <div class="sort-option" onclick="sortDons('montant_asc')">💰 Montant (↑)</div>
                            <div class="sort-option" onclick="sortDons('nom_asc')">👤 Nom (A→Z)</div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-400 mb-2 opacity-0">Reset</label>
                    <button onclick="resetFiltersDons()" 
                            class="w-full bg-gradient-to-r from-red-900/30 to-orange-900/30 border-2 border-red-500/50 rounded-xl px-4 py-3 text-white font-bold transition-all hover:shadow-lg hover:shadow-red-500/30 hover:scale-105">
                        🔄 Reset
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-cyan-500 text-cyan-400">
                            <th class="py-4 px-6">Date</th>
                            <th class="py-4 px-6">Donateur</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6">Montant</th>
                            <th class="py-4 px-6">Association</th>
                            <th class="py-4 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="donsTableBody">
                        <?php foreach ($dons as $d): 
                            $don_id = $d['id_don'] ?? $d['id'];
                            $don_nom = ($d['prenom'] ? $d['prenom'] . ' ' : '') . $d['nom'];
                        ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition don-row"
                            data-id="<?php echo $don_id; ?>"
                            data-nom="<?php echo htmlspecialchars($don_nom); ?>"
                            data-email="<?php echo htmlspecialchars($d['email'] ?? ''); ?>"
                            data-montant="<?php echo $d['montant']; ?>"
                            data-association="<?php echo htmlspecialchars($d['association_nom'] ?? ''); ?>"
                            data-association-id="<?php echo $d['id_association']; ?>"
                            data-date="<?php echo $d['date_don']; ?>">
                            <td class="py-5 px-6"><?= date('d/m/Y', strtotime($d['date_don'])) ?></td>
                            <td class="py-5 px-6 font-medium"><?= htmlspecialchars($don_nom) ?></td>
                            <td class="py-5 px-6 text-gray-400"><?= htmlspecialchars($d['email'] ?? 'N/A') ?></td>
                            <td class="py-5 px-6 text-2xl font-bold text-emerald-400"><?= number_format($d['montant'], 2) ?> €</td>
                            <td class="py-5 px-6 text-cyan-400 font-medium"><?= htmlspecialchars($d['association_nom'] ?? '—') ?></td>
                            <td class="py-5 px-6 text-center">
                                <div class="flex justify-center gap-6">
                                    <a href="edit.php?id=<?= $don_id ?>" class="text-yellow-400 hover:text-yellow-300">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $don_id ?>" onclick="return confirm('Supprimer ce don ?')" class="text-red-500 hover:text-red-400">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- TABLEAU DES CHALLENGES AVEC ÉDITION INLINE ET TRI -->
        <div class="card rounded-3xl p-10 glow mt-20">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Challenges • <?= count($challenges) ?></h2>
                <a href="../frontoffice/don.php#modalChallenge" class="bg-gradient-to-r from-purple-500 to-cyan-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    ➕ Nouveau Challenge
                </a>
            </div>
            
            <!-- Filtres et Tri Challenges - Design Gaming Créatif -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Filtre Range Min-Max -->
                <div class="col-span-2">
                    <label class="block text-sm text-purple-300 mb-2 font-bold">💰 Plage d'Objectif (€)</label>
                    <div class="flex gap-2">
                        <input type="number" 
                               id="filterMinChallenges" 
                               placeholder="Min..." 
                               step="1"
                               class="w-1/2 bg-gradient-to-r from-purple-900/30 to-purple-800/30 border-2 border-purple-500/50 rounded-xl px-4 py-3 focus:border-purple-400 focus:outline-none text-white text-center font-bold transition-all hover:shadow-lg hover:shadow-purple-500/30">
                        <span class="text-purple-400 text-2xl self-center">→</span>
                        <input type="number" 
                               id="filterMaxChallenges" 
                               placeholder="Max..." 
                               step="1"
                               class="w-1/2 bg-gradient-to-r from-purple-800/30 to-pink-900/30 border-2 border-pink-500/50 rounded-xl px-4 py-3 focus:border-pink-400 focus:outline-none text-white text-center font-bold transition-all hover:shadow-lg hover:shadow-pink-500/30">
                    </div>
                </div>
                
                <!-- Bouton Reset Filtres -->
                <div>
                    <label class="block text-sm text-gray-400 mb-2 opacity-0">Reset</label>
                    <button onclick="resetFiltersChallenges()" 
                            class="w-full bg-gradient-to-r from-red-900/30 to-orange-900/30 border-2 border-red-500/50 rounded-xl px-4 py-3 text-white font-bold transition-all hover:shadow-lg hover:shadow-red-500/30 hover:scale-105">
                        🔄 Reset
                    </button>
                </div>
                
                <!-- Tri Créatif -->
                <div>
                    <label class="block text-sm text-purple-300 mb-2 font-bold">⚡ Tri Rapide</label>
                    <div class="sort-dropdown">
                        <button class="sort-button sort-button-challenges" onclick="toggleSortMenuChallenges(event)">
                            <span>⚡</span>
                            <span id="sortLabelChallenges">Trier...</span>
                            <span>▼</span>
                        </button>
                        <div class="sort-menu sort-menu-challenges" id="sortMenuChallenges">
                            <div class="sort-option" onclick="sortChallenges('id_desc')">🔢 ID (↓ élevé)</div>
                            <div class="sort-option" onclick="sortChallenges('id_asc')">🔢 ID (↑ bas)</div>
                            <div class="sort-option" onclick="sortChallenges('nom_asc')">📝 Nom (A→Z)</div>
                            <div class="sort-option" onclick="sortChallenges('objectif_desc')">🎯 Objectif (↓)</div>
                            <div class="sort-option" onclick="sortChallenges('progression_desc')">📊 Progression (↓)</div>
                            <div class="sort-option" onclick="sortChallenges('pourcentage_desc')">💯 % (↓)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-purple-500 text-purple-300">
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Nom du Challenge</th>
                            <th class="py-4 px-6">Association</th>
                            <th class="py-4 px-6">Objectif (€)</th>
                            <th class="py-4 px-6">Progression (€)</th>
                            <th class="py-4 px-6">Pourcentage</th>
                            <th class="py-4 px-6">Récompense</th>
                            <th class="py-4 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="challengesTableBody">
                        <?php foreach ($challenges as $c): 
                            $challenge_id = $c['id_challenge'] ?? $c['id'];
                            $challenge_name = $c['name'] ?? $c['nom'] ?? 'N/A';
                            $objectif = floatval($c['objectif'] ?? 0);
                            $progression = floatval($c['progression'] ?? 0);
                            $pourcentage = $objectif > 0 ? min(100, round(($progression / $objectif) * 100, 2)) : 0;
                        ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition challenge-row" 
                            data-challenge-id="<?= $challenge_id ?>"
                            data-id="<?= $challenge_id ?>"
                            data-nom="<?= htmlspecialchars($challenge_name) ?>"
                            data-objectif="<?= $objectif ?>"
                            data-progression="<?= $progression ?>"
                            data-pourcentage="<?= $pourcentage ?>">
                            <!-- ID -->
                            <td class="py-5 px-6 text-gray-400">#<?= htmlspecialchars($challenge_id) ?></td>
                            
                            <!-- Nom du Challenge -->
                            <td class="py-5 px-6 text-cyan-300 font-medium"><?= htmlspecialchars($challenge_name) ?></td>
                            
                            <!-- Association -->
                            <td class="py-5 px-6 text-purple-400"><?= htmlspecialchars($c['association_nom'] ?? 'Non définie') ?></td>
                            
                            <!-- Objectif (Non éditable) -->
                            <td class="py-5 px-6 text-emerald-400 font-bold"><?= number_format($objectif, 2) ?> €</td>
                            
                            <!-- Progression (Éditable) -->
                            <td class="py-5 px-6">
                                <div class="editable-cell text-yellow-400 font-bold" 
                                     data-field="progression" 
                                     data-value="<?= $progression ?>"
                                     title="Cliquez pour modifier">
                                    <span class="display-value"><?= number_format($progression, 2) ?> €</span>
                                </div>
                            </td>
                            
                            <!-- Pourcentage avec barre de progression -->
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-gray-700 rounded-full h-2.5">
                                        <div class="progress-bar bg-gradient-to-r from-purple-500 to-cyan-500 h-2.5 rounded-full transition-all duration-500" 
                                             style="width: <?= $pourcentage ?>%"></div>
                                    </div>
                                    <span class="percentage-display text-sm font-medium"><?= $pourcentage ?>%</span>
                                </div>
                            </td>
                            
                            <!-- Récompense -->
                            <td class="py-5 px-6 text-gray-300"><?= htmlspecialchars($c['recompense']) ?></td>
                            
                            <!-- Actions -->
                            <td class="py-5 px-6 text-center">
                                <div class="flex justify-center gap-6">
                                    <a href="../backoffice/editchallenge.php?id=<?= $challenge_id ?>" 
                                       class="text-yellow-400 hover:text-yellow-300 transition"
                                       title="Modifier">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                    <a href="../backoffice/deletechallenge.php?id=<?= $challenge_id ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce challenge ?')" 
                                       class="text-red-500 hover:text-red-400 transition"
                                       title="Supprimer">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($challenges)): ?>
                        <tr>
                            <td colspan="8" class="py-10 text-center text-gray-400 text-lg">
                                🎮 Aucun challenge pour le moment. Créez-en un !
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-center py-8 text-gray-500 border-t border-gray-800 mt-20">
        <p>Copyright © 2025 <span class="text-cyan-400 font-bold">Play to Help</span> - Gaming pour l'Humanitaire</p>
        <p class="text-sm mt-2">Dashboard Admin • Tous droits réservés</p>
    </footer>
    <!-- SCRIPTS -->
    <script>
        feather.replace();
        
        // ===== GRAPHIQUES =====
        // Doughnut
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($statsAssoc)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($statsAssoc)) ?>,
                    backgroundColor: ['#22d3ee','#10b981','#a78bfa','#f59e0b','#f87171','#06b6d4'],
                    borderColor: '#0f172a',
                    borderWidth: 4,
                    hoverOffset: 20
                }]
            },
            options: { 
                plugins: { 
                    legend: { 
                        labels: { 
                            color: '#e2e8f0',
                            font: {
                                size: 14,
                                family: 'Space Mono'
                            }
                        } 
                    } 
                } 
            }
        });
        
        // Line Chart
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'],
                datasets: [{
                    label: 'Dons (€)',
                    data: <?= json_encode($monthly) ?>,
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34, 211, 238, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointBackgroundColor: '#22d3ee',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: { 
                plugins: { 
                    legend: { 
                        display: false 
                    } 
                },
                scales: {
                    y: {
                        ticks: {
                            color: '#e2e8f0',
                            callback: function(value) {
                                return value + ' €';
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#e2e8f0'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });
        
        // ===== FILTRAGE ET RECHERCHE DONS =====
        const searchInput = document.getElementById('searchInput');
        const filterAssociation = document.getElementById('filterAssociation');
        const donsRows = document.querySelectorAll('.don-row');
        
        function filterDons() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedAssoc = filterAssociation.value;
            
            donsRows.forEach(row => {
                const nom = row.dataset.nom.toLowerCase();
                const email = row.dataset.email.toLowerCase();
                const association = row.dataset.association.toLowerCase();
                const associationId = row.dataset.associationId;
                
                const matchesSearch = nom.includes(searchTerm) || email.includes(searchTerm) || association.includes(searchTerm);
                const matchesAssoc = !selectedAssoc || associationId === selectedAssoc;
                
                if (matchesSearch && matchesAssoc) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', filterDons);
        filterAssociation.addEventListener('change', filterDons);
        
        // Fonction Reset pour les dons
        function resetFiltersDons() {
            searchInput.value = '';
            filterAssociation.value = '';
            document.getElementById('sortLabel').textContent = 'Trier...';
            filterDons();
            
            // Animation de reset
            const btn = event.target;
            btn.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                btn.style.transform = 'rotate(0deg)';
            }, 500);
        }
        
        // ===== TRI DONS =====
        function toggleSortMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('sortMenu');
            menu.classList.toggle('active');
        }
        
        function sortDons(type) {
            const tbody = document.getElementById('donsTableBody');
            const rows = Array.from(tbody.querySelectorAll('.don-row'));
            
            rows.sort((a, b) => {
                switch(type) {
                    case 'date_desc':
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                    case 'date_asc':
                        return new Date(a.dataset.date) - new Date(b.dataset.date);
                    case 'montant_desc':
                        return parseFloat(b.dataset.montant) - parseFloat(a.dataset.montant);
                    case 'montant_asc':
                        return parseFloat(a.dataset.montant) - parseFloat(b.dataset.montant);
                    case 'nom_asc':
                        return a.dataset.nom.localeCompare(b.dataset.nom);
                    default:
                        return 0;
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
            
            // Mettre à jour le label
            const labels = {
                'date_desc': '📅 Date (récent → ancien)',
                'date_asc': '📅 Date (ancien → récent)',
                'montant_desc': '💰 Montant (élevé → bas)',
                'montant_asc': '💰 Montant (bas → élevé)',
                'nom_asc': '👤 Nom (A → Z)'
            };
            document.getElementById('sortLabel').textContent = labels[type];
            document.getElementById('sortMenu').classList.remove('active');
        }
        
        // ===== FILTRAGE CHALLENGES PAR MIN/MAX =====
        const filterMinChallenges = document.getElementById('filterMinChallenges');
        const filterMaxChallenges = document.getElementById('filterMaxChallenges');
        const challengeRows = document.querySelectorAll('.challenge-row');
        
        function filterChallenges() {
            const minValue = parseFloat(filterMinChallenges.value) || 0;
            const maxValue = parseFloat(filterMaxChallenges.value) || Infinity;
            
            challengeRows.forEach(row => {
                const objectif = parseFloat(row.dataset.objectif);
                
                const matchesMin = objectif >= minValue;
                const matchesMax = objectif <= maxValue;
                
                if (matchesMin && matchesMax) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        filterMinChallenges.addEventListener('input', filterChallenges);
        filterMaxChallenges.addEventListener('input', filterChallenges);
        
        // Fonction Reset pour les challenges
        function resetFiltersChallenges() {
            filterMinChallenges.value = '';
            filterMaxChallenges.value = '';
            document.getElementById('sortLabelChallenges').textContent = 'Trier...';
            filterChallenges();
            
            // Animation de reset
            const btn = event.target;
            btn.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                btn.style.transform = 'rotate(0deg)';
            }, 500);
        }
        
        // ===== TRI CHALLENGES =====
        function toggleSortMenuChallenges(event) {
            event.stopPropagation();
            const menu = document.getElementById('sortMenuChallenges');
            menu.classList.toggle('active');
        }
        
        function sortChallenges(type) {
            const tbody = document.getElementById('challengesTableBody');
            const rows = Array.from(tbody.querySelectorAll('.challenge-row'));
            
            rows.sort((a, b) => {
                switch(type) {
                    case 'id_desc':
                        return parseInt(b.dataset.id) - parseInt(a.dataset.id);
                    case 'id_asc':
                        return parseInt(a.dataset.id) - parseInt(b.dataset.id);
                    case 'nom_asc':
                        return a.dataset.nom.localeCompare(b.dataset.nom);
                    case 'objectif_desc':
                        return parseFloat(b.dataset.objectif) - parseFloat(a.dataset.objectif);
                    case 'progression_desc':
                        return parseFloat(b.dataset.progression) - parseFloat(a.dataset.progression);
                    case 'pourcentage_desc':
                        return parseFloat(b.dataset.pourcentage) - parseFloat(a.dataset.pourcentage);
                    default:
                        return 0;
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
            
            // Mettre à jour le label
            const labels = {
                'id_desc': '🔢 ID (élevé → bas)',
                'id_asc': '🔢 ID (bas → élevé)',
                'nom_asc': '📝 Nom (A → Z)',
                'objectif_desc': '🎯 Objectif (élevé → bas)',
                'progression_desc': '📊 Progression (élevé → bas)',
                'pourcentage_desc': '% Pourcentage (élevé → bas)'
            };
            document.getElementById('sortLabelChallenges').textContent = labels[type];
            document.getElementById('sortMenuChallenges').classList.remove('active');
        }
        
        // Fermer les menus au clic extérieur
        document.addEventListener('click', () => {
            document.getElementById('sortMenu').classList.remove('active');
            document.getElementById('sortMenuChallenges').classList.remove('active');
        });
        
        // ===== ÉDITION DE LA PROGRESSION - OUVRIR UNE PAGE =====
        document.querySelectorAll('.editable-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                const row = cell.closest('tr');
                const challengeId = row.dataset.challengeId;
                const challengeName = row.dataset.nom;
                const currentProgression = parseFloat(cell.dataset.value);
                const objectif = parseFloat(row.dataset.objectif);
                
                // Ouvrir la page d'édition
                window.location.href = `edit_progression_page.php?id=${challengeId}&nom=${encodeURIComponent(challengeName)}&progression=${currentProgression}&objectif=${objectif}`;
            });
        });

    </script>
</body>
</html>
