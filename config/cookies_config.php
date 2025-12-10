<?php
/**
 * Configuration des cookies - Conformité RGPD
 */

// Durées de vie des cookies (en jours)
define('COOKIE_CONSENT_DURATION', 365); // Consentement valide 1 an
define('COOKIE_PREFERENCES_DURATION', 90); // Préférences valides 3 mois
define('COOKIE_SESSION_DURATION', 30); // Session étendue 30 jours

// Noms des cookies
define('COOKIE_CONSENT_NAME', 'playtohelp_consent');
define('COOKIE_PREFERENCES_NAME', 'playtohelp_preferences');
define('COOKIE_SESSION_NAME', 'playtohelp_session');

// Options de sécurité des cookies
define('COOKIE_SECURE', false); // true en production HTTPS
define('COOKIE_HTTPONLY', true); // Protection contre XSS
define('COOKIE_SAMESITE', 'Lax'); // 'Strict', 'Lax' ou 'None'

/**
 * Définir un cookie sécurisé
 */
function setSecureCookie($name, $value, $days = 30) {
    $expire = time() + ($days * 24 * 60 * 60);
    $path = '/';
    $domain = ''; // Laisser vide pour le domaine actuel
    
    setcookie(
        $name,
        $value,
        [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => COOKIE_SECURE,
            'httponly' => COOKIE_HTTPONLY,
            'samesite' => COOKIE_SAMESITE
        ]
    );
}

/**
 * Récupérer un cookie
 */
function getSecureCookie($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
}

/**
 * Supprimer un cookie
 */
function deleteSecureCookie($name) {
    if (isset($_COOKIE[$name])) {
        setcookie($name, '', time() - 3600, '/');
        unset($_COOKIE[$name]);
    }
}

/**
 * Vérifier si l'utilisateur a donné son consentement
 */
function hasUserConsent() {
    $consent = getSecureCookie(COOKIE_CONSENT_NAME);
    return $consent === 'accepted';
}

/**
 * Enregistrer le consentement de l'utilisateur
 */
function setUserConsent($accepted = true) {
    $value = $accepted ? 'accepted' : 'rejected';
    setSecureCookie(COOKIE_CONSENT_NAME, $value, COOKIE_CONSENT_DURATION);
    
    // Logger le consentement (optionnel, pour conformité RGPD)
    logConsent($value);
}

/**
 * Logger le consentement (pour audit RGPD)
 */
function logConsent($status) {
    $logFile = __DIR__ . '/../logs/consent_log.txt';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logEntry = sprintf(
        "[%s] IP: %s | User-Agent: %s | Status: %s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        $status
    );
    
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Sauvegarder les préférences utilisateur
 */
function saveUserPreferences($preferences) {
    if (!is_array($preferences)) {
        return false;
    }
    
    $json = json_encode($preferences);
    setSecureCookie(COOKIE_PREFERENCES_NAME, $json, COOKIE_PREFERENCES_DURATION);
    return true;
}

/**
 * Récupérer les préférences utilisateur
 */
function getUserPreferences() {
    $cookie = getSecureCookie(COOKIE_PREFERENCES_NAME);
    if (!$cookie) {
        return [];
    }
    
    $preferences = json_decode($cookie, true);
    return is_array($preferences) ? $preferences : [];
}

/**
 * Nettoyer tous les cookies du site
 */
function clearAllCookies() {
    deleteSecureCookie(COOKIE_CONSENT_NAME);
    deleteSecureCookie(COOKIE_PREFERENCES_NAME);
    deleteSecureCookie(COOKIE_SESSION_NAME);
}
