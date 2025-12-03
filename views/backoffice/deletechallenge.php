<?php
// backoffice/components/deletechallenge.php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /play to help/views/frontoffice/index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $challengeC = new ChallengeController();
        $challengeC->delete((int)$_POST['id']);
        
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>