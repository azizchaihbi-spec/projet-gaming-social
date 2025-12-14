<?php
// Simple API for stream stats (views, likes, dislikes, comments)
// Clean any output buffer before sending JSON
if (ob_get_level()) ob_clean();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../controllers/StreamController.php';

$controller = new StreamController();
$action = $_REQUEST['action'] ?? 'list';

function jsonResponse(bool $success, $data = null, string $message = ''): void {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
    ]);
    exit;
}

try {
    switch ($action) {
        case 'list':
            $streams = $controller->listStreams();
            jsonResponse(true, $streams);
            break;

        case 'view':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($id <= 0) jsonResponse(false, null, 'ID manquant');
            $ok = $controller->incrementerVues($id, 1);
            jsonResponse($ok, null, $ok ? '' : 'Erreur lors de la mise à jour des vues');
            break;

        case 'like':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($id <= 0) jsonResponse(false, null, 'ID manquant');
            $ok = $controller->ajouterLike($id);
            jsonResponse($ok, null, $ok ? '' : 'Erreur lors de l\'ajout du like');
            break;

        case 'dislike':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($id <= 0) jsonResponse(false, null, 'ID manquant');
            $ok = $controller->ajouterDislike($id);
            jsonResponse($ok, null, $ok ? '' : 'Erreur lors de l\'ajout du dislike');
            break;

        case 'comment':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($id <= 0) jsonResponse(false, null, 'ID manquant');
            $ok = $controller->ajouterCommentaire($id);
            jsonResponse($ok, null, $ok ? '' : 'Erreur lors de l\'ajout du commentaire');
            break;

        default:
            jsonResponse(false, null, 'Action inconnue');
    }
} catch (Throwable $e) {
    jsonResponse(false, null, 'Erreur: ' . $e->getMessage());
}
