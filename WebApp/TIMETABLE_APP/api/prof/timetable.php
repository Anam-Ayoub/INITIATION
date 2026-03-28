<?php
/**
 * API CHRONOS - Point de connexion de l'emploi du temps des professeurs
 * Retourne l'emploi du temps du professeur authentifié
 * GET /api/prof/timetable.php
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton et obtenir l'utilisateur
$tokenData = getAuthUser($pdo);

// Vérifier que l'utilisateur est un professeur
if ($tokenData['user_type'] !== 'professor') {
    jsonResponse(false, null, 'Access denied. Professors only.');
}

$profId = $tokenData['user_id'];

// Récupérer le nom du professeur
$stmt = $pdo->prepare("SELECT NOM_PROF FROM PROF WHERE ID_PROF = :id");
$stmt->execute(['id' => $profId]);
$prof = $stmt->fetch();

if (!$prof) {
    jsonResponse(false, null, 'Professor not found');
}

// Récupérer l'emploi du temps pour le professeur
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
