<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Accueil</title>
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <link rel="apple-touch-icon" href="assets/images/logooo.png">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/dons-assoc.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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

          <!-- ***** Hero Section Start ***** -->
          <div class="hero-section">
            <div class="hero-content">
              <div class="hero-text">
                <h1 class="hero-title">Play to Help – Jouer pour faire la différence</h1>
                <p class="hero-subtitle">
                  Découvrez une nouvelle façon de contribuer au bien-être social à travers le gaming. 
                  Rejoignez notre communauté de streamers et de joueurs engagés qui transforment leur passion 
                  en actions solidaires pour soutenir des associations caritatives.
                </p>
                <div class="hero-buttons">
                  <a href="streams.php" class="hero-btn hero-btn-primary">
                    <i class="fa fa-play-circle"></i>
                    Découvrir les streams
                  </a>
                  <a href="association.php" class="hero-btn hero-btn-secondary">
                    <i class="fa fa-heart"></i>
                    Soutenir une association
                  </a>
                  <a href="register.php" class="hero-btn hero-btn-tertiary">
                    <i class="fa fa-users"></i>
                    Rejoindre la communauté
                  </a>
                </div>
              </div>
              <div class="hero-visual">
                <div class="hero-gaming-elements">
                  <div class="gaming-icon gaming-icon-1">🎮</div>
                  <div class="gaming-icon gaming-icon-2">❤️</div>
                  <div class="gaming-icon gaming-icon-3">🌟</div>
                  <div class="gaming-icon gaming-icon-4">🎯</div>
                </div>
              </div>
            </div>
          </div>
          <!-- ***** Hero Section End ***** -->

          <!-- ***** À propos Section Start ***** -->
          <div class="about-section">
            <div class="container-fluid">
              <div class="row align-items-center">
                <div class="col-lg-6">
                  <div class="about-content">
                    <div class="section-heading">
                      <h2 class="about-title">À propos de Play to Help</h2>
                      <div class="title-underline"></div>
                    </div>
                    <p class="about-description">
                      <strong>Play to Help</strong> est une plateforme innovante qui révolutionne l'engagement social 
                      dans l'univers du gaming. Notre mission est de créer un pont entre la passion du jeu vidéo 
                      et l'aide humanitaire, permettant aux gamers de contribuer positivement à la société.
                    </p>
                    <div class="about-features">
                      <div class="feature-item">
                        <div class="feature-icon">🎮</div>
                        <div class="feature-text">
                          <h4>Gaming Solidaire</h4>
                          <p>Transformez votre passion du gaming en actions concrètes pour aider les autres.</p>
                        </div>
                      </div>
                      <div class="feature-item">
                        <div class="feature-icon">🤝</div>
                        <div class="feature-text">
                          <h4>Partenariats Associatifs</h4>
                          <p>Collaborez avec des associations reconnues pour maximiser l'impact de vos contributions.</p>
                        </div>
                      </div>
                      <div class="feature-item">
                        <div class="feature-icon">🌍</div>
                        <div class="feature-text">
                          <h4>Impact Global</h4>
                          <p>Participez à un mouvement mondial qui unit gaming et responsabilité sociale.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="about-visual">
                    <div class="stats-container">
                      <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Streamers Engagés</div>
                      </div>
                      <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Associations Partenaires</div>
                      </div>
                      <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Heures de Stream</div>
                      </div>
                      <div class="stat-item">
                        <div class="stat-number">25K€</div>
                        <div class="stat-label">Fonds Collectés</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- ***** À propos Section End ***** -->

          <!-- ***** Comment ça marche Section Start ***** -->
          <div class="how-it-works-section">
            <div class="container-fluid">
              <div class="section-heading text-center">
                <h2 class="section-title">Comment ça marche</h2>
                <div class="title-underline mx-auto"></div>
                <p class="section-subtitle">Découvrez le processus simple qui transforme le gaming en aide solidaire</p>
              </div>
              
              <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                  <article class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">🎮</div>
                    <div class="step-content">
                      <h3 class="step-title">Les streamers diffusent</h3>
                      <p class="step-description">
                        Nos streamers partenaires créent du contenu gaming engageant et divertissant 
                        sur leurs plateformes préférées, tout en sensibilisant leur audience aux causes solidaires.
                      </p>
                    </div>
                    <div class="step-arrow">→</div>
                  </article>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                  <article class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">🤝</div>
                    <div class="step-content">
                      <h3 class="step-title">La communauté participe</h3>
                      <p class="step-description">
                        Les viewers interagissent, partagent et soutiennent les streams. Chaque vue, 
                        like et participation contribue à générer des fonds pour les associations partenaires.
                      </p>
                    </div>
                    <div class="step-arrow">→</div>
                  </article>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                  <article class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">❤️</div>
                    <div class="step-content">
                      <h3 class="step-title">Les associations reçoivent l'aide</h3>
                      <p class="step-description">
                        Les fonds collectés sont directement reversés aux associations caritatives, 
                        permettant de financer leurs projets et d'amplifier leur impact social.
                      </p>
                    </div>
                  </article>
                </div>
              </div>
              
              <div class="text-center mt-5">
                <a href="register.php" class="cta-button">
                  <i class="fa fa-rocket"></i>
                  Rejoindre le mouvement
                </a>
              </div>
            </div>
          </div>
          <!-- ***** Comment ça marche Section End ***** -->

          <!-- ***** Témoignage Section Start ***** -->
          <div class="testimonial-section">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-8">
                  <div class="testimonial-card">
                    <div class="quote-icon">"</div>
                    <blockquote class="testimonial-quote">
                      Le gaming n'est plus seulement un divertissement, c'est devenu un véritable levier de changement social. 
                      Grâce à Play to Help, chaque heure passée à jouer peut contribuer à améliorer la vie de personnes dans le besoin. 
                      C'est la preuve que notre passion peut avoir un impact positif sur le monde.
                    </blockquote>
                    <div class="testimonial-author">
                      <div class="author-avatar">
                        <img src="assets/images/avatar-testimonial.jpg" alt="Avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="avatar-fallback">👤</div>
                      </div>
                      <div class="author-info">
                        <h4 class="author-name">Alex Martin</h4>
                        <p class="author-role">Streamer & Ambassadeur Play to Help</p>
                      </div>
                    </div>
                    <div class="testimonial-stats">
                      <div class="stat-item">
                        <span class="stat-number">2.5K€</span>
                        <span class="stat-label">Collectés</span>
                      </div>
                      <div class="stat-item">
                        <span class="stat-number">150h</span>
                        <span class="stat-label">Streamées</span>
                      </div>
                      <div class="stat-item">
                        <span class="stat-number">5</span>
                        <span class="stat-label">Associations aidées</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- ***** Témoignage Section End ***** -->

          <!-- ***** Most Popular Start ***** -->
          <div class="most-popular">
            <div class="row">
              <div class="col-lg-12">
                <div class="heading-section">
                  <h4><em>Most Popular</em> Right Now</h4>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-6">
                    <div class="item">
                      <img src="assets/images/popular-01.jpg" alt="">
                      <h4>Fortnite<br><span>Sandbox</span></h4>
                      <ul>
                        <li><i class="fa fa-star"></i> 4.8</li>
                        <li><i class="fa fa-download"></i> 2.3M</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="item">
                      <img src="assets/images/popular-02.jpg" alt="">
                      <h4>PubG<br><span>Battle S</span></h4>
                      <ul>
                        <li><i class="fa fa-star"></i> 4.8</li>
                        <li><i class="fa fa-download"></i> 2.3M</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="item">
                      <img src="assets/images/popular-03.jpg" alt="">
                      <h4>Dota2<br><span>Steam-X</span></h4>
                      <ul>
                        <li><i class="fa fa-star"></i> 4.8</li>
                        <li><i class="fa fa-download"></i> 2.3M</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="item">
                      <img src="assets/images/popular-04.jpg" alt="">
                      <h4>CS-GO<br><span>Legendary</span></h4>
                      <ul>
                        <li><i class="fa fa-star"></i> 4.8</li>
                        <li><i class="fa fa-download"></i> 2.3M</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="item">
                      <div class="row">
                        <div class="col-lg-6 col-sm-6">
                          <div class="item inner-item">
                            <img src="assets/images/popular-05.jpg" alt="">
                            <h4>Mini Craft<br><span>Legendary</span></h4>
                            <ul>
                              <li><i class="fa fa-star"></i> 4.8</li>
                              <li><i class="fa fa-download"></i> 2.3M</li>
                            </ul>
                          </div>
                        </div>
                        <div class="col-lg-6 col-sm-6">
                          <div class="item">
                            <img src="assets/images/popular-06.jpg" alt="">
                            <h4>Eagles Fly<br><span>Matrix Games</span></h4>
                            <ul>
                              <li><i class="fa fa-star"></i> 4.8</li>
                              <li><i class="fa fa-download"></i> 2.3M</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="item">
                      <img src="assets/images/popular-07.jpg" alt="">
                      <h4>Warface<br><span>Max 3D</span></h4>
                      <ul>
                        <li><i class="fa fa-star"></i> 4.8</li>
                        <li><i class="fa fa-download"></i> 2.3M</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="item">
                      <img src="assets/images/popular-08.jpg" alt="">
                      <h4>Warcraft<br><span>Legend</span></h4>
                      <ul>
                        <li><i class="fa fa-star"></i> 4.8</li>
                        <li><i class="fa fa-download"></i> 2.3M</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="main-button">
                      <a href="browse.php">Discover Popular</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- ***** Most Popular End ***** -->

          <!-- ***** Gaming Library Start ***** -->
          <div class="gaming-library">
            <div class="col-lg-12">
              <div class="heading-section">
                <h4><em>Your Gaming</em> Library</h4>
              </div>
              <div class="item">
                <ul>
                  <li><img src="assets/images/game-01.jpg" alt="" class="templatemo-item"></li>
                  <li><h4>Dota 2</h4><span>Sandbox</span></li>
                  <li><h4>Date Added</h4><span>24/08/2036</span></li>
                  <li><h4>Hours Played</h4><span>634 H 22 Mins</span></li>
                  <li><h4>Currently</h4><span>Downloaded</span></li>
                  <li><div class="main-border-button border-no-active"><a href="#">Donwloaded</a></div></li>
                </ul>
              </div>
              <div class="item">
                <ul>
                  <li><img src="assets/images/game-02.jpg" alt="" class="templatemo-item"></li>
                  <li><h4>Fortnite</h4><span>Sandbox</span></li>
                  <li><h4>Date Added</h4><span>22/06/2036</span></li>
                  <li><h4>Hours Played</h4><span>740 H 52 Mins</span></li>
                  <li><h4>Currently</h4><span>Downloaded</span></li>
                  <li><div class="main-border-button"><a href="#">Donwload</a></div></li>
                </ul>
              </div>
              <div class="item last-item">
                <ul>
                  <li><img src="assets/images/game-03.jpg" alt="" class="templatemo-item"></li>
                  <li><h4>CS-GO</h4><span>Sandbox</span></li>
                  <li><h4>Date Added</h4><span>21/04/2036</span></li>
                  <li><h4>Hours Played</h4><span>892 H 14 Mins</span></li>
                  <li><h4>Currently</h4><span>Downloaded</span></li>
                  <li><div class="main-border-button border-no-active"><a href="#">Donwloaded</a></div></li>
                </ul>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="main-button">
                <a href="streams.php">Voir les Streams</a>
              </div>
            </div>
          </div>
          <!-- ***** Gaming Library End ***** -->
        </div>
      </div>
    </div>
  </div>
  
  <?php include 'includes/footer.php'; ?>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/dons-assoc.js"></script>
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



    // ===== ANIMATIONS DES STATISTIQUES =====
    function animateCounters() {
      const counters = document.querySelectorAll('.stat-number');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const counter = entry.target;
            const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
              current += step;
              if (current >= target) {
                current = target;
                clearInterval(timer);
              }
              
              // Formatage des nombres
              let displayValue = Math.floor(current);
              if (counter.textContent.includes('K')) {
                displayValue = (displayValue / 1000).toFixed(0) + 'K';
              } else if (counter.textContent.includes('€')) {
                displayValue = displayValue + 'K€';
              } else if (displayValue >= 1000) {
                displayValue = displayValue.toLocaleString();
              }
              
              counter.textContent = displayValue + (counter.textContent.includes('+') ? '+' : '');
            }, 16);
            
            observer.unobserve(counter);
          }
        });
      }, { threshold: 0.5 });

      counters.forEach(counter => observer.observe(counter));
    }

    // ===== EFFETS DE PARTICULES =====
    function createParticles() {
      const heroSection = document.querySelector('.hero-section');
      if (!heroSection) return;

      for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.style.cssText = `
          position: absolute;
          width: 2px;
          height: 2px;
          background: rgba(138, 43, 226, 0.6);
          border-radius: 50%;
          pointer-events: none;
          animation: particleFloat ${3 + Math.random() * 4}s ease-in-out infinite;
          animation-delay: ${Math.random() * 2}s;
          left: ${Math.random() * 100}%;
          top: ${Math.random() * 100}%;
        `;
        heroSection.appendChild(particle);
      }

      // CSS pour l'animation des particules
      const style = document.createElement('style');
      style.textContent = `
        @keyframes particleFloat {
          0%, 100% { 
            transform: translateY(0px) translateX(0px); 
            opacity: 0.3;
          }
          25% { 
            transform: translateY(-20px) translateX(10px); 
            opacity: 0.8;
          }
          50% { 
            transform: translateY(-10px) translateX(-15px); 
            opacity: 0.6;
          }
          75% { 
            transform: translateY(-30px) translateX(5px); 
            opacity: 0.9;
          }
        }
      `;
      document.head.appendChild(style);
    }



    // ===== INITIALISATION =====
    document.addEventListener('DOMContentLoaded', function() {
      animateCounters();
      createParticles();
      
      // Effet de typing sur le titre principal
      const heroTitle = document.querySelector('.hero-title');
      if (heroTitle) {
        const text = heroTitle.textContent;
        heroTitle.textContent = '';
        heroTitle.style.borderRight = '2px solid #8A2BE2';
        
        let i = 0;
        const typeWriter = () => {
          if (i < text.length) {
            heroTitle.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
          } else {
            setTimeout(() => {
              heroTitle.style.borderRight = 'none';
            }, 1000);
          }
        };
        
        setTimeout(typeWriter, 1000);
      }
    });


  </script>
</body>
</html>
