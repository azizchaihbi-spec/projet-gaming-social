<?php
require_once '../../config/config.php';
require_once '../../models/Don.php';
require_once '../../controllers/DonController.php';

$error = "";
$success = "";
$bookC = new DonController();

if ($_POST) {
    if (
        isset($_POST["prenom"]) &&
        isset($_POST["nom"]) &&
        isset($_POST["email"]) &&
        isset($_POST["montant"]) &&
        isset($_POST["id_association"])
    ) {
        if (
            !empty($_POST["montant"]) && 
            $_POST["montant"] > 0 && 
            !empty($_POST["id_association"])
        ) {
            $don = new Don(
                null,
                (int)$_POST['id_association'],
                $_POST['prenom'] ?: null,
                $_POST['nom'] ?: null,
                $_POST['email'] ?: null,
                (float)$_POST['montant']
            );

            $bookC->add($don);

            // Si c'est une requête AJAX depuis le frontoffice
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => number_format($_POST['montant'], 2) . ' €']);
                exit;
            }

            // Sinon, redirection classique (si accès direct)
            $success = "Don ajouté avec succès !";
            // header('Location: indexsinda.php');
        } else {
            $error = "Montant et association sont obligatoires.";
        }
    } else {
        $error = "Données manquantes.";
    }
}

// Pour le backoffice (accès direct)
$associations = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Don - Backoffice</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../assets/css/main.css" />
</head>
<body>
    <!-- Ton sidebar et header backoffice ici si tu veux -->

    <div class="container mt-5">
        <h2 class="text-center mb-4">Ajouter un Don (Backoffice)</h2>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Association *</label>
                    <select name="id_association" class="form-control" required>
                        <option value="">Sélectionner</option>
                        <?php foreach($associations as $a): ?>
                            <option value="<?= $a['id_association'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Montant (€) *</label>
                    <input type="number" step="0.01" name="montant" class="form-control" required min="0.01">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">Ajouter le Don</button>
                <a href="indexsinda.php" class="btn btn-secondary btn-lg ms-3">Retour</a>
            </div>
        </form>
    </div>
</body>
</html>