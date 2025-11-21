<?php
echo "<h2>Vérification configuration PHP</h2>";

// Vérifier les erreurs
echo "Error reporting: " . ini_get('error_reporting') . "<br>";
echo "Display errors: " . ini_get('display_errors') . "<br>";

// Vérifier les extensions
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅' : '❌') . "<br>";
echo "JSON: " . (extension_loaded('json') ? '✅' : '❌') . "<br>";

// Vérifier la méthode de requête
echo "Méthode requête: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Non défini') . "<br>";

// Test de décodage JSON
$input = '{"test": "value"}';
$decoded = json_decode($input, true);
echo "JSON decode: " . (json_last_error() === JSON_ERROR_NONE ? '✅' : '❌ ' . json_last_error_msg()) . "<br>";

// Vérifier les permissions d'écriture
$logFile = 'debug_test.log';
$canWrite = @file_put_contents($logFile, 'test');
echo "Permissions écriture: " . ($canWrite ? '✅' : '❌') . "<br>";
if ($canWrite) unlink($logFile);
?>