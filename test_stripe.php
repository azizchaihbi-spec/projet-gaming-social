<?php
// Test de la configuration Stripe
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de configuration Stripe</h2>";

// 1. Vérifier le fichier .env
echo "<h3>1. Fichier .env</h3>";
if (file_exists('.env')) {
    echo "✅ Fichier .env trouvé<br>";
} else {
    echo "❌ Fichier .env introuvable<br>";
}

// 2. Charger env_loader.php
echo "<h3>2. Chargement env_loader.php</h3>";
try {
    require_once __DIR__ . '/config/env_loader.php';
    echo "✅ env_loader.php chargé<br>";
    echo "STRIPE_SECRET_KEY: " . (defined('STRIPE_SECRET_KEY') ? substr(STRIPE_SECRET_KEY, 0, 20) . "..." : "NON DÉFINI") . "<br>";
    echo "BASE_URL: " . (defined('BASE_URL') ? BASE_URL : "NON DÉFINI") . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// 3. Vérifier Stripe SDK
echo "<h3>3. Stripe SDK</h3>";
if (file_exists('vendor/stripe/stripe-php/init.php')) {
    echo "✅ Stripe SDK trouvé<br>";
    require_once __DIR__ . '/vendor/stripe/stripe-php/init.php';
    echo "✅ Stripe SDK chargé<br>";
    echo "Version Stripe: " . \Stripe\Stripe::VERSION . "<br>";
} else {
    echo "❌ Stripe SDK introuvable dans vendor/stripe/stripe-php/<br>";
}

// 4. Tester la connexion à la base de données
echo "<h3>4. Base de données</h3>";
try {
    require_once __DIR__ . '/config/db.php';
    echo "✅ Connexion BDD réussie<br>";
    
    // Tester la requête association
    $stmt = $conn->prepare("SELECT name FROM association WHERE id_association = ?");
    $test_id = 1;
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "✅ Table 'association' accessible<br>";
    } else {
        echo "⚠️ Aucune association trouvée avec id=1<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur BDD: " . $e->getMessage() . "<br>";
}

// 5. Tester PaymentController
echo "<h3>5. PaymentController</h3>";
try {
    require_once __DIR__ . '/controllers/PaymentController.php';
    echo "✅ PaymentController chargé<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Si tous les tests sont ✅, le système devrait fonctionner !</strong></p>";
echo "<p>Accédez à <a href='views/frontoffice/don.php'>don.php</a> pour tester le formulaire.</p>";
