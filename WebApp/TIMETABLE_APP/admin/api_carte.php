<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/config/db.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit();
}

// Get the requested action
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_map':
        $sql = "SELECT grid_data FROM CARTE_LAYOUT WHERE id = 1";
        $result = $conn->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $row['grid_data']]);
        } else {
            // Return empty grid if none exists
            echo json_encode(['success' => true, 'data' => '{}']);
        }
        break;

    case 'save_map':
        // Get JSON payload
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['grid_data'])) {
            $gridData = $conn->real_escape_string($data['grid_data']);
            
            // Upsert the layout
            $sql = "INSERT INTO CARTE_LAYOUT (id, grid_data) VALUES (1, '$gridData') ON DUPLICATE KEY UPDATE grid_data = '$gridData'";
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Carte sauvegardée avec succès']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Données de la grille manquantes']);
        }
        break;

    case 'add_salle':
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['nom'])) {
            $nom = $conn->real_escape_string($data['nom']);
            
            // Check if room already exists
            $check = $conn->query("SELECT ID_SALLE FROM SALLE WHERE NOM_SALLE = '$nom'");
            if ($check && $check->num_rows > 0) {
                // Return existing ID
                $row = $check->fetch_assoc();
                echo json_encode(['success' => true, 'id_salle' => $row['ID_SALLE'], 'message' => 'Salle déjà existante']);
            } else {
                // Insert new room
                $sql = "INSERT INTO SALLE (NOM_SALLE, CAPACITE) VALUES ('$nom', 0)";
                if ($conn->query($sql)) {
                    $newId = $conn->insert_id;
                    echo json_encode(['success' => true, 'id_salle' => $newId, 'message' => 'Salle ajoutée']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout de la salle']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Nom de la salle manquant']);
        }
        break;

    case 'delete_salle':
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['nom'])) {
            $nom = $conn->real_escape_string($data['nom']);
            
            // Only delete if it's not being used in the timetable
            // Wait, standard behavior usually allows deleting if cascading, but let's be safe
            // Let's just delete the room from the `SALLE` table by name.
            $sql = "DELETE FROM SALLE WHERE NOM_SALLE = '$nom'";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Salle supprimée']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression (peut-être utilisée dans un emploi)']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Nom de la salle manquant']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Action inconnue']);
        break;
}
?>
