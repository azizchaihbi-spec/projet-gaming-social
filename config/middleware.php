<?php
/**
 * Session & Role Middleware
 * Protects routes and enforces role-based access control
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 * Returns: true if authenticated, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Check if user has admin role
 * Returns: true if user is admin, false otherwise
 */
function isAdmin() {
    return isAuthenticated() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Check if user has a specific role
 * @param string $role - role to check (admin, streamer, viewer)
 * Returns: true if user has the role, false otherwise
 */
function hasRole($role) {
    return isAuthenticated() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

/**
 * Require authentication - redirects to login if not authenticated
 * Used for protecting routes that require any logged-in user
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ../View/FrontOffice/login.php');
        exit();
    }
}

/**
 * Require admin role - redirects to error page if not admin
 * Used for protecting admin-only routes like BackOffice
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('HTTP/1.1 403 Forbidden');
        $_SESSION['errors'] = ['Accès refusé : vous n\'avez pas les droits admin'];
        header('Location: ../View/FrontOffice/login.php');
        exit();
    }
}

/**
 * Get current user from session
 * Returns: associative array of user data, or null if not authenticated
 */
function getCurrentUser() {
    if (isAuthenticated()) {
        return $_SESSION['user'];
    }
    return null;
}

/**
 * Get user info (ID, email, role)
 * Returns: string with user summary or 'Anonyme'
 */
function getUserInfo() {
    if (isAuthenticated()) {
        $user = getCurrentUser();
        return htmlspecialchars($user['username'] ?? $user['email'] ?? 'Utilisateur');
    }
    return 'Anonyme';
}

/**
 * Logout user and clear session
 */
function logoutUser() {
    session_destroy();
    session_unset();
    header('Location: ../View/FrontOffice/login.php');
    exit();
}

?>
