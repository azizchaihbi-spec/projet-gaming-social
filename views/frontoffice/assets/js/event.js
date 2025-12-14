// assets/js/event.js - Version améliorée
document.addEventListener("DOMContentLoaded", function () {
    // Vérification que Swiper est disponible
    if (typeof Swiper === 'undefined') {
        console.error('Swiper non chargé');
        return;
    }

    try {
        const spotlightSwiper = new Swiper(".spotlight-swiper", {
            slidesPerView: 1,
            spaceBetween: 30,
            centeredSlides: true,
            loop: true,
            
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },
            
            speed: 800,
            effect: "slide",
            
            // Gestion du clavier
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            
            // Accessibilité
            a11y: {
                prevSlideMessage: 'Événement précédent',
                nextSlideMessage: 'Événement suivant',
                paginationBulletMessage: 'Aller à l\'événement {{index}}',
            },
            
            on: {
                init: function () {
                    // Rendre les flèches toujours visibles
                    const arrows = document.querySelectorAll(".swiper-button-next, .swiper-button-prev");
                    arrows.forEach(btn => {
                        btn.style.opacity = "1";
                        btn.style.transform = "scale(1.3)";
                        btn.setAttribute('tabindex', '0'); // Accessibilité clavier
                    });
                    
                    // Ajouter des labels ARIA
                    const prevBtn = document.querySelector(".swiper-button-prev");
                    const nextBtn = document.querySelector(".swiper-button-next");
                    if (prevBtn) prevBtn.setAttribute('aria-label', 'Événement précédent');
                    if (nextBtn) nextBtn.setAttribute('aria-label', 'Événement suivant');
                },
                
                // Mise à jour des indicateurs live
                slideChange: function () {
                    updateLiveIndicators();
                }
            }
        });

        // Pause autoplay on hover avec gestion des préférences de mouvement
        const container = document.querySelector(".spotlight-swiper");
        if (container) {
            const shouldReduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            if (!shouldReduceMotion) {
                container.addEventListener("mouseenter", () => {
                    spotlightSwiper.autoplay.stop();
                });
                container.addEventListener("mouseleave", () => {
                    spotlightSwiper.autoplay.start();
                });
            }
            
            // Touch gestures améliorées
            container.addEventListener('touchstart', function(e) {
                this.style.cursor = 'grabbing';
            });
            
            container.addEventListener('touchend', function(e) {
                this.style.cursor = 'grab';
            });
        }

        // Mise à jour des indicateurs live
        function updateLiveIndicators() {
            const liveBadges = document.querySelectorAll('.live-badge');
            const now = new Date();
            
            liveBadges.forEach(badge => {
                // Logique simplifiée pour déterminer si l'événement est en direct
                // À remplacer par votre logique métier
                const eventTime = badge.closest('.spotlight-card').getAttribute('data-event-time');
                if (eventTime && new Date(eventTime) <= now) {
                    badge.textContent = 'En Direct';
                    badge.style.background = '#ff2e63';
                } else {
                    badge.textContent = 'Bientôt';
                    badge.style.background = '#e75e8d';
                }
            });
        }

        // Gestion du formulaire d'inscription améliorée
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', handleFormSubmit);
        }

    } catch (error) {
        console.error('Erreur initialisation Swiper:', error);
        fallbackCarousel();
    }

    // Fallback si Swiper échoue
    function fallbackCarousel() {
        const slides = document.querySelectorAll('.swiper-slide');
        if (slides.length > 0) {
            slides[0].style.display = 'block'; // Afficher seulement le premier
            console.warn('Swiper non disponible - affichage du premier événement seulement');
        }
    }
});

// Gestion du formulaire avec feedback utilisateur
function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    form.setAttribute('novalidate', true); // Disable browser validation
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Validation basique
    if (!validateForm(form)) {
        return;
    }
    
    // État de chargement
    submitBtn.textContent = 'Inscription en cours...';
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.7';
    
    // Simulation d'envoi (remplacer par votre appel API)
    setTimeout(() => {
        // Ici, votre appel AJAX vers l'API
        // $.post('register_event.php', $(form).serialize(), function(response) {
        //     handleFormResponse(response);
        // }).fail(function() {
        //     handleFormError();
        // });
        
        // Simulation de réussite
        handleFormSuccess();
        
    }, 1500);
}

function validateForm(form) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const event = document.getElementById('event').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (name === '') {
        showNotification('Veuillez entrer votre nom.', 'error');
        document.getElementById('name').style.borderColor = '#ff2e63';
        return false;
    } else {
        document.getElementById('name').style.borderColor = '';
    }

    if (!emailRegex.test(email)) {
        showNotification('Veuillez entrer une adresse email valide.', 'error');
        document.getElementById('email').style.borderColor = '#ff2e63';
        return false;
    } else {
        document.getElementById('email').style.borderColor = '';
    }

    if (event === '') {
        showNotification('Veuillez choisir un événement.', 'error');
        document.getElementById('event').style.borderColor = '#ff2e63';
        return false;
    } else {
        document.getElementById('event').style.borderColor = '';
    }

    return true;
}

function handleFormSuccess() {
    const form = document.getElementById('registerForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    submitBtn.textContent = '✓ Inscrit !';
    submitBtn.style.background = '#00ff96';
    
    showNotification('Inscription réussie ! Vous recevrez un email de confirmation.', 'success');
    
    // Reset après délai
    setTimeout(() => {
        $('#registerModal').modal('hide');
        form.reset();
        submitBtn.textContent = 'S\'inscrire';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.background = '';
    }, 2000);
}

function handleFormError() {
    const submitBtn = document.querySelector('#registerForm button[type="submit"]');
    
    submitBtn.textContent = 'Erreur - Réessayer';
    submitBtn.disabled = false;
    submitBtn.style.opacity = '1';
    
    showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
}

function showNotification(message, type) {
    // Créer une notification toast
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        z-index: 10001;
        max-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    notification.style.background = type === 'success' ? '#00ff96' : '#ff2e63';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Gestion des erreurs d'images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.spotlight-poster img, .event-poster img');
    
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'assets/images/placeholder-event.jpg';
            this.alt = 'Image de l\'événement non disponible';
            this.style.background = '#2a2a2a';
        });
        
        // Chargement paresseux
        if ('loading' in HTMLImageElement.prototype) {
            img.loading = 'lazy';
        }
    });
});

// Performance - Intersection Observer pour le lazy loading
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}