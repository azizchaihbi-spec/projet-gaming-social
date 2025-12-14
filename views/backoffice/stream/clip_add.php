<?php
require_once __DIR__ . '/../../../controllers/StreamController.php';
require_once __DIR__ . '/../../../models/clip.php';

session_start();

$clipController = new StreamController();
$streamController = $clipController;

$clip = null;
$stream = null;
$id_stream = isset($_GET['stream']) ? (int)$_GET['stream'] : null;

// Get the stream
if ($id_stream) {
    $streamData = $streamController->getStreamById($id_stream);
    if ($streamData) {
        $stream = $streamData;
    } else {
        header("Location: streams.php?error=Stream not found");
        exit;
    }
}

// Edit clip
if (isset($_GET['edit'])) {
    $clipData = $clipController->getClipById((int)$_GET['edit']);
    if ($clipData) {
        $clip = $clipData;
        $id_stream = $clip['id_stream'];
    } else {
        header("Location: streams.php?error=Clip not found");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Ajouter/Modifier Clip</title>
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
        .is-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important; }
        .field-error { color: #fca5a5; font-size: 0.875rem; margin-top: 6px; display: flex; align-items: center; gap: 6px; }
        .field-error::before { content: "⚠️"; }
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
                <span class="text-gray-300">Formulaire Clip</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="streams.php" class="text-cyan-400 hover:text-cyan-300 transition">← Retour aux Streams</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 max-w-4xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-bold font-orbitron neon animate-pulse">
                <?= $clip ? 'MODIFIER' : 'NOUVEAU' ?> CLIP
            </h1>
            <p class="text-cyan-400 text-xl mt-4">Formulaire de gestion</p>
        </div>

        <!-- FORMULAIRE -->
        <div class="card rounded-3xl p-10 glow border-4 border-cyan-500/50">
            <form id="clipForm" method="POST" action="clip_actions.php" novalidate>
                <input type="hidden" name="id_clip" value="<?= $clip['id_clip'] ?? '' ?>">
                <input type="hidden" name="id_stream" value="<?= $id_stream ?? '' ?>">

                <?php if ($stream): ?>
                    <div class="mb-6 p-4 rounded-lg bg-cyan-500/10 border border-cyan-500/30">
                        <p class="text-cyan-400"><strong>Stream associé:</strong> <?= htmlspecialchars($stream['titre'] ?? '') ?></p>
                    </div>
                <?php endif; ?>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="film" class="w-4 h-4"></i>
                        Titre du clip *
                    </label>
                    <input type="text" name="titre" value="<?= htmlspecialchars($clip['titre'] ?? '') ?>" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Ex: Best moment de la partie...">
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="align-left" class="w-4 h-4"></i>
                        Description
                    </label>
                    <textarea name="description" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Décrivez le contenu du clip..."
                           rows="4"><?= htmlspecialchars($clip['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="link" class="w-4 h-4"></i>
                        URL de la vidéo *
                    </label>
                    <input type="text" name="url_video" value="<?= htmlspecialchars($clip['url_video'] ?? '') ?>" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Ex: https://youtube.com/watch?v=...">
                    <p class="text-gray-400 text-sm mt-2">Supporte YouTube, Twitch, et autres plateformes vidéo.</p>
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="submit" name="save_clip" 
                            class="flex-1 bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-105 transition">
                        💾 Enregistrer
                    </button>
                    <?php if ($clip): ?>
                        <a href="clip_actions.php?delete=<?= (int)$clip['id_clip'] ?>" 
                           onclick="return confirm('Supprimer ce clip définitivement ?')"
                           class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-105 transition text-center">
                            🗑️ Supprimer
                        </a>
                    <?php endif; ?>
                    <a href="streams.php" 
                       class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-105 transition text-center">
                        ❌ Annuler
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2025 Play2Help • Plateforme de Streaming Solidaire</p>
        </div>
    </footer>

    <script src="../js/add-clip.js" defer></script>
    <script>
        feather.replace({ width: 16, height: 16 });
    </script>
</body>
</html>
