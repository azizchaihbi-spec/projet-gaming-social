
<?php
session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../models/stream.php';
require_once __DIR__ . '/../../../controllers/StreamController.php';

$controller = new StreamController();

$streams = [];
try {
    $streams = $controller->listStreams();
} catch (Throwable $e) {
    $error = "Erreur chargement: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Gestion Streams</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

    <!-- Navigation -->
    <nav class="bg-gray-900/80 backdrop-blur-lg border-b border-cyan-500/30 py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="../dashboard.php" class="text-2xl font-bold text-cyan-400 font-orbitron hover:text-cyan-300 transition">PLAY2HELP</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-300">Gestion Streams</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="../events/browse.php" class="text-cyan-400 hover:text-cyan-300 transition">Événements</a>
                <a href="../../../frontoffice/browse.html" class="text-cyan-400 hover:text-cyan-300 transition">Site Principal</a>
            </div>
        </div>
    </nav>

    <?php if (isset($error)): ?>
        <div class="container mx-auto px-6 py-4">
            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4">
                <span class="text-red-400"><?= $error ?></span>
            </div>
        </div>
    <?php endif; ?>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">GESTION STREAMS</h1>
            <p class="text-cyan-400 text-xl mt-4">Streams Caritatifs • <?= count($streams) ?> stream(s)</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="container mx-auto px-6 mb-6">
                <div class="bg-emerald-500/20 border border-emerald-500 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <i data-feather="check-circle" class="text-emerald-400"></i>
                        <span class="text-emerald-400"><?= htmlspecialchars($_GET['success']) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="container mx-auto px-6 mb-6">
                <div class="bg-emerald-500/20 border border-emerald-500 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <i data-feather="check-circle" class="text-emerald-400"></i>
                        <span class="text-emerald-400"><?= htmlspecialchars($_GET['deleted']) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="container mx-auto px-6 mb-6">
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <i data-feather="alert-circle" class="text-red-400"></i>
                        <span class="text-red-400"><?= htmlspecialchars($_GET['error']) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <?php
            $totalStreams = count($streams);
            $activeStreams = array_filter($streams, fn($s) => $s['statut'] === 'en_cours');
            $totalDons = array_sum(array_column($streams, 'don_total'));
            $totalVues = array_sum(array_column($streams, 'nb_vues'));
            ?>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-cyan-400 neon"><?= $totalStreams ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Total Streams</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-emerald-400"><?= count($activeStreams) ?></h3>
                <p class="text-gray-300 mt-3 text-lg">En Cours</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-purple-400"><?= number_format($totalDons, 2) ?> DT</h3>
                <p class="text-gray-300 mt-3 text-lg">Total Dons</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-yellow-400"><?= number_format($totalVues) ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Total Vues</p>
            </div>
        </div>

        <!-- TABLEAU -->
        <div class="card rounded-3xl p-10 glow border-4 border-cyan-500/50">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Liste des Streams</h2>
                <div class="flex gap-4">
                    <a href="streamadd.php?add=1" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                        + Nouveau Stream
                    </a>
                    <a href="streamer_streams.php" class="bg-gradient-to-r from-purple-500 to-pink-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                        Streamers
                    </a>
                    <a href="clips.php" class="bg-gradient-to-r from-cyan-600 to-blue-600 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                        Clips
                    </a>
                </div>
            </div>

            <!-- FILTRES -->
            <div class="bg-gray-800/50 rounded-xl p-6 mb-8 border border-cyan-500/20">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Plateforme</label>
                        <input type="text" id="filterPlateforme" placeholder="Rechercher une plateforme..." class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Période</label>
                        <select id="filterDateRecente" class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="7days">7 derniers jours</option>
                            <option value="30days">30 derniers jours</option>
                            <option value="90days">90 derniers jours</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Statut</label>
                        <select id="filterStatut" class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">Tous les Statuts</option>
                            <option value="planifie">📅 Planifié</option>
                            <option value="en_cours">🔴 En cours</option>
                            <option value="termine">✅ Terminé</option>
                            <option value="annule">❌ Annulé</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Min Dons (DT)</label>
                        <input type="number" id="filterDonsMin" placeholder="0" min="0" step="0.01" class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="resetFilters()" class="bg-gradient-to-r from-pink-500 to-red-500 px-6 py-2 rounded-lg font-bold hover:scale-105 transition">
                        ✕ Réinitialiser
                    </button>
                    <button onclick="filterStreams()" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-6 py-2 rounded-lg font-bold hover:scale-105 transition">
                        🔍 Filtrer
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <div class="text-gray-400 text-sm">
                    Affichage <span id="currentPageInfo">1-10</span> sur <span id="totalStreams"><?= count($streams) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-cyan-400 text-sm">Éléments par page:</label>
                    <select id="itemsPerPage" class="bg-gray-700 border border-cyan-500/30 rounded px-3 py-1 text-white text-sm">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="all">Tous</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto border-2 border-cyan-500/30 rounded-xl bg-gray-800/80">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-cyan-500 text-cyan-400 bg-gray-800/50">
                            <th class="py-5 px-8">ID</th>
                            <th class="py-5 px-8">Streamer</th>
                            <th class="py-5 px-8">Titre</th>
                            <th class="py-5 px-8">Plateforme</th>
                            <th class="py-5 px-8">Début</th>
                            <th class="py-5 px-8">Fin</th>
                            <th class="py-5 px-8">Statut</th>
                            <th class="py-5 px-8">Dons</th>
                            <th class="py-5 px-8">Statistiques</th>
                            <th class="py-5 px-8 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="streamTableBody">
                        <?php if (!empty($streams)): ?>
                            <?php foreach ($streams as $row): ?>
                            <tr class="stream-row border-b border-gray-700 hover:bg-gray-800/80 transition" 
                                data-debut="<?= date('Y-m-d', strtotime($row['date_debut'])) ?>" 
                                data-fin="<?= date('Y-m-d', strtotime($row['date_fin'])) ?>" 
                                data-statut="<?= htmlspecialchars($row['statut']) ?>"
                                data-dons="<?= (float)$row['don_total'] ?>">
                                <td class="py-6 px-8 font-mono text-cyan-400">#<?= (int)$row['id_stream'] ?></td>
                                <td class="py-6 px-8">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                                            <span class="font-bold">🎥</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white"><?= htmlspecialchars($row['streamer_pseudo'] ?? '-') ?></div>
                                            <div class="text-gray-400 text-sm"><?= htmlspecialchars($row['streamer_email'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6 px-8 text-gray-300"><?= htmlspecialchars($row['titre'] ?? '') ?></td>
                                <td class="py-6 px-8">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        <?= ($row['plateforme'] ?? '') === 'Twitch' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' ?>">
                                        <?= htmlspecialchars($row['plateforme'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="py-6 px-8 text-gray-400">
                                    <?= !empty($row['date_debut']) ? date('d/m/Y H:i', strtotime($row['date_debut'])) : '-' ?>
                                </td>
                                <td class="py-6 px-8 text-gray-400">
                                    <?= !empty($row['date_fin']) ? date('d/m/Y H:i', strtotime($row['date_fin'])) : '-' ?>
                                </td>
                                <td class="py-6 px-8">
                                    <?php
                                    $statusMap = [
                                        'planifie' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', 'Planifié'],
                                        'en_cours' => ['bg-green-500/20 text-green-400 border-green-500/30', 'En_cours'],
                                        'termine' => ['bg-gray-500/20 text-gray-400 border-gray-500/30', 'Terminé'],
                                        'annule' => ['bg-red-500/20 text-red-400 border-red-500/30', 'Annulé']
                                    ];
                                    $status = $row['statut'] ?? 'planifie';
                                    [$class, $label] = $statusMap[$status] ?? ['bg-gray-500/20 text-gray-400', 'Inconnu'];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium border <?= $class ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="py-6 px-8 text-emerald-400 font-bold">
                                    <?= number_format((float)($row['don_total'] ?? 0), 2) ?> DT
                                </td>
                                <td class="py-6 px-8 text-sm">
                                    <div class="text-gray-300">
                                        👁️ <?= number_format((int)($row['nb_vues'] ?? 0)) ?> |
                                        👍 <?= (int)($row['nb_likes'] ?? 0) ?> |
                                        👎 <?= (int)($row['nb_dislikes'] ?? 0) ?>
                                    </div>
                                    <div class="text-gray-400 mt-1">
                                        💬 <?= (int)($row['nb_commentaires'] ?? 0) ?>
                                    </div>
                                </td>
                                <td class="py-6 px-8 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="clip_add.php?stream=<?= (int)$row['id_stream'] ?>" 
                                           class="text-violet-400 hover:text-violet-300 transition transform hover:scale-110 relative group" 
                                           title="Ajouter un clip">
                                            <i data-feather="film" class="w-5 h-5"></i>
                                            <?php if (isset($row['clip_count']) && $row['clip_count'] > 0): ?>
                                                <span class="absolute -top-1 -right-1 bg-violet-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"><?= (int)$row['clip_count'] ?></span>
                                            <?php endif; ?>
                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-violet-600 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Clip</span>
                                        </a>
                                        <a href="clips.php?stream=<?= (int)$row['id_stream'] ?>" 
                                           class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 hover:text-white hover:bg-cyan-600 transition" 
                                           title="Voir clips">
                                            <i data-feather="list" class="w-4 h-4"></i>
                                            <span class="text-sm">Voir clips</span>
                                        </a>
                                        <a href="streamadd.php?edit=<?= (int)$row['id_stream'] ?>" 
                                           class="text-yellow-400 hover:text-yellow-300 transition transform hover:scale-110 relative group" 
                                           title="Modifier">
                                            <i data-feather="edit-2" class="w-5 h-5"></i>
                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-yellow-600 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Modifier</span>
                                        </a>
                                        <a href="deletestream.php?delete=<?= (int)$row['id_stream'] ?>" 
                                           onclick="return confirm('Supprimer ce stream ?')"
                                           class="text-red-500 hover:text-red-400 transition transform hover:scale-110 relative group" 
                                           title="Supprimer">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-red-600 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Supprimer</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="py-8 text-center text-gray-400">
                                    <i data-feather="video-off" class="w-12 h-12 mx-auto mb-4"></i>
                                    <p>Aucun stream trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div id="paginationContainer" class="flex justify-center items-center gap-3 mt-8">
                <!-- Buttons will be inserted by JS -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2025 Play2Help • Plateforme de Streaming Solidaire</p>
        </div>
    </footer>

    <script>
        feather.replace({ width: 20, height: 20 });

        // All streams data
        const allStreams = <?= json_encode($streams) ?>;
        let currentPage = 1;
        let itemsPerPage = 10;
        let filteredStreams = [...allStreams];

        function renderStreams() {
            const tbody = document.getElementById('streamTableBody');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = itemsPerPage === 'all' ? filteredStreams.length : startIndex + itemsPerPage;
            const streamsToShow = filteredStreams.slice(startIndex, endIndex);

            tbody.innerHTML = '';

            if (streamsToShow.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="py-8 text-center text-gray-400">
                            <i data-feather="video-off" class="w-12 h-12 mx-auto mb-4"></i>
                            <p>Aucun stream trouvé</p>
                        </td>
                    </tr>
                `;
                feather.replace();
                return;
            }

            streamsToShow.forEach(row => {
                const statusMap = {
                    'planifie': ['bg-blue-500/20 text-blue-400 border-blue-500/30', 'Planifié'],
                    'en_cours': ['bg-green-500/20 text-green-400 border-green-500/30', 'En_cours'],
                    'termine': ['bg-gray-500/20 text-gray-400 border-gray-500/30', 'Terminé'],
                    'annule': ['bg-red-500/20 text-red-400 border-red-500/30', 'Annulé']
                };
                const [statusClass, statusLabel] = statusMap[row.statut] || ['bg-gray-500/20 text-gray-400', 'Inconnu'];
                const platformClass = row.plateforme === 'Twitch' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30';

                tbody.innerHTML += `
                    <tr class="stream-row border-b border-gray-700 hover:bg-gray-800/80 transition">
                        <td class="py-6 px-8 font-mono text-cyan-400">#${row.id_stream}</td>
                        <td class="py-6 px-8">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                                    <span class="font-bold">🎥</span>
                                </div>
                                <div>
                                    <div class="font-medium text-white">${row.streamer_pseudo || '-'}</div>
                                    <div class="text-gray-400 text-sm">${row.streamer_email || ''}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-gray-300">${row.titre || ''}</td>
                        <td class="py-6 px-8">
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${platformClass}">
                                ${row.plateforme || ''}
                            </span>
                        </td>
                        <td class="py-6 px-8 text-gray-400">
                            ${row.date_debut ? new Date(row.date_debut).toLocaleString('fr-FR') : '-'}
                        </td>
                        <td class="py-6 px-8 text-gray-400">
                            ${row.date_fin ? new Date(row.date_fin).toLocaleString('fr-FR') : '-'}
                        </td>
                        <td class="py-6 px-8">
                            <span class="px-3 py-1 rounded-full text-sm font-medium border ${statusClass}">
                                ${statusLabel}
                            </span>
                        </td>
                        <td class="py-6 px-8 text-emerald-400 font-bold">
                            ${parseFloat(row.don_total || 0).toFixed(2)} DT
                        </td>
                        <td class="py-6 px-8 text-sm">
                            <div class="text-gray-300">
                                👁️ ${parseInt(row.nb_vues || 0).toLocaleString()} |
                                👍 ${parseInt(row.nb_likes || 0)} |
                                👎 ${parseInt(row.nb_dislikes || 0)}
                            </div>
                            <div class="text-gray-400 mt-1">
                                💬 ${parseInt(row.nb_commentaires || 0)}
                            </div>
                        </td>
                        <td class="py-6 px-8 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="clip_add.php?stream=${row.id_stream}" 
                                   class="text-violet-400 hover:text-violet-300 transition transform hover:scale-110 relative group" 
                                   title="Ajouter un clip">
                                    <i data-feather="film" class="w-5 h-5"></i>
                                </a>
                                <a href="streamadd.php?edit=${row.id_stream}" 
                                   class="text-yellow-400 hover:text-yellow-300 transition transform hover:scale-110 relative group" 
                                   title="Modifier">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </a>
                                <a href="deletestream.php?delete=${row.id_stream}" 
                                   onclick="return confirm('Supprimer ce stream ?')"
                                   class="text-red-500 hover:text-red-400 transition transform hover:scale-110 relative group" 
                                   title="Supprimer">
                                    <i data-feather="trash-2" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });

            feather.replace();
            updatePagination();
            updatePageInfo();
        }

        function updatePagination() {
            const container = document.getElementById('paginationContainer');
            const totalPages = itemsPerPage === 'all' ? 1 : Math.ceil(filteredStreams.length / itemsPerPage);
            
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';

            // Previous button
            html += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
                class="px-4 py-2 rounded-lg font-bold transition ${currentPage === 1 ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gradient-to-r from-cyan-500 to-emerald-500 hover:scale-105'}">
                ← Précédent
            </button>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button onclick="changePage(${i})" 
                        class="px-4 py-2 rounded-lg font-bold transition ${i === currentPage ? 'bg-gradient-to-r from-purple-500 to-pink-500' : 'bg-gray-700 hover:bg-gray-600'}">
                        ${i}
                    </button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span class="px-2 text-gray-500">...</span>`;
                }
            }

            // Next button
            html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} 
                class="px-4 py-2 rounded-lg font-bold transition ${currentPage === totalPages ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gradient-to-r from-cyan-500 to-emerald-500 hover:scale-105'}">
                Suivant →
            </button>`;

            container.innerHTML = html;
        }

        function updatePageInfo() {
            const startIndex = (currentPage - 1) * itemsPerPage + 1;
            const endIndex = itemsPerPage === 'all' ? filteredStreams.length : Math.min(currentPage * itemsPerPage, filteredStreams.length);
            document.getElementById('currentPageInfo').textContent = `${startIndex}-${endIndex}`;
            document.getElementById('totalStreams').textContent = filteredStreams.length;
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredStreams.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderStreams();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.getElementById('itemsPerPage').addEventListener('change', function() {
            itemsPerPage = this.value === 'all' ? 'all' : parseInt(this.value);
            currentPage = 1;
            renderStreams();
        });

        function filterStreams() {
            const dateRecente = document.getElementById('filterDateRecente').value;
            const donsMin = parseFloat(document.getElementById('filterDonsMin').value) || 0;
            const donsMax = parseFloat(document.getElementById('filterDonsMax').value) || Infinity;
            const statut = document.getElementById('filterStatut').value;
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            filteredStreams = allStreams.filter(stream => {
                const streamDate = new Date(stream.date_debut);
                const streamDons = parseFloat(stream.don_total || 0);
                const streamStatut = stream.statut;
                
                // Vérifier date récente
                if (dateRecente) {
                    const daysDiff = Math.floor((today - streamDate) / (1000 * 60 * 60 * 24));
                    
                    if (dateRecente === 'today' && daysDiff !== 0) return false;
                    if (dateRecente === '7days' && daysDiff > 7) return false;
                    if (dateRecente === '30days' && daysDiff > 30) return false;
                    if (dateRecente === '90days' && daysDiff > 90) return false;
                }

                // Vérifier dons min/max
                if (streamDons < donsMin || streamDons > donsMax) return false;

                // Vérifier statut
                if (statut && streamStatut !== statut) return false;

                return true;
            });

            currentPage = 1;
            renderStreams();
        }

        function resetFilters() {
            document.getElementById('filterDateRecente').value = '';
            document.getElementById('filterDonsMin').value = '';
            document.getElementById('filterDonsMax').value = '';
            document.getElementById('filterStatut').value = '';
            
            filteredStreams = [...allStreams];
            currentPage = 1;
            renderStreams();
        }

        // Filtrer automatiquement quand un champ change
        document.getElementById('filterDateRecente')?.addEventListener('change', filterStreams);
        document.getElementById('filterDonsMin')?.addEventListener('input', filterStreams);
        document.getElementById('filterDonsMax')?.addEventListener('input', filterStreams);
        document.getElementById('filterStatut')?.addEventListener('change', filterStreams);

        // Initial render
        renderStreams();
    </script>
</body>
</html>
