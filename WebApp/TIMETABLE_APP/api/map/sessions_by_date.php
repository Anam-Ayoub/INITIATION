<?php
/**
 * API CHRONOS - Point de connexion des sessions par date
 * Retourne les sessions pour une date spécifique (utilisé pour la coloration des salles sur la carte)
 * GET /api/map/sessions_by_date.php?date=2026-03-16
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton (tout utilisateur authentifié peut voir les sessions)
$tokenData = getAuthUser($pdo);

// Obtenir le paramètre de date (par défaut aujourd'hui)
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Valider le format de la date
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    jsonResponse(false, null, 'Invalid date format. Use YYYY-MM-DD');
}

// Convertir la date en nom de jour en français
$timestamp = strtotime($date);
$dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$dayName = $dayNames[date('w', $timestamp)];

// Récupérer toutes les sessions de cette journée dans toutes les classes
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

// Grouper les sessions par ID de salle pour une recherche rapide
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

// Obtenir la liste de toutes les salles qui ont des sessions aujourd'hui
$activeRoomIds = array_keys($sessionsByRoom);

jsonResponse(true, [
    'date' => $date,
    'day_name' => $dayName,
    'active_room_ids' => $activeRoomIds,
    'sessions_by_room' => $sessionsByRoom,
    'total_sessions' => count($sessions)
]);
