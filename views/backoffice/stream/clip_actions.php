<?php
require_once __DIR__ . '/../../../config/discord.php';
require_once __DIR__ . '/../../../controllers/StreamController.php';
require_once __DIR__ . '/../../../controllers/DiscordController.php';
require_once __DIR__ . '/../../../models/clip.php';

session_start();

$controller = new StreamController();

if (isset($_POST['save_clip'])) {
    // Server-side validation
    $errors = [];
    
    $titre = trim($_POST['titre'] ?? '');
    if (empty($titre) || strlen($titre) < 3 || strlen($titre) > 100) {
        $errors[] = "Titre invalide (3-100 caractères).";
    }
    
    $url_video = trim($_POST['url_video'] ?? '');
    if (empty($url_video)) {
        $errors[] = "L'URL de la vidéo est obligatoire.";
    } else {
        if (!filter_var($url_video, FILTER_VALIDATE_URL)) {
            $errors[] = "L'URL de la vidéo n'est pas valide.";
        }
    }
    
    $id_stream = !empty($_POST['id_stream']) ? (int)$_POST['id_stream'] : null;
    if (!$id_stream) {
        $errors[] = "Stream ID is required.";
    }

    if ($errors) {
        $errorMsg = implode(', ', $errors);
        header("Location: clip_add.php?" . (!empty($_POST['id_clip']) ? "edit=" . $_POST['id_clip'] : "stream=" . $_POST['id_stream']) . "&error=" . urlencode($errorMsg));
        exit;
    }

    $description = trim($_POST['description'] ?? '');
    $id_clip = !empty($_POST['id_clip']) ? (int)$_POST['id_clip'] : null;

    $clip = new Clip(
        $id_clip,
        $id_stream,
        $titre,
        $description ?: null,
        $url_video
    );

    if ($id_clip) {
        $controller->updateClip($clip, $id_clip);
        DiscordController::sendSimpleEmbed(
            DiscordConfig::WEBHOOK_STREAMS,
            "📝 Clip Modifié",
            "**" . htmlspecialchars($titre) . "**\n" .
            "📺 Stream: #" . (int)$id_stream . "\n" .
            "🔗 URL: " . htmlspecialchars(substr($url_video, 0, 50)) . "...",
            DiscordConfig::COLOR_EVENT
        );
    } else {
        $controller->addClip($clip);
        DiscordController::sendSimpleEmbed(
            DiscordConfig::WEBHOOK_STREAMS,
            "✨ Nouveau Clip Créé",
            "**" . htmlspecialchars($titre) . "**\n" .
            "📺 Stream: #" . (int)$id_stream . "\n" .
            "🔗 URL: " . htmlspecialchars(substr($url_video, 0, 50)) . "...",
            DiscordConfig::COLOR_SUCCESS
        );
    }

    header("Location: streams.php?success=Clip " . ($id_clip ? "modifié" : "ajouté") . " avec succès");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $clipData = $controller->getClipById((int)$_GET['delete']);
    $controller->deleteClip((int)$_GET['delete']);
    // Send Discord notification for clip deletion
    if ($clipData) {
        DiscordController::sendSimpleEmbed(
            DiscordConfig::WEBHOOK_STREAMS,
            "🗑️ Clip Supprimé",
            "**" . htmlspecialchars($clipData['titre'] ?? 'Inconnu') . "**\n" .
            "📺 Stream: #" . (int)($clipData['id_stream'] ?? '-'),
            DiscordConfig::COLOR_ERROR
        );
    }
    header("Location: streams.php?success=Clip supprimé");
    exit;
}
