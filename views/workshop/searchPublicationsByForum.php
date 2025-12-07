<?php
/**
 * searchPublicationsByForum.php
 * WORKSHOP JOINTURE - Recherche de publications par forum
 * Chemin: C:\xampp\htdocs\play-to-help\views\workshop\searchPublicationsByForum.php
 */

require_once '../../config/database.php';

// Connexion BDD
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER, 
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

require_once '../../models/PublicationModel.php';
$publicationModel = new PublicationModel($pdo);

// Récupérer tous les forums
$forums = $publicationModel->getForums();

// Initialisation
$publications = [];
$forumSelectionne = null;
$statsForums = [];

// STATISTIQUES PAR FORUM (JOINTURE + GROUP BY)
try {
    $sql = "SELECT f.id_forum, 
                   f.nom as forum_nom, 
                   f.couleur,
                   COUNT(p.id_publication) as nb_publications
            FROM forum f
            LEFT JOIN publication p ON f.id_forum = p.id_forum AND p.supprimee = 0
            GROUP BY f.id_forum, f.nom, f.couleur
            ORDER BY nb_publications DESC";
    $stmt = $pdo->query($sql);
    $statsForums = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur stats: " . $e->getMessage());
}

// Si un forum est sélectionné
if (isset($_GET['id_forum']) && !empty($_GET['id_forum'])) {
    $idForum = $_GET['id_forum'];
    
    // JOINTURE PRINCIPALE : Récupérer les publications du forum
    try {
        $sql = "SELECT p.*, 
                       f.nom as forum_nom, 
                       f.description as forum_description,
                       f.couleur as forum_couleur,
                       u.prenom, 
                       u.nom as auteur_nom,
                       (SELECT COUNT(*) FROM reponse r WHERE r.id_publication = p.id_publication AND r.supprimee = 0) as nb_reponses
                FROM publication p
                INNER JOIN forum f ON p.id_forum = f.id_forum
                INNER JOIN utilisateur u ON p.id_auteur = u.id_user
                WHERE p.id_forum = ? AND p.supprimee = 0
                ORDER BY p.date_publication DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idForum]);
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les infos du forum sélectionné
        foreach ($forums as $forum) {
            if ($forum['id_forum'] == $idForum) {
                $forumSelectionne = $forum;
                break;
            }
        }
    } catch (PDOException $e) {
        error_log("Erreur publications: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Jointure - Recherche par Forum</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #0d001a, #1a0033);
            color: #e0e0ff;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .workshop-header {
            background: linear-gradient(135deg, rgba(110,110,255,0.2), rgba(255,105,180,0.2));
            border: 3px solid #ff69b4;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 0 50px rgba(255,105,180,0.3);
        }
        
        .workshop-header h1 {
            color: #ff69b4;
            font-size: 3rem;
            margin-bottom: 15px;
            text-shadow: 0 0 20px #ff1493;
        }
        
        .workshop-header .badge {
            background: linear-gradient(45deg, #6e6eff, #ff69b4);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            margin-top: 10px;
        }

        .forum-selector {
            background: rgba(20, 20, 40, 0.8);
            padding: 35px;
            border-radius: 15px;
            border: 2px solid #6e6eff;
            margin: 30px 0;
            box-shadow: 0 10px 40px rgba(110, 110, 255, 0.3);
        }
        
        .forum-selector h2 {
            color: #ff69b4;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        
        .forum-selector select {
            width: 100%;
            padding: 18px 25px;
            font-size: 1.2rem;
            background: rgba(10, 10, 30, 0.9);
            border: 2px solid #6e6eff;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .forum-selector select:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 25px rgba(255, 105, 180, 0.5);
        }
        
        .forum-selector button {
            width: 100%;
            margin-top: 20px;
            background: linear-gradient(45deg, #ff1493, #ff69b4);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .forum-selector button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 105, 180, 0.6);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        
        .stat-card {
            background: rgba(110, 110, 255, 0.15);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            border: 2px solid;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(110, 110, 255, 0.4);
        }
        
        .stat-card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .stat-card .count {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
        }

        .selected-forum-banner {
            background: rgba(255, 105, 180, 0.1);
            border: 2px solid;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        
        .selected-forum-banner h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .sql-example {
            background: rgba(10, 10, 30, 0.9);
            border: 2px solid #39ff14;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            font-family: 'Courier New', monospace;
            color: #39ff14;
        }
        
        .sql-example h3 {
            color: #ff69b4;
            margin-bottom: 15px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        
        .sql-example code {
            display: block;
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .publication-card {
            background: rgba(15, 15, 35, 0.95);
            border: 2px solid #6e6eff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.4s ease;
        }
        
        .publication-card:hover {
            transform: translateY(-8px);
            border-color: #ff69b4;
            box-shadow: 0 15px 45px rgba(255, 105, 180, 0.4);
        }
        
        .publication-title {
            color: #ff69b4;
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .publication-meta {
            color: #6e6eff;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .publication-content {
            color: #ddd;
            line-height: 1.7;
            margin: 15px 0;
        }
        
        .publication-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid rgba(110, 110, 255, 0.3);
        }
        
        .likes-info {
            color: #39ff14;
            font-weight: bold;
        }
        
        .replies-badge {
            background: rgba(255, 105, 180, 0.2);
            color: #ff69b4;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            border: 1px solid #ff69b4;
        }

        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: #ff69b4;
            font-size: 1.5rem;
        }
        
        .back-link {
            text-align: center;
            margin: 50px 0;
        }
        
        .back-link a {
            color: #6e6eff;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: bold;
            padding: 15px 30px;
            border: 2px solid #6e6eff;
            border-radius: 10px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            background: rgba(110, 110, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .section-title {
            color: #ff69b4;
            text-align: center;
            margin: 50px 0 30px;
            font-size: 2rem;
            text-shadow: 0 0 15px rgba(255, 105, 180, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header du Workshop -->
        <div class="workshop-header">
            <h1>🔗 Workshop Jointure SQL</h1>
            <p style="color: #b0b0ff; font-size: 1.2rem; margin: 15px 0;">
                Recherche de publications par forum avec JOINTURE entre tables
            </p>
            <span class="badge">publication ⟷ forum ⟷ utilisateur</span>
        </div>

        <!-- Statistiques par forum -->
        <h2 class="section-title">📊 Statistiques par Forum (JOINTURE + GROUP BY)</h2>
        <div class="stats-grid">
            <?php foreach ($statsForums as $stat): ?>
                <div class="stat-card" style="border-color: <?= htmlspecialchars($stat['couleur']) ?>;">
                    <h3 style="color: <?= htmlspecialchars($stat['couleur']) ?>;">
                        <?= htmlspecialchars($stat['forum_nom']) ?>
                    </h3>
                    <div class="count"><?= $stat['nb_publications'] ?></div>
                    <div style="color: #aaa; font-size: 0.9rem;">publications</div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulaire de sélection -->
        <div class="forum-selector">
            <h2>🎯 Sélectionner un Forum</h2>
            <form method="GET" action="">
                <select name="id_forum" id="forumSelect" required>
                    <option value="">-- Choisir un forum --</option>
                    <?php foreach ($forums as $forum): ?>
                        <option value="<?= htmlspecialchars($forum['id_forum']) ?>"
                                <?= (isset($_GET['id_forum']) && $_GET['id_forum'] == $forum['id_forum']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($forum['nom']) ?> - <?= htmlspecialchars($forum['description']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">🔍 Afficher les publications</button>
            </form>
        </div>

        <!-- Affichage du forum sélectionné -->
        <?php if ($forumSelectionne): ?>
            <div class="selected-forum-banner" style="border-color: <?= htmlspecialchars($forumSelectionne['couleur']) ?>;">
                <h2 style="color: <?= htmlspecialchars($forumSelectionne['couleur']) ?>;">
                    📁 <?= htmlspecialchars($forumSelectionne['nom']) ?>
                </h2>
                <p style="color: #b0b0ff; font-size: 1.1rem; margin-top: 10px;">
                    <?= htmlspecialchars($forumSelectionne['description']) ?>
                </p>
                <p style="color: #ddd; margin-top: 15px;">
                    <strong><?= count($publications) ?></strong> publication(s) trouvée(s)
                </p>
            </div>

            <!-- Exemple de requête SQL -->
            <div class="sql-example">
                <h3>💻 Requête SQL avec JOINTURE utilisée :</h3>
                <code>SELECT p.*, 
       f.nom as forum_nom, 
       u.prenom, 
       u.nom as auteur_nom
FROM publication p
<strong style="color: #ff69b4;">INNER JOIN forum f ON p.id_forum = f.id_forum</strong>
<strong style="color: #6e6eff;">INNER JOIN utilisateur u ON p.id_auteur = u.id_user</strong>
WHERE p.id_forum = <?= $idForum ?> AND p.supprimee = 0
ORDER BY p.date_publication DESC;</code>
            </div>
        <?php endif; ?>

        <!-- Liste des publications -->
        <?php if (!empty($publications)): ?>
            <h2 class="section-title">📝 Publications du forum</h2>
            
            <?php foreach ($publications as $pub): ?>
                <div class="publication-card">
                    <div class="publication-title">
                        <?= htmlspecialchars($pub['titre']) ?>
                    </div>
                    
                    <div class="publication-meta">
                        👤 Par <strong><?= htmlspecialchars($pub['prenom']) ?> <?= htmlspecialchars($pub['auteur_nom']) ?></strong>
                        • 📅 <?= date('d/m/Y à H:i', strtotime($pub['date_publication'])) ?>
                        • 📁 Forum: <span style="color: <?= htmlspecialchars($pub['forum_couleur']) ?>;">
                            <?= htmlspecialchars($pub['forum_nom']) ?>
                        </span>
                    </div>
                    
                    <div class="publication-content">
                        <?= nl2br(htmlspecialchars($pub['contenu'])) ?>
                    </div>
                    
                    <?php if (!empty($pub['image'])): ?>
                        <img src="/play-to-help/<?= htmlspecialchars($pub['image']) ?>" 
                             alt="Image" 
                             style="max-width: 100%; border-radius: 10px; margin-top: 15px;">
                    <?php endif; ?>
                    
                    <div class="publication-footer">
                        <div class="likes-info">
                            👍 <?= $pub['likes'] ?> likes
                        </div>
                        <div class="replies-badge">
                            💬 <?= $pub['nb_reponses'] ?> réponse(s)
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
        <?php elseif (isset($_GET['id_forum'])): ?>
            <div class="no-results">
                😢 Aucune publication dans ce forum pour le moment
            </div>
        <?php endif; ?>

        <!-- Lien retour -->
        <div class="back-link">
            <a href="/play-to-help/index.php?page=front">
                ← Retour au Forum Q&A
            </a>
        </div>
    </div>

    <script>
        // Soumission automatique au changement
        document.getElementById('forumSelect').addEventListener('change', function() {
            if (this.value) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>