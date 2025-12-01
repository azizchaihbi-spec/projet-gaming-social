<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le contrôleur
include_once(__DIR__ . '/../../Controller/usercontroller.php');

// Gestion du routing
$action = $_GET['action'] ?? 'index';
$controller = new UserController();

// Router vers la méthode appropriée
switch ($action) {
    case 'create':
        $controller->create();
        exit;
    case 'edit':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->edit($id);
        } else {
            header('Location: index.php');
            exit;
        }
        exit;
    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->delete($id);
        } else {
            header('Location: index.php');
            exit;
        }
        exit;
    case 'view':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->view($id);
        } else {
            header('Location: index.php');
            exit;
        }
        exit;
    default:
        // Action par défaut : afficher le dashboard
        $stats = $controller->getUserStats();
        $users = $controller->listUsers();
        $monthlySubscriptions = $controller->getMonthlySubscriptions();

        // Extraire les variables pour le template
        $totalUsers = $stats['totalUsers'];
        $streamersCount = $stats['streamersCount'];
        $newUsersThisMonth = $stats['newUsersThisMonth'];
        $activeUsers = $stats['activeUsers'];
        break;
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Gestion Utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
        .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
        .glow:hover { box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); }
        .scanline { position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, transparent, #22d3ee, transparent); animation: scan 6s linear infinite; }
        @keyframes scan { 0% { transform: translateY(-100%); } 100% { transform: translateY(100vh); } }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    <div class="scanline"></div>

    <!-- Navigation Personnalisée -->
    <nav class="bg-gray-900/80 backdrop-blur-lg border-b border-cyan-500/30 py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h1 class="🎥 Streamer	ext-2xl font-bold text-cyan-400 font-orbitron">PLAY2HELP</h1>
                <span class="text-gray-400">|</span>
                <span class="text-gray-300">Admin Dashboard</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="../../View/FrontOffice/login.php" class="text-cyan-400 hover:text-cyan-300 transition">Site Principal</a>
               
            </div>
        </div>
    </nav>

    <!-- Messages de notification -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="container mx-auto px-6 py-4">
            <div class="<?= $_SESSION['message_type'] === 'success' ? 'bg-green-500/20 border-green-500/30' : 'bg-red-500/20 border-red-500/30' ?> border rounded-lg p-4 text-center">
                <span class="<?= $_SESSION['message_type'] === 'success' ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $_SESSION['message'] ?>
                </span>
            </div>
        </div>
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <!-- Messages d'erreur -->
    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
        <div class="container mx-auto px-6 py-4">
            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4">
                <h3 class="text-red-400 font-bold mb-2">Erreurs :</h3>
                <ul class="list-disc list-inside">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li class="text-red-300"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">

        <!-- TITRE FUTURISTE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">GESTION UTILISATEURS</h1>
            <p class="text-cyan-400 text-xl mt-4">Dashboard Administrateur</p>
        </div>

        <!-- 4 CARTES DE STATS UTILISATEURS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-cyan-400 neon"><?= isset($totalUsers) ? number_format($totalUsers, 0, '', ' ') : '0' ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Utilisateurs Total</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-emerald-400"><?= $streamersCount ?? '0' ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Streamers</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-purple-400"><?= $newUsersThisMonth ?? '0' ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Nouveaux ce mois</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-yellow-400"><?= $activeUsers ?? '0' ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Utilisateurs Actifs</p>
            </div>
        </div>

        <!-- GRAPHIQUES UTILISATEURS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
            <!-- Doughnut - Répartition des rôles -->
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Répartition des Rôles</h2>
                <canvas id="roleChart"></canvas>
            </div>

            <!-- Line Chart - Inscriptions par mois -->
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Inscriptions 2025</h2>
                <canvas id="subscriptionChart"></canvas>
            </div>
        </div>

        <!-- TABLEAU DES UTILISATEURS -->
        <div class="card rounded-3xl p-10 glow">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Gestion des Utilisateurs • <?= isset($totalUsers) ? $totalUsers : '0' ?></h2>
                <a href="index.php?action=create" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    + Nouvel utilisateur
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-cyan-500 text-cyan-400">
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Utilisateur</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6">Rôle</th>
                            <th class="py-4 px-6">Inscription</th>
                            <th class="py-4 px-6">Statut</th>
                            <th class="py-4 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($users) && is_array($users) && count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition">
                                <td class="py-5 px-6 font-mono text-cyan-400">#<?= htmlspecialchars($user['id'] ?? 'N/A') ?></td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center">
                                            <span class="font-bold"><?= strtoupper(substr($user['first_name'] ?? '?', 0, 1)) ?></span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">
                                                <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
                                            </div>
                                            <div class="text-gray-400 text-sm">
                                                @<?= htmlspecialchars($user['username'] ?? 'N/A') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="text-gray-300"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                                    <div class="text-gray-500 text-sm"><?= htmlspecialchars($user['country'] ?? 'N/A') ?></div>
                                </td>
                                <td class="py-5 px-6">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium whitespace-nowrap
                                        <?= match($user['role'] ?? '') {
                                            'streamer' => 'bg-purple-500/20 text-purple-400 border border-purple-500/30',
                                            'admin'    => 'bg-red-500/20 text-red-400 border border-red-500/30',
                                            default    => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                        } ?>">
                                        
                                        <?= match($user['role'] ?? '') {
                                            'streamer' => '🎥 Streamer',
                                            'admin'    => '⚙️ Admin',
                                            default    => '👁️ Viewer',
                                        } ?>
                                    </span>

                                </td>
                                <td class="py-5 px-6 text-gray-400">
                                    <?= isset($user['join_date']) ? date('d/m/Y', strtotime($user['join_date'])) : 'N/A' ?>
                                </td>
                                <td class="py-5 px-6">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400 border border-green-500/30 whitespace-nowrap">
                                        ✅ Actif
                                    </span>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="flex justify-center gap-4">
                                        <a href="index.php?action=edit&id=<?= $user['id'] ?? '' ?>" 
                                           class="text-yellow-400 hover:text-yellow-300 transition transform hover:scale-110" 
                                           title="Modifier">
                                            <i data-feather="edit-2" class="w-5 h-5"></i>
                                        </a>
                                        <a href="index.php?action=delete&id=<?= $user['id'] ?? '' ?>" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')"
                                           class="text-red-500 hover:text-red-400 transition transform hover:scale-110" 
                                           title="Supprimer">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                        </a>
                                        <a href="index.php?action=view&id=<?= $user['id'] ?? '' ?>" 
                                           class="text-cyan-400 hover:text-cyan-300 transition transform hover:scale-110" 
                                           title="Voir profil">
                                            <i data-feather="eye" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    <i data-feather="users" class="w-12 h-12 mx-auto mb-4"></i>
                                    <p>Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($users) && is_array($users) && count($users) > 0): ?>
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-800">
                <div class="text-gray-400">
                    Affichage de <span class="text-white">1-<?= count($users) ?></span> sur <span class="text-white"><?= $totalUsers ?? 0 ?></span> utilisateurs
                </div>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 bg-gray-800 rounded-lg text-gray-400 cursor-not-allowed">
                        <i data-feather="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button class="px-4 py-2 bg-cyan-600 rounded-lg text-white">1</button>
                    <button class="px-4 py-2 bg-gray-800 rounded-lg text-gray-300 hover:bg-gray-700">2</button>
                    <button class="px-4 py-2 bg-gray-800 rounded-lg text-gray-300 hover:bg-gray-700">3</button>
                    <button class="px-4 py-2 bg-gray-800 rounded-lg text-gray-300 hover:bg-gray-700">
                        <i data-feather="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">
                &copy; 2025 Play2Help • Plateforme de Streaming Solidaire
            </p>
            <p class="text-gray-500 text-sm mt-2">
                Dashboard Administrateur - Gestion des Utilisateurs
            </p>
        </div>
    </footer>

    <!-- Icônes + Graphiques -->
    <script>
        feather.replace({ width: 20, height: 20 });

        // Graphique des rôles
        <?php
        $streamers = $streamersCount ?? 0;
        $viewers = isset($totalUsers) ? ($totalUsers - $streamers) : 0;
        ?>
        new Chart(document.getElementById('roleChart'), {
            type: 'doughnut',
            data: {
                labels: ['Streamers', 'Viewers'],
                datasets: [{
                    data: [<?= $streamers ?>, <?= $viewers ?>],
                    backgroundColor: ['#a855f7', '#3b82f6'],
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
                            font: { size: 14 }
                        } 
                    } 
                }
            }
        });

        // Graphique des inscriptions
        new Chart(document.getElementById('subscriptionChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'],
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: <?= isset($monthlySubscriptions) ? json_encode($monthlySubscriptions) : '[]' ?>,
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34, 211, 238, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#22d3ee',
                    pointBorderColor: '#0f172a'
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { 
                        display: true,
                        labels: {
                            color: '#e2e8f0',
                            font: { size: 14 }
                        }
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#94a3b8' }
                    },
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });

        // Animation au survol des lignes du tableau
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(8px)';
                    this.style.transition = 'transform 0.2s ease';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</body>
</html>