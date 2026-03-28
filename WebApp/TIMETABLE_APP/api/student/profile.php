<?php
/**
 * API CHRONOS - Point de connexion du profil étudiant
 * Retourne les informations de profil de l'étudiant authentifié
 * GET /api/student/profile.php
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton et obtenir l'utilisateur
$tokenData = getAuthUser($pdo);

// Vérifier que l'utilisateur est un étudiant
if ($tokenData['user_type'] !== 'student') {
    jsonResponse(false, null, 'Access denied. Students only.');
}

$studentId = $tokenData['user_id'];

// Récupérer le profil de l'étudiant avec les informations de la classe
$stmt = $pdo->prepare("
    SELECT s.ID_STUDENT, s.FULL_NAME, s.EMAIL, c.ID_CLASSE, c.NUMERO as CLASS_NAME
    FROM STUDENT s
    LEFT JOIN CLASSE c ON s.ID_CLASSE = c.ID_CLASSE
    WHERE s.ID_STUDENT = :id
    LIMIT 1
");
$stmt->execute(['id' => $studentId]);
$student = $stmt->fetch();

if (!$student) {
    jsonResponse(false, null, 'Student not found');
}

jsonResponse(true, [
    'id' => (int)$student['ID_STUDENT'],
    'full_name' => $student['FULL_NAME'],
    'email' => $student['EMAIL'],
    'class_id' => (int)$student['ID_CLASSE'],
    'class_name' => $student['CLASS_NAME'] ?? 'Unknown'
]);
