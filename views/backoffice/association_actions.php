<?php
header('Content-Type: application/json');
require_once '../../controllers/AssociationController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';
$associationController = new AssociationController();

switch ($action) {
    case 'add':
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($name) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'Le nom et la description sont requis']);
            exit;
        }
        
        $result = $associationController->add($name, $description);
        echo json_encode(['success' => $result, 'message' => $result ? 'Association créée avec succès' : 'Erreur lors de la création']);
        break;
        
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if ($id <= 0 || empty($name) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            exit;
        }
        
        $result = $associationController->update($id, $name, $description);
        echo json_encode(['success' => $result, 'message' => $result ? 'Association modifiée avec succès' : 'Erreur lors de la modification']);
        break;
        
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit;
        }
        
        $result = $associationController->delete($id);
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        break;
}
?>