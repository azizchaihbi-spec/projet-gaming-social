<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Ne pas afficher les erreurs dans la réponse JSON

try {
    require_once __DIR__ . '/../../controllers/PaymentController.php';
    require_once __DIR__ . '/../../config/config.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur de chargement: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = floatval($_POST['montant']);
    $donateur_nom = $_POST['nom'];
    $donateur_email = $_POST['email'];
    $id_association = intval($_POST['id_association']);
    
    // Validation
    if ($montant <= 0) {
        echo json_encode(['success' => false, 'error' => 'Montant invalide']);
        exit;
    }
    
    if (empty($donateur_nom) || empty($donateur_email)) {
        echo json_encode(['success' => false, 'error' => 'Informations manquantes']);
        exit;
    }
    
    try {
        // Récupérer le nom de l'association
        $stmt = $conn->prepare("SELECT name FROM association WHERE id_association = ?");
        $stmt->bind_param("i", $id_association);
        $stmt->execute();
        $result = $stmt->get_result();
        $association = $result->fetch_assoc();
        
        if (!$association) {
            echo json_encode(['success' => false, 'error' => 'Association introuvable (ID: ' . $id_association . ')']);
            exit;
        }
        
        // Créer la session Stripe
        $result = PaymentController::createStripeCheckout(
            $montant,
            $donateur_nom,
            $donateur_email,
            $id_association,
            $association['name']
        );
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'sessionId' => $result['sessionId'],
                'redirect_url' => $result['url']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $result['error']
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
