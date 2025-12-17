<?php
require_once '../../config/config.php';
require_once '../../controllers/AssociationController.php';

$error = "";
$success = "";
$associationC = new AssociationController();

if ($_POST) {
    if (
        isset($_POST["name"]) &&
        isset($_POST["description"])
    ) {
        if (
            !empty($_POST["name"]) && 
            !empty($_POST["description"])
        ) {
            $result = $associationC->add(
                trim($_POST['name']),
                trim($_POST['description'])
            );

            if ($result) {
                $success = "Association créée avec succès !";
                // Optionnel: redirection après succès
                // header('Location: indexsinda.php');
            } else {
                $error = "Erreur lors de la création de l'association.";
            }
        } else {
            $error = "Le nom et la description sont obligatoires.";
        }
    } else {
        $error = "Données manquantes.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Association • Play2Help</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Space Mono', monospace; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
        }
        .card { 
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(34, 211, 238, 0.3); 
        }
        .neon { 
            text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; 
        }
        .glow:hover { 
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); 
        }
        .font-orbitron { 
            font-family: 'Orbitron', sans-serif; 
        }
        .form-input {
            background: rgba(30, 41, 59, 0.8);
            border: 2px solid rgba(34, 211, 238, 0.3);
            color: white;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #22d3ee;
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.5);
            outline: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(34, 211, 238, 0.4);
        }
        .btn-secondary {
            background: rgba(107, 114, 128, 0.8);
            border: 2px solid rgba(107, 114, 128, 0.5);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(107, 114, 128, 1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold neon font-orbitron mb-4">Nouvelle Association</h1>
            <p class="text-gray-400 text-lg">Créez une nouvelle association partenaire</p>
        </div>

        <!-- Formulaire -->
        <div class="max-w-2xl mx-auto">
            <div class="card rounded-3xl p-10 glow">
                <!-- Messages d'erreur/succès -->
                <?php if ($error): ?>
                    <div class="bg-red-900/30 border-2 border-red-500/50 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i data-feather="alert-circle" class="text-red-400 mr-3"></i>
                            <span class="text-red-300 font-semibold"><?= htmlspecialchars($error) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-900/30 border-2 border-green-500/50 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i data-feather="check-circle" class="text-green-400 mr-3"></i>
                            <span class="text-green-300 font-semibold"><?= htmlspecialchars($success) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <!-- Nom de l'association -->
                    <div>
                        <label for="name" class="block text-cyan-300 font-bold mb-3 text-lg">
                            <i data-feather="users" class="inline w-5 h-5 mr-2"></i>
                            Nom de l'association
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input w-full px-4 py-3 rounded-xl text-lg"
                            placeholder="Ex: UNICEF, WWF, Médecins Sans Frontières..."
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            required
                        >
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-cyan-300 font-bold mb-3 text-lg">
                            <i data-feather="file-text" class="inline w-5 h-5 mr-2"></i>
                            Description
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5"
                            class="form-input w-full px-4 py-3 rounded-xl text-lg resize-none"
                            placeholder="Décrivez la mission et les objectifs de l'association..."
                            required
                        ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Boutons -->
                    <div class="flex gap-4 pt-6">
                        <button 
                            type="submit" 
                            class="btn-primary flex-1 px-8 py-4 rounded-xl text-xl font-bold text-white"
                        >
                            <i data-feather="plus-circle" class="inline w-6 h-6 mr-2"></i>
                            Créer l'Association
                        </button>
                        
                        <a 
                            href="indexsinda.php" 
                            class="btn-secondary flex-1 px-8 py-4 rounded-xl text-xl font-bold text-white text-center inline-flex items-center justify-center"
                        >
                            <i data-feather="arrow-left" class="inline w-6 h-6 mr-2"></i>
                            Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="max-w-2xl mx-auto mt-8">
            <div class="card rounded-2xl p-6">
                <h3 class="text-xl font-bold text-purple-300 mb-4">
                    <i data-feather="info" class="inline w-5 h-5 mr-2"></i>
                    Informations importantes
                </h3>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-start">
                        <i data-feather="check" class="text-green-400 mr-2 mt-1 flex-shrink-0"></i>
                        Une fois créée, l'association sera disponible pour recevoir des dons
                    </li>
                    <li class="flex items-start">
                        <i data-feather="check" class="text-green-400 mr-2 mt-1 flex-shrink-0"></i>
                        Les utilisateurs pourront créer des challenges pour cette association
                    </li>
                    <li class="flex items-start">
                        <i data-feather="check" class="text-green-400 mr-2 mt-1 flex-shrink-0"></i>
                        Les statistiques seront automatiquement calculées
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        feather.replace();
        
        // Auto-focus sur le premier champ
        document.getElementById('name').focus();
        
        // Animation de succès
        <?php if ($success): ?>
        setTimeout(() => {
            if (confirm('Association créée avec succès ! Voulez-vous retourner au tableau de bord ?')) {
                window.location.href = 'indexsinda.php';
            }
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>