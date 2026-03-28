<?php
/**
 * API CHRONOS - Point de connexion de l'emploi du temps pour la sécurité
 * Retourne tous les horaires pour toutes les classes (pour le personnel de sécurité)
 * GET /api/security/timetable.php
 */

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton et obtenir l'utilisateur
$tokenData = getAuthUser($pdo);

// Vérifier que l'utilisateur fait partie du personnel de sécurité
if ($tokenData['user_type'] !== 'security') {
    jsonResponse(false, null, 'Access denied. Security personnel only.');
}

$securityId = $tokenData['user_id'];

// Récupérer le nom de l'agent de sécurité
$stmt = $pdo->prepare("SELECT FULL_NAME FROM SECURITY WHERE ID_SEC = :id");
$stmt->execute(['id' => $securityId]);
$security = $stmt->fetch();

if (!$security) {
    jsonResponse(false, null, 'Security personnel not found');
}

// Récupérer toutes les salles avec leur statut actuel
$stmtRooms = $pdo->query("SELECT ID_SALLE, NOM_SALLE FROM SALLE ORDER BY NOM_SALLE");
$rooms = $stmtRooms->fetchAll();

// Récupérer tous les emplois du temps groupés par classe
$sql = "
    SELECT 
        e.ID_EMPLOI,
        e.JOUR,
        TIME_FORMAT(e.HEURE_DEB, '%H:%i') as HEURE_DEB,
        TIME_FORMAT(e.HEURE_FIN, '%H:%i') as HEURE_FIN,
        c.NOM_COURS,
        cl.NUMERO as NOM_CLASSE,
        cl.ID_CLASSE,
        p.NOM_PROF,
        s.NOM_SALLE,
        s.ID_SALLE
    FROM EMPLOI_DU_TEMPS e
    LEFT JOIN COURS c ON e.ID_COURS = c.ID_COURS
    LEFT JOIN CLASSE cl ON e.ID_CLASSE = cl.ID_CLASSE
    LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF
    LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
    ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.HEURE_DEB, cl.NUMERO
";

$stmt = $pdo->query($sql);
$sessions = $stmt->fetchAll();

// Organiser par jour et par classe
$days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$scheduleByDay = [];
$classes = [];

foreach ($days as $day) {
    $scheduleByDay[$day] = [];
}

foreach ($sessions as $session) {
    // Suivre les classes uniques
    if (!isset($classes[$session['ID_CLASSE']])) {
        $classes[$session['ID_CLASSE']] = [
            'id' => (int)$session['ID_CLASSE'],
            'name' => $session['NOM_CLASSE']
        ];
    }
    
    if (isset($scheduleByDay[$session['JOUR']])) {
        $scheduleByDay[$session['JOUR']][] = [
            'id' => (int)$session['ID_EMPLOI'],
            'course' => $session['NOM_COURS'] ?? 'Unknown Course',
            'class_id' => (int)$session['ID_CLASSE'],
            'class' => $session['NOM_CLASSE'] ?? 'Unknown Class',
            'professor' => $session['NOM_PROF'] ?? 'Unknown Professor',
            'room_id' => (int)$session['ID_SALLE'],
            'room' => $session['NOM_SALLE'] ?? 'N/A',
            'start_time' => $session['HEURE_DEB'],
            'end_time' => $session['HEURE_FIN']
        ];
    }
}

jsonResponse(true, [
    'security_id' => (int)$securityId,
    'security_name' => $security['FULL_NAME'],
    'classes' => array_values($classes),
    'rooms' => $rooms,
    'schedule' => $scheduleByDay
]);
