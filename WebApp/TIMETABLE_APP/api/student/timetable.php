<?php
/**
 * API CHRONOS - Point de connexion de l'emploi du temps des étudiants
 * Retourne l'emploi du temps pour la classe de l'étudiant authentifié
 * GET /api/student/timetable.php
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

// Récupérer l'ID de la classe de l'étudiant
$stmt = $pdo->prepare("SELECT ID_CLASSE FROM STUDENT WHERE ID_STUDENT = :id");
$stmt->execute(['id' => $studentId]);
$student = $stmt->fetch();

if (!$student || !$student['ID_CLASSE']) {
    jsonResponse(false, null, 'Student class not found');
}

$classId = $student['ID_CLASSE'];

// Récupérer l'emploi du temps pour la classe
$sql = "
    SELECT 
        e.ID_EMPLOI,
        e.JOUR,
        TIME_FORMAT(e.HEURE_DEB, '%H:%i') as HEURE_DEB,
        TIME_FORMAT(e.HEURE_FIN, '%H:%i') as HEURE_FIN,
        c.NOM_COURS,
        p.NOM_PROF,
        s.NOM_SALLE
    FROM EMPLOI_DU_TEMPS e
    LEFT JOIN COURS c ON e.ID_COURS = c.ID_COURS
    LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF
    LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
    WHERE e.ID_CLASSE = :class_id
    ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.HEURE_DEB
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['class_id' => $classId]);
$sessions = $stmt->fetchAll();

// Organiser par jour
$days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$scheduleByDay = [];

foreach ($days as $day) {
    $scheduleByDay[$day] = [];
}

foreach ($sessions as $session) {
    if (isset($scheduleByDay[$session['JOUR']])) {
        $scheduleByDay[$session['JOUR']][] = [
            'id' => (int)$session['ID_EMPLOI'],
            'course' => $session['NOM_COURS'] ?? 'Unknown Course',
            'professor' => $session['NOM_PROF'] ?? 'Unknown Professor',
            'room' => $session['NOM_SALLE'] ?? 'N/A',
            'start_time' => $session['HEURE_DEB'],
            'end_time' => $session['HEURE_FIN']
        ];
    }
}

jsonResponse(true, [
    'class_id' => (int)$classId,
    'schedule' => $scheduleByDay
]);
