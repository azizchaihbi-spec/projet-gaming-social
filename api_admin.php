<?php
require_once 'config/database.php';

// Connexion BDD
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER, 
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed']));
}

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
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
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
        echo json_encode(['error' => 'Action non reconnue']);

// Ajoutez ce cas dans le switch statement

case 'edit_post':
    $id = $_POST['id'] ?? 0;
    $titre = $_POST['titre'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    
    if ($publicationModel->updatePublication($id, $titre, $contenu)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur de modification']);
    }
    break;
    }
?>