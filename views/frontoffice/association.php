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
    
    <!-- HEADER -->
    <header id="mainHeader" class="header-area header-sticky">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <nav class="main-nav d-flex align-items-center justify-content-between">
                        <a href="index.html" class="logo">
                            <img src="assets/images/logooo.png" alt="Play to Help - Manette Solidaire" height="50">
                        </a>
                        <div class="search-input" style="flex-grow: 1; max-width: 400px; margin-left: 20px;">
                            <form id="search" action="search.html" class="d-flex align-items-center">
                                <input type="text" class="form-control" placeholder="Rechercher association, don ou challenge..." name="q" />
                                <button type="submit" style="background:none; border:none; color:#666; font-size:1.2em; cursor:pointer;">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <span class="sr-only">Rechercher</span>
                                </button>
                            </form>
                        </div>
                        <ul class="nav d-flex align-items-center mb-0">
                            <li><a href="index.html">Accueil</a></li>
                            <li><a href="browse.html">Événements</a></li>
                            <li><a href="streams.html">Streams Solidaires</a></li>
                            <li><a href="association.php" class="active">Associations</a></li>
                            <li><a href="don.php">Dons & Challenges</a></li>
                            <li><a href="../backoffice/index.php">Back-Office</a></li>
                            <li><a href="profile.html">Profil</a></li>
                        </ul>
                        <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

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
  </section>

  <!-- Liste Associations -->
  <section class="container" aria-label="Liste des associations">
    <div id="liste-associations">
      <?php foreach ($associations as $association): ?>
        <div class="solidaire-card" role="region" aria-labelledby="assoc-<?= $association['id_association'] ?>-title">
          <h4 id="assoc-<?= $association['id_association'] ?>-title"><?= htmlspecialchars($association['name']) ?></h4>
          <p><?= htmlspecialchars($association['description']) ?></p>
          <ul>
            <li><i class="fa fa-euro"></i> Total : <?= number_format($association['total_dons_reel'], 0, ',', ' ') ?>€</li>
            <li><i class="fa fa-users"></i> Soutiens : <?= $association['nombre_donateurs'] ?></li>
          </ul>
          <a href="don.php?association=<?= $association['id_association'] ?>" class="btn neon-btn" role="button">Faire un Don</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

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
              <a href="streams.html" class="neon-btn" role="button">Rejoindre en Stream</a>
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

  <!-- FOOTER FUTURISTE HEXAGONAL -->
  <footer class="footer-compact">
    <div class="footer-glow"></div>
    
    <div class="container">
      <div class="footer-main">
        <!-- Logo et titre -->
        <div class="footer-brand">
          <img src="assets/images/logooo.png" alt="Play to Help" class="footer-logo">
          <h3>PLAY TO HELP</h3>
          <p>🎮 Gaming pour l'Humanitaire</p>
        </div>

        <!-- Navigation rapide -->
        <div class="footer-nav">
          <a href="index.html">Accueil</a>
          <a href="don.php">Dons</a>
          <a href="streams.html">Streams</a>
          <a href="association.php">Associations</a>
        </div>

        <!-- Réseaux sociaux -->
        <div class="footer-social">
          <a href="#" class="social-btn discord"><i class="fab fa-discord"></i></a>
          <a href="#" class="social-btn twitch"><i class="fab fa-twitch"></i></a>
          <a href="#" class="social-btn youtube"><i class="fab fa-youtube"></i></a>
          <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
        </div>
      </div>

      <!-- Copyright -->
      <div class="footer-copyright">
        <div class="glow-line"></div>
        <p>© 2025 Play to Help - Gaming Solidaire • Tous droits réservés</p>
      </div>
    </div>
  </footer>

  <style>
    /* ===== FOOTER COMPACT CRÉATIF ===== */
    .footer-compact {
      background: linear-gradient(135deg, rgba(15, 12, 41, 0.95), rgba(30, 30, 50, 1));
      position: relative;
      padding: 60px 0 30px;
      margin-top: 80px;
      overflow: hidden;
    }

    .footer-glow {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 50% 0%, rgba(102, 126, 234, 0.1), transparent 70%);
      pointer-events: none;
    }

    .footer-main {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 40px;
      margin-bottom: 40px;
    }

    .footer-brand {
      text-align: center;
    }

    .footer-logo {
      width: 50px;
      height: 50px;
      margin-bottom: 15px;
      filter: drop-shadow(0 0 10px rgba(102, 126, 234, 0.6));
    }

    .footer-brand h3 {
      color: white;
      font-size: 1.8rem;
      font-weight: 800;
      margin-bottom: 10px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .footer-brand p {
      color: rgba(255, 255, 255, 0.7);
      font-size: 1rem;
      margin: 0;
    }

    .footer-nav {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }

    .footer-nav a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
    }

    .footer-nav a:hover {
      color: #667eea;
      transform: translateY(-2px);
    }

    .footer-nav a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      transition: width 0.3s ease;
    }

    .footer-nav a:hover::after {
      width: 100%;
    }

    .footer-social {
      display: flex;
      gap: 15px;
    }

    .social-btn {
      width: 45px;
      height: 45px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .social-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .social-btn:hover::before {
      left: 100%;
    }

    .social-btn:hover {
      transform: translateY(-5px) scale(1.1);
    }

    .discord { background: linear-gradient(135deg, #5865F2, #4752C4); }
    .twitch { background: linear-gradient(135deg, #9146FF, #6441A5); }
    .youtube { background: linear-gradient(135deg, #FF0000, #CC0000); }
    .twitter { background: linear-gradient(135deg, #1DA1F2, #0C85D0); }

    .footer-copyright {
      text-align: center;
      padding-top: 30px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .glow-line {
      width: 200px;
      height: 2px;
      background: linear-gradient(90deg, transparent, #667eea, #764ba2, transparent);
      margin: 0 auto 20px;
      animation: lineGlow 2s ease-in-out infinite;
    }

    @keyframes lineGlow {
      0%, 100% { opacity: 0.5; }
      50% { opacity: 1; }
    }

    .footer-copyright p {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.9rem;
      margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .footer-main {
        flex-direction: column;
        text-align: center;
        gap: 30px;
      }

      .footer-nav {
        justify-content: center;
      }

      .footer-social {
        justify-content: center;
      }
    }
  </style>

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