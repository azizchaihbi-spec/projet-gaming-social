<?php
// views/backoffice/events/event_actions.php

require_once __DIR__ . '/../../../config/discord.php';
require_once __DIR__ . '/../../../controllers/EventController.php';
require_once __DIR__ . '/../../../controllers/DiscordController.php';

session_start();

$controller = new EventController();

if (isset($_POST['save_event'])) {

    // Server-side validation
    $errors = [];
    $titre = trim($_POST['titre'] ?? '');
    if (empty($titre) || strlen($titre) < 3 || strlen($titre) > 100) {
        $errors[] = "Titre invalide.";
    }
    $date_debut = $_POST['date_debut'] ?? '';
    if (empty($date_debut)) {
        $errors[] = "Date de début requise.";
    }
    $date_fin = $_POST['date_fin'] ?? '';
    if (empty($date_fin)) {
        $errors[] = "Date de fin requise.";
    } elseif ($date_debut && $date_fin < $date_debut) {
        $errors[] = "Date de fin doit être après la date de début.";
    }
    $description = trim($_POST['description'] ?? '');
    if (empty($description)) {
        // For new events, description is required. For edits, preserve existing.
        if (!$id) {
            $errors[] = "Description trop courte (min 10 caractères).";
        }
    } elseif (strlen($description) < 10) {
        $errors[] = "Description trop courte (min 10 caractères).";
    } elseif (strlen($description) > 2000) {
        $errors[] = "Description trop longue (max 2000 caractères).";
    }
    $objectif = trim($_POST['objectif'] ?? '');
    if (empty($objectif)) {
        $errors[] = "L'objectif est obligatoire.";
    } else {
        $objNum = (float)$objectif;
        if ($objNum <= 0 || $objNum > 1000000) {
            $errors[] = "Objectif invalide.";
        }
    }

    if ($errors) {
        // Redirect back with errors
        $errorMsg = implode(', ', $errors);
        header("Location: event_add_edit.php?" . (!empty($_POST['id_evenement']) ? "edit=" . $_POST['id_evenement'] : "add=1") . "&error=" . urlencode($errorMsg));
        exit;
    }

    // Convert empty id_evenement to null (critical!)
    $id = !empty($_POST['id_evenement']) ? (int)$_POST['id_evenement'] : null;

    // Handle thumbnail upload
    $banner_url = null;
    
    // If updating existing event, fetch current thumbnail to preserve it
    if ($id) {
        $currentEventData = $controller->getEventById($id);
        if ($currentEventData && !empty($currentEventData['banner_url'])) {
            $banner_url = $currentEventData['banner_url'];
        }
        // Preserve description if not modified
        if (empty($description) && !empty($currentEventData['description'])) {
            $description = $currentEventData['description'];
        }
    }
    
    // Only process new upload if file is provided
    if (!empty($_FILES['thumbnail_file']['name'])) {
        $file = $_FILES['thumbnail_file'];
        
        error_log("Thumbnail upload attempt: " . $file['name'] . " for event ID: " . ($id ?? 'new'));
        
        // Validate file
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowed)) {
            $errors[] = "Format d'image invalide. Utilisez JPG, PNG, GIF ou WebP.";
            error_log("Invalid file type: " . $file['type']);
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload du fichier.";
            error_log("Upload error code: " . $file['error']);
        } else {
            // Create upload directory if needed (save into frontoffice images folder)
            $upload_dir = realpath(__DIR__ . '/../../../') . '/views/frontoffice/assets/images/';
            error_log("Upload directory resolved to: " . $upload_dir);
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
                error_log("Created upload directory");
            }
            
            // Generate unique filename with timestamp to avoid cache/overwrites
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filepath = $upload_dir . $filename;
            
            // Move file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $banner_url = '/views/frontoffice/assets/images/' . $filename;
                error_log("Thumbnail saved successfully: " . $banner_url);
            } else {
                $errors[] = "Impossible de sauvegarder l'image.";
                error_log("Failed to move uploaded file to: " . $filepath);
            }
        }
        
        if ($errors) {
            $errorMsg = implode(', ', $errors);
            header("Location: event_add_edit.php?" . (!empty($_POST['id_evenement']) ? "edit=" . $_POST['id_evenement'] : "add=1") . "&error=" . urlencode($errorMsg));
            exit;
        }
    }

    $event = new Event(
        $id,                                      // now null or int → perfect
        $titre,
        trim($_POST['theme'] ?? ''),
        $banner_url,
        $description,
        $date_debut,
        $date_fin,
        $objectif
    );

    if ($id) {
        $controller->updateEvent($event, $id);
        // Send Discord notification for event update
        DiscordController::sendSimpleEmbed(
            DiscordConfig::WEBHOOK_EVENTS,
            "📝 Événement Modifié",
            "**" . htmlspecialchars($titre) . "**\n" .
            "🎮 Thème: " . htmlspecialchars(trim($_POST['theme'] ?? '')) . "\n" .
            "📅 Du " . date('d/m/Y H:i', strtotime($date_debut)) . " au " . date('d/m/Y H:i', strtotime($date_fin)) . "\n" .
            "🎯 Objectif: " . number_format((float)$objectif, 2) . " DT",
            DiscordConfig::COLOR_EVENT
        );
    } else {
        $controller->addEvent($event);
        // Send Discord notification for new event
        DiscordController::sendSimpleEmbed(
            DiscordConfig::WEBHOOK_EVENTS,
            "✨ Nouvel Événement Créé",
            "**" . htmlspecialchars($titre) . "**\n" .
            "🎮 Thème: " . htmlspecialchars(trim($_POST['theme'] ?? '')) . "\n" .
            "📅 Du " . date('d/m/Y H:i', strtotime($date_debut)) . " au " . date('d/m/Y H:i', strtotime($date_fin)) . "\n" .
            "🎯 Objectif: " . number_format((float)$objectif, 2) . " DT",
            DiscordConfig::COLOR_SUCCESS
        );
    }

    header("Location: browse.php?success=1");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $eventData = $controller->getEventById((int)$_GET['delete']);
    $controller->deleteEvent((int)$_GET['delete']);
    // Send Discord notification for event deletion
    if ($eventData) {
        DiscordController::sendSimpleEmbed(
            DiscordConfig::WEBHOOK_EVENTS,
            "🗑️ Événement Supprimé",
            "**" . htmlspecialchars($eventData['titre'] ?? 'Inconnu') . "**\n" .
            "ID: #" . (int)$_GET['delete'],
            DiscordConfig::COLOR_ERROR
        );
    }
    header("Location: browse.php?deleted=1");
    exit;
}