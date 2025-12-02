<?php
class AdminController {
    private $publicationModel;
    private $reponseModel;

    public function __construct($publicationModel, $reponseModel) {
        $this->publicationModel = $publicationModel;
        $this->reponseModel = $reponseModel;
    }

    public function index() {
        require 'views/backoffice/admin.php';
    }

    public function getStats() {
        header('Content-Type: application/json');
        
        try {
            $totalPosts = $this->publicationModel->getTotalPublications();
            $totalAnswers = $this->reponseModel->getTotalReponses();
            $bannedCount = 0; // À implémenter si vous avez une table banned_users
            
            $posts = $this->publicationModel->getAllPublicationsForAdmin();
            
            echo json_encode([
                'stats' => [
                    'totalPosts' => $totalPosts,
                    'totalAnswers' => $totalAnswers,
                    'bannedCount' => $bannedCount
                ],
                'posts' => $posts
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deletePost() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            header('Content-Type: application/json');
            
            if ($this->publicationModel->deletePublication($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }
}
?>