<?php
require_once '../../config/config.php';

try {
    $pdo = config::getConnexion();
    
    // Test d'insertion
    $testEmail = "test_insert_" . time() . "@test.com";
    $hashedPassword = password_hash("test123", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (first_name, last_name, username, email, birthdate, gender, country, role, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        'Test', 'Insert', 'testinsert', $testEmail, '2000-01-01', 'male', 'FR', 'viewer', $hashedPassword
    ]);
    
    if ($result) {
        $lastId = $pdo->lastInsertId();
        echo "✅ Insertion réussie - ID: " . $lastId . "<br>";
        
        // Vérification immédiate
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$lastId]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Vérification réussie - Utilisateur trouvé: " . $user['username'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
        } else {
            echo "❌ Utilisateur non trouvé après insertion<br>";
        }
    } else {
        echo "❌ Échec insertion<br>";
    }
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>