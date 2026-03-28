<?php
/**
 * API CHRONOS - Point de connexion de la disposition de la carte
 * Retourne les données de la grille de la carte de la faculté
 * GET /api/map/layout.php
 */

// Activer le rapport d'erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

// Accepter uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Valider le jeton (tout utilisateur authentifié peut voir la carte)
$tokenData = getAuthUser($pdo);

// Récupérer la disposition de la carte de la base de données
$stmt = $pdo->prepare("SELECT grid_data FROM CARTE_LAYOUT WHERE id = 1");
$stmt->execute();
$row = $stmt->fetch();

if ($row && !empty($row['grid_data']) && $row['grid_data'] !== '{}') {
    $gridData = json_decode($row['grid_data'], true);
} else {
    // Retourner une structure de grille vide
    $gridData = [];
}

// Analyser les salles de classe pour extraire les informations sur les salles
$classrooms = [];
$roads = [];
$entrances = [];

foreach ($gridData as $cell) {
    if ($cell['type'] === 'classroom' && isset($cell['id'])) {
        $classrooms[] = [
            'index' => (int)$cell['index'],
            'name' => $cell['name'],
            'room_id' => (int)$cell['id'],
            'row' => (int)floor($cell['index'] / 30),
            'col' => (int)($cell['index'] % 30)
        ];
    } elseif ($cell['type'] === 'road') {
        $roads[] = [
            'index' => (int)$cell['index'],
            'row' => (int)floor($cell['index'] / 30),
            'col' => (int)($cell['index'] % 30)
        ];
    } elseif ($cell['type'] === 'entrance') {
        $entrances[] = [
            'index' => (int)$cell['index'],
            'name' => $cell['name'] ?? 'Entrée',
            'row' => (int)floor($cell['index'] / 30),
            'col' => (int)($cell['index'] % 30)
        ];
    }
}

jsonResponse(true, [
    'grid_width' => 30,
    'grid_height' => 20,
    'cell_size' => 40,
    'classrooms' => $classrooms,
    'roads' => $roads,
    'entrances' => $entrances,
    'raw_grid' => $gridData
]);
