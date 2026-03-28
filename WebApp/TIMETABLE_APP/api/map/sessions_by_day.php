<?php
/**
 * API CHRONOS - Point de connexion des sessions par jour
 * Retourne les sessions pour un jour spécifique de la semaine (Lundi-Samedi)
 * GET /api/map/sessions_by_day.php?day=Lundi
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton (tout utilisateur authentifié peut voir les sessions)
$tokenData = getAuthUser($pdo);

// Obtenir le paramètre du jour
$day = isset($_GET['day']) ? $_GET['day'] : 'Lundi';

// Valider le nom du jour
$validDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
if (!in_array($day, $validDays)) {
    jsonResponse(false, null, 'Invalid day. Must be one of: ' . implode(', ', $validDays));
}

// Construire une requête spécifique au rôle basée sur le type d'utilisateur authentifié
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
        // Les étudiants ne voient que les salles où leur classe a des sessions
        $baseSql .= " WHERE e.JOUR = :jour AND e.ID_CLASSE = (SELECT ID_CLASSE FROM STUDENT WHERE ID_STUDENT = :user_id)";
        $params['user_id'] = $userId;
        break;

    case 'professor':
        // Les professeurs ne voient que les salles où ils enseignent
        $baseSql .= " WHERE e.JOUR = :jour AND e.ID_PROF = :user_id";
        $params['user_id'] = $userId;
        break;

    case 'security':
    default:
        // La sécurité voit toutes les salles utilisées
        $baseSql .= " WHERE e.JOUR = :jour";
        break;
}

$baseSql .= " ORDER BY e.ID_SALLE, e.HEURE_DEB";

$stmt = $pdo->prepare($baseSql);
$stmt->execute($params);
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

// Obtenir la liste de toutes les salles qui ont des sessions ce jour
$activeRoomIds = array_keys($sessionsByRoom);

jsonResponse(true, [
    'day' => $day,
    'active_room_ids' => $activeRoomIds,
    'sessions_by_room' => $sessionsByRoom,
    'total_sessions' => count($sessions)
]);
