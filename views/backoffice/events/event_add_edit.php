<?php
require_once __DIR__ . '/../../../controllers/EventController.php';

session_start();

$controller = new EventController();
$event = null;
$themes = $controller->listThemes();

if (isset($_GET['edit'])) {
    $data = $controller->getEventById((int)$_GET['edit']);
    if ($data) {
        $event = new Event(
            $data['id_evenement'],
            $data['titre'],
            $data['theme'],
            $data['banner_url'] ?? null,
            $data['description'] ?? null,
            $data['date_debut'],
            $data['date_fin'],
            $data['objectif']
        );
    } else {
        header("Location: browse.php?error=notfound");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Ajouter/Modifier Événement</title>
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
        #errorBox { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; }
        .field-error { color: #fca5a5; font-size: 0.875rem; margin-top: 6px; display: flex; align-items-center; gap: 6px; }
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
                <span class="text-gray-300">Formulaire Événement</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="browse.php" class="text-cyan-400 hover:text-cyan-300 transition">← Retour aux Événements</a>
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['error'])): ?>
        <div class="container mx-auto px-6 py-4">
            <div id="errorBox" class="rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i data-feather="alert-circle" class="text-red-400"></i>
                    <span class="text-red-400"><?= htmlspecialchars($_GET['error']) ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <main class="container mx-auto px-6 py-12 max-w-4xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-bold font-orbitron neon animate-pulse">
                <?= $event ? 'MODIFIER' : 'NOUVEL' ?> ÉVÉNEMENT
            </h1>
            <p class="text-cyan-400 text-xl mt-4">Formulaire de gestion</p>
        </div>

        <!-- FORMULAIRE -->
        <div class="card rounded-3xl p-10 glow border-4 border-cyan-500/50">
            <form id="eventForm" method="POST" action="event_actions.php" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="id_evenement" value="<?= $event?->getIdEvenement() ?? '' ?>">

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="type" class="w-4 h-4"></i>
                        Titre de l'événement *
                    </label>
                    <input type="text" name="titre" value="<?= htmlspecialchars($event?->getTitre() ?? '') ?>" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="image" class="w-4 h-4"></i>
                        Miniature de l'événement
                    </label>
                    <div class="relative">
                        <input type="file" id="thumbnail_file" name="thumbnail_file" accept="image/*" 
                               class="hidden" onchange="previewThumbnail(this)">
                        <label for="thumbnail_file" class="flex items-center justify-center w-full px-4 py-6 bg-gray-800 border-2 border-dashed border-cyan-500/30 rounded-lg cursor-pointer hover:border-cyan-500 transition">
                            <div class="text-center">
                                <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-2 text-cyan-400"></i>
                                <p class="text-cyan-400 font-semibold">Cliquez pour sélectionner une image</p>
                                <p class="text-gray-400 text-sm">Format: JPG, PNG (recommandé: 400x225 px)</p>
                            </div>
                        </label>
                    </div>
                    <div id="thumbnailPreview" class="mt-4 hidden">
                        <img id="thumbnailImg" src="" alt="Aperçu" class="w-full h-32 object-cover rounded-lg border border-cyan-500/30">
                        <button type="button" onclick="removeThumbnail()" class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                            ❌ Supprimer la miniature
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="tag" class="w-4 h-4"></i>
                        Thème / Jeu *
                    </label>
                    <select name="theme" id="themeSelect"
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                        <option value="">-- Sélectionner un thème --</option>
                        <?php foreach ($themes as $t): ?>
                            <option value="<?= htmlspecialchars($t['nom_theme']) ?>" 
                                    <?= ($event && $event->getTheme() === $t['nom_theme']) ? 'selected' : '' ?>>
                                🎮 <?= htmlspecialchars($t['nom_theme']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        Description *
                    </label>
                    <textarea name="description" rows="4"
                              class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition"
                              placeholder="Détaillez l'événement, les règles, l'association soutenue... (min 10 caractères)"><?= htmlspecialchars($event?->getDescription() ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                            Date de début *
                        </label>
                        <input type="date" name="date_debut" value="<?= $event?->getDateDebut()?->format('Y-m-d') ?? '' ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>

                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                            Date de fin *
                        </label>
                        <input type="date" name="date_fin" value="<?= $event?->getDateFin()?->format('Y-m-d') ?? '' ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="target" class="w-4 h-4"></i>
                        Objectif (en DT) *
                    </label>
                    <input type="text" name="objectif" step="0.01" value="<?= htmlspecialchars($event?->getObjectif() ?? '') ?>" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Ex: 5000.00">
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="submit" name="save_event" 
                            class="flex-1 bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-105 transition">
                        💾 Enregistrer
                    </button>
                    <a href="browse.php" 
                       class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-105 transition text-center">
                        ❌ Annuler
                    </a>
                </div>
            </form>
        </div>

        <script>
            function previewThumbnail(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('thumbnailImg').src = e.target.result;
                        document.getElementById('thumbnailPreview').classList.remove('hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function removeThumbnail() {
                document.getElementById('thumbnail_file').value = '';
                document.getElementById('thumbnailPreview').classList.add('hidden');
            }

            // Drag and drop
            const dropZone = document.querySelector('label[for="thumbnail_file"]');
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#06b6d4';
                dropZone.style.backgroundColor = 'rgba(6, 182, 212, 0.1)';
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.style.borderColor = 'rgba(34, 211, 238, 0.3)';
                dropZone.style.backgroundColor = '';
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = 'rgba(34, 211, 238, 0.3)';
                dropZone.style.backgroundColor = '';
                const files = e.dataTransfer.files;
                document.getElementById('thumbnail_file').files = files;
                if (files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('thumbnailImg').src = e.target.result;
                        document.getElementById('thumbnailPreview').classList.remove('hidden');
                    };
                    reader.readAsDataURL(files[0]);
                }
            });
        </script>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2025 Play2Help • Plateforme de Streaming Solidaire</p>
        </div>
    </footer>

    <script src="../js/add-event.js" defer></script>
    <script>
        feather.replace({ width: 16, height: 16 });
    </script>
</body>
</html>
