<?php
require_once __DIR__ . '/config/db.php';

echo "Extracting Users...\n\n";

// Function to print users
function printUsers($pdo, $table, $idCol, $nameCol) {
    try {
        $stmt = $pdo->query("SELECT $idCol, $nameCol, EMAIL FROM $table");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "=== " . strtoupper($table) . " ===\n";
        foreach ($users as $user) {
            echo "ID: " . $user[$idCol] . "\n";
            echo "Name: " . $user[$nameCol] . "\n";
            echo "Email: " . $user['EMAIL'] . "\n";
            echo "------------------------\n";
        }
        echo "\n";
    } catch (PDOException $e) {
        echo "Error fetching from $table: " . $e->getMessage() . "\n\n";
    }
}

// Ensure running via CLI
if (php_sapi_name() === 'cli') {
    printUsers($pdo, 'STUDENT', 'ID_STUDENT', 'FULL_NAME');
    printUsers($pdo, 'PROF', 'ID_PROF', 'NOM_PROF');
    printUsers($pdo, 'SECURITY', 'ID_SEC', 'FULL_NAME');
} else {
    echo "Please run this script from the command line.";
}
?>
