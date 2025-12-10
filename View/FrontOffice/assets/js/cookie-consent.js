/**
 * Système de gestion des cookies - Conformité RGPD
 * Play to Help - 2025
 */

(function() {
    'use strict';

    // Configuration
    const COOKIE_CONSENT_NAME = 'playtohelp_consent';
    const COOKIE_PREFERENCES_NAME = 'playtohelp_preferences';
    const COOKIE_DURATION = 365; // jours

    // Catégories de cookies
    const cookieCategories = {
        necessary: {
            name: 'Cookies essentiels',
            description: 'Nécessaires au fonctionnement du site (authentification, sécurité)',
            required: true,
            enabled: true
        },
        functional: {
            name: 'Cookies fonctionnels',
            description: 'Améliorent l\'expérience utilisateur (préférences, langue)',
            required: false,
            enabled: true
        },
        analytics: {
            name: 'Cookies analytiques',
            description: 'Nous aident à comprendre comment vous utilisez le site',
            required: false,
            enabled: false
        },
        marketing: {
            name: 'Cookies marketing',
            description: 'Utilisés pour afficher des publicités pertinentes',
            required: false,
            enabled: false
        }
    };

    // Vérifier si le bandeau doit être affiché
    function shouldShowBanner() {
        const consent = getCookie(COOKIE_CONSENT_NAME);
        return !consent;
    }

    // Afficher le bandeau
    function showBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
            banner.classList.add('show');
        }
    }

    // Cacher le bandeau
    function hideBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
            banner.classList.remove('show');
            setTimeout(() => {
                banner.style.display = 'none';
            }, 400);
        }
    }

    // Accepter tous les cookies
    function acceptAllCookies() {
        // Activer toutes les catégories
        Object.keys(cookieCategories).forEach(category => {
            cookieCategories[category].enabled = true;
        });
        
        saveConsent('accepted', cookieCategories);
        hideBanner();
        
        // Initialiser les services qui nécessitent le consentement
        initializeServices();
        
        console.log('✅ Tous les cookies acceptés');
    }

    // Rejeter les cookies non essentiels
    function rejectCookies() {
        // Désactiver toutes les catégories sauf nécessaires
        Object.keys(cookieCategories).forEach(category => {
            if (!cookieCategories[category].required) {
                cookieCategories[category].enabled = false;
            }
        });
        
        saveConsent('rejected', cookieCategories);
        hideBanner();
        
        console.log('❌ Cookies non essentiels rejetés');
    }

    // Sauvegarder les préférences personnalisées
    function saveCustomPreferences() {
        const preferences = {};
        
        Object.keys(cookieCategories).forEach(category => {
            const checkbox = document.getElementById(`cookie-${category}`);
            if (checkbox && !checkbox.disabled) {
                cookieCategories[category].enabled = checkbox.checked;
            }
            preferences[category] = cookieCategories[category].enabled;
        });
        
        saveConsent('custom', cookieCategories);
        closeSettingsModal();
        hideBanner();
        
        // Initialiser uniquement les services autorisés
        initializeServices();
        
        console.log('⚙️ Préférences personnalisées sauvegardées:', preferences);
    }

    // Sauvegarder le consentement dans un cookie
    function saveConsent(status, categories) {
        const consentData = {
            status: status,
            timestamp: new Date().toISOString(),
            categories: {}
        };
        
        Object.keys(categories).forEach(category => {
            consentData.categories[category] = categories[category].enabled;
        });
        
        setCookie(COOKIE_CONSENT_NAME, JSON.stringify(consentData), COOKIE_DURATION);
        setCookie(COOKIE_PREFERENCES_NAME, JSON.stringify(consentData.categories), COOKIE_DURATION);
    }

    // Ouvrir le modal des paramètres
    function openSettingsModal() {
        const modal = document.getElementById('cookieSettingsModal');
        if (modal) {
            // Charger les préférences actuelles
            loadCurrentPreferences();
            modal.classList.add('show');
        }
    }

    // Fermer le modal des paramètres
    function closeSettingsModal() {
        const modal = document.getElementById('cookieSettingsModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    // Charger les préférences actuelles dans le modal
    function loadCurrentPreferences() {
        const consent = getCookie(COOKIE_CONSENT_NAME);
        if (consent) {
            try {
                const data = JSON.parse(consent);
                if (data.categories) {
                    Object.keys(data.categories).forEach(category => {
                        const checkbox = document.getElementById(`cookie-${category}`);
                        if (checkbox) {
                            checkbox.checked = data.categories[category];
                        }
                    });
                }
            } catch (e) {
                console.error('Erreur lors du chargement des préférences:', e);
            }
        }
    }

    // Initialiser les services selon les préférences
    function initializeServices() {
        const consent = getCookie(COOKIE_CONSENT_NAME);
        if (!consent) return;
        
        try {
            const data = JSON.parse(consent);
            const categories = data.categories || {};
            
            // Cookies analytiques (Google Analytics, etc.)
            if (categories.analytics) {
                console.log('📊 Initialisation des cookies analytiques');
                // Exemple: initGoogleAnalytics();
            }
            
            // Cookies marketing
            if (categories.marketing) {
                console.log('📢 Initialisation des cookies marketing');
                // Exemple: initMarketingPixels();
            }
            
            // Cookies fonctionnels
            if (categories.functional) {
                console.log('⚙️ Initialisation des cookies fonctionnels');
                // Exemple: loadUserPreferences();
            }
            
        } catch (e) {
            console.error('Erreur lors de l\'initialisation des services:', e);
        }
    }

    // Utilitaires pour gérer les cookies
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        const cookie = `${name}=${encodeURIComponent(value)}; expires=${expires.toUTCString()}; path=/; SameSite=Lax`;
        document.cookie = cookie;
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
        }
        return null;
    }

    function deleteCookie(name) {
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    }

    // Vérifier si une catégorie est activée
    window.isCookieCategoryEnabled = function(category) {
        const consent = getCookie(COOKIE_CONSENT_NAME);
        if (!consent) return false;
        
        try {
            const data = JSON.parse(consent);
            return data.categories && data.categories[category] === true;
        } catch (e) {
            return false;
        }
    };

    // Révoquer le consentement
    window.revokeCookieConsent = function() {
        deleteCookie(COOKIE_CONSENT_NAME);
        deleteCookie(COOKIE_PREFERENCES_NAME);
        location.reload();
    };

    // Initialisation au chargement de la page
    function init() {
        // Afficher le bandeau si nécessaire
        if (shouldShowBanner()) {
            showBanner();
        } else {
            // Initialiser les services si le consentement existe déjà
            initializeServices();
        }

        // Event listeners
        const acceptBtn = document.getElementById('acceptCookies');
        const rejectBtn = document.getElementById('rejectCookies');
        const settingsBtn = document.getElementById('cookieSettings');
        const savePrefsBtn = document.getElementById('savePreferences');
        const closeModalBtn = document.getElementById('closeSettingsModal');
        const modal = document.getElementById('cookieSettingsModal');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', acceptAllCookies);
        }

        if (rejectBtn) {
            rejectBtn.addEventListener('click', rejectCookies);
        }

        if (settingsBtn) {
            settingsBtn.addEventListener('click', openSettingsModal);
        }

        if (savePrefsBtn) {
            savePrefsBtn.addEventListener('click', saveCustomPreferences);
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeSettingsModal);
        }

        // Fermer le modal en cliquant à l'extérieur
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeSettingsModal();
                }
            });
        }
    }

    // Initialiser dès que le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
