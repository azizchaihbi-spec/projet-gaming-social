<?php
<<<<<<< HEAD
class config {
    private static $pdo = null;

    public static function getConnexion() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=localhost;dbname=playtohelp_merged;charset=utf8mb4",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
?>
=======
class config
{   
    private static $pdo = null;
    
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername="localhost";
            $username="root";
            $password ="";
            $dbname="playtohelp_merged";
            try {
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname",
                        $username,
                        $password
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Initialiser la connexion PDO
$pdo = config::getConnexion();

// Créer aussi une connexion MySQLi pour compatibilité
$conn = new mysqli("localhost", "root", "", "playtohelp_merged");
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>









>>>>>>> sinda
