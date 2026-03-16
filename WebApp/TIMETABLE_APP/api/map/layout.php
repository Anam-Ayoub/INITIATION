<?php
/**
 * CHRONOS API - Map Layout Endpoint
 * Returns the faculty map grid data
 * GET /api/map/layout.php
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, null, 'Method not allowed');
}

// Validate token (any authenticated user can view the map)
$tokenData = getAuthUser($pdo);

// Fetch map layout from database
$stmt = $pdo->prepare("SELECT grid_data FROM CARTE_LAYOUT WHERE id = 1");
$stmt->execute();
$row = $stmt->fetch();

if ($row && !empty($row['grid_data']) && $row['grid_data'] !== '{}') {
    $gridData = json_decode($row['grid_data'], true);
} else {
    // Return empty grid structure
    $gridData = [];
}

// Parse classrooms to extract room information
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
