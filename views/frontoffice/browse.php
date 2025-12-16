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
    <title>Play to Help - Événements Solidaires</title>

    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="assets/css/cookie-banner.css">
    <link rel="stylesheet" href="assets/css/dons-assoc.css">
    <link rel="stylesheet" href="assets/css/event.css">
    <link rel="stylesheet" href="assets/css/browse.css">
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
                            <li><a href="browse.php" class="active">Événements</a></li>
                            <li><a href="streams.php">Streams Solidaires</a></li>
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

                    <!-- HERO SECTION -->
                    <div class="hero-events text-center">
                        <h1>Événements Solidaires</h1>
                        <p>Participez à nos tournois et challenges, jouez et faites une différence.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                            <span class="badge-live">📅 <span id="event-count">0</span> événements</span>
                            <a href="https://discord.gg/zbGbn4Pz" target="_blank" rel="noopener" class="btn btn-discord d-inline-flex align-items-center gap-2">
                                <i class="fa fa-discord"></i> Rejoindre le Discord
                            </a>
                        </div>
                    </div>

                    <!-- FILTERS SECTION -->
                    <div class="filters-card">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label text-white-50" for="filterGame">Jeu</label>
                                <select id="filterGame" class="form-select">
                                    <option value="">Tous les jeux</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-white-50" for="filterStatus">Statut</label>
                                <select id="filterStatus" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="upcoming">À venir</option>
                                    <option value="live">En direct</option>
                                    <option value="finished">Terminé</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-white-50" for="filterSort">Trier par</label>
                                <select id="filterSort" class="form-select">
                                    <option value="recent">Récents</option>
                                    <option value="participants">Plus de participants</option>
                                    <option value="date">Prochains événements</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex justify-content-md-end">
                                <button id="resetFilters" class="btn btn-outline-danger w-100">Réinitialiser</button>
                            </div>
                        </div>
                    </div>

                    <!-- EVENTS GRID -->
                    <div class="section-title">
                        <h4>Tous les événements</h4>
                        <span class="text-white-50">Découvrez les événements disponibles</span>
                    </div>
                    <div class="row" id="events-grid"></div>
                    <div id="events-empty" class="empty-state">Aucun événement à afficher.</div>

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

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="eventModalTitle">Événement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <img id="eventModalImg" src="" alt="visuel" class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%; min-height: 220px;">
                        </div>
                        <div class="col-md-7 d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2 text-uppercase text-secondary small">
                                <span id="eventModalTheme" class="badge bg-gradient">Thème</span>
                                <span id="eventModalDate"></span>
                            </div>
                            <div class="text-white-50" id="eventModalDescription" style="white-space: pre-wrap;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="registerModalLabel">Inscription Événement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <form id="registerForm" novalidate>
              <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email">
              </div>
              <div class="mb-3">
                <label class="form-label d-block">Type de participation</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="mode" id="modeSolo" value="solo" checked>
                  <label class="form-check-label" for="modeSolo">Solo</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="mode" id="modeTeam" value="team">
                  <label class="form-check-label" for="modeTeam">Team</label>
                </div>
              </div>
              <div class="mb-3" id="teamFields" style="display:none;">
                <label class="form-label">Infos Équipe</label>
                <input type="text" class="form-control mb-2" name="teamName" placeholder="Nom de l'équipe">
                <input type="number" min="1" max="10" class="form-control" name="teamSize" placeholder="Nombre de joueurs">
              </div>
              <div class="mb-3">
                <label for="event" class="form-label">Événement</label>
                <select class="form-select" id="event" name="event">
                  <option value="">Choisir un événement</option>
                  <option value="CSGO">CS:GO Charity Tournament</option>
                  <option value="Valorant">Valorant Speedrun Challenge</option>
                  <option value="Fortnite">Fortnite Charity Build Battle</option>
                </select>
              </div>
              <button type="submit" class="btn btn-success w-100">S'inscrire</button>
              <script>
              (function(){
                const solo=document.getElementById('modeSolo');
                const team=document.getElementById('modeTeam');
                const teamFields=document.getElementById('teamFields');
                function toggle(){teamFields.style.display=team.checked?'block':'none';}
                solo.addEventListener('change',toggle);team.addEventListener('change',toggle);toggle();
              })();
              </script>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/path-utils.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/events.js"></script>
    <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
    <script src="assets/js/event.js"></script>
    <script>
      // Cacher le preloader dès que la page est prête (max 1.5s)
      window.addEventListener('load', function() {
        var preloader = document.getElementById('js-preloader');
        if (preloader) {
          preloader.classList.add('loaded');
        }
      });
      // Fallback rapide si le load prend trop de temps
      setTimeout(function() {
        var preloader = document.getElementById('js-preloader');
        if (preloader) {
          preloader.classList.add('loaded');
        }
      }, 1500);
    </script>
</body>
</html>
