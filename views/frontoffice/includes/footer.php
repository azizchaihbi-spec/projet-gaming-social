<!-- CHATBOT IA -->
<?php include __DIR__ . '/chatbot.php'; ?>

<!-- FOOTER CRÉATIF GAMING -->
<footer class="footer-gaming">
    <div class="footer-particles"></div>
    <div class="container">
        <div class="row">
            <!-- Colonne Logo & Mission -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-brand">
                    <img src="assets/images/logooo.png" alt="Play to Help" class="footer-logo-img mb-3">
                    <h4 class="footer-title">Play to Help</h4>
                    <p class="footer-tagline">🎮 Gaming pour l'Humanitaire</p>
                    <p class="footer-description">Transformez votre passion du gaming en force pour le bien. Chaque partie compte, chaque don change des vies.</p>
                </div>
            </div>

            <!-- Colonne Navigation Rapide -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="footer-heading">Navigation</h5>
                <ul class="footer-links">
                    <li><a href="Accueil.php">🏠 Accueil</a></li>
                    <li><a href="browse.php">🎯 Événements</a></li>
                    <li><a href="streams.php">📺 Streams</a></li>
                    <li><a href="association.php">🤝 Associations</a></li>
                    <li><a href="don.php">💚 Dons</a></li>
                </ul>
            </div>

            <!-- Colonne Communauté -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">Communauté</h5>
                <ul class="footer-links">
                    <li><a href="https://discord.gg/zbGbn4Pz" target="_blank">👥 Rejoindre Discord</a></li>
                    <li><a href="#">📧 Newsletter</a></li>
                    <li><a href="index.php">📖 Forum Q&A</a></li>
                    <li><a href="#">❓ FAQ</a></li>
                    <li><a href="#">📞 Contact</a></li>
                </ul>
            </div>

            <!-- Colonne Réseaux Sociaux -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">Suivez-nous</h5>
                <div class="footer-social">
                    <a href="#" class="social-icon social-twitch" title="Twitch">
                        <i class="fab fa-twitch"></i>
                    </a>
                    <a href="https://discord.gg/zbGbn4Pz" target="_blank" class="social-icon social-discord" title="Discord">
                        <i class="fab fa-discord"></i>
                    </a>
                    <a href="#" class="social-icon social-twitter" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon social-youtube" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/play2_help_?igsh=ZmxsbTR0enQxNWo5" target="_blank" class="social-icon social-instagram" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
                <div class="footer-stats mt-4">
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Gamers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50K€</span>
                        <span class="stat-label">Collectés</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de copyright -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="footer-copyright">
                        © 2025 <span class="brand-highlight">Play to Help</span> - Tous droits réservés
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-legal">
                        <a href="privacy-policy.php">Mentions légales</a>
                        <span class="separator">•</span>
                        <a href="privacy-policy.php">Confidentialité</a>
                        <span class="separator">•</span>
                        <a href="#">CGU</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Effet de vague animée -->
    <div class="footer-wave">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="wave-path"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="wave-path"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="wave-path"></path>
        </svg>
    </div>
</footer>

<style>
/* ===== FOOTER GAMING CRÉATIF ===== */
.footer-gaming {
    background: linear-gradient(180deg, rgba(15, 12, 41, 0.95), rgba(20, 20, 35, 1));
    position: relative;
    padding: 80px 0 30px;
    margin-top: 100px;
    overflow: hidden;
    border-top: 2px solid rgba(102, 126, 234, 0.3);
}

.footer-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 30%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(67, 233, 123, 0.1) 0%, transparent 50%);
    animation: particleFloat 20s ease-in-out infinite;
    pointer-events: none;
}

.footer-brand {
    position: relative;
    z-index: 2;
}

.footer-logo-img {
    width: 80px;
    height: auto;
    filter: drop-shadow(0 0 10px rgba(102, 126, 234, 0.5));
}

.footer-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.footer-tagline {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.footer-description {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.95rem;
    line-height: 1.6;
}

.footer-heading {
    color: white;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-bottom: 10px;
}

.footer-heading::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-links a:hover {
    color: #667eea;
    transform: translateX(5px);
    text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
}

/* Réseaux sociaux */
.footer-social {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.social-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.social-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    opacity: 0;
    transition: opacity 0.3s;
}

.social-icon:hover::before {
    opacity: 1;
}

.social-twitch {
    background: linear-gradient(135deg, #9146FF, #6441A5);
    color: white;
}

.social-discord {
    background: linear-gradient(135deg, #5865F2, #4752C4);
    color: white;
}

.social-twitter {
    background: linear-gradient(135deg, #1DA1F2, #0C85D0);
    color: white;
}

.social-youtube {
    background: linear-gradient(135deg, #FF0000, #CC0000);
    color: white;
}

.social-instagram {
    background: linear-gradient(135deg, #E1306C, #C13584, #833AB4);
    color: white;
}

.social-icon:hover {
    transform: translateY(-5px) scale(1.1);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

/* Stats */
.footer-stats {
    display: flex;
    gap: 20px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    border: 1px solid rgba(102, 126, 234, 0.3);
    flex: 1;
}

.stat-number {
    color: #667eea;
    font-size: 1.5rem;
    font-weight: 800;
    display: block;
}

.stat-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
    margin-top: 5px;
}

/* Bottom bar */
.footer-bottom {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-copyright {
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
    font-size: 0.9rem;
}

.brand-highlight {
    color: #667eea;
    font-weight: 700;
}

.footer-legal {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: center;
}

.footer-legal a {
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s;
}

.footer-legal a:hover {
    color: #667eea;
}

.footer-legal .separator {
    color: rgba(255, 255, 255, 0.3);
}

/* Vague animée */
.footer-wave {
    position: absolute;
    top: -100px;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
}

.footer-wave svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 120px;
}

.wave-path {
    fill: rgba(102, 126, 234, 0.1);
    animation: waveAnimation 10s ease-in-out infinite;
}

@keyframes waveAnimation {
    0%, 100% {
        transform: translateX(0);
    }
    50% {
        transform: translateX(-25px);
    }
}

@keyframes particleFloat {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .footer-gaming {
        padding: 50px 0 20px;
        margin-top: 60px;
    }

    .footer-social {
        justify-content: center;
    }

    .footer-stats {
        justify-content: center;
    }

    .footer-legal {
        margin-top: 15px;
    }

    .footer-wave {
        top: -50px;
    }

    .footer-wave svg {
        height: 60px;
    }
}
</style>