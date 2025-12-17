<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/PaymentController.php';
require_once __DIR__ . '/../../controllers/EmailController.php';
require_once __DIR__ . '/../../controllers/ExportController.php';

$session_id = $_GET['session_id'] ?? '';
$montant = $_GET['montant'] ?? 0;
$nom = $_GET['nom'] ?? '';
$email = $_GET['email'] ?? '';
$id_association = $_GET['id_association'] ?? 0;

// Vérifier le paiement avec Stripe
$verification = PaymentController::verifyPayment($session_id);

if (!$verification['success']) {
    header('Location: don.php?error=payment_failed');
    exit;
}

// Récupérer le nom de l'association
$stmt = $conn->prepare("SELECT name FROM association WHERE id_association = ?");
$stmt->bind_param("i", $id_association);
$stmt->execute();
$result = $stmt->get_result();
$association = $result->fetch_assoc();

// 1. Enregistrer le don dans la base de données
// Séparer le nom complet en prénom et nom
$nom_parts = explode(' ', $nom, 2);
$prenom = isset($nom_parts[1]) ? $nom_parts[0] : '';
$nom_famille = isset($nom_parts[1]) ? $nom_parts[1] : $nom_parts[0];

$stmt = $conn->prepare("INSERT INTO don (id_association, prenom, nom, email, montant, date_don) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("isssd", $id_association, $prenom, $nom_famille, $email, $montant);
$stmt->execute();
$don_id = $conn->insert_id;

// Préparer les données du don
$donData = [
    'id' => $don_id,
    'nom' => $nom, // Nom complet pour l'affichage
    'prenom' => $prenom,
    'nom_famille' => $nom_famille,
    'email' => $email,
    'montant' => $montant,
    'date_don' => date('Y-m-d H:i:s'),
    'association_nom' => $association['name'],
    'id_association' => $id_association
];

// 2. Générer le PDF AVANT d'envoyer l'email
$pdfResult = ExportController::generatePDF($don_id);
$pdfPath = $pdfResult['success'] ? $pdfResult['filepath'] : null;

// 3. Envoyer l'email au donateur AVEC le PDF attaché
$emailResult = EmailController::sendDonationReceipt($donData, $pdfPath);

// 4. Envoyer l'email à Play to Help
$adminEmailResult = EmailController::sendDonationReceiptToPlayToHelp($donData);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement réussi - Play to Help</title>
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <link rel="apple-touch-icon" href="assets/images/logooo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        .amount {
            font-size: 48px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 20px 0;
        }
        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 20px 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1 class="mb-3">🎉 Paiement réussi !</h1>
        <p class="text-muted">Merci pour votre générosité</p>
        
        <div class="amount"><?php echo number_format($montant, 2); ?> €</div>
        
        <div class="info-box">
            <h5>📋 Détails de votre don</h5>
            <p class="mb-1"><strong>Donateur :</strong> <?php echo htmlspecialchars($nom); ?></p>
            <p class="mb-1"><strong>Email :</strong> <?php echo htmlspecialchars($email); ?></p>
            <p class="mb-1"><strong>Association :</strong> <?php echo htmlspecialchars($association['name']); ?></p>
            <p class="mb-0"><strong>Date :</strong> <?php echo date('d/m/Y à H:i'); ?></p>
        </div>
        
        <?php if ($emailResult['success']): ?>
            <div class="alert alert-success">
                ✅ Un email de confirmation a été envoyé à <?php echo htmlspecialchars($email); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($pdfResult['success']): ?>
            <a href="download_pdf.php?id=<?php echo $don_id; ?>" class="btn-download">
                📄 Télécharger le reçu PDF
            </a>
        <?php endif; ?>
        
        <a href="Accueil.php" class="btn-download" style="background: #6c757d;">
            🏠 Retour à l'accueil
        </a>
        
        <a href="don.php" class="btn-download" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            💚 Faire un autre don
        </a>
        
        <p class="text-muted mt-4" style="font-size: 14px;">
            Votre contribution aide directement l'association à réaliser ses missions.
        </p>
    </div>
</body>
</html>
