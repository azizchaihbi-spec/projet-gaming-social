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
    die("Erreur de connexion : " . $e->getMessage());
}

require_once 'models/PublicationModel.php';
require_once 'models/ReponseModel.php';

$publicationModel = new PublicationModel($pdo);
$reponseModel = new ReponseModel($pdo);

// Router
$page = $_GET['page'] ?? 'front';

switch ($page) {
    case 'admin':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController($publicationModel, $reponseModel);
        $controller->index();
        break;
        
    case 'front':
    default:
        require_once 'controllers/QAController.php';
        $controller = new QAController($publicationModel, $reponseModel);
        
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'create_publication':
                $controller->createPublication();
                break;
            case 'create_reponse':
                $controller->createReponse();
                break;
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;
}
?>