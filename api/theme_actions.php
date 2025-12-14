<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/ThemeController.php';
require_once __DIR__ . '/../models/theme.php';

$controller = new ThemeController();

if (!isset($_GET['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'list':
        $themes = $controller->listThemes();
        echo json_encode(['success' => true, 'themes' => $themes]);
        break;

    case 'get':
        $id_theme = isset($_GET['id_theme']) ? (int)$_GET['id_theme'] : null;
        if (!$id_theme) {
            echo json_encode(['success' => false, 'message' => 'Theme ID required']);
            exit;
        }
        $theme = $controller->getThemeById($id_theme);
        if ($theme) {
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Theme not found']);
        }
        break;

    case 'streams':
        $id_theme = isset($_GET['id_theme']) ? (int)$_GET['id_theme'] : null;
        if (!$id_theme) {
            echo json_encode(['success' => false, 'message' => 'Theme ID required']);
            exit;
        }
        $streams = $controller->getStreamsByTheme($id_theme);
        echo json_encode(['success' => true, 'streams' => $streams]);
        break;

    case 'count':
        $id_theme = isset($_GET['id_theme']) ? (int)$_GET['id_theme'] : null;
        if (!$id_theme) {
            echo json_encode(['success' => false, 'message' => 'Theme ID required']);
            exit;
        }
        $count = $controller->countStreamsByTheme($id_theme);
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST method required']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        
        $theme = new Theme(
            null,
            $data['nom_theme'] ?? null,
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['icon_url'] ?? null,
            $data['couleur'] ?? null
        );
        
        if ($controller->addTheme($theme)) {
            echo json_encode(['success' => true, 'message' => 'Theme added']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add theme']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
