<?php
/**
 * CHRONOS API - Logout Endpoint
 * Revokes the user's API token
 * POST /api/auth/logout.php
 */

require_once __DIR__ . '/../config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method not allowed');
}

// Validate token
$tokenData = getAuthUser($pdo);

if ($tokenData) {
    // Revoke token
    revokeToken($pdo, $tokenData['token']);
}

jsonResponse(true, null, 'Logout successful');
