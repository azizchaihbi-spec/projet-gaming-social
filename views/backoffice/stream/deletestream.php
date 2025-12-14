<?php

session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/discord.php';
require_once __DIR__ . '/../../../models/stream.php';
require_once __DIR__ . '/../../../controllers/StreamController.php';
require_once __DIR__ . '/../../../controllers/DiscordController.php';

$controller = new StreamController();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Get stream data before deleting
        $streamData = $controller->getStreamById($id);
        
        $controller->deleteStream($id);
        
        // Send Discord notification for stream deletion
        if ($streamData) {
            DiscordController::sendSimpleEmbed(
                DiscordConfig::WEBHOOK_STREAMS,
                "🗑️ Stream Supprimé",
                "**" . htmlspecialchars($streamData['titre'] ?? 'Inconnu') . "**\n" .
                "ID: #" . (int)$id . "\n" .
                "📺 Plateforme: " . htmlspecialchars($streamData['plateforme'] ?? '-'),
                DiscordConfig::COLOR_ERROR
            );
        }
        
        header("Location: streams.php?deleted=Stream supprimé avec succès");
        exit;
    } catch (Throwable $e) {
        $error = "Erreur suppression: " . htmlspecialchars($e->getMessage());
        header("Location: streams.php?error=" . urlencode($error));
        exit;
    }
}

header("Location: streams.php");
exit;
?>
