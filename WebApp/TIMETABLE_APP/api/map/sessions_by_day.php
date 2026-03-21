<?php
/**
 * CHRONOS API - Sessions by Day Endpoint
 * Returns sessions for a specific day of the week (Lundi-Samedi)
 * GET /api/map/sessions_by_day.php?day=Lundi
 */

require_once __DIR__ . '/../config.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Validate token (any authenticated user can view sessions)
$tokenData = getAuthUser($pdo);

// Get day parameter
$day = isset($_GET['day']) ? $_GET['day'] : 'Lundi';

// Validate day name
$validDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
if (!in_array($day, $validDays)) {
    jsonResponse(false, null, 'Invalid day. Must be one of: ' . implode(', ', $validDays));
}

// Build role-specific query based on the authenticated user's type
$userType = $tokenData['user_type'];
$userId = (int)$tokenData['user_id'];

$baseSql = "
    SELECT 
        e.ID_EMPLOI,
        e.ID_SALLE,
        e.ID_CLASSE,
        e.JOUR,
        TIME_FORMAT(e.HEURE_DEB, '%H:%i') as HEURE_DEB,
        TIME_FORMAT(e.HEURE_FIN, '%H:%i') as HEURE_FIN,
        c.NOM_COURS,
        p.NOM_PROF,
        s.NOM_SALLE as ROOM_NAME,
        cl.NUMERO as CLASS_NAME
    FROM EMPLOI_DU_TEMPS e
    LEFT JOIN COURS c ON e.ID_COURS = c.ID_COURS
    LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF
    LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
    LEFT JOIN CLASSE cl ON e.ID_CLASSE = cl.ID_CLASSE
";

$params = ['jour' => $day];

switch ($userType) {
    case 'student':
        // Students see only rooms where their class has sessions
        $baseSql .= " WHERE e.JOUR = :jour AND e.ID_CLASSE = (SELECT ID_CLASSE FROM STUDENT WHERE ID_STUDENT = :user_id)";
        $params['user_id'] = $userId;
        break;

    case 'professor':
        // Professors see only rooms where they teach
        $baseSql .= " WHERE e.JOUR = :jour AND e.ID_PROF = :user_id";
        $params['user_id'] = $userId;
        break;

    case 'security':
    default:
        // Security sees all rooms that are in use
        $baseSql .= " WHERE e.JOUR = :jour";
        break;
}

$baseSql .= " ORDER BY e.ID_SALLE, e.HEURE_DEB";

$stmt = $pdo->prepare($baseSql);
$stmt->execute($params);
$sessions = $stmt->fetchAll();

// Group sessions by room_id for quick lookup
$sessionsByRoom = [];
foreach ($sessions as $session) {
    $roomId = (int)$session['ID_SALLE'];
    if (!isset($sessionsByRoom[$roomId])) {
        $sessionsByRoom[$roomId] = [];
    }
    $sessionsByRoom[$roomId][] = [
        'id' => (int)$session['ID_EMPLOI'],
        'course' => $session['NOM_COURS'] ?? 'Unknown Course',
        'professor' => $session['NOM_PROF'] ?? 'Unknown Professor',
        'class' => $session['CLASS_NAME'] ?? 'Unknown Class',
        'room_name' => $session['ROOM_NAME'] ?? 'N/A',
        'start_time' => $session['HEURE_DEB'],
        'end_time' => $session['HEURE_FIN'],
        'day' => $session['JOUR']
    ];
}

// Get list of all rooms that have sessions this day
$activeRoomIds = array_keys($sessionsByRoom);

jsonResponse(true, [
    'day' => $day,
    'active_room_ids' => $activeRoomIds,
    'sessions_by_room' => $sessionsByRoom,
    'total_sessions' => count($sessions)
]);
