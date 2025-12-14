<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/StreamController.php';
require_once __DIR__ . '/../models/clip.php';

$controller = new StreamController();

if (!isset($_GET['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'top':
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
        $clips = $controller->getTopClips($limit);
        echo json_encode(['success' => true, 'clips' => $clips]);
        break;

    case 'stream':
        $id_stream = isset($_GET['id_stream']) ? (int)$_GET['id_stream'] : null;
        if (!$id_stream) {
            echo json_encode(['success' => false, 'message' => 'Stream ID required']);
            exit;
        }
        $clips = $controller->getClipsByStream($id_stream);
        echo json_encode(['success' => true, 'clips' => $clips]);
        break;

    case 'get':
        $id_clip = isset($_GET['id_clip']) ? (int)$_GET['id_clip'] : null;
        if (!$id_clip) {
            echo json_encode(['success' => false, 'message' => 'Clip ID required']);
            exit;
        }
        $clip = $controller->getClipById($id_clip);
        if ($clip) {
            $controller->incrementViews($id_clip);
            echo json_encode(['success' => true, 'clip' => $clip]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Clip not found']);
        }
        break;

    case 'like':
        $id_clip = isset($_POST['id_clip']) ? (int)$_POST['id_clip'] : null;
        if (!$id_clip) {
            echo json_encode(['success' => false, 'message' => 'Clip ID required']);
            exit;
        }
        if ($controller->incrementLikes($id_clip)) {
            echo json_encode(['success' => true, 'message' => 'Like added']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add like']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
