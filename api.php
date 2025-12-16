<?php
require_once 'config/config.php';

$pdo = config::getConnexion();

require_once 'models/PublicationModel.php';
require_once 'models/ReponseModel.php';

$publicationModel = new PublicationModel($pdo);
$reponseModel = new ReponseModel($pdo);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        $filter = $_GET['filter'] ?? 'all';
        $publications = $publicationModel->getAllPublications($filter);
        
        foreach ($publications as &$pub) {
            $pub['answers'] = $reponseModel->getReponsesByPublication($pub['id_publication']);
        }
        
        echo json_encode($publications);
        break;
        
    case 'like':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        if ($publicationModel->likePublication($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
        
    case 'dislike':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        if ($publicationModel->dislikePublication($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
        
    case 'add_reply':
        session_start();
        $input = json_decode(file_get_contents('php://input'), true);
        $idPublication = $input['id_publication'] ?? 0;
        $contenu = $input['contenu'] ?? '';
        $idAuteur = $_SESSION['user']['id'] ?? 1;
        
        if ($reponseModel->createReponse($idPublication, $idAuteur, $contenu)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'create_publication':
        session_start();
        
        // Debug
        error_log("=== DEBUG CREATE PUBLICATION ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        error_log("SESSION user: " . print_r($_SESSION['user'] ?? 'NO SESSION', true));
        
        // Récupérer les données du formulaire (FormData)
        $titre = $_POST['titre'] ?? '';
        $contenu = $_POST['contenu'] ?? '';
        $idForum = $_POST['id_forum'] ?? 1;
        $idAuteur = $_SESSION['user']['id'] ?? 1;
        
        error_log("Données récupérées - Titre: $titre, Contenu: $contenu, Forum: $idForum, Auteur: $idAuteur");
        
        // Nouvelles données pour emojis, GIFs et stickers
        $emojis = isset($_POST['emojis']) ? json_decode($_POST['emojis'], true) : null;
        $gifUrl = $_POST['gif_url'] ?? null;
        $stickerUrl = $_POST['sticker_url'] ?? null;
        
        // Gestion de l'upload d'image
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'views/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $imagePath = $uploadPath;
                }
            }
        }
        
        error_log("Avant appel createPublicationWithMedia - Params: idAuteur=$idAuteur, titre='$titre', contenu='$contenu', idForum=$idForum, imagePath='$imagePath'");
        
        $result = $publicationModel->createPublicationWithMedia($idAuteur, $titre, $contenu, $idForum, $imagePath, $emojis, $gifUrl, $stickerUrl);
        error_log("Résultat création: " . ($result ? 'SUCCESS' : 'FAILED'));
        error_log("=== FIN DEBUG ===");
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la création - vérifiez les logs']);
        }
        break;

    case 'edit_post':
        session_start();
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $titre = $input['titre'] ?? '';
        $contenu = $input['contenu'] ?? '';
        
        // Vérifier que c'est bien l'auteur
        $check = $pdo->prepare("SELECT id_auteur FROM publication WHERE id_publication = ? AND supprimee = 0");
        $check->execute([$id]);
        $auteur = $check->fetchColumn();
        $currentUserId = $_SESSION['user']['id'] ?? 0;
        
        if ($auteur == $currentUserId) {
            if ($publicationModel->updatePublication($id, $titre, $contenu)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Pas votre publication']);
        }
        break;

   case 'delete_post':
    session_start();
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    
    // Vérifier que c'est bien l'auteur
    $check = $pdo->prepare("SELECT id_auteur FROM publication WHERE id_publication = ? AND supprimee = 0");
    $check->execute([$id]);
    $auteur = $check->fetchColumn();
    $currentUserId = $_SESSION['user']['id'] ?? 0;
    
    if ($auteur == $currentUserId) {
        $success = $publicationModel->deletePublication($id);
        
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
    } else {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Pas votre publication']);
    }
    break;
        
    case 'edit_reply':
        session_start();
        $input = json_decode(file_get_contents('php://input'), true);
        $id_reponse = $input['id'] ?? 0;
        $nouveau_contenu = $input['contenu'] ?? '';

        // Vérifie que c'est bien l'auteur
        $check = $pdo->prepare("SELECT id_auteur FROM reponse WHERE id_reponse = ? AND supprimee = 0");
        $check->execute([$id_reponse]);
        $auteur = $check->fetchColumn();
        $currentUserId = $_SESSION['user']['id'] ?? 0;

        if ($auteur == $currentUserId) {
            $stmt = $pdo->prepare("UPDATE reponse SET contenu = ? WHERE id_reponse = ?");
            $success = $stmt->execute([$nouveau_contenu, $id_reponse]);
            echo json_encode(['success' => $success]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Pas ta réponse']);
        }
        break;

    case 'delete_reply':
        session_start();
        $input = json_decode(file_get_contents('php://input'), true);
        $id_reponse = $input['id'] ?? 0;

        $check = $pdo->prepare("SELECT id_auteur FROM reponse WHERE id_reponse = ? AND supprimee = 0");
        $check->execute([$id_reponse]);
        $auteur = $check->fetchColumn();
        $currentUserId = $_SESSION['user']['id'] ?? 0;

        if ($auteur == $currentUserId) {
            $stmt = $pdo->prepare("UPDATE reponse SET supprimee = 1 WHERE id_reponse = ?");
            $success = $stmt->execute([$id_reponse]);
            echo json_encode(['success' => $success]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Pas ta réponse']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Action non reconnue']);
        break;
}
?>