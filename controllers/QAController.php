<?php
class QAController {
    private $publicationModel;
    private $reponseModel;

    public function __construct($publicationModel, $reponseModel) {
        $this->publicationModel = $publicationModel;
        $this->reponseModel = $reponseModel;
    }

    public function index() {
        $filter = $_GET['filter'] ?? 'all';
        $publications = $this->publicationModel->getAllPublications($filter);
        $forums = $this->publicationModel->getForums();
        
        foreach ($publications as &$publication) {
            $publication['reponses'] = $this->reponseModel->getReponsesByPublication($publication['id_publication']);
        }
        
        require __DIR__ . '/../views/frontoffice/q&a.php';
    }

    public function createPublication() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idAuteur = 1;
            $titre = $_POST['title'] ?? '';
            $contenu = $_POST['content'] ?? '';
            $idForum = $_POST['forum'] ?? 1;
            
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $this->handleImageUpload($_FILES['image']);
            }

            if ($this->publicationModel->createPublication($idAuteur, $titre, $contenu, $idForum, $image)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Erreur de publication']);
                exit;
            }
        }
    }

    public function createReponse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPublication = $_POST['id_publication'] ?? 0;
            $idAuteur = 1;
            $contenu = $_POST['contenu'] ?? '';

            if ($this->reponseModel->createReponse($idPublication, $idAuteur, $contenu)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Erreur de réponse']);
                exit;
            }
        }
    }

    private function handleImageUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) {
            return null;
        }

        $uploadDir = 'storage/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        }

        return null;
    }
}
?>