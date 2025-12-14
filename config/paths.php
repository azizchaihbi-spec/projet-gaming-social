<?php
/**
 * Configuration des chemins de l'application
 * Ce fichier permet de gérer automatiquement les chemins peu importe le nom du dossier
 */

class PathConfig {
    private static $basePath = null;
    private static $baseUrl = null;
    
    /**
     * Obtient le chemin de base de l'application (ex: /mon-dossier)
     */
    public static function getBasePath() {
        if (self::$basePath === null) {
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            
            // Si on est dans le dossier api, remonter d'un niveau
            if (basename($scriptDir) === 'api') {
                self::$basePath = dirname($scriptDir);
            } else {
                self::$basePath = $scriptDir;
            }
            
            // Si on est à la racine, pas de préfixe
            if (self::$basePath === '/') {
                self::$basePath = '';
            }
        }
        
        return self::$basePath;
    }
    
    /**
     * Obtient l'URL de base complète (ex: http://localhost/mon-dossier)
     */
    public static function getBaseUrl() {
        if (self::$baseUrl === null) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $basePath = self::getBasePath();
            
            self::$baseUrl = $protocol . '://' . $host . $basePath;
        }
        
        return self::$baseUrl;
    }
    
    /**
     * Convertit un chemin relatif en URL absolue
     */
    public static function toAbsoluteUrl($relativePath) {
        $basePath = self::getBasePath();
        
        // Si le chemin commence déjà par http, le retourner tel quel
        if (str_starts_with($relativePath, 'http')) {
            return $relativePath;
        }
        
        // Ajouter le slash initial si nécessaire
        if (!str_starts_with($relativePath, '/')) {
            $relativePath = '/' . $relativePath;
        }
        
        return $basePath . $relativePath;
    }
}