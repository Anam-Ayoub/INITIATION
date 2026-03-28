<?php
/**
 * API CHRONOS - Point de connexion de connexion
 * Authentifie les étudiants, professeurs ou le personnel de sécurité
 * POST /api/auth/login.php
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method not allowed');
}

// Obtenir les données d'entrée
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Essayer les données de formulaire si l'analyse JSON échoue
    $input = $_POST;
}

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

// Valider l'entrée
if (empty($email) || empty($password)) {
    jsonResponse(false, null, 'Email and password are required');
}

// Essayer d'abord de s'authentifier en tant qu'étudiant
$stmt = $pdo->prepare("SELECT ID_STUDENT, FULL_NAME, EMAIL, PASSWORD, ID_CLASSE FROM STUDENT WHERE EMAIL = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

$userType = 'student';

if (!$user || !password_verify($password, $user['PASSWORD'])) {
    // Essayer professeur
    $stmt = $pdo->prepare("SELECT ID_PROF, NOM_PROF as FULL_NAME, EMAIL, PASSWORD FROM PROF WHERE EMAIL = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    $userType = 'professor';
    
    if (!$user || !password_verify($password, $user['PASSWORD'])) {
        // Essayer sécurité
        $stmt = $pdo->prepare("SELECT ID_SEC, FULL_NAME, EMAIL, PASSWORD FROM SECURITY WHERE EMAIL = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        $userType = 'security';
        
        if (!$user || !password_verify($password, $user['PASSWORD'])) {
            jsonResponse(false, null, 'Invalid email or password');
        }
    }
}

// Générer un jeton
$token = generateApiToken();

// Obtenir l'ID de l'utilisateur en fonction du type
$userId = $user['ID_STUDENT'] ?? $user['ID_PROF'] ?? $user['ID_SEC'];

// Stocker le jeton
storeToken($pdo, $userId, $userType, $token);

// Construire la réponse des données utilisateur
$userData = [
    'id' => $userId,
    'full_name' => $user['FULL_NAME'],
    'email' => $user['EMAIL'],
    'type' => $userType
];

// Ajouter les informations de classe pour les étudiants
if ($userType === 'student' && isset($user['ID_CLASSE'])) {
    $stmt = $pdo->prepare("SELECT NUMERO FROM CLASSE WHERE ID_CLASSE = :id");
    $stmt->execute(['id' => $user['ID_CLASSE']]);
    $class = $stmt->fetch();
    $userData['class_id'] = $user['ID_CLASSE'];
    $userData['class_name'] = $class['NUMERO'] ?? 'Unknown';
}

jsonResponse(true, [
    'token' => $token,
    'user' => $userData
], 'Login successful');
