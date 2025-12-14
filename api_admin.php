<?php
require_once 'config/config.php';

$pdo = config::getConnexion();

require_once 'models/PublicationModel.php';
require_once 'models/ReponseModel.php';
require_once 'controllers/AdminController.php';

$publicationModel = new PublicationModel($pdo);
$reponseModel = new ReponseModel($pdo);
$adminController = new AdminController($publicationModel, $reponseModel);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        $search = $_GET['search'] ?? '';
        
        // Utilisez les méthodes du controller
        $totalPosts = $publicationModel->getTotalPublications();
        $totalAnswers = $reponseModel->getTotalReponses();
        $bannedCount = 0;
        $posts = $publicationModel->getAllPublicationsForAdmin($search);
        
        echo json_encode([
            'stats' => [
                'totalPosts' => $totalPosts,
                'totalAnswers' => $totalAnswers,
                'bannedCount' => $bannedCount
            ],
            'posts' => $posts
        ]);
        break;
        
    case 'delete_post':
        $id = $_POST['id'] ?? 0;
        if ($publicationModel->deletePublication($id)) {
            echo json_encode(['success' => true, 'message' => 'Publication supprimée']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur de suppression']);
        }
        break;
    
    // === NOUVELLE ACTION : MODIFICATION D'UNE PUBLICATION ===
    case 'edit_post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            
            // Validation
            if (empty($id) || empty($titre) || empty($contenu)) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'ID, titre et contenu requis'
                ]);
                break;
            }
            
            // Mise à jour
            if ($publicationModel->updatePublication($id, $titre, $contenu)) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Publication modifiée avec succès'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Erreur lors de la modification'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'error' => 'Méthode non autorisée'
            ]);
        }
        break;
        
    case 'ban_user':
    case 'unban_user':
        // À implémenter si vous avez une table banned_users
        echo json_encode(['success' => true, 'message' => 'Fonction à implémenter']);
        break;
        
    case 'clear_all':
        // Sécurité - À utiliser avec précaution
        $confirm = $_POST['confirm'] ?? '';
        if ($confirm === 'OUI_ADMIN_2025') {
            // Implémentez la suppression totale si nécessaire
            echo json_encode(['success' => true, 'message' => 'Suppression totale']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Confirmation invalide']);
        }
        break;
        
    default:
        echo json_encode([
            'error' => 'Action non reconnue',
            'available_actions' => [
                'get_all',
                'delete_post',
                'edit_post',
                'ban_user',
                'unban_user',
                'clear_all'
            ]
        ]);
}
?>