<?php
require_once '../../config/db.php';
require_once '../../controllers/DonController.php';
require_once '../../controllers/ChallengeController.php';

session_start();
// Vérification d'accès admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /play to help/views/frontoffice/index.html');
    exit;
}

$donC = new DonController();
$challengeC = new ChallengeController();

$dons = $donC->list();
$challenges = $challengeC->list();

// Stats dons
$totalCollecte = array_sum(array_column($dons, 'montant'));
$nombreDons = count($dons);
$associationsDons = count(array_unique(array_column($dons, 'id_association')));

// Stats challenges
$nombreChallenges = count($challenges);
$totalObjectifsChallenges = array_sum(array_column($challenges, 'objectif'));
$totalProgressionChallenges = array_sum(array_column($challenges, 'progression'));

// Répartition des dons par association
$statsAssoc = [];
foreach ($dons as $d) {
    $nom = $d['association_nom'] ?? 'Autre';
    $statsAssoc[$nom] = ($statsAssoc[$nom] ?? 0) + $d['montant'];
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Backoffice Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
        .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
        .glow:hover { box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            z-index: 1000;
        }
        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1e293b;
            border-radius: 20px;
            padding: 2rem;
            min-width: 400px;
            max-width: 800px;
            z-index: 1001;
            display: none;
            border: 2px solid #22d3ee;
        }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    <!-- NAVBAR SIMPLIFIÉE -->
    <nav class="bg-gray-900 border-b border-cyan-800 p-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="text-3xl font-bold text-cyan-400 font-orbitron">PLAY2HELP</div>
                <span class="text-gray-400">|</span>
                <div class="text-xl text-gray-300">Dashboard Admin</div>
            </div>
            <div class="flex items-center space-x-6">
                <a href="/play to help/views/frontoffice/don.php" 
                   class="text-cyan-400 hover:text-cyan-300 transition">
                    <i data-feather="globe" class="inline mr-2"></i> Site Public
                </a>
                <a href="?logout" 
                   class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition">
                    <i data-feather="log-out" class="inline mr-2"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <!-- GESTION DES MESSAGES -->
    <?php if (isset($_GET['message'])): ?>
    <div class="container mx-auto px-6 mt-6">
        <div class="bg-emerald-900 border border-emerald-600 text-emerald-200 px-4 py-3 rounded-lg">
            <?= htmlspecialchars($_GET['message']) ?>
        </div>
    </div>
    <?php endif; ?>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">DASHBOARD</h1>
            <p class="text-cyan-400 text-xl mt-4">Centre de contrôle Play2Help</p>
        </div>

        <!-- CARTES DE STATISTIQUES -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-cyan-400 neon"><?= number_format($totalCollecte, 0, '', ' ') ?> €</h3>
                <p class="text-gray-300 mt-3 text-lg">Total collecté</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-emerald-400"><?= $nombreDons ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Dons reçus</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-purple-400"><?= $nombreChallenges ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Challenges actifs</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-yellow-400">
                    <?= number_format($totalProgressionChallenges, 0, '', ' ') ?> / <?= number_format($totalObjectifsChallenges, 0, '', ' ') ?> €
                </h3>
                <p class="text-gray-300 mt-3 text-lg">Progression Challenges</p>
            </div>
        </div>

        <!-- SECTION GESTION RAPIDE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <!-- Gestion Rapide des Dons -->
            <div class="card rounded-3xl p-10 glow">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <h2 class="text-3xl font-bold neon font-orbitron">Dons récents (<?= $nombreDons ?>)</h2>
                    <button onclick="window.location.href='add.php?type=don'" 
                            class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-6 py-3 rounded-full text-lg font-bold hover:scale-105 transition">
                        <i data-feather="plus" class="inline mr-2"></i> Nouveau don
                    </button>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-gray-800">
                            <tr class="border-b-2 border-cyan-500 text-cyan-400">
                                <th class="py-3 px-4 text-sm">Donateur</th>
                                <th class="py-3 px-4 text-sm">Montant</th>
                                <th class="py-3 px-4 text-sm">Association</th>
                                <th class="py-3 px-4 text-sm">Date</th>
                                <th class="py-3 px-4 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentDons = array_slice($dons, 0, 15);
                            foreach ($recentDons as $d): 
                            ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition">
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars(($d['prenom']??'') . ' ' . ($d['nom']??'') ?: 'Anonyme') ?>
                                </td>
                                <td class="py-3 px-4 font-bold text-emerald-400">
                                    <?= number_format($d['montant'], 2) ?> €
                                </td>
                                <td class="py-3 px-4 text-cyan-400">
                                    <?= htmlspecialchars($d['association_nom'] ?? '—') ?>
                                </td>
                                <td class="py-3 px-4 text-gray-400">
                                    <?= date('d/m/Y', strtotime($d['date_don'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button onclick="window.location.href='edit.php?type=don&id=<?= $d['id_don'] ?>'"
                                                class="text-yellow-400 hover:text-yellow-300 transition">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <button onclick="confirmDelete('don', <?= $d['id_don'] ?>)"
                                                class="text-red-400 hover:text-red-300 transition">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gestion Rapide des Challenges -->
            <div class="card rounded-3xl p-10 glow">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <h2 class="text-3xl font-bold neon font-orbitron">Challenges (<?= $nombreChallenges ?>)</h2>
                    <button onclick="showModal('addChallengeModal')" 
                            class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-3 rounded-full text-lg font-bold hover:scale-105 transition">
                        <i data-feather="plus" class="inline mr-2"></i> Nouveau challenge
                    </button>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-gray-800">
                            <tr class="border-b-2 border-purple-500 text-purple-400">
                                <th class="py-3 px-4 text-sm">Nom</th>
                                <th class="py-3 px-4 text-sm">Objectif</th>
                                <th class="py-3 px-4 text-sm">Progression</th>
                                <th class="py-3 px-4 text-sm">Association</th>
                                <th class="py-3 px-4 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($challenges as $challenge): 
                                $pourcentage = $challenge['objectif'] > 0 ? 
                                    ($challenge['progression'] / $challenge['objectif']) * 100 : 0;
                            ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition" 
                                id="challenge-<?= $challenge['id_challenge'] ?>">
                                <td class="py-3 px-4 font-medium">
                                    <?= htmlspecialchars($challenge['name']) ?>
                                </td>
                                <td class="py-3 px-4 text-emerald-400">
                                    <?= number_format($challenge['objectif'], 2) ?> €
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-700 rounded-full h-2.5">
                                            <div class="bg-cyan-500 h-2.5 rounded-full" 
                                                 style="width: <?= min($pourcentage, 100) ?>%"></div>
                                        </div>
                                        <span class="text-sm text-gray-400">
                                            <?= round($pourcentage) ?>%
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-purple-400">
                                    <?= htmlspecialchars($challenge['association_nom'] ?? 'Non assignée') ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button onclick="editChallenge(<?= $challenge['id_challenge'] ?>)"
                                                class="text-yellow-400 hover:text-yellow-300 transition">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <button onclick="deleteChallenge(<?= $challenge['id_challenge'] ?>)"
                                                class="text-red-400 hover:text-red-300 transition">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RAPPORTS ET GRAPHIQUES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Graphique répartition dons -->
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Répartition des dons</h2>
                <canvas id="donutChart"></canvas>
            </div>

            <!-- Statistiques synthétiques -->
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Statistiques détaillées</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-6 bg-gray-800/50 rounded-xl">
                            <div class="text-2xl font-bold text-emerald-400"><?= $associationsDons ?></div>
                            <div class="text-gray-400">Associations soutenues</div>
                        </div>
                        <div class="text-center p-6 bg-gray-800/50 rounded-xl">
                            <div class="text-2xl font-bold text-cyan-400">
                                <?= $totalObjectifsChallenges > 0 ? round(($totalProgressionChallenges / $totalObjectifsChallenges) * 100, 1) : 0 ?>%
                            </div>
                            <div class="text-gray-400">Taux d'avancement challenges</div>
                        </div>
                    </div>
                    <div class="text-center p-6 bg-gray-800/50 rounded-xl">
                        <div class="text-2xl font-bold text-purple-400">
                            <?= number_format($totalCollecte + $totalProgressionChallenges, 2) ?> €
                        </div>
                        <div class="text-gray-400">Total collecté global</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL AJOUT CHALLENGE -->
    <div id="addChallengeModal" class="modal-overlay" onclick="hideModal('addChallengeModal')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">Créer un nouveau challenge</h3>
                <button onclick="hideModal('addChallengeModal')" class="text-gray-400 hover:text-white">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form id="addChallengeForm" method="POST" action="addchallenge.php">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-300 mb-2">Nom du challenge *</label>
                        <input type="text" name="name" required 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-cyan-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2">Association *</label>
                        <select name="id_association" required 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-cyan-500 focus:outline-none">
                            <option value="">Sélectionner une association</option>
                            <?php
                            $stmt = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="'.$row['id_association'].'">'.htmlspecialchars($row['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 mb-2">Objectif (€) *</label>
                            <input type="number" name="objectif" step="0.01" min="1" required 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-2">Progression initiale (€)</label>
                            <input type="number" name="progression" step="0.01" min="0" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-cyan-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2">Récompense</label>
                        <textarea name="recompense" rows="3" 
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-cyan-500 focus:outline-none"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="hideModal('addChallengeModal')" 
                            class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                        Créer le challenge
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ÉDITION CHALLENGE -->
    <div id="editChallengeModal" class="modal-overlay" onclick="hideModal('editChallengeModal')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-yellow-400">Modifier le challenge</h3>
                <button onclick="hideModal('editChallengeModal')" class="text-gray-400 hover:text-white">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form id="editChallengeForm" method="POST" action="editchallenge.php">
                <input type="hidden" id="editId" name="id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-300 mb-2">Nom du challenge *</label>
                        <input type="text" id="editName" name="name" required 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2">Association *</label>
                        <select id="editAssociation" name="id_association" required 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none">
                            <option value="">Sélectionner une association</option>
                            <?php
                            $stmt = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="'.$row['id_association'].'">'.htmlspecialchars($row['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 mb-2">Objectif (€) *</label>
                            <input type="number" id="editObjectif" name="objectif" step="0.01" min="1" required 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-2">Progression (€)</label>
                            <input type="number" id="editProgression" name="progression" step="0.01" min="0" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2">Récompense</label>
                        <textarea id="editRecompense" name="recompense" rows="3" 
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none"></textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="hideModal('editChallengeModal')" 
                            class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-800 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        feather.replace();

        // Fonctions pour les modals
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function hideModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Édition d'un challenge
        function editChallenge(id) {
            fetch(`getchallenge.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('editId').value = data.id_challenge;
                        document.getElementById('editName').value = data.name;
                        document.getElementById('editAssociation').value = data.id_association;
                        document.getElementById('editObjectif').value = data.objectif;
                        document.getElementById('editProgression').value = data.progression;
                        document.getElementById('editRecompense').value = data.recompense;
                        showModal('editChallengeModal');
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Suppression d'un challenge
        function deleteChallenge(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce challenge ?')) {
                fetch('deletechallenge.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`challenge-${id}`).remove();
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
            }
        }

        // Graphique en donut
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($statsAssoc)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($statsAssoc)) ?>,
                    backgroundColor: [
                        '#22d3ee', '#10b981', '#a78bfa', '#f59e0b', 
                        '#f87171', '#06b6d4', '#8b5cf6', '#f97316'
                    ],
                    borderColor: '#0f172a',
                    borderWidth: 4,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#e2e8f0',
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>