<?php
$host = "localhost";
$dbname = "chronos_db";
$username = "root"; // Nom d'utilisateur par défaut XAMPP/WAMP
$password = ""; // Mot de passe par défaut XAMPP/WAMP

// =====================================================================
// Connexion PDO — Unifiée pour toute l'application
// =====================================================================
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configurer le mode d'erreur PDO sur exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Utiliser des tableaux associatifs par défaut pour les récupérations
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Échec de la connexion à la base de données : " . $e->getMessage());
}
?>
