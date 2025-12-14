<?php
session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../models/clip.php';
require_once __DIR__ . '/../../../controllers/StreamController.php';

$controller = new StreamController();
$clips = [];
$stream = null;
$id_stream = isset($_GET['stream']) ? (int)$_GET['stream'] : null;

try {
    if ($id_stream) {
        $stream = $controller->getStreamById($id_stream);
        $clips = $controller->getClipsByStream($id_stream);
    } else {
        $clips = $controller->listClips();
    }
} catch (Throwable $e) {
    $error = "Erreur: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Clips</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
        .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
        .glow:hover { box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    <nav class="bg-gray-900/80 backdrop-blur-lg border-b border-cyan-500/30 py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="../dashboard.php" class="text-2xl font-bold text-cyan-400 font-orbitron hover:text-cyan-300 transition">PLAY2HELP</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-300">Clips</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="streams.php" class="text-cyan-400 hover:text-cyan-300 transition">← Retour aux Streams</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-bold font-orbitron neon animate-pulse">
                <?= $id_stream ? 'CLIPS DU STREAM' : 'TOUS LES CLIPS' ?>
            </h1>
            <?php if ($stream): ?>
            <p class="text-cyan-400 text-xl mt-4">Stream: <?= htmlspecialchars($stream['titre'] ?? '') ?> (#<?= (int)$stream['id_stream'] ?>)</p>
            <?php endif; ?>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4 mb-6">
                <span class="text-red-400"><?= $error ?></span>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto border-2 border-cyan-500/30 rounded-xl bg-gray-800/80">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-cyan-500 text-cyan-400 bg-gray-800/50">
                        <th class="py-5 px-8">ID</th>
                        <th class="py-5 px-8">Titre</th>
                        <th class="py-5 px-8">Stream</th>
                        <th class="py-5 px-8">URL</th>
                        <th class="py-5 px-8">Création</th>
                        <th class="py-5 px-8 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clips)): ?>
                        <?php foreach ($clips as $c): ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-800/80 transition">
                                <td class="py-6 px-8 font-mono text-cyan-400">#<?= (int)$c['id_clip'] ?></td>
                                <td class="py-6 px-8 text-gray-300"><?= htmlspecialchars($c['titre'] ?? '') ?></td>
                                <td class="py-6 px-8 text-gray-300">
                                    <?php if ($id_stream): ?>
                                        #<?= (int)$c['id_stream'] ?>
                                    <?php else: ?>
                                        <a href="clips.php?stream=<?= (int)$c['id_stream'] ?>" class="text-cyan-400 hover:text-cyan-300">
                                            <?= htmlspecialchars($c['stream_titre'] ?? ('Stream #' . (int)$c['id_stream'])) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="py-6 px-8 text-blue-400"><a href="<?= htmlspecialchars($c['url_video'] ?? '#') ?>" target="_blank">Ouvrir</a></td>
                                <td class="py-6 px-8 text-gray-400"><?= !empty($c['date_creation']) ? date('d/m/Y H:i', strtotime($c['date_creation'])) : '-' ?></td>
                                <td class="py-6 px-8 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="clip_add.php?edit=<?= (int)$c['id_clip'] ?>" class="text-yellow-400 hover:text-yellow-300" title="Modifier">
                                            <i data-feather="edit-2" class="w-5 h-5"></i>
                                        </a>
                                        <a href="clip_actions.php?delete=<?= (int)$c['id_clip'] ?>" onclick="return confirm('Supprimer ce clip ?')" class="text-red-500 hover:text-red-400" title="Supprimer">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                <i data-feather="film" class="w-12 h-12 mx-auto mb-4"></i>
                                <p>Aucun clip trouvé</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2025 Play2Help • Plateforme de Streaming Solidaire</p>
        </div>
    </footer>

    <script>
        feather.replace({ width: 20, height: 20 });
    </script>
</body>
</html>
