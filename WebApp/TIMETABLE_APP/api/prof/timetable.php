<?php
/**
 * CHRONOS API - Professor Timetable Endpoint
 * Returns the timetable for the authenticated professor
 * GET /api/prof/timetable.php
 */

require_once __DIR__ . '/../config.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Validate token and get user
$tokenData = getAuthUser($pdo);

// Verify the user is a professor
if ($tokenData['user_type'] !== 'professor') {
    jsonResponse(false, null, 'Access denied. Professors only.');
}

$profId = $tokenData['user_id'];

// Fetch professor's name
$stmt = $pdo->prepare("SELECT NOM_PROF FROM PROF WHERE ID_PROF = :id");
$stmt->execute(['id' => $profId]);
$prof = $stmt->fetch();

if (!$prof) {
    jsonResponse(false, null, 'Professor not found');
}

// Fetch timetable for the professor
$sql = "
    SELECT 
        e.ID_EMPLOI,
        e.JOUR,
        TIME_FORMAT(e.HEURE_DEB, '%H:%i') as HEURE_DEB,
        TIME_FORMAT(e.HEURE_FIN, '%H:%i') as HEURE_FIN,
        c.NOM_COURS,
        cl.NUMERO as NOM_CLASSE,
        s.NOM_SALLE
    FROM EMPLOI_DU_TEMPS e
    LEFT JOIN COURS c ON e.ID_COURS = c.ID_COURS
    LEFT JOIN CLASSE cl ON e.ID_CLASSE = cl.ID_CLASSE
    LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
    WHERE e.ID_PROF = :prof_id
    ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.HEURE_DEB
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['prof_id' => $profId]);
$sessions = $stmt->fetchAll();

// Organize by day
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
            'class' => $session['NOM_CLASSE'] ?? 'Unknown Class',
            'room' => $session['NOM_SALLE'] ?? 'N/A',
            'start_time' => $session['HEURE_DEB'],
            'end_time' => $session['HEURE_FIN']
        ];
    }
}

jsonResponse(true, [
    'prof_id' => (int)$profId,
    'prof_name' => $prof['NOM_PROF'],
    'schedule' => $scheduleByDay
]);
