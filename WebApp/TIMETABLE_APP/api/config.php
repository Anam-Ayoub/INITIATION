<?php
/**
 * CHRONOS API Configuration
 * CORS headers and database connection for mobile API
 */

// Enable CORS for mobile app access
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database configuration
require_once __DIR__ . '/../../config/db.php';

/**
 * Send JSON response
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
 * Generate secure API token
 */
function generateApiToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Validate API token from Authorization header
 */
function validateToken($pdo) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (!preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
        return null;
    }
    
    $token = $matches[1];
    
    // Check token in database
    $stmt = $pdo->prepare("SELECT * FROM API_TOKENS WHERE token = :token AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    return $stmt->fetch();
}

/**
 * Get authenticated user from token
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
 * Store API token
 */
function storeToken($pdo, $userId, $userType, $token) {
    // Expire old tokens for this user
    $stmt = $pdo->prepare("DELETE FROM API_TOKENS WHERE user_id = :user_id AND user_type = :user_type");
    $stmt->execute(['user_id' => $userId, 'user_type' => $userType]);
    
    // Store new token (expires in 24 hours)
    $stmt = $pdo->prepare("INSERT INTO API_TOKENS (user_id, user_type, token, expires_at) VALUES (:user_id, :user_type, :token, DATE_ADD(NOW(), INTERVAL 24 HOUR))");
    $stmt->execute([
        'user_id' => $userId,
        'user_type' => $userType,
        'token' => $token
    ]);
}

/**
 * Revoke API token
 */
function revokeToken($pdo, $token) {
    $stmt = $pdo->prepare("DELETE FROM API_TOKENS WHERE token = :token");
    $stmt->execute(['token' => $token]);
}
