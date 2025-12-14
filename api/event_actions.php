<?php
// api/event_actions.php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../controllers/EventController.php';

try {
    $controller = new EventController();
    $action = $_GET['action'] ?? '';

    if ($action === 'list') {
        // Get all events
        $events = $controller->listEvents();
        
        // Add status calculation for each event
        $now = new DateTime();
        foreach ($events as &$event) {
            $debut = new DateTime($event['date_debut']);
            $fin = new DateTime($event['date_fin']);
            
            if ($debut <= $now && $now <= $fin) {
                $event['statut'] = 'live';
            } elseif ($debut > $now) {
                $event['statut'] = 'upcoming';
            } else {
                $event['statut'] = 'finished';
            }
            
            // Laisser le front-office gérer les chemins des images
            // Ne pas modifier banner_url, le JavaScript s'en occupera

            // Count participants (if needed, can be added later)
            $event['participants'] = 0;
        }
        
        http_response_code(200);
        error_log('DEBUG: Events with banner_url: ' . json_encode($events));
        echo json_encode(['success' => true, 'data' => $events]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
