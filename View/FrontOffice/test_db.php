<?php
require_once '../../config/config.php';

try {
    $pdo = config::getConnexion();
    echo "✅ Connexion à la base de données réussie!<br>";
    
    // Vérifier si la table users existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'users' existe!<br>";
        
        // Vérifier la structure de la table
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Colonnes de la table users: " . implode(', ', $columns) . "<br>";
    } else {
        echo "❌ Table 'users' n'existe pas!<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
}
?>