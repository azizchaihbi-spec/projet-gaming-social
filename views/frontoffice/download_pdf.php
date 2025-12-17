<?php
require_once __DIR__ . '/../../controllers/ExportController.php';

$don_id = $_GET['id'] ?? 0;

if ($don_id <= 0) {
    die('ID de don invalide');
}

$result = ExportController::generatePDF($don_id);

if ($result['success']) {
    $filepath = $result['filepath'];
    $filename = $result['filename'];
    
    if (file_exists($filepath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        die('Fichier PDF introuvable');
    }
} else {
    die('Erreur lors de la génération du PDF: ' . $result['error']);
}
