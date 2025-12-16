<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Play to Help - Streams Solidaires</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="assets/css/dons-assoc.css">
    <link rel="stylesheet" href="assets/css/event.css">
    <style>
      @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
      .hero-streams { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 25%, #c44569 50%, #f8b500 75%, #ff6b6b 100%); background-size: 400% 400%; animation: gradientShift 8s ease infinite; border-radius: 20px; padding: 60px 30px; margin-bottom: 40px; position: relative; overflow: hidden; }
      .hero-streams h1 { font-weight: 800; color: #11cc4c; }
      .hero-streams p { color: rgba(255,255,255,0.9); }
      .filters-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 18px; margin-bottom: 26px; box-shadow: 0 10px 30px rgba(0,0,0,0.18); backdrop-filter: blur(6px); }
      .filters-card .form-select, .filters-card .btn { background-color: rgba(255,255,255,0.04); color: #fff; border: 1px solid rgba(255,255,255,0.08); }
      .filters-card .form-select option { background-color: #1a1a2e; color: #fff; }
      .filters-card .form-select:focus { background-color: rgba(255,255,255,0.08); color: #fff; border-color: #ff6b6b; box-shadow: 0 0 0 2px rgba(255,107,107,0.25); }
      .filters-card label { font-weight: 600; }
      .section-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
      .badge-live { background: #28a745; color: #fff; padding: 6px 12px; border-radius: 50px; font-weight: 700; }
      .empty-state { display: none; color: #999; padding: 15px; text-align: center; border: 1px dashed rgba(255,255,255,0.2); border-radius: 12px; }
      .btn-events { background: linear-gradient(135deg, #ff6b6b, #ff8c42); color: #fff; font-weight: 800; padding: 10px 16px; border-radius: 12px; border: 1px solid rgba(255,107,107,0.35); box-shadow: 0 12px 30px rgba(255,107,107,0.35); text-decoration: none; transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease; }
      .btn-events:hover { transform: translateY(-2px); box-shadow: 0 16px 36px rgba(255,107,107,0.45); opacity: 0.95; color: #fff; }
    </style>
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
                        <a href="Accueil.php" class="logo">
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
                            <li><a href="Accueil.php">Accueil</a></li>
                            <li><a href="index.php">Forum</a></li>
                            <li><a href="browse.php">Événements</a></li>
                            <li><a href="streams.php" class="active">Streams Solidaires</a></li>
                            <li><a href="association.html">Associations</a></li>
                            <li><a href="don.html">Dons & Challenges</a></li>
                            <?php if (isset($_SESSION['user'])): ?>
                                <li><a href="profile.php">Profil</a></li>
                                <li><a href="logout.php">Déconnexion</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Connexion</a></li>
                                <li><a href="register.php">Inscription</a></li>
                            <?php endif; ?>
                        </ul>
                        <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="page-content">

            <div class="hero-streams text-center">
              <h1>Streams Solidaires</h1>
              <p>Suivez les créateurs engagés, soutenez les associations et partagez le live.</p>
              <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                <span class="badge-live">🔴 <span id="live-count">0</span> en direct</span>
                <a href="browse.php" class="btn btn-events d-inline-flex align-items-center gap-2">
                  <i class="fa fa-calendar"></i> Voir les événements
                </a>
              </div>
            </div>

            <div class="filters-card">
              <div class="row g-3 align-items-end">
                <div class="col-md-3">
                  <label class="form-label text-white-50" for="filterPlatform">Plateforme</label>
                  <select id="filterPlatform" class="form-select">
                    <option value="">Toutes</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-white-50" for="filterStatus">Statut</label>
                  <select id="filterStatus" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_cours">En direct</option>
                    <option value="scheduled">Programmé</option>
                    <option value="termine">Terminé</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-white-50" for="filterSort">Trier par</label>
                  <select id="filterSort" class="form-select">
                    <option value="recent">Récents</option>
                    <option value="views">Vues</option>
                    <option value="likes">Likes</option>
                  </select>
                </div>
                <div class="col-md-3 d-flex justify-content-md-end">
                  <button id="resetFilters" class="btn btn-outline-danger w-100">Réinitialiser</button>
                </div>
              </div>
            </div>

            <div class="section-title">
              <h4 class="text-danger mb-0">Top streamers</h4>
              <span class="text-white-50">Classement par engagement</span>
            </div>
            <div id="top-streamers" class="d-flex flex-wrap gap-3 mb-4"></div>

            <div class="section-title">
              <h4 class="text-danger mb-0">🎬 Meilleur Clips</h4>
              <span class="text-white-50">Les clips les plus regardés</span>
            </div>
            <div class="row" id="top-clips"></div>

            <div class="section-title">
              <h4 class="text-danger mb-0">En direct maintenant</h4>
              <span id="no-live" class="text-white-50" style="display:none;">Pas de live pour le moment.</span>
            </div>
            <div class="row" id="live-streams"></div>

            <div class="section-title mt-4">
              <h4 class="text-danger mb-0">Tous les streams</h4>
              <span class="text-white-50">Replays et programmés</span>
            </div>
            <div class="row" id="streams-grid"></div>
            <div id="streams-empty" class="empty-state">Aucun stream à afficher.</div>

          </div>
        </div>
      </div>
    </div>

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © 2025 Play to Help • Tous droits réservés.</p>
        </div>
      </div>
    </div>
  </footer>

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/path-utils.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/streams.js"></script>
  <script src="assets/js/clips.js"></script>
  <script>
    // Cacher le preloader dès que la page est prête (max 1.5s)
    window.addEventListener('load', function() {
      var preloader = document.getElementById('js-preloader');
      if (preloader) {
        preloader.classList.add('loaded');
      }
    });
    // Fallback rapide
    setTimeout(function() {
      var preloader = document.getElementById('js-preloader');
      if (preloader) {
        preloader.classList.add('loaded');
      }
    }, 1500);
  </script>
</body>
</html>
