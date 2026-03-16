<?php
/**
 * CHRONOS API - Sessions by Date Endpoint
 * Returns sessions for a specific date (used for map room coloring)
 * GET /api/map/sessions_by_date.php?date=2026-03-16
 */

require_once __DIR__ . '/../config.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Validate token (any authenticated user can view sessions)
$tokenData = getAuthUser($pdo);

// Get date parameter (default to today)
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    jsonResponse(false, null, 'Invalid date format. Use YYYY-MM-DD');
}

// Convert date to day name in French
$timestamp = strtotime($date);
$dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$dayName = $dayNames[date('w', $timestamp)];

// Fetch all sessions for this day across all classes
$sql = "
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
    WHERE e.JOUR = :jour
    ORDER BY e.ID_SALLE, e.HEURE_DEB
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['jour' => $dayName]);
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

// Get list of all rooms that have sessions today
$activeRoomIds = array_keys($sessionsByRoom);

jsonResponse(true, [
    'date' => $date,
    'day_name' => $dayName,
    'active_room_ids' => $activeRoomIds,
    'sessions_by_room' => $sessionsByRoom,
    'total_sessions' => count($sessions)
]);
