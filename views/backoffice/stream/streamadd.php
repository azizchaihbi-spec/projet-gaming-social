<?php

session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/discord.php';
require_once __DIR__ . '/../../../models/stream.php';
require_once __DIR__ . '/../../../controllers/StreamController.php';
require_once __DIR__ . '/../../../controllers/DiscordController.php';

$controller = new StreamController();

try {
    $db = Config::getConnexion();
    $streamers = $db->query("SELECT s.id_user, s.pseudo, u.email FROM streamer s JOIN utilisateur u ON s.id_user = u.id_user ORDER BY s.pseudo")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $streamers = [];
}

?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Ajouter/Modifier Stream</title>
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
                <span class="text-gray-300">Formulaire Stream</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="streams.php" class="text-cyan-400 hover:text-cyan-300 transition">← Retour aux Streams</a>
            </div>
        </div>
    </nav>

<?php

if (isset($_POST['save_stream'])) {
    $id_stream   = !empty($_POST['id_stream']) ? (int)$_POST['id_stream'] : null;
    $id_streamer = !empty($_POST['id_streamer']) ? (int)$_POST['id_streamer'] : null;
    $titre       = trim($_POST['titre'] ?? '');
    $plateforme  = trim($_POST['plateforme'] ?? '');
    $url         = trim($_POST['url'] ?? '');

    $date_debut  = !empty($_POST['date_debut']) ? DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date_debut']) : null;
    $date_fin    = !empty($_POST['date_fin'])   ? DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date_fin'])   : null;

    $statut      = $_POST['statut'] ?? 'planifie';
    $don_total   = isset($_POST['don_total']) ? (float)$_POST['don_total'] : 0.0;

    // Streamer platforms: array of selected platforms
    $streamer_platforms = $_POST['streamer_platform'] ?? [];
    $streamer_platform_str = !empty($streamer_platforms) ? implode(',', array_map('trim', $streamer_platforms)) : 'Twitch';

    $stream = new Stream(
        $id_stream,
        $titre,
        $plateforme ?: null,
        $url ?: null,
        $date_debut ?: null,
        $date_fin ?: null,
        $statut,
        $don_total
    );

    try {
        // Save stream + thumbnail
        if ($id_stream) {
            $ok = $controller->updateStream($stream, $id_stream, $id_streamer);
        } else {
            $ok = $controller->addStream($stream, $id_streamer);
            if ($ok) {
                // Get the newly inserted ID
                $id_stream = (int)$db->lastInsertId();
            }
        }

        // Update streamer platform if streamer selected
        if ($ok && $id_streamer) {
            $stmt = $db->prepare("UPDATE streamer SET plateforme = ? WHERE id_user = ?");
            $stmt->execute([$streamer_platform_str, $id_streamer]);
        }

        // Handle stream thumbnail upload if provided
        if ($ok && !empty($_FILES['stream_thumb']['tmp_name'])) {
            $controller->saveStreamThumb($id_stream, $_FILES['stream_thumb']);
        }
        if ($ok) {
            // Send Discord notification
            $streamerName = 'Streamer #' . $id_streamer;
            if ($id_streamer) {
                $streamerStmt = $db->prepare("SELECT pseudo FROM streamer WHERE id_user = ?");
                $streamerStmt->execute([$id_streamer]);
                $streamerData = $streamerStmt->fetch(PDO::FETCH_ASSOC);
                if ($streamerData) $streamerName = htmlspecialchars($streamerData['pseudo']);
            }
            
            $isUpdate = !empty($_POST['id_stream']);
            DiscordController::sendSimpleEmbed(
                DiscordConfig::WEBHOOK_STREAMS,
                ($isUpdate ? "📝 Stream Modifié" : "✨ Nouveau Stream Créé"),
                "**" . htmlspecialchars($titre) . "**\n" .
                "👤 Streamer: " . $streamerName . "\n" .
                "📺 Plateforme: " . htmlspecialchars($plateforme) . "\n" .
                "📅 Du " . ($date_debut ? $date_debut->format('d/m/Y H:i') : '-') . " au " . ($date_fin ? $date_fin->format('d/m/Y H:i') : '-') . "\n" .
                "💰 Dons: " . number_format($don_total, 2) . " DT",
                $isUpdate ? DiscordConfig::COLOR_EVENT : DiscordConfig::COLOR_SUCCESS
            );
            
            header("Location: streams.php?success=Stream " . ($isUpdate ? "modifié" : "ajouté") . " avec succès");
            exit;
        } else {
            $error = "Échec de l'enregistrement du stream.";
        }
    } catch (Throwable $e) {
        $error = "Erreur: " . htmlspecialchars($e->getMessage());
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $edit = $controller->getStreamById($editId);
    } catch (Throwable $e) {
        $error = "Erreur chargement du stream: " . htmlspecialchars($e->getMessage());
    }
}

$editDebut = '';
$editFin   = '';
if ($edit && !empty($edit['date_debut'])) {
    $editDebut = date('Y-m-d\TH:i', strtotime($edit['date_debut']));
}
if ($edit && !empty($edit['date_fin'])) {
    $editFin = date('Y-m-d\TH:i', strtotime($edit['date_fin']));
}
?>

    <main class="container mx-auto px-6 py-12 max-w-4xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-bold font-orbitron neon animate-pulse">
                <?= isset($_GET['edit']) ? 'MODIFIER' : 'NOUVEAU' ?> STREAM
            </h1>
            <p class="text-cyan-400 text-xl mt-4">Formulaire de gestion</p>
        </div>

        <?php if (isset($error)): ?>
            <div id="errorBox" class="rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2">
                    <i data-feather="alert-circle" class="text-red-400"></i>
                    <span class="text-red-400"><?= $error ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- FORMULAIRE -->
        <div class="card rounded-3xl p-10 glow border-4 border-cyan-500/50">
            <form method="POST" id="streamForm" enctype="multipart/form-data">
                <input type="hidden" name="id_stream" value="<?= $edit['id_stream'] ?? '' ?>">

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="user" class="w-4 h-4"></i>
                        Streamer
                    </label>
                    <select name="id_streamer" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                        <option value="">-- Sélectionner un streamer --</option>
                        <?php foreach ($streamers as $streamer): ?>
                            <option value="<?= $streamer['id_user'] ?>" <?= (isset($edit['id_streamer']) && $edit['id_streamer'] == $streamer['id_user']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($streamer['pseudo']) ?> (<?= htmlspecialchars($streamer['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="tv" class="w-4 h-4"></i>
                        Plateforme(s) du streamer
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 text-white cursor-pointer">
                            <input type="checkbox" name="streamer_platform[]" value="Twitch" class="w-4 h-4 rounded" <?= (isset($edit['id_streamer']) && strpos($streamers_platforms[$edit['id_streamer']] ?? '', 'Twitch') !== false) ? 'checked' : '' ?>>
                            <span>Twitch</span>
                        </label>
                        <label class="flex items-center gap-3 text-white cursor-pointer">
                            <input type="checkbox" name="streamer_platform[]" value="YouTube" class="w-4 h-4 rounded" <?= (isset($edit['id_streamer']) && strpos($streamers_platforms[$edit['id_streamer']] ?? '', 'YouTube') !== false) ? 'checked' : '' ?>>
                            <span>YouTube</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="image" class="w-4 h-4"></i>
                        Avatar du streamer (auto-chargé)
                    </label>
                    <div id="avatar-preview" style="width: 150px; height: 150px; border-radius: 8px; background: rgba(34, 211, 238, 0.1); border: 2px dashed #22d3ee; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9em; text-align: center; padding: 10px;">
                        Sélectionnez un streamer
                    </div>
                    <p class="text-gray-400 text-sm mt-2">L'image sera chargée automatiquement depuis <code>assets/images/streamers/{id_streamer}.jpg</code></p>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="image" class="w-4 h-4"></i>
                        Vignette du stream (JPG/PNG/WEBP, max 2MB)
                    </label>
                    <input type="file" name="stream_thumb" id="stream-thumb-input" accept="image/jpeg,image/png,image/webp" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    <p class="text-gray-400 text-sm mt-2">Le fichier sera enregistré sous <code>assets/images/streams/{id_stream}.ext</code></p>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                        <i data-feather="type" class="w-4 h-4"></i>
                        Titre du stream
                    </label>
                    <input type="text" name="titre" value="<?= htmlspecialchars($edit['titre'] ?? '') ?>" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="monitor" class="w-4 h-4"></i>
                            Plateforme
                        </label>
                        <select name="plateforme" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">-- Sélectionner --</option>
                            <option value="Twitch" <?= (isset($edit['plateforme']) && $edit['plateforme'] === 'Twitch') ? 'selected' : '' ?>>Twitch</option>
                            <option value="YouTube" <?= (isset($edit['plateforme']) && $edit['plateforme'] === 'YouTube') ? 'selected' : '' ?>>YouTube</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="link" class="w-4 h-4"></i>
                            URL du stream
                        </label>
                        <input type="text" name="url" value="<?= htmlspecialchars($edit['url'] ?? '') ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                            Date et heure de début
                        </label>
                        <input type="datetime-local" name="date_debut" value="<?= $editDebut ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>

                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                            Date et heure de fin
                        </label>
                        <input type="datetime-local" name="date_fin" value="<?= $editFin ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="activity" class="w-4 h-4"></i>
                            Statut
                        </label>
                        <select name="statut" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                            <?php
                            $stat = $edit['statut'] ?? 'planifie';
                            $opts = $controller->getStatusOptions();
                            foreach ($opts as $val => $lbl):
                            ?>
                                <option value="<?= $val ?>" <?= $stat === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-cyan-400 mb-2 font-semibold flex items-center gap-2">
                            <i data-feather="dollar-sign" class="w-4 h-4"></i>
                            Total Dons (DT)
                        </label>
                        <input type="text" step="0.01" name="don_total" value="<?= isset($edit['don_total']) ? (float)$edit['don_total'] : 0.00 ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>

                <div class="flex gap-4 mt-10">
                    <button type="submit" name="save_stream" 
                            class="flex-1 bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-105 transition">
                        💾 Enregistrer
                    </button>
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

    <script src="../js/add-stream.js"></script>
    <script>
        feather.replace({ width: 16, height: 16 });

        // Avatar preview (auto-loaded, read-only)
        const streamerSelect = document.querySelector('select[name="id_streamer"]');
        const avatarPreview = document.getElementById('avatar-preview');
        const streamThumbInput = document.getElementById('stream-thumb-input');

        function updateAvatarPreview() {
          const streamerId = streamerSelect.value;
          if (!streamerId) {
            avatarPreview.innerHTML = '<div style="color: #999;">Sélectionnez<br>un streamer</div>';
            avatarPreview.style.background = 'rgba(34, 211, 238, 0.1)';
            avatarPreview.style.backgroundImage = 'none';
            return;
          }

          // Try to load existing streamer avatar
          const img = new Image();
          img.onerror = () => {
            // Fallback: show placeholder with initials
            const streamerOption = streamerSelect.querySelector(`option[value="${streamerId}"]`);
            const name = streamerOption ? streamerOption.textContent.split('(')[0].trim() : 'S';
            const initial = name.charAt(0).toUpperCase();
            avatarPreview.innerHTML = `<div style="font-size: 60px; font-weight: bold; color: #22d3ee;">${initial}</div>`;
            avatarPreview.style.background = 'rgba(34, 211, 238, 0.2)';
            avatarPreview.style.backgroundImage = 'none';
          };
          img.onload = () => {
            avatarPreview.innerHTML = '';
            avatarPreview.style.background = 'none';
            avatarPreview.style.backgroundImage = `url('${img.src}')`;
            avatarPreview.style.backgroundSize = 'cover';
            avatarPreview.style.backgroundPosition = 'center';
          };
          img.src = `../../frontoffice/assets/images/streamers/${streamerId}.jpg`;
        }

        streamerSelect.addEventListener('change', updateAvatarPreview);
        updateAvatarPreview(); // Initialize on page load
    </script>
</body>
</html>
