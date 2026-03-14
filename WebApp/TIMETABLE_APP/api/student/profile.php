<?php
/**
 * CHRONOS API - Student Profile Endpoint
 * Returns the authenticated student's profile information
 * GET /api/student/profile.php
 */

require_once __DIR__ . '/../config.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Validate token and get user
$tokenData = getAuthUser($pdo);

// Verify the user is a student
if ($tokenData['user_type'] !== 'student') {
    jsonResponse(false, null, 'Access denied. Students only.');
}

$studentId = $tokenData['user_id'];

// Fetch student profile with class info
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
