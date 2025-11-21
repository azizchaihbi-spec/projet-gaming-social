<?php
require_once '../../config/config.php';

try {
    $pdo = config::getConnexion();
    
    echo "<h2>Informations de la base de données</h2>";
    
    // Informations de connexion
    echo "<h3>Connexion :</h3>";
    echo "Host: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "<br>";
    
    // Base de données actuelle
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $db = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Base de données: " . $db['db_name'] . "<br>";
    
    // Compter les utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Nombre d'utilisateurs dans la table: " . $count['user_count'] . "<br>";
    
    // Lister les utilisateurs récents
    echo "<h3>Utilisateurs récents :</h3>";
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Date</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Aucun utilisateur trouvé dans la table.";
    }
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>