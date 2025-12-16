<?php
require_once '../../config/config.php';
require_once '../../models/Don.php';
require_once '../../controllers/DonController.php';

$controller = new DonController();
$error = $success = "";

if (!isset($_GET['id'])) { 
    header('Location: indexsinda.php'); 
    exit; 
}

$don = $controller->getOne($_GET['id']);
if (!$don) { 
    die("Don introuvable"); 
}

if ($_POST) {
    // Validation côté serveur rapide (au cas où JS désactivé)
    $montant = (float)($_POST['montant'] ?? 0);
    $association = (int)($_POST['id_association'] ?? 0);
    $email = trim($_POST['email'] ?? '');

    if ($montant <= 0) {
        $error = "Le montant doit être supérieur à 0";
    } elseif ($association <= 0) {
        $error = "Veuillez choisir une association";
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email est invalide";
    } else {
        $don->setIdAssociation($association);
        $don->setPrenom($_POST['prenom'] ?? null);
        $don->setNom($_POST['nom'] ?? null);
        $don->setEmail($email ?: null);
        $don->setMontant($montant);

        $controller->update($don);
        $success = "Don modifié avec succès !";
    }
}

$associations = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-900 text-green-200">
<head>
    <meta charset="UTF-8">
    <title>Modifier Don #<?= $don->getIdDon() ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../style.css">
</head>
<body class="font-mono">
    <custom-navbar></custom-navbar>

    <main class="container mx-auto px-6 py-10 max-w-3xl">
        <div class="bg-gray-800 rounded-xl border border-green-400 p-8 shadow-2xl">
            <h2 class="text-3xl font-bold mb-8 text-center neon-glow">Modifier Don #<?= $don->getIdDon() ?></h2>

            <?php if($success): ?>
                <div class="bg-green-900 border border-green-500 text-green-200 px-6 py-4 rounded-lg mb-6 text-center font-bold">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="bg-red-900 border border-red-500 text-red-200 px-6 py-4 rounded-lg mb-6 text-center">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form id="formEditDon" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Association -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Association <span class="text-red-400">*</span></label>
                        <select name="id_association" class="w-full bg-gray-700 border rounded-lg px-4 py-3 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 outline-none transition">
                            <option value="">Choisir une association...</option>
                            <?php foreach($associations as $a): ?>
                                <option value="<?= $a['id_association'] ?>" <?= $a['id_association'] == $don->getIdAssociation() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-red-400 hidden" id="error-association">Veuillez sélectionner une association</p>
                    </div>

                    <!-- Montant -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Montant (€) <span class="text-red-400">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="montant" 
                            value="<?= $don->getMontant() ?>" 
                            placeholder="10.00"
                            class="w-full bg-gray-700 border rounded-lg px-4 py-3 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 outline-none transition">
                        <p class="mt-1 text-xs text-red-400 hidden" id="error-montant">Le montant doit être supérieur à 0 €</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Prénom <span class="text-gray-500 text-xs">(facultatif)</span></label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($don->getPrenom() ?? '') ?>" class="w-full bg-gray-700 border rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Nom <span class="text-gray-500 text-xs">(facultatif)</span></label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($don->getNom() ?? '') ?>" class="w-full bg-gray-700 border rounded-lg px-4 py-3">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-2">Email <span class="text-gray-500 text-xs">(facultatif)</span></label>
                    <input 
                        type="text" 
                        name="email" 
                        value="<?= htmlspecialchars($don->getEmail() ?? '') ?>" 
                        placeholder="jean@example.com"
                        class="w-full bg-gray-700 border rounded-lg px-4 py-3 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 outline-none transition">
                    <p class="mt-1 text-xs text-red-400 hidden" id="error-email">Email invalide (ex: jean@exemple.com)</p>
                </div>

                <div class="text-center pt-8">
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-white font-bold py-4 px-12 rounded-lg transition transform hover:scale-105 shadow-lg">
                        Sauvegarder les modifications
                    </button>
                    <a href="indexsinda.php" class="inline-block mt-6 ml-6 text-gray-400 hover:text-green-400 underline">Annuler</a>
                </div>
            </form>
        </div>
    </main>

    <custom-footer></custom-footer>
    <script src="../../components/navbar.js"></script>
    <script src="../../components/footer.js"></script>

    <!-- VALIDATION JS IDENTIQUE À don.php -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("formEditDon");
        if (!form) return;

        function clearErrors() {
            document.querySelectorAll("[id^='error-']").forEach(el => el.classList.add("hidden"));
            document.querySelectorAll("input, select").forEach(el => {
                el.classList.remove("border-red-500", "ring-2", "ring-red-500");
                el.classList.add("border-gray-600");
            });
        }

        function showError(id, focusField = null) {
            const errorEl = document.getElementById(id);
            if (errorEl) errorEl.classList.remove("hidden");
            if (focusField) {
                focusField.classList.add("border-red-500", "ring-2", "ring-red-500");
                focusField.focus();
            }
        }

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            clearErrors();

            const email = form.email.value.trim();
            const montant = parseFloat(form.montant.value);
            const association = form.id_association.value;

            let hasError = false;

            // Email : facultatif mais valide si rempli
            if (email !== "" && !/^\S+@\S+\.\S+$/.test(email)) {
                showError("error-email", form.email);
                hasError = true;
            }

            // Montant
            if (!montant || montant <= 0) {
                showError("error-montant", form.montant);
                hasError = true;
            }

            // Association
            if (!association || association === "") {
                showError("error-association", form.id_association);
                hasError = true;
            }

            if (!hasError) {
                form.submit(); // Envoi réel du formulaire
            }
        });
    });
    </script>
</body>
</html>