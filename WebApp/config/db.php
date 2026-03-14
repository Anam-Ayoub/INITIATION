<?php
$host = "localhost";
$dbname = "if0_41365925_timetable_system";
$username = "root"; // Nom d'utilisateur par défaut XAMPP/WAMP
$password = ""; // Mot de passe par défaut XAMPP/WAMP

// =====================================================================
// Connexion PDO (Pour les nouvelles interfaces Utilisateur: Étudiant, Prof, Sécurité)
// =====================================================================
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configurer le mode d'erreur PDO sur exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Utiliser des tableaux associatifs par défaut pour les récupérations
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Échec de la connexion PDO à la base de données : " . $e->getMessage());
}

// =====================================================================
// Connexion MySQLi (Pour les pages d'administration existantes)
// =====================================================================
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion MySQLi à la base de données : " . $conn->connect_error);
}
?>
