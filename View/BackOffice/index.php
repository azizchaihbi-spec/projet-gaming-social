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
    case 'ban':
        $controller->banUser();
        exit;
    case 'unban':
        $controller->unbanUser();
        exit;
    case 'getBanStatus':
        $controller->getBanStatus();
        exit;
    case 'getTableData':
        $controller->getTableData();
        exit;
    case 'getUsersByCountry':
        $controller->getUsersByCountry();
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
        // Corriger le compteur d'utilisateurs actifs en excluant bannis et suspendus
        $computedActiveUsers = 0;
        if (isset($users) && is_array($users)) {
            $nowTs = time();
            foreach ($users as $u) {
                $isBanned = isset($u['is_banned']) && (int)$u['is_banned'] === 1;
                $banType = $u['ban_type'] ?? null;
                $bannedUntil = $u['banned_until'] ?? null;

                $isSuspended = false;
                if ($isBanned) {
                    if ($banType === 'permanent') {
                        $isSuspended = true;
                    } elseif ($banType === 'soft' && !empty($bannedUntil)) {
                        $untilTs = strtotime($bannedUntil);
                        if ($untilTs !== false && $untilTs > $nowTs) {
                            $isSuspended = true;
                        }
                    }
                }

                if (!$isBanned && !$isSuspended) {
                    $computedActiveUsers++;
                }
            }
        }
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        /* Tooltip pour la carte */
        .leaflet-tooltip.map-tooltip {
            background: rgba(15, 23, 42, 0.95);
            color: #e2e8f0;
            border: 1px solid rgba(34, 211, 238, 0.35);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            padding: 8px 10px;
            font-size: 13px;
            line-height: 1.35;
        }
        .leaflet-tooltip.map-tooltip .title { color: #22d3ee; font-weight: 700; }
        .leaflet-tooltip.map-tooltip .metric { color: #e2e8f0; }
    </style>
    <link rel="stylesheet" href="styleback.css">
    <script src="script.js"></script>
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
                <h3 class="text-5xl font-bold text-yellow-400"><?= isset($computedActiveUsers) ? $computedActiveUsers : ($activeUsers ?? '0') ?></h3>
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

        <!-- CARTE MONDE UTILISATEURS -->
        <div class="management-container mb-16">
            <div class="management-header-wrapper">
                <div class="management-header mb-6">
                    <h2 class="management-header-title font-orbitron">Répartition géographique</h2>
                    <div class="flex flex-wrap gap-3 items-center">
                        <select id="mapStatusFilter" class="filter-select">
                            <option value="all">Tous les statuts</option>
                            <option value="active">Actifs</option>
                            <option value="banned">Bannis</option>
                        </select>
                        <select id="mapPeriodFilter" class="filter-select">
                            <option value="all">Toutes périodes</option>
                            <option value="7d">7 derniers jours</option>
                            <option value="30d">30 derniers jours</option>
                            <option value="90d">90 derniers jours</option>
                        </select>
                        <button id="refreshMapBtn" class="reset-button">
                            <i data-feather="refresh-ccw" class="w-4 h-4"></i>
                            Mettre à jour
                        </button>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="card p-5 rounded-xl glow">
                        <p class="text-gray-400 text-sm">Utilisateurs</p>
                        <p id="mapTotalUsers" class="text-3xl font-bold text-cyan-400">0</p>
                    </div>
                    <div class="card p-5 rounded-xl glow">
                        <p class="text-gray-400 text-sm">Pays couverts</p>
                        <p id="mapTotalCountries" class="text-3xl font-bold text-emerald-400">0</p>
                    </div>
                    <div class="card p-5 rounded-xl glow">
                        <p class="text-gray-400 text-sm">Nouveaux (30j)</p>
                        <p id="mapNew30d" class="text-3xl font-bold text-purple-400">0</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                    <div class="lg:col-span-2 card p-4 rounded-xl glow relative">
                        <div id="worldMap" class="w-full rounded-lg" style="height: 480px; background: #0f172a;"></div>
                    </div>
                    <div class="card p-5 rounded-xl glow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-white">Top pays</h3>
                            <span class="text-gray-500 text-sm">Top 10</span>
                        </div>
                        <div id="topCountriesList" class="space-y-3 text-sm"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLEAU DES UTILISATEURS -->
        <div class="management-container">
            <!-- Carte encapsulant header et filtres -->
            <div class="management-header-wrapper">
                <!-- Header avec Titre, Recherche et CTA -->
                <div class="management-header">
                    <h2 class="management-header-title font-orbitron">Gestion des Utilisateurs</h2>
                    <div class="management-controls">
                        <div class="search-group">
                            <input id="userSearch" type="text" placeholder="Rechercher par nom complet..." 
                                   class="search-input-field" />
                            <span class="search-icon">
                                <i data-feather="search" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <a href="index.php?action=create" class="create-button">
                            + Nouvel utilisateur
                        </a>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="filter-section">
                    <label class="filter-label">
                        <i data-feather="filter" class="w-4 h-4"></i>
                        Filtrer par :
                    </label>
                    
                    <select id="roleFilter" class="filter-select">
                        <option value="">Tous les rôles</option>
                        <option value="viewer">👁️ Viewer</option>
                        <option value="streamer">🎥 Streamer</option>
                        <option value="admin">⚙️ Admin</option>
                    </select>

                    <select id="statusFilter" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="active">✅ Actif</option>
                        <option value="banned">🚫 Banni</option>
                        <option value="suspended">⏸️ Suspendu</option>
                    </select>

                    <button id="resetFilters" class="reset-button">
                        <i data-feather="x-circle" class="w-4 h-4"></i>
                        Réinitialiser
                    </button>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-wrapper">
                <div id="tableScrollContainer" class="table-scroll-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <!-- Colonne Sticky Gauche: ID -->
                                <th class="sticky-left-col" style="min-width: 80px;" onclick="sortTable('id')" title="Cliquez pour trier">
                                    <div class="flex items-center gap-2">
                                        ID
                                        <span class="sort-icon opacity-50" data-column="id">
                                            <i data-feather="chevrons-up" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </th>
                                
                                <!-- Colonne Sticky Gauche: Utilisateur -->
                                <th class="sticky-left-col" style="min-width: 280px;" onclick="sortTable('user')" title="Cliquez pour trier">
                                    <div class="flex items-center gap-2">
                                        Utilisateur
                                        <span class="sort-icon opacity-50" data-column="user">
                                            <i data-feather="chevrons-up" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </th>
                                
                                <!-- Colonnes Essentielles (défilent horizontalement) -->
                                <th onclick="sortTable('email')" title="Cliquez pour trier">
                                    <div class="flex items-center gap-2">
                                        Email
                                        <span class="sort-icon opacity-50" data-column="email">
                                            <i data-feather="chevrons-up" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </th>
                                <th onclick="sortTable('role')" title="Cliquez pour trier">
                                    <div class="flex items-center gap-2">
                                        Rôle
                                        <span class="sort-icon opacity-50" data-column="role">
                                            <i data-feather="chevrons-up" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </th>
                                <th onclick="sortTable('status')" title="Cliquez pour trier">
                                    <div class="flex items-center gap-2">
                                        Statut
                                        <span class="sort-icon opacity-50" data-column="status">
                                            <i data-feather="chevrons-up" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </th>
                                
                                <!-- Colonne Sticky Droite: Actions -->
                                <th class="sticky-right-col text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="5" class="text-center py-8">
                                    <i data-feather="loader" class="w-12 h-12 mx-auto mb-4 animate-spin"></i>
                                    <p class="text-gray-400">Chargement...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Scroll Hint Overlay -->
                <div class="table-scroll-hint left"></div>
                <div class="table-scroll-hint right"></div>
            </div>

            <!-- Pagination -->
            <div id="paginationControls" class="pagination-section">
                <div class="pagination-info">
                    Affichage de <span id="rangeStart" class="text-white">0</span>-<span id="rangeEnd" class="text-white">0</span> sur <span id="totalVisible" class="text-white">0</span> utilisateurs
                </div>
                <div class="pagination-buttons">
                    <button id="prevPage" class="pagination-button prev">
                        <i data-feather="chevron-left" class="w-4 h-4"></i>
                        Précédent
                    </button>
                    <div id="pageNumbers" class="pagination-numbers">
                        <!-- Les numéros de page seront insérés ici par JavaScript -->
                    </div>
                    <button id="nextPage" class="pagination-button next">
                        Suivant
                        <i data-feather="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
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

    <!-- Icônes Feather et Graphiques -->
    <script>
        // Initialize dashboard on page load
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace({ width: 20, height: 20 });

            // Charger les données initiales du tableau
            loadTableData();

            // Wire up search and filter inputs
            const searchInput = document.getElementById('userSearch');
            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            const roleFilter = document.getElementById('roleFilter');
            if (roleFilter) {
                roleFilter.addEventListener('change', applyFilters);
            }

            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', applyFilters);
            }

            const resetFilters = document.getElementById('resetFilters');
            if (resetFilters) {
                resetFilters.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (roleFilter) roleFilter.value = '';
                    if (statusFilter) statusFilter.value = '';
                    tableState.searchQuery = '';
                    tableState.roleFilter = '';
                    tableState.statusFilter = '';
                    tableState.currentPage = 1;
                    loadTableData();
                });
            }

            // Wire up pagination buttons
            const prevPageBtn = document.getElementById('prevPage');
            if (prevPageBtn) {
                prevPageBtn.addEventListener('click', function() {
                    if (tableState.currentPage > 1) {
                        tableState.currentPage--;
                        loadTableData();
                    }
                });
            }

            const nextPageBtn = document.getElementById('nextPage');
            if (nextPageBtn) {
                nextPageBtn.addEventListener('click', function() {
                    // Le nombre de pages est mis à jour par loadTableData
                    tableState.currentPage++;
                    loadTableData();
                });
            }

            // Handle window resize to switch between table and card view
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    loadTableData(); // Rerender with appropriate view mode
                }, 250);
            });
        });

        // Chart.js - Doughnut Chart for user roles
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

        // Chart.js - Line Chart for monthly subscriptions (from user join_date)
        <?php
        $monthlyData = array_fill(0, 12, 0);
        if (isset($users) && is_array($users)) {
            foreach ($users as $u) {
                if (!empty($u['join_date'])) {
                    $ts = strtotime($u['join_date']);
                    if ($ts !== false) {
                        $month = (int)date('n', $ts); // 1..12
                        $monthlyData[$month - 1]++;
                    }
                }
            }
        }
        ?>

        const normalizedSubs = <?= json_encode($monthlyData) ?>;

        new Chart(document.getElementById('subscriptionChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'],
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: normalizedSubs,
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

        // ------------------------------------------------------------------
        // Carte mondiale - Leaflet
        // ------------------------------------------------------------------
        let worldMap = null;
        let markersLayer = null;
        let mapData = [];

        const formatNumber = (n) => Number(n || 0).toLocaleString('fr-FR');

        function initWorldMap() {
            const mapEl = document.getElementById('worldMap');
            if (!mapEl) return;

            worldMap = L.map('worldMap', {
                center: [25, 0],
                zoom: 2,
                minZoom: 2,
                maxZoom: 10,
                zoomControl: true
            });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap, &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(worldMap);

            markersLayer = L.markerClusterGroup({
                maxClusterRadius: 80,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    let size = 'small';
                    if (count > 50) size = 'large';
                    else if (count > 10) size = 'medium';

                    return L.divIcon({
                        html: '<div><span>' + count + '</span></div>',
                        className: 'marker-cluster marker-cluster-' + size,
                        iconSize: L.point(40, 40)
                    });
                }
            });

            worldMap.addLayer(markersLayer);

            const statusFilter = document.getElementById('mapStatusFilter');
            const periodFilter = document.getElementById('mapPeriodFilter');
            const refreshBtn = document.getElementById('refreshMapBtn');

            if (statusFilter) statusFilter.addEventListener('change', loadMapData);
            if (periodFilter) periodFilter.addEventListener('change', loadMapData);
            if (refreshBtn) refreshBtn.addEventListener('click', loadMapData);

            loadMapData();
        }

        function loadMapData() {
            const status = document.getElementById('mapStatusFilter')?.value || 'all';
            const period = document.getElementById('mapPeriodFilter')?.value || 'all';

            fetch(`index.php?action=getUsersByCountry&status=${status}&period=${period}`)
                .then(res => res.json())
                .then(payload => {
                    if (!payload.success) {
                        console.error('Erreur chargement carte:', payload.message);
                        return;
                    }
                    mapData = payload.data || [];
                    updateMapMarkers(mapData);
                    updateMapStats(payload);
                    updateTopCountries(mapData);
                })
                .catch(err => console.error('Erreur fetch carte:', err));
        }

        function updateMapMarkers(countries) {
            if (!markersLayer) return;
            markersLayer.clearLayers();

            countries.forEach(country => {
                if (!country.lat && !country.lng) return;

                const activeRatio = country.users > 0 ? country.active / country.users : 0;
                const bannedRatio = country.users > 0 ? country.banned / country.users : 0;

                let markerColor = '#06b6d4'; // neutre
                if (bannedRatio > 0.5) markerColor = '#dc2626';
                else if (activeRatio > 0.8) markerColor = '#10b981';

                const radius = Math.min(Math.sqrt(Math.max(country.users, 1)) * 2.5, 30);

                const marker = L.circleMarker([country.lat, country.lng], {
                    radius,
                    fillColor: markerColor,
                    color: '#0ea5e9',
                    weight: 2.5,
                    opacity: 0.9,
                    fillOpacity: 0.7
                });

                const popup = `
                    <div style="min-width: 200px; padding: 6px 4px;">
                        <div style="font-weight:700; color:#06b6d4; margin-bottom:6px; font-size:14px;">
                            ${country.countryName}
                        </div>
                        <div style="display:grid; grid-template-columns:auto 1fr; gap:4px 10px; font-size:13px; color:#e2e8f0;">
                            <span>Total</span><span>${formatNumber(country.users)}</span>
                            <span style="color:#10b981;">Actifs</span><span>${formatNumber(country.active)}</span>
                            <span style="color:#dc2626;">Bannis</span><span>${formatNumber(country.banned)}</span>
                            <span style="color:#a855f7;">Nouveaux 30j</span><span>${formatNumber(country.new30d)}</span>
                        </div>
                    </div>`;

                marker.bindPopup(popup);

                // Tooltip léger au survol
                marker.bindTooltip(`
                    <div class="title">${country.countryName}</div>
                    <div class="metric">Total : ${formatNumber(country.users)}</div>
                `, {
                    direction: 'top',
                    sticky: true,
                    className: 'map-tooltip',
                    opacity: 0.95,
                    offset: [0, -4]
                });

                // Effet hover doux
                marker.on('mouseover', () => marker.setStyle({ radius: radius + 3, weight: 3 }));
                marker.on('mouseout', () => marker.setStyle({ radius, weight: 2.5 }));

                markersLayer.addLayer(marker);
            });

            const layers = markersLayer.getLayers();
            if (layers.length > 0) {
                worldMap.fitBounds(markersLayer.getBounds(), { padding: [30, 30], maxZoom: 5 });
            } else {
                worldMap.setView([25, 0], 2);
            }
        }

        function updateMapStats(payload) {
            const totalUsers = payload.total_users ?? 0;
            const totalCountries = payload.total_countries ?? 0;
            const new30d = mapData.reduce((sum, c) => sum + (c.new30d || 0), 0);

            const elUsers = document.getElementById('mapTotalUsers');
            const elCountries = document.getElementById('mapTotalCountries');
            const elNew30 = document.getElementById('mapNew30d');

            if (elUsers) elUsers.textContent = totalUsers.toLocaleString();
            if (elCountries) elCountries.textContent = totalCountries;
            if (elNew30) elNew30.textContent = new30d.toLocaleString();
        }

        function updateTopCountries(countries) {
            const container = document.getElementById('topCountriesList');
            if (!container) return;

            if (!countries || countries.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">Aucune donnée disponible</p>';
                return;
            }

            const sorted = [...countries].sort((a, b) => b.users - a.users).slice(0, 10);
            const maxUsers = sorted[0]?.users || 1;

            container.innerHTML = sorted.map((c, idx) => {
                const width = (c.users / maxUsers) * 100;
                return `
                    <div class="flex items-center gap-3">
                        <div class="text-gray-500 text-xs font-mono w-6">${idx + 1}.</div>
                        <div class="flex-1">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-200">${c.countryName}</span>
                                <span class="text-cyan-400 font-semibold">${c.users}</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2 overflow-hidden">
                                <div class="h-2 bg-gradient-to-r from-cyan-500 to-emerald-500" style="width:${width}%"></div>
                            </div>
                        </div>
                    </div>`;
            }).join('');
        }

        // Boot
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initWorldMap);
        } else {
            initWorldMap();
        }
    </script>
</body>
</html>