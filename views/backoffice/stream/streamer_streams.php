<?php

session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../controllers/StreamController.php';

$controller = new StreamController();

?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Gestion des Streamers</title>
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
        .streamer-card { cursor: pointer; transition: all 0.3s ease; }
        .stream-details { display: none; }
        .stream-details.active { display: block;
}
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
                <span class="text-gray-300">Gestion des Streamers</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="streams.php" class="text-cyan-400 hover:text-cyan-300 transition">← Retour aux Streams</a>
            </div>
        </div>
    </nav>

<?php
$streamers = [];
try {
    $streamers = $controller->getStreamerStreams();
} catch (Throwable $e) {
    $error = "Erreur chargement: " . htmlspecialchars($e->getMessage());
}

$streamer_details = [];
foreach ($streamers as $streamer) {
    $id = $streamer['id_user'];
    try {
        $streamer_details[$id] = $controller->getStreamsByStreamer($id);
    } catch (Throwable $e) {
        $streamer_details[$id] = [];
    }
}
?>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">STREAMERS</h1>
            <p class="text-cyan-400 text-xl mt-4">Analyse des performances • <?= count($streamers) ?> streamer(s)</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4 mb-6">
                <span class="text-red-400"><?= $error ?></span>
            </div>
        <?php endif; ?>

        <!-- PLATFORM FILTER -->
        <div class="card rounded-2xl p-6 mb-8 border-2 border-cyan-500/50">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <label class="text-cyan-400 font-semibold text-lg flex items-center gap-2">
                    <i data-feather="filter" class="w-5 h-5"></i>
                    Filtrer par Plateforme:
                </label>
                <select id="platformFilter" onchange="filterByPlatform()" class="bg-gray-800 border border-cyan-500/50 rounded-lg px-6 py-3 text-white focus:outline-none focus:border-cyan-500 transition min-w-[250px] font-semibold">
                    <option value="">Toutes les Plateformes</option>
                    <?php
                    $platforms = [];
                    foreach ($streamers as $streamer) {
                        if (!empty($streamer['streamer_plateforme'])) {
                            $platforms[] = strtolower(trim($streamer['streamer_plateforme']));
                        }
                    }
                    $platforms = array_unique($platforms);
                    sort($platforms);
                    foreach ($platforms as $platform):
                    ?>
                        <option value="<?= htmlspecialchars($platform) ?>"><?= htmlspecialchars(ucfirst($platform)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button onclick="resetFilter()" class="bg-gradient-to-r from-pink-500 to-red-500 px-6 py-3 rounded-full font-bold hover:scale-105 transition">
                    ✕ Réinitialiser
                </button>
            </div>
        </div>

        <!-- STREAMER CARDS -->
        <?php foreach ($streamers as $streamer): ?>
            <div class="card rounded-3xl p-8 mb-8 glow border-2 border-cyan-500/50 streamer-card" 
                 data-platform="<?= htmlspecialchars($streamer['streamer_plateforme'] ?? '') ?>"
                 onclick="toggleStreams(<?= $streamer['id_user'] ?>)">>
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-3xl">
                            🎥
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-cyan-400 neon">
                                <?= htmlspecialchars($streamer['streamer_pseudo']) ?>
                            </div>
                            <div class="text-gray-400 text-sm flex items-center gap-3 mt-1">
                                <span>📧 <?= htmlspecialchars($streamer['streamer_email']) ?></span>
                                <span>📺 <?= htmlspecialchars($streamer['streamer_plateforme'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-emerald-400">
                            <?= (int)$streamer['nb_streams'] ?> stream(s)
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="card p-4 rounded-xl text-center">
                        <div class="text-3xl font-bold text-emerald-400"><?= number_format((float)($streamer['total_dons'] ?? 0), 2) ?> DT</div>
                        <div class="text-gray-400 text-sm mt-2">💰 Total Dons</div>
                    </div>
                    <div class="card p-4 rounded-xl text-center">
                        <div class="text-3xl font-bold text-cyan-400"><?= number_format((int)($streamer['total_vues'] ?? 0)) ?></div>
                        <div class="text-gray-400 text-sm mt-2">👁️ Vues</div>
                    </div>
                    <div class="card p-4 rounded-xl text-center">
                        <div class="text-3xl font-bold text-purple-400"><?= number_format((int)($streamer['total_likes'] ?? 0)) ?></div>
                        <div class="text-gray-400 text-sm mt-2">👍 Likes</div>
                    </div>
                    <div class="card p-4 rounded-xl text-center">
                        <div class="text-3xl font-bold text-yellow-400"><?= number_format((int)($streamer['total_commentaires'] ?? 0)) ?></div>
                        <div class="text-gray-400 text-sm mt-2">💬 Commentaires</div>
                    </div>
                </div>

                <div class="stream-details" id="streams-<?= $streamer['id_user'] ?>">
                    <div class="border-t-2 border-cyan-500/30 pt-6 mt-4">
                        <h3 class="text-2xl font-bold text-cyan-400 mb-4">Streams de <?= htmlspecialchars($streamer['streamer_pseudo']) ?></h3>
                        <?php if (!empty($streamer_details[$streamer['id_user']])): ?>
                            <div class="space-y-4">
                                <?php foreach ($streamer_details[$streamer['id_user']] as $stream): ?>
                                    <div class="bg-gray-800/50 border border-cyan-500/20 rounded-xl p-6">
                                        <h4 class="text-xl font-bold text-white mb-3"><?= htmlspecialchars($stream['titre']) ?></h4>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                                            <div>
                                                <span class="text-gray-400">Plateforme:</span>
                                                <span class="text-white font-semibold ml-2"><?= htmlspecialchars($stream['plateforme']) ?></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Statut:</span>
                                                <span class="text-white font-semibold ml-2"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $stream['statut']))) ?></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Début:</span>
                                                <span class="text-white font-semibold ml-2"><?= date('d/m/Y H:i', strtotime($stream['date_debut'])) ?></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Dons:</span>
                                                <span class="text-emerald-400 font-bold ml-2"><?= number_format((float)$stream['don_total'], 2) ?> DT</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-gray-300 mb-4">
                                            <span>👁️ <?= number_format((int)$stream['nb_vues']) ?></span>
                                            <span>👍 <?= (int)$stream['nb_likes'] ?></span>
                                            <span>👎 <?= (int)$stream['nb_dislikes'] ?></span>
                                            <span>💬 <?= (int)$stream['nb_commentaires'] ?></span>
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="streamadd.php?edit=<?= $stream['id_stream'] ?>" 
                                               class="bg-gradient-to-r from-yellow-500 to-orange-500 px-4 py-2 rounded-lg text-sm font-semibold hover:scale-105 transition">
                                                ✏️ Modifier
                                            </a>
                                            <a href="deletestream.php?delete=<?= $stream['id_stream'] ?>" 
                                               onclick="return confirm('Supprimer ce stream ?')"
                                               class="bg-gradient-to-r from-red-500 to-pink-500 px-4 py-2 rounded-lg text-sm font-semibold hover:scale-105 transition">
                                                🗑️ Supprimer
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 text-center py-8">Aucun stream trouvé pour ce streamer.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($streamers)): ?>
            <div class="text-center py-20">
                <i data-feather="users" class="w-20 h-20 mx-auto mb-4 text-gray-600"></i>
                <h3 class="text-2xl text-gray-400">Aucun streamer trouvé</h3>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2025 Play2Help • Plateforme de Streaming Solidaire</p>
        </div>
    </footer>

    <script>
        function toggleStreams(streamerId) {
            const detailsDiv = document.getElementById('streams-' + streamerId);
            detailsDiv.classList.toggle('active');
        }

        function filterByPlatform() {
            const selectedPlatform = document.getElementById('platformFilter').value;
            const cards = document.querySelectorAll('.streamer-card');
            
            cards.forEach(card => {
                const platform = card.getAttribute('data-platform').toLowerCase();
                if (selectedPlatform === '' || platform === selectedPlatform.toLowerCase()) {
                    card.style.display = '';
                    card.style.animation = 'fadeIn 0.5s ease-in-out';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function resetFilter() {
            document.getElementById('platformFilter').value = '';
            const cards = document.querySelectorAll('.streamer-card');
            cards.forEach(card => {
                card.style.display = '';
            });
        }
        
        feather.replace({ width: 20, height: 20 });
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
