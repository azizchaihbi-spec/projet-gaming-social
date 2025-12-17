<?php 
require_once '../../config/config.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Récupérer les dons récents depuis la base de données
try {
    $stmt = $pdo->query("
        SELECT d.*, a.name as association_name 
        FROM don d 
        JOIN association a ON d.id_association = a.id_association 
        ORDER BY d.date_don DESC 
        LIMIT 10
    ");
    $dons_recents = $stmt->fetchAll();
} catch (Exception $e) {
    $dons_recents = [];
    error_log("Erreur lors de la récupération des dons récents: " . $e->getMessage());
}

// Récupérer les 3 derniers challenges
try {
    $stmt = $pdo->query("
        SELECT c.*, a.name as association_name 
        FROM challenge c 
        JOIN association a ON c.id_association = a.id_association 
        ORDER BY c.id_challenge DESC 
        LIMIT 3
    ");
    $challenges_epiques = $stmt->fetchAll();
} catch (Exception $e) {
    $challenges_epiques = [];
    error_log("Erreur lors de la récupération des challenges épiques: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Dons & Challenges</title>
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <link rel="apple-touch-icon" href="assets/images/logooo.png">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/dons-assoc.css" />
    <link rel="stylesheet" href="assets/css/don-page.css" />

</head>
<body>

    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <?php include 'includes/header.php'; ?>

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

    <!-- SECTIONS SÉPARÉES CRÉATIVES -->
    <div class="container">
        <!-- SECTION DONS RÉCENTS -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="section-header-creative">
                    <div class="section-icon">💚</div>
                    <h3 class="section-title">DONS RÉCENTS</h3>
                    <div class="section-subtitle">Les héros qui changent le monde</div>
                    <div class="section-line"></div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="dons-carousel-wrapper">
                    <div class="dons-grid" id="liste-dons">
                        <?php if (!empty($dons_recents)): ?>
                            <?php foreach ($dons_recents as $index => $don): ?>
                                <div class="item don-item">
                                    <div class="thumb position-relative">
                                        <img src="assets/images/challenge-1.png" alt="Don <?= htmlspecialchars($don['association_name']) ?>">
                                        <div class="hover-effect">
                                            <ul style="list-style:none; padding:0; margin:0;">
                                                <li><?= number_format($don['montant'], 2, ',', ' ') ?>€</li>
                                                <li><?= date('d/m/Y', strtotime($don['date_don'])) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="down-content">
                                        <div class="avatar">
                                            <img src="assets/images/avatar-<?= sprintf('%02d', ($index % 4) + 1) ?>.jpg" alt="<?= htmlspecialchars($don['prenom'] ?: 'Anonyme') ?>">
                                        </div>
                                        <span><?= htmlspecialchars($don['prenom'] ?: 'Anonyme') ?> <?= htmlspecialchars($don['nom'] ?: '') ?></span>
                                        <h4>Pour <?= htmlspecialchars($don['association_name']) ?></h4>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Affichage par défaut si aucun don -->
                            <div class="item don-item">
                                <div class="thumb position-relative">
                                    <img src="assets/images/challenge-1.png" alt="Aucun don">
                                    <div class="hover-effect">
                                        <ul style="list-style:none; padding:0; margin:0;">
                                            <li>Aucun don</li>
                                            <li>pour le moment</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <div class="avatar">
                                        <img src="assets/images/avatar-01.jpg" alt="Soyez le premier">
                                    </div>
                                    <span>Soyez le premier !</span>
                                    <h4>À faire un don</h4>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION CHALLENGES ÉPIQUES -->
        <div class="row mb-5 mt-5">
            <div class="col-lg-12">
                <div class="section-header-creative">
                    <div class="section-icon">🎮</div>
                    <h3 class="section-title" style="background: linear-gradient(135deg, #667eea, #764ba2, #f093fb); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CHALLENGES ÉPIQUES</h3>
                    <div class="section-subtitle">Relevez le défi et changez des vies</div>
                    <div class="section-line" style="background: linear-gradient(90deg, transparent, #667eea, #764ba2, transparent);"></div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="challenges-carousel-wrapper">
                    <div class="challenges-grid">
                        <?php if (!empty($challenges_epiques)): ?>
                            <?php foreach ($challenges_epiques as $index => $challenge): ?>
                                <?php 
                                    $pourcentage = ($challenge['objectif'] > 0) ? ($challenge['progression'] / $challenge['objectif']) * 100 : 0;
                                    $pourcentage = min(100, max(0, $pourcentage)); // Limiter entre 0 et 100%
                                ?>
                                <div class="item challenge-card">
                                    <div class="thumb position-relative">
                                        <img src="assets/images/feature-<?= ($index % 2 == 0) ? 'left' : 'right' ?>.jpg" alt="Défi <?= htmlspecialchars($challenge['association_name']) ?>">
                                        <div class="hover-effect">
                                            <h6 style="margin:0;">Défi : <?= htmlspecialchars($challenge['name']) ?></h6>
                                        </div>
                                    </div>
                                    <div class="down-content">
                                        <h4><?= htmlspecialchars($challenge['name']) ?> pour <?= htmlspecialchars($challenge['association_name']) ?></h4>
                                        <p>Récompense : <?= htmlspecialchars($challenge['recompense']) ?></p>
                                        <div class="progress-container">
                                            <div class="progress-bar-bg">
                                                <div class="progress-fill" style="width: <?= $pourcentage ?>%;"></div>
                                            </div>
                                        </div>
                                        <small class="progress-text"><?= number_format($challenge['progression'], 0, ',', ' ') ?>€ / <?= number_format($challenge['objectif'], 0, ',', ' ') ?>€ (<?= number_format($pourcentage, 0) ?>%)</small>
                                        <a href="streams.php" class="btn-challenge mt-2" role="button">Rejoindre en Stream</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Affichage par défaut si aucun challenge épique -->
                            <div class="item challenge-card">
                                <div class="thumb position-relative">
                                    <img src="assets/images/feature-left.jpg" alt="Aucun challenge épique">
                                    <div class="hover-effect">
                                        <h6 style="margin:0;">Aucun challenge épique</h6>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <h4>Aucun challenge épique pour le moment</h4>
                                    <p>Les challenges épiques apparaissent quand ils dépassent 1000€</p>
                                    <div class="progress-container">
                                        <div class="progress-bar-bg">
                                            <div class="progress-fill" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                    <small class="progress-text">Soyez le premier à créer un challenge épique !</small>
                                    <a href="#modalDon" data-bs-toggle="modal" class="btn-challenge mt-2" role="button">Créer un Challenge</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION ACTIONS RAPIDES -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="action-buttons-creative">
                    <div class="action-card action-card-don">
                        <div class="action-icon">💚</div>
                        <h4>Don Simple</h4>
                        <p>Soutenez directement une cause</p>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalDon" class="btn btn-donate" role="button">Faire un Don</a>
                    </div>
                    <div class="action-card action-card-challenge">
                        <div class="action-icon">🎮</div>
                        <h4>Challenge Stream</h4>
                        <p>Créez votre propre défi gaming</p>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalChallenge" class="btn btn-challenge" role="button">Lancer un Challenge</a>
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

                        <div class="alert alert-info small p-3 mb-4" role="alert" style="background: rgba(0, 212, 255, 0.15); border: 2px solid rgba(0, 212, 255, 0.4); border-radius: 15px;">
                            <strong>ℹ️ Ces informations sont 100 % facultatives.</strong> Tu peux donner de façon totalement anonyme !
                        </div>

                        <!-- Montant -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold">Montant (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-secondary text-light border-success" name="montant" step="0.01" placeholder="300" style="font-size: 1.2rem; padding: 15px;">
                            <div class="invalid-feedback">Le montant doit être supérieur à 0 €</div>
                        </div>

                        <!-- Association -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold">Association <span class="text-danger">*</span></label>
                            <select class="form-select bg-secondary text-light border-success" name="id_association" style="font-size: 1.1rem; padding: 15px;">
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

                        <!-- Mode de Paiement -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold">Mode de Paiement <span class="text-danger">*</span></label>
                            
                            <div class="payment-options row g-3">
                                <!-- Option Stripe -->
                                <div class="col-md-6">
                                    <div class="payment-option payment-option-stripe" style="background: transparent; border: 2px solid rgba(0, 212, 255, 0.5); border-radius: 15px; padding: 20px; cursor: pointer; transition: all 0.3s; height: 100%;">
                                        <label class="d-flex align-items-start cursor-pointer" style="cursor: pointer; margin: 0;">
                                            <input type="radio" name="payment_mode" value="stripe" checked class="me-3 mt-1" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span style="font-size: 1.3rem; margin-right: 8px;">💳</span>
                                                    <strong style="font-size: 1rem; color: #fff;">Paiement en ligne via Stripe</strong>
                                                </div>
                                                
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Option Don Direct -->
                                <div class="col-md-6">
                                    <div class="payment-option payment-option-direct" style="background: transparent; border: 2px solid rgba(255, 193, 7, 0.5); border-radius: 15px; padding: 20px; cursor: pointer; transition: all 0.3s; height: 100%;">
                                        <label class="d-flex align-items-start cursor-pointer" style="cursor: pointer; margin: 0;">
                                            <input type="radio" name="payment_mode" value="direct" class="me-3 mt-1" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span style="font-size: 1.3rem; margin-right: 8px;">💰</span>
                                                    <strong style="font-size: 1rem; color: #fff;">Don Direct</strong>
                                                </div>
                                               
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="btnSubmitDon" class="btn btn-success btn-lg w-100 py-3 fs-4 shadow-lg text-uppercase fw-bold" style="background: linear-gradient(135deg, #00d4ff, #0099ff); border: none; border-radius: 15px; letter-spacing: 1px;">
                            💳 Procéder au Paiement
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

    <?php include 'includes/footer.php'; ?>




    <!-- SCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/dons-assoc.js"></script>

    <!-- STRIPE SDK -->
    <script src="https://js.stripe.com/v3/"></script>

    <!-- ANIMATIONS GAMING OPTIMISÉES -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Effet hover simple sur les cartes (optimisé - pas de parallax lourd)
        const cards = document.querySelectorAll('.don-item, .challenge-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
                this.style.boxShadow = '0 20px 40px rgba(102, 126, 234, 0.3)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '';
            });
        });

        // Confetti léger pour les succès (seulement quand appelé)
        window.createConfetti = function() {
            const colors = ['#667eea', '#764ba2', '#00f2fe', '#43e97b'];
            for (let i = 0; i < 20; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti-particle';
                confetti.style.cssText = `
                    position: fixed;
                    width: 8px;
                    height: 8px;
                    background: ${colors[i % colors.length]};
                    top: -10px;
                    left: ${Math.random() * 100}%;
                    z-index: 9999;
                    border-radius: 50%;
                    animation: confettiFall 2s ease-out forwards;
                `;
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 2000);
            }
        };
    });

    // Animation CSS légère pour confetti
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confettiFall {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0; }
        }
        .don-item, .challenge-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    `;
    document.head.appendChild(style);
    </script>

    <!-- AJAX POUR LE DON AVEC 2 MODES DE PAIEMENT -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("formDon");
        if (!form) return;

        // Effet hover sur les options de paiement
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => {
            const isDirect = option.classList.contains('payment-option-direct');
            const isStripe = option.classList.contains('payment-option-stripe');
            
            option.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                if (isDirect) {
                    this.style.boxShadow = '0 8px 25px rgba(255, 193, 7, 0.3)';
                } else if (isStripe) {
                    this.style.boxShadow = '0 8px 25px rgba(0, 212, 255, 0.3)';
                }
            });
            option.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                updatePaymentSelection();
            });
        });

        // Mettre à jour la sélection visuelle
        function updatePaymentSelection() {
            paymentOptions.forEach(opt => {
                const radio = opt.querySelector('input[type="radio"]');
                const isDirect = opt.classList.contains('payment-option-direct');
                const isStripe = opt.classList.contains('payment-option-stripe');
                
                if (radio.checked) {
                    if (isDirect) {
                        opt.style.borderColor = 'rgba(255, 193, 7, 1)';
                        opt.style.borderWidth = '3px';
                        opt.style.background = 'rgba(255, 193, 7, 0.15)';
                    } else if (isStripe) {
                        opt.style.borderColor = 'rgba(0, 212, 255, 1)';
                        opt.style.borderWidth = '3px';
                        opt.style.background = 'rgba(0, 212, 255, 0.15)';
                    }
                } else {
                    if (isDirect) {
                        opt.style.borderColor = 'rgba(255, 193, 7, 0.5)';
                        opt.style.borderWidth = '2px';
                        opt.style.background = 'transparent';
                    } else if (isStripe) {
                        opt.style.borderColor = 'rgba(0, 212, 255, 0.5)';
                        opt.style.borderWidth = '2px';
                        opt.style.background = 'transparent';
                    }
                }
            });
        }

        // Écouter les changements de radio
        document.querySelectorAll('input[name="payment_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updatePaymentSelection();
                updateButtonText();
            });
        });

        // Mettre à jour le texte du bouton selon le mode
        function updateButtonText() {
            const btn = document.getElementById('btnSubmitDon');
            const selectedMode = document.querySelector('input[name="payment_mode"]:checked').value;
            
            if (selectedMode === 'stripe') {
                btn.innerHTML = '💳 Procéder au Paiement';
            } else {
                btn.innerHTML = '💰 Procéder au Paiement';
            }
        }

        // Initialiser la sélection
        updatePaymentSelection();
        updateButtonText();

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const btn = form.querySelector("button[type='submit']");
            const result = document.getElementById("donResult");

            // Validation
            const montant = parseFloat(form.querySelector('[name="montant"]').value);
            const id_association = form.querySelector('[name="id_association"]').value;
            const nom = form.querySelector('[name="nom"]').value.trim() || 'Anonyme';
            const prenom = form.querySelector('[name="prenom"]').value.trim();
            const email = form.querySelector('[name="email"]').value.trim() || 'anonyme@playtohelp.com';
            const paymentMode = form.querySelector('input[name="payment_mode"]:checked').value;

            // Validation du montant
            if (!montant || montant <= 0) {
                result.innerHTML = '<div class="alert alert-danger">Le montant doit être supérieur à 0 €</div>';
                return;
            }

            // Validation de l'association
            if (!id_association) {
                result.innerHTML = '<div class="alert alert-danger">Veuillez sélectionner une association</div>';
                return;
            }

            // Construire le nom complet
            const nomComplet = prenom ? `${prenom} ${nom}` : nom;

            btn.disabled = true;

            // MODE STRIPE - Redirection vers Stripe Checkout
            if (paymentMode === 'stripe') {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirection vers Stripe...';
                result.innerHTML = '<div class="alert alert-info">🔒 Redirection sécurisée vers Stripe...</div>';

                const formData = new FormData();
                formData.append('montant', montant);
                formData.append('nom', nomComplet);
                formData.append('email', email);
                formData.append('id_association', id_association);

                try {
                    const response = await fetch("../backoffice/process_payment.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success && data.redirect_url) {
                        result.innerHTML = `
                            <div class="alert alert-success p-4 text-center">
                                <h5>✅ Redirection vers le paiement sécurisé...</h5>
                                <p class="mb-0">Vous allez être redirigé vers Stripe</p>
                            </div>`;
                        
                        // Rediriger vers Stripe Checkout
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1000);
                    } else {
                        result.innerHTML = `<div class="alert alert-danger p-3">❌ ${data.error || 'Erreur lors de la création du paiement'}</div>`;
                        btn.disabled = false;
                        btn.innerHTML = '💳 Procéder au Paiement';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    result.innerHTML = '<div class="alert alert-danger p-3">❌ Erreur réseau. Réessayez.</div>';
                    btn.disabled = false;
                    btn.innerHTML = '💳 Procéder au Paiement';
                }
            } 
            // MODE DON DIRECT - Enregistrement direct dans la BDD
            else if (paymentMode === 'direct') {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement du don...';
                result.innerHTML = '<div class="alert alert-info">💰 Enregistrement de votre don direct...</div>';

                const formData = new FormData();
                formData.append('montant', montant);
                formData.append('prenom', prenom || '');
                formData.append('nom', nom);
                formData.append('email', email);
                formData.append('id_association', id_association);

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
                                <h5>✅ Don enregistré avec succès !</h5>
                                <p class="mb-0">Montant : ${data.message}</p>
                                <p class="mb-0 mt-2">Merci pour votre générosité ! 💚</p>
                            </div>`;
                        
                        // Réinitialiser le formulaire
                        form.reset();
                        updatePaymentSelection();
                        
                        // Fermer le modal après 3 secondes
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById("modalDon"));
                            if (modal) modal.hide();
                            window.location.reload();
                        }, 3000);
                    } else {
                        result.innerHTML = `<div class="alert alert-danger p-3">❌ ${data.error || 'Erreur lors de l\'enregistrement'}</div>`;
                        btn.disabled = false;
                        btn.innerHTML = '💳 Procéder au Paiement';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    result.innerHTML = '<div class="alert alert-danger p-3">❌ Erreur réseau. Réessayez.</div>';
                    btn.disabled = false;
                    btn.innerHTML = '💳 Procéder au Paiement';
                }
            }
        });

        // Validation email en temps réel
        const emailInput = document.getElementById('emailInput');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !isValidEmail(email)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
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