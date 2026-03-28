<?php
/**
 * Configuration de l'API CHRONOS
 * En-têtes CORS et connexion à la base de données pour l'API mobile
 */

// Activer CORS pour l'accès à l'application mobile
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Gérer la requête de pré-vérification OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuration de la base de données
require_once __DIR__ . '/../../config/db.php';

/**
 * Envoyer une réponse JSON
 */
function jsonResponse($success, $data = null, $message = '') {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Générer un jeton API sécurisé
 */
function generateApiToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Valider le jeton API à partir de l'en-tête Authorization
 */
function validateToken($pdo) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (!preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
        return null;
    }
    
    $token = $matches[1];
    
    // Vérifier le jeton dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM API_TOKENS WHERE token = :token AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    return $stmt->fetch();
}

/**
 * Obtenir l'utilisateur authentifié à partir du jeton
 */
function getAuthUser($pdo) {
    $tokenData = validateToken($pdo);
    
    if (!$tokenData) {
        http_response_code(401);
        jsonResponse(false, null, 'Session expirée. Veuillez vous reconnecter.');
    }
    
    return $tokenData;
}

/**
 * Stocker le jeton API
 */
function storeToken($pdo, $userId, $userType, $token) {
    // Faire expirer les anciens jetons pour cet utilisateur
    $stmt = $pdo->prepare("DELETE FROM API_TOKENS WHERE user_id = :user_id AND user_type = :user_type");
    $stmt->execute(['user_id' => $userId, 'user_type' => $userType]);
    
    // Stocker le nouveau jeton (expire dans 24 heures)
    $stmt = $pdo->prepare("INSERT INTO API_TOKENS (user_id, user_type, token, expires_at) VALUES (:user_id, :user_type, :token, DATE_ADD(NOW(), INTERVAL 24 HOUR))");
    $stmt->execute([
        'user_id' => $userId,
        'user_type' => $userType,
        'token' => $token
    ]);
}

/**
 * Révoquer le jeton API
 */
function revokeToken($pdo, $token) {
    $stmt = $pdo->prepare("DELETE FROM API_TOKENS WHERE token = :token");
    $stmt->execute(['token' => $token]);
}
