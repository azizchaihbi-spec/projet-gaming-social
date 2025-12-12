<?php
/**
 * Bandeau de consentement des cookies - RGPD
 * À inclure dans toutes les pages du site
 */
?>

<!-- Bandeau de consentement des cookies -->
<div id="cookieBanner" class="cookie-banner">
    <div class="cookie-banner-content">
        <div class="cookie-banner-text">
            <h3>
                <i class="fa fa-cookie-bite" aria-hidden="true"></i>
                Nous utilisons des cookies
            </h3>
            <p>
                Nous utilisons des cookies pour améliorer votre expérience sur notre site. 
                Certains cookies sont essentiels au fonctionnement du site, tandis que d'autres nous aident à l'améliorer.
                <a href="privacy-policy.php" target="_blank">En savoir plus</a>
            </p>
        </div>
        
        <div class="cookie-banner-actions">
            <button id="acceptCookies" class="cookie-btn cookie-btn-accept">
                <i class="fa fa-check"></i>
                Tout accepter
            </button>
            <button id="rejectCookies" class="cookie-btn cookie-btn-reject">
                <i class="fa fa-times"></i>
                Tout refuser
            </button>
            <button id="cookieSettings" class="cookie-btn cookie-btn-settings">
                <i class="fa fa-cog"></i>
                Personnaliser
            </button>
        </div>
    </div>
</div>

<!-- Modal des paramètres de cookies -->
<div id="cookieSettingsModal" class="cookie-settings-modal">
    <div class="cookie-settings-content">
        <div class="cookie-settings-header">
            <h3>
                <i class="fa fa-sliders-h"></i>
                Paramètres des cookies
            </h3>
            <button id="closeSettingsModal" class="cookie-close-btn" aria-label="Fermer">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div class="cookie-categories">
            <!-- Cookies essentiels -->
            <div class="cookie-category">
                <div class="cookie-category-header">
                    <h4>
                        Cookies essentiels
                        <span class="cookie-required-badge">REQUIS</span>
                    </h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookie-necessary" checked disabled>
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>
                    Ces cookies sont nécessaires au fonctionnement du site et ne peuvent pas être désactivés. 
                    Ils permettent l'authentification, la sécurité et les fonctionnalités de base.
                </p>
            </div>
            
            <!-- Cookies fonctionnels -->
            <div class="cookie-category">
                <div class="cookie-category-header">
                    <h4>Cookies fonctionnels</h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookie-functional" checked>
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>
                    Ces cookies permettent de mémoriser vos préférences (langue, thème, etc.) 
                    pour améliorer votre expérience sur le site.
                </p>
            </div>
            
            <!-- Cookies analytiques -->
            <div class="cookie-category">
                <div class="cookie-category-header">
                    <h4>Cookies analytiques</h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookie-analytics">
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>
                    Ces cookies nous aident à comprendre comment les visiteurs utilisent notre site 
                    en collectant des informations de manière anonyme.
                </p>
            </div>
            
            <!-- Cookies marketing -->
            <div class="cookie-category">
                <div class="cookie-category-header">
                    <h4>Cookies marketing</h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookie-marketing">
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>
                    Ces cookies sont utilisés pour afficher des publicités pertinentes 
                    et mesurer l'efficacité de nos campagnes publicitaires.
                </p>
            </div>
        </div>
        
        <div class="cookie-settings-actions">
            <button id="savePreferences" class="cookie-btn cookie-btn-accept" style="flex: 1;">
                <i class="fa fa-save"></i>
                Enregistrer mes préférences
            </button>
        </div>
    </div>
</div>
