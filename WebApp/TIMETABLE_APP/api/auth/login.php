<?php
/**
 * CHRONOS API - Login Endpoint
 * Authenticates students, professors, or security personnel
 * POST /api/auth/login.php
 */

require_once __DIR__ . '/../config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method not allowed');
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Try form data if JSON parsing fails
    $input = $_POST;
}

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    jsonResponse(false, null, 'Email and password are required');
}

// Try to authenticate as student first
$stmt = $pdo->prepare("SELECT ID_STUDENT, FULL_NAME, EMAIL, PASSWORD, ID_CLASSE FROM STUDENT WHERE EMAIL = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

$userType = 'student';

if (!$user || !password_verify($password, $user['PASSWORD'])) {
    // Try professor
    $stmt = $pdo->prepare("SELECT ID_PROF, NOM_PROF as FULL_NAME, EMAIL, PASSWORD FROM PROF WHERE EMAIL = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    $userType = 'professor';
    
    if (!$user || !password_verify($password, $user['PASSWORD'])) {
        // Try security
        $stmt = $pdo->prepare("SELECT ID_SEC, FULL_NAME, EMAIL, PASSWORD FROM SECURITY WHERE EMAIL = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        $userType = 'security';
        
        if (!$user || !password_verify($password, $user['PASSWORD'])) {
            jsonResponse(false, null, 'Invalid email or password');
        }
    }
}

// Generate token
$token = generateApiToken();

// Get user ID based on type
$userId = $user['ID_STUDENT'] ?? $user['ID_PROF'] ?? $user['ID_SEC'];

// Store token
storeToken($pdo, $userId, $userType, $token);

// Build user data response
$userData = [
    'id' => $userId,
    'full_name' => $user['FULL_NAME'],
    'email' => $user['EMAIL'],
    'type' => $userType
];

// Add class info for students
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
