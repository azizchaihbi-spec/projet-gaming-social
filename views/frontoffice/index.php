<?php
// Routes front depuis le dossier frontoffice
$basePath = dirname(__DIR__, 2);

require_once $basePath . '/config/config.php';
require_once $basePath . '/models/PublicationModel.php';
require_once $basePath . '/models/ReponseModel.php';

$pdo = config::getConnexion();

$publicationModel = new PublicationModel($pdo);
$reponseModel = new ReponseModel($pdo);

$page = $_GET['page'] ?? 'front';

switch ($page) {
	case 'admin':
		require_once $basePath . '/controllers/AdminController.php';
		$controller = new AdminController($publicationModel, $reponseModel);
		$controller->index();
		break;

	case 'front':
	default:
		require_once $basePath . '/controllers/QAController.php';
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
