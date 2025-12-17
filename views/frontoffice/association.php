<?php
require_once '../../config/config.php';

// Récupérer les associations avec les totaux réels des dons
try {
    $stmt = $pdo->query("
        SELECT a.*, 
               COALESCE(SUM(d.montant), 0) as total_dons_reel,
               COUNT(d.id_don) as nombre_donateurs
        FROM association a 
        LEFT JOIN don d ON a.id_association = d.id_association 
        GROUP BY a.id_association 
        ORDER BY total_dons_reel DESC
    ");
    $associations = $stmt->fetchAll();
} catch (Exception $e) {
    $associations = [];
    error_log("Erreur lors de la récupération des associations: " . $e->getMessage());
}

// Récupérer les challenges depuis la base de données
try {
    $stmt = $pdo->query("
        SELECT c.*, a.name as association_name 
        FROM challenge c 
        JOIN association a ON c.id_association = a.id_association 
        ORDER BY c.progression DESC
    ");
    $challenges = $stmt->fetchAll();
} catch (Exception $e) {
    $challenges = [];
    error_log("Erreur lors de la récupération des challenges: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Play to Help - Associations Partenaires</title>
  <link rel="icon" type="image/png" href="assets/images/logooo.png">
  <link rel="apple-touch-icon" href="assets/images/logooo.png">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/fontawesome.css" />
  <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
  <link rel="stylesheet" href="assets/css/owl.css" />
  <link rel="stylesheet" href="assets/css/animate.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
  <link rel="stylesheet" href="assets/css/dons-assoc.css" />
  
</head>
<body>
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots"><span></span><span></span><span></span></div>
        </div>
    </div>
    
    <?php include 'includes/header.php'; ?>

  <!-- HERO CREATIVE -->
  <section class="hero-banner" aria-label="Intro section">
    <h2>Rejoignez la Révolution Gaming Solidaire !</h2>
    <p>Plongez dans un univers où chaque kill, chaque stream, et chaque don devient une arme contre l'injustice. Choisissez votre association, lancez un défi épique, et transformez vos victoires virtuelles en victoires réelles.</p>
    <a href="don.php" class="btn-hero" role="button">Lancez Votre Défi Maintenant</a>
  </section>

  <!-- Associations Vedettes Slider -->
  <section class="container my-5" aria-label="Associations en vedette">
    <div class="heading-section mb-4 text-center">
      <h4><em>Nos</em> Associations Partenaires</h4>
    </div>
    <div class="slider-container">
      <div class="owl-features owl-carousel" id="assoc-slider" aria-live="polite" aria-roledescription="carousel">
        <?php foreach ($associations as $index => $association): ?>
          <?php if ($index < 4): // Limiter à 4 associations dans le slider ?>
            <div class="item" role="group" aria-label="Association <?= htmlspecialchars($association['name']) ?>">
              <div class="thumb">
                <img src="assets/images/assoc-<?= ($index % 2) + 1 ?>.jpg" alt="<?= htmlspecialchars($association['name']) ?>" />
                <div class="hover-effect"><h6>Total Dons : <?= number_format($association['total_dons_reel'], 0, ',', ' ') ?>€</h6></div>
              </div>
              <h4><?= htmlspecialchars($association['name']) ?><br /><span><?= htmlspecialchars(substr($association['description'], 0, 50)) ?>...</span></h4>
              <ul>
                <li><i class="fa fa-heart"></i> <?= $association['nombre_donateurs'] ?> Dons</li>
                <li><a href="don.php?association=<?= $association['id_association'] ?>" class="neon-btn" role="button">Soutenir</a></li>
              </ul>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Liste Associations -->
  <section class="container" aria-label="Liste des associations">
    <div id="liste-associations">
      <?php foreach ($associations as $association): ?>
        <div class="solidaire-card clickable-card" 
             role="region" 
             aria-labelledby="assoc-<?= $association['id_association'] ?>-title"
             data-id="<?= $association['id_association'] ?>"
             data-name="<?= htmlspecialchars($association['name']) ?>"
             data-description="<?= htmlspecialchars($association['description']) ?>"
             data-total="<?= number_format($association['total_dons_reel'], 0, ',', ' ') ?>"
             data-donateurs="<?= $association['nombre_donateurs'] ?>"
             data-email="<?= htmlspecialchars($association['email'] ?? 'contact@playtohelp.com') ?>"
             data-website="<?= htmlspecialchars($association['website'] ?? '') ?>">
          <div class="card-click-hint">👆 Cliquez pour plus de détails</div>
          <h4 id="assoc-<?= $association['id_association'] ?>-title"><?= htmlspecialchars($association['name']) ?></h4>
          <p><?= htmlspecialchars($association['description']) ?></p>
          <ul>
            <li><i class="fa fa-euro"></i> Total : <?= number_format($association['total_dons_reel'], 0, ',', ' ') ?>€</li>
            <li><i class="fa fa-users"></i> Soutiens : <?= $association['nombre_donateurs'] ?></li>
          </ul>
          <a href="don.php?association=<?= $association['id_association'] ?>" class="btn neon-btn" role="button" onclick="event.stopPropagation();">Faire un Don</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Modal Détails Association -->
  <div class="modal fade" id="modalAssociation" tabindex="-1" aria-labelledby="modalAssociationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content association-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAssociationLabel">
            <span class="modal-icon">🏢</span>
            <span id="modal-assoc-name">Nom de l'association</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="assoc-detail-grid">
            <div class="assoc-detail-main">
              <div class="detail-section">
                <h6><i class="fa fa-info-circle"></i> Description</h6>
                <p id="modal-assoc-description">Description de l'association...</p>
              </div>
              
              <div class="detail-section">
                <h6><i class="fa fa-envelope"></i> Contact</h6>
                <p id="modal-assoc-email">email@association.com</p>
              </div>
              
              <div class="detail-section" id="website-section" style="display: none;">
                <h6><i class="fa fa-globe"></i> Site Web</h6>
                <a id="modal-assoc-website" href="#" target="_blank">Visiter le site</a>
              </div>
            </div>
            
            <div class="assoc-detail-stats">
              <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value" id="modal-assoc-total">0€</div>
                <div class="stat-label">Total des dons</div>
              </div>
              
              <div class="stat-card">
                <div class="stat-icon">❤️</div>
                <div class="stat-value" id="modal-assoc-donateurs">0</div>
                <div class="stat-label">Donateurs</div>
              </div>
              
              <div class="stat-card">
                <div class="stat-icon">🎮</div>
                <div class="stat-value">Play to Help</div>
                <div class="stat-label">Partenaire officiel</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          <a id="modal-donate-btn" href="don.php" class="btn neon-btn">💚 Faire un Don</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Challenges actifs -->
  <section class="container my-5" aria-label="Challenges actifs">
    <div class="heading-section mb-4 text-center">
      <h4><em>Challenges</em> Actifs pour ces Associations</h4>
    </div>
    <div id="challenges-assoc">
      <?php foreach ($challenges as $index => $challenge): ?>
        <?php if ($index < 6): // Limiter l'affichage ?>
          <?php 
            $pourcentage = ($challenge['objectif'] > 0) ? ($challenge['progression'] / $challenge['objectif']) * 100 : 0;
            $pourcentage = min(100, max(0, $pourcentage)); // Limiter entre 0 et 100%
          ?>
          <div class="challenge-card" role="group" aria-label="Défi <?= htmlspecialchars($challenge['name']) ?> pour <?= htmlspecialchars($challenge['association_name']) ?>">
            <div class="thumb">
              <img src="assets/images/feature-<?= ($index % 2 == 0) ? 'left' : 'right' ?>.jpg" alt="Défi <?= htmlspecialchars($challenge['association_name']) ?>" />
              <div class="hover-effect"><h6>Défi : <?= htmlspecialchars($challenge['name']) ?></h6></div>
            </div>
            <div class="down-content">
              <h4><?= htmlspecialchars($challenge['name']) ?> pour <?= htmlspecialchars($challenge['association_name']) ?></h4>
              <p>Récompense : <?= htmlspecialchars($challenge['recompense']) ?></p>
              <div class="progression-bar">
                <div class="progression-fill" style="width: <?= $pourcentage ?>%;"></div>
              </div>
              <small><?= number_format($challenge['progression'], 0, ',', ' ') ?>€ / <?= number_format($challenge['objectif'], 0, ',', ' ') ?>€ (<?= number_format($pourcentage, 0) ?>%)</small>
              <a href="streams.php" class="neon-btn" role="button">Rejoindre en Stream</a>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <div class="main-button">
        <a href="don.php" class="btn-donate" role="button">Faire un Don Maintenant</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <!-- SCRIPTS -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="assets/js/dons-assoc.js"></script>
</body>
</html>