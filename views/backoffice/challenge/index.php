<?php
require_once '../../controllers/ChallengeController.php';
$controller = new ChallengeController();
$challenges = $controller->list();
?>

<div class="container mt-5">
    <h2 class="text-center mb-4 neon">Gestion des Challenges</h2>
    <a href="views/backoffice/challenge/add.php" class="btn btn-success mb-4">+ Ajouter un Challenge</a>

    <div class="row">
        <?php foreach ($challenges as $c): ?>
        <div class="col-md-6 mb-4">
            <div class="card bg-dark text-light border-cyan">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($c['name']) ?></h5>
                    <p><strong>Association :</strong> <?= htmlspecialchars($c['association_nom']) ?></p>
                    <p><strong>Objectif :</strong> <?= number_format($c['objectif'], 2) ?> €</p>
                    <p><strong>Récompense :</strong> <?= htmlspecialchars($c['recompense']) ?></p>
                    <div class="progress mt-3" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: <?= ($c['progression']/$c['objectif']*100) ?>%">
                            <?= number_format(($c['progression']/$c['objectif']*100), 1) ?>%
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="edit.php?id=<?= $c['id_challenge'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="delete.php?id=<?= $c['id_challenge'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce challenge ?')">Supprimer</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>