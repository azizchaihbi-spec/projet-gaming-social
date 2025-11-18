<?php
require_once '../../config/db.php';
require_once '../../controllers/DonController.php';

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

    <custom-navbar></custom-navbar>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">

        <!-- TITRE FUTURISTE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">PLAY2HELP</h1>
            <p class="text-cyan-400 text-xl mt-4"> Live Dashboard</p>
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
            <!-- Doughnut -->
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Répartition des dons</h2>
                <canvas id="donutChart"></canvas>
            </div>

            <!-- Line Chart -->
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Progression 2025</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- TABLEAU DES DONS -->
        <div class="card rounded-3xl p-10 glow">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Tous les dons • <?= $nombreDons ?></h2>
                <a href="../frontoffice/don.php" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    + Nouveau don
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-cyan-500 text-cyan-400">
                            <th class="py-4 px-6">Date</th>
                            <th class="py-4 px-6">Donateur</th>
                            <th class="py-4 px-6">Montant</th>
                            <th class="py-4 px-6">Association</th>
                            <th class="py-4 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dons as $d): ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition">
                            <td class="py-5 px-6"><?= date('d/m/Y', strtotime($d['date_don'])) ?></td>
                            <td class="py-5 px-6 font-medium">
                                <?= htmlspecialchars(($d['prenom']??'') . ' ' . ($d['nom']??'') ?: 'Anonyme') ?>
                            </td>
                            <td class="py-5 px-6 text-2xl font-bold text-emerald-400">
                                <?= number_format($d['montant'], 2) ?> €
                            </td>
                            <td class="py-5 px-6 text-cyan-400 font-medium">
                                <?= htmlspecialchars($d['association_nom'] ?? '—') ?>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <div class="flex justify-center gap-6">
                                    <a href="edit.php?id=<?= $d['id_don'] ?>" 
                                       class="text-yellow-400 hover:text-yellow-300 transition" title="Modifier">
                                        <i data-feather="edit-2" class="w-5 h-5"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $d['id_don'] ?>" 
                                       onclick="return confirm('Supprimer ce don ?')"
                                       class="text-red-500 hover:text-red-400 transition" title="Supprimer">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <custom-footer></custom-footer>

    <!-- Icônes + Graphiques -->
    <script>
        feather.replace({ width: 20, height: 20 });

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
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { color: '#e2e8f0', padding: 20 } } }
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
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } } }
            }
        });
    </script>
</body>
</html>