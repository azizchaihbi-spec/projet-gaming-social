<?php require_once '../../config/db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Dons & Challenges</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/dons-assoc.css" />
    
    <style>
        .border-purple {
            border-color: #a78bfa !important;
        }
        .btn-challenge {
            background: linear-gradient(135deg, #a78bfa 0%, #22d3ee 100%);
            border: none;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-challenge:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.6);
        }
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
                            <li><a href="association.html">Associations</a></li>
                            <li><a href="don.php" class="active">Dons & Challenges</a></li>
                            <li><a href="../../../play to help/views/backoffice/index.php">Back-Office</a></li>
                            <li><a href="profile.html">Profil</a></li>
                        </ul>
                        <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO BANNER -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="hero-banner">
                    <h2>Rejoignez la Révolution Gaming Solidaire !</h2>
                    <p>Plongez dans un univers où chaque kill, chaque stream, et chaque don devient une arme contre l'injustice.</p>
                    <div class="main-button">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalDon" class="btn-donate" role="button">Faire un Don Maintenant</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID DE DONATIONS ET CHALLENGES -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-content">
                    <div class="live-stream">
                        <div>
                            <div class="header-section mb-4">
                                <h4><em>Derniers</em> Dons & Challenges Solidaires</h4>
                            </div>
                        </div>
                        <div class="challenge-grid" id="liste-dons">
                            <!-- Tes dons statiques exactement comme tu les avais -->
                            <div class="item don-item">
                                <div class="thumb position-relative">
                                    <img src="assets/images/challenge-1.png" alt="Don UNICEF">
                                    <div class="hover-effect">
                                        <ul style="list-style:none; padding:0; margin:0;">
                                            <li>50€</li>
                                            <li>2025-11-09</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <div class="avatar">
                                        <img src="assets/images/avatar-01.jpg" alt="Test User">
                                    </div>
                                    <span>Test User</span>
                                    <h4>Pour UNICEF</h4>
                                </div>
                            </div>

                            <div class="item don-item">
                                <div class="thumb position-relative">
                                    <img src="assets/images/challenge-1.png" alt="Don WWF">
                                    <div class="hover-effect">
                                        <ul style="list-style:none; padding:0; margin:0;">
                                            <li>30€</li>
                                            <li>2025-11-08</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <div class="avatar">
                                        <img src="assets/images/avatar-02.jpg" alt="Gamer Pro">
                                    </div>
                                    <span>Gamer Pro</span>
                                    <h4>Pour WWF</h4>
                                </div>
                            </div>

                            <!-- Tes challenges exactement comme avant -->
                            <div class="item challenge-card">
                                <div class="thumb position-relative">
                                    <img src="assets/images/feature-left.jpg" alt="Défi UNICEF">
                                    <div class="hover-effect">
                                        <h6 style="margin:0;">Défi : 10 kills Fortnite</h6>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <h4>10 kills Fortnite pour UNICEF</h4>
                                    <p>Récompense : Badge Épique + Shoutout</p>
                                    <div class="progress-container">
                                        <div class="progress-bar-bg">
                                            <div class="progress-fill" style="width: 50%;"></div>
                                        </div>
                                    </div>
                                    <small class="progress-text">50€ / 100€ (50%)</small>
                                    <a href="streams.html" class="btn-challenge mt-2" role="button">Rejoindre en Stream</a>
                                </div>
                            </div>

                            <div class="item challenge-card">
                                <div class="thumb position-relative">
                                    <img src="assets/images/feature-right.jpg" alt="Défi WWF">
                                    <div class="hover-effect">
                                        <h6 style="margin:0;">Défi : Marathon WoW</h6>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <h4>Marathon WoW 24h pour WWF</h4>
                                    <p>Récompense : NFT Solidaire</p>
                                    <div class="progress-container">
                                        <div class="progress-bar-bg">
                                            <div class="progress-fill" style="width: 25%;"></div>
                                        </div>
                                    </div>
                                    <small class="progress-text">50€ / 200€ (25%)</small>
                                    <a href="streams.html" class="btn-challenge mt-2" role="button">Rejoindre en Stream</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalDon" class="btn btn-donate me-3" role="button">Faire un Don Simple</a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalChallenge" class="btn btn-challenge" role="button">Lancer un Challenge Stream</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DON -->
    <div class="modal fade" id="modalDon" tabindex="-1" aria-labelledby="modalDonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-light border border-success">
                <div class="modal-header border-success">
                    <h5 class="modal-title" id="modalDonLabel">Faire un Don Solidaire</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formDon">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom <small class="text-muted">(facultatif)</small></label>
                                <input type="text" class="form-control bg-secondary text-light border-success" name="nom" placeholder="Dupont">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom <small class="text-muted">(facultatif)</small></label>
                                <input type="text" class="form-control bg-secondary text-light border-success" name="prenom" placeholder="Jean">
                            </div>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Email <small class="text-muted">(facultatif)</small></label>
                            <input 
                                type="text" 
                                class="form-control bg-secondary text-light border-success" 
                                name="email" 
                                placeholder="jean@example.com"
                                id="emailInput">
                            <div class="invalid-feedback">
                                Email invalide ! Exemple correct : jean@exemple.com
                            </div>
                        </div>

                        <div class="alert alert-info small p-2" role="alert">
                            Ces informations sont <strong>100 % facultatives</strong>. Tu peux donner de façon totalement anonyme !
                        </div>

                        <!-- Montant -->
                        <div class="mb-3">
                            <label class="form-label">Montant (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-secondary text-light border-success" name="montant" step="0.01" placeholder="10.00">
                            <div class="invalid-feedback">Le montant doit être supérieur à 0 €</div>
                        </div>

                        <!-- Association -->
                        <div class="mb-4">
                            <label class="form-label">Association <span class="text-danger">*</span></label>
                            <select class="form-select bg-secondary text-light border-success" name="id_association">
                                <option value="">Choisissez votre cause...</option>
                                <?php
                                try {
                                    $stmt = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="'.$row['id_association'].'">'.htmlspecialchars($row['name']).'</option>';
                                    }
                                } catch (Exception $e) {
                                    echo '<option disabled>Erreur chargement associations</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner une association</div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 py-3 fs-5 shadow-lg">
                            Donner et Inspirer
                        </button>
                        <div id="donResult" class="mt-3 text-center fw-bold fs-5"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CHALLENGE AMÉLIORÉ -->
    <div class="modal fade" id="modalChallenge" tabindex="-1" aria-labelledby="modalChallengeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-light border border-purple">
                <div class="modal-header border-purple">
                    <h5 class="modal-title" id="modalChallengeLabel">
                        🎮 Créer un Challenge Don Stream
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="formChallenge" class="needs-validation" novalidate>
                        
                        <!-- Association -->
                        <div class="mb-3">
                            <label for="challenge-assoc" class="form-label">
                                Association <span class="text-danger">*</span>
                            </label>
                            <select class="form-control bg-secondary text-light border-purple" 
                                    id="challenge-assoc" 
                                    name="challenge-assoc" 
                                    required>
                                <option value="">Sélectionnez votre cause héroïque...</option>
                                <?php
                                try {
                                    $stmt = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="'.$row['id_association'].'">'.htmlspecialchars($row['name']).'</option>';
                                    }
                                } catch (Exception $e) {
                                    echo '<option disabled>Erreur chargement associations</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner une association</div>
                        </div>

                        <!-- Défi In-Game -->
                        <div class="mb-3">
                            <label for="defi" class="form-label">
                                Défi In-Game <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-secondary text-light border-purple" 
                                   id="defi" 
                                   name="defi" 
                                   placeholder="Ex: 10 kills Fortnite, Marathon WoW 24h..." 
                                   required>
                            <div class="invalid-feedback">Le défi est requis</div>
                            <small class="text-muted">Décrivez votre mission épique !</small>
                        </div>

                        <!-- Objectif Dons -->
                        <div class="mb-3">
                            <label for="objectif" class="form-label">
                                Objectif Dons (€) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control bg-secondary text-light border-purple" 
                                   id="objectif" 
                                   name="objectif" 
                                   min="10" 
                                   step="0.01" 
                                   placeholder="100.00" 
                                   required>
                            <div class="invalid-feedback">L'objectif doit être d'au moins 10€</div>
                            <small class="text-muted">Le niveau à atteindre (minimum 10€)</small>
                        </div>

                        <!-- Récompense -->
                        <div class="mb-4">
                            <label for="recompense" class="form-label">
                                Récompense <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-secondary text-light border-purple" 
                                   id="recompense" 
                                   name="recompense" 
                                   placeholder="Ex: Badge Épique + Shoutout, NFT Solidaire..." 
                                   required>
                            <div class="invalid-feedback">La récompense est requise</div>
                            <small class="text-muted">Le trésor pour les héros !</small>
                        </div>

                        <div class="alert alert-info small p-2" role="alert">
                            <strong>💡 Astuce :</strong> Plus votre challenge est créatif et engageant, plus vous mobiliserez la communauté !
                        </div>

                        <button type="submit" class="btn btn-challenge w-100 py-3 fs-5 shadow-lg">
                            🚀 Lancer le Challenge Épique !
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <p>Copyright © 2025 <a href="#">Play to Help</a> - Gaming pour l'Humanitaire. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/dons-assoc.js"></script>

    <!-- AJAX POUR LE DON -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("formDon");
        if (!form) return;

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const btn = form.querySelector("button[type='submit']");
            const result = document.getElementById("donResult");

            btn.disabled = true;
            btn.innerHTML = "Don en cours...";
            result.innerHTML = '<div class="alert alert-info">Enregistrement du don...</div>';

            const formData = new FormData(form);

            try {
                const response = await fetch("../backoffice/add.php", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    result.innerHTML = `
                        <div class="alert alert-success p-4 text-center">
                            <h4>❤️ Merci pour ton don de <strong>${data.message}</strong> !</h4>
                            <p>Tu viens de rendre le monde un peu meilleur.</p>
                        </div>`;
                    form.reset();
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById("modalDon")).hide();
                    }, 4000);
                } else {
                    result.innerHTML = `<div class="alert alert-danger p-3">${data.message}</div>`;
                }
            } catch (error) {
                result.innerHTML = '<div class="alert alert-danger p-3">Erreur réseau. Réessayez.</div>';
            }

            btn.disabled = false;
            btn.innerHTML = "Donner et Inspirer";
        });
    });
    </script>

    <!-- AJAX POUR LE CHALLENGE -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const formChallenge = document.getElementById("formChallenge");
        if (!formChallenge) return;

        formChallenge.addEventListener("submit", async function (e) {
            e.preventDefault();

            const btn = formChallenge.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;

            // Validation côté client
            const assoc = formChallenge.querySelector('#challenge-assoc').value;
            const defi = formChallenge.querySelector('#defi').value.trim();
            const objectif = parseFloat(formChallenge.querySelector('#objectif').value);
            const recompense = formChallenge.querySelector('#recompense').value.trim();

            // Supprimer les messages d'erreur précédents
            const existingAlert = formChallenge.querySelector('.alert:not(.alert-info)');
            if (existingAlert) existingAlert.remove();

            // Validation
            if (!assoc) {
                showChallengeAlert('danger', 'Veuillez sélectionner une association');
                return;
            }
            if (!defi) {
                showChallengeAlert('danger', 'Le défi est requis');
                return;
            }
            if (!objectif || objectif < 10) {
                showChallengeAlert('danger', 'L\'objectif doit être d\'au moins 10€');
                return;
            }
            if (!recompense) {
                showChallengeAlert('danger', 'La récompense est requise');
                return;
            }

            // Désactiver le bouton
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...';

            const formData = new FormData(formChallenge);

            try {
                const response = await fetch("../backoffice/addchallenge.php", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showChallengeAlert('success', `
                        <h5 class="mb-2">🎮 Challenge Créé avec Succès !</h5>
                        <p class="mb-0">${data.message}</p>
                    `);
                    
                    formChallenge.reset();
                    
                    // Fermer le modal après 3 secondes
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById("modalChallenge"));
                        if (modal) modal.hide();
                        
                        // Recharger la page pour afficher le nouveau challenge
                        window.location.reload();
                    }, 3000);
                } else {
                    showChallengeAlert('danger', data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                showChallengeAlert('danger', 'Erreur réseau. Veuillez réessayer.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        // Fonction helper pour afficher les alertes
        function showChallengeAlert(type, message) {
            const existingAlert = formChallenge.querySelector('.alert:not(.alert-info)');
            if (existingAlert) existingAlert.remove();

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} mt-3`;
            alert.innerHTML = message;
            
            const submitBtn = formChallenge.querySelector("button[type='submit']");
            submitBtn.parentNode.insertBefore(alert, submitBtn);
        }
    });
    </script>

</body>
</html>