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
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <link rel="apple-touch-icon" href="assets/images/logooo.png">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="assets/css/dons-assoc.css">
    <link rel="stylesheet" href="assets/css/event.css">
    <link rel="stylesheet" href="assets/css/streams.css">
</head>
<body>
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <?php include 'includes/header.php'; ?>

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

  <?php include 'includes/footer.php'; ?>

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/path-utils.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/dons-assoc.js"></script>
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
