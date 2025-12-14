/**
 * Utilitaires pour la gestion des chemins de l'application
 * Ce fichier permet de gérer automatiquement les chemins peu importe le nom du dossier
 */

window.PathUtils = (function() {
    let basePath = null;
    
    /**
     * Détecte automatiquement le chemin de base de l'application
     */
    function getBasePath() {
        if (basePath === null) {
            const path = window.location.pathname;
            const segments = path.split('/').filter(s => s);
            
            // Si on est dans un sous-dossier, utiliser le premier segment comme base
            if (segments.length > 0 && !segments[0].includes('.')) {
                basePath = '/' + segments[0];
            } else {
                basePath = '';
            }
        }
        
        return basePath;
    }
    
    /**
     * Résout l'URL de l'API en fonction de l'environnement
     */
    function resolveApiUrl(apiFile) {
        const currentBasePath = getBasePath();
        
        // Pour Live Server (port 5500), essayer de deviner le chemin localhost
        if (window.location.port === '5500') {
            const currentPath = window.location.pathname;
            const folderMatch = currentPath.match(/\/([^\/]+)\//);
            const folderName = folderMatch ? folderMatch[1] : 'play-to-help';
            return `http://localhost/${folderName}/api/${apiFile}`;
        }
        
        return `${currentBasePath}/api/${apiFile}`;
    }
    
    /**
     * Convertit un chemin relatif en chemin absolu avec le bon préfixe
     */
    function toAbsolutePath(relativePath) {
        const currentBasePath = getBasePath();
        
        // Si le chemin commence déjà par http, le retourner tel quel
        if (relativePath.startsWith('http')) {
            return relativePath;
        }
        
        // Ajouter le slash initial si nécessaire
        if (!relativePath.startsWith('/')) {
            relativePath = '/' + relativePath;
        }
        
        return currentBasePath + relativePath;
    }
    
    // API publique
    return {
        getBasePath: getBasePath,
        resolveApiUrl: resolveApiUrl,
        toAbsolutePath: toAbsolutePath
    };
})();