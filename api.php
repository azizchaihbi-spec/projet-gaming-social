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
        $input = json_decode(file_get_contents('php://input'), true);
        $idPublication = $input['id_publication'] ?? 0;
        $contenu = $input['contenu'] ?? '';
        $idAuteur = 1;
        
        if ($reponseModel->createReponse($idPublication, $idAuteur, $contenu)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'create_publication':
        $input = json_decode(file_get_contents('php://input'), true);
        $titre = $input['titre'] ?? '';
        $contenu = $input['contenu'] ?? '';
        $idForum = $input['id_forum'] ?? 1;
        $idAuteur = 1; // À remplacer par $_SESSION['id_user']
        
        // Nouvelles données pour emojis, GIFs et stickers
        $emojis = $input['emojis'] ?? null;
        $gifUrl = $input['gif_url'] ?? null;
        $stickerUrl = $input['sticker_url'] ?? null;
        
        if ($publicationModel->createPublicationWithMedia($idAuteur, $titre, $contenu, $idForum, null, $emojis, $gifUrl, $stickerUrl)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'edit_post':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $titre = $input['titre'] ?? '';
        $contenu = $input['contenu'] ?? '';
        
        if ($publicationModel->updatePublication($id, $titre, $contenu)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

   case 'delete_post':
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    
    error_log("=== DEBUG SUPPRESSION ===");
    error_log("ID à supprimer: " . $id);
    
    $success = $publicationModel->deletePublication($id);
    
    error_log("Résultat suppression: " . ($success ? 'SUCCÈS' : 'ÉCHEC'));
    error_log("=== FIN DEBUG ===");
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
    }
    break;
        
    default:
        echo json_encode(['error' => 'Action non reconnue']); 
    
        case 'edit_reply':
        $input = json_decode(file_get_contents('php://input'), true);
        $id_reponse = $input['id'] ?? 0;
        $nouveau_contenu = $input['contenu'] ?? '';

        // Vérifie que c'est bien l'auteur (id_auteur = 1 pour l'instant)
        $check = $pdo->prepare("SELECT id_auteur FROM reponse WHERE id_reponse = ? AND supprimee = 0");
        $check->execute([$id_reponse]);
        $auteur = $check->fetchColumn();

        if ($auteur == 1) { // ← plus tard : $_SESSION['id_user']
            $stmt = $pdo->prepare("UPDATE reponse SET contenu = ? WHERE id_reponse = ?");
            $success = $stmt->execute([$nouveau_contenu, $id_reponse]);
            echo json_encode(['success' => $success]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Pas ta réponse']);
        }
        break;

    case 'delete_reply':
        $input = json_decode(file_get_contents('php://input'), true);
        $id_reponse = $input['id'] ?? 0;

        $check = $pdo->prepare("SELECT id_auteur FROM reponse WHERE id_reponse = ? AND supprimee = 0");
        $check->execute([$id_reponse]);
        $auteur = $check->fetchColumn();

        if ($auteur == 1) {
            $stmt = $pdo->prepare("UPDATE reponse SET supprimee = 1 WHERE id_reponse = ?");
            $success = $stmt->execute([$id_reponse]);
            echo json_encode(['success' => $success]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Pas ta réponse']);
        }
        break;
}
?>