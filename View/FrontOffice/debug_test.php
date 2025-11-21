<?php
session_start();
require_once '../../config/config.php';

echo "<h2>Debug Test - Play to Help</h2>";

// Test de connexion à la base de données
try {
    $pdo = config::getConnexion();
    echo "✅ Connexion DB réussie<br>";
    
    // Test d'insertion d'un utilisateur
    $testEmail = "test_" . time() . "@test.com";
    $hashedPassword = password_hash("test123", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (first_name, last_name, username, email, birthdate, gender, country, role, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        'Test', 'User', 'testuser', $testEmail, '2000-01-01', 'male', 'FR', 'viewer', $hashedPassword
    ]);
    
    if ($result) {
        echo "✅ Insertion test réussie - ID: " . $pdo->lastInsertId() . "<br>";
        
        // Test de sélection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$testEmail]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Sélection test réussie - Username: " . $user['username'] . "<br>";
            
            // Test de mise à jour
            $updateStmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $updateResult = $updateStmt->execute(['assets/images/avatars/avatar2.png', $user['id']]);
            
            if ($updateResult) {
                echo "✅ Mise à jour test réussie<br>";
            } else {
                echo "❌ Échec mise à jour test<br>";
            }
            
            // Nettoyage
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->execute([$user['id']]);
            echo "✅ Nettoyage test réussi<br>";
        }
    } else {
        echo "❌ Échec insertion test<br>";
        print_r($stmt->errorInfo());
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur DB: " . $e->getMessage() . "<br>";
}

// Test des sessions
$_SESSION['test'] = 'session_works';
echo "✅ Test session: " . ($_SESSION['test'] === 'session_works' ? 'OK' : 'FAIL') . "<br>";

// Test du chemin des fichiers
echo "✅ Chemin authController: " . file_exists('../Controller/authController.php') ? 'EXISTE' : 'NEXISTE PAS' . "<br>";
echo "✅ Chemin config: " . file_exists('../config/config.php') ? 'EXISTE' : 'NEXISTE PAS' . "<br>";
?>