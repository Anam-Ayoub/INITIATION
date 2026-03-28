<?php
/**
 * API CHRONOS - Point de connexion de déconnexion
 * Révoque le jeton API de l'utilisateur
 * POST /api/auth/logout.php
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton
$tokenData = getAuthUser($pdo);

if ($tokenData) {
    // Révoquer le jeton
    revokeToken($pdo, $tokenData['token']);
}

jsonResponse(true, null, 'Logout successful');
