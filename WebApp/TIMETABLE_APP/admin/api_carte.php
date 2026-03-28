<?php
session_start();
include __DIR__ . "/../../config/db.php";

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit();
}

// Obtenir l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_map':
        $sql = "SELECT grid_data FROM CARTE_LAYOUT WHERE id = 1";
        $stmt = $pdo->query($sql);
        
        if ($stmt && $row = $stmt->fetch()) {
            echo json_encode(['success' => true, 'data' => $row['grid_data']]);
        } else {
            // Retourner une grille vide si aucune n'existe
            echo json_encode(['success' => true, 'data' => '{}']);
        }
        break;

    case 'save_map':
        // Obtenir le flux JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['grid_data'])) {
            $gridData = $data['grid_data'];
            
            // Upsert du layout
            $sql = "INSERT INTO CARTE_LAYOUT (id, grid_data) VALUES (1, ?) ON DUPLICATE KEY UPDATE grid_data = ?";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$gridData, $gridData])) {
                echo json_encode(['success' => true, 'message' => 'Carte sauvegardée avec succès']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Données de la grille manquantes']);
        }
        break;

    case 'add_salle':
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['nom'])) {
            $nom = $data['nom'];
            
            // Vérifier si la salle existe déjà
            $stmt = $pdo->prepare("SELECT ID_SALLE FROM SALLE WHERE NOM_SALLE = ?");
            $stmt->execute([$nom]);
            if ($row = $stmt->fetch()) {
                // Retourner l'ID existant
                echo json_encode(['success' => true, 'id_salle' => $row['ID_SALLE'], 'message' => 'Salle déjà existante']);
            } else {
                // Insérer la nouvelle salle
                $stmt = $pdo->prepare("INSERT INTO SALLE (NOM_SALLE, CAPACITE) VALUES (?, 0)");
                if ($stmt->execute([$nom])) {
                    $newId = $pdo->lastInsertId();
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
            $nom = $data['nom'];
            
            // Supprimer la salle par son nom
            $stmt = $pdo->prepare("DELETE FROM SALLE WHERE NOM_SALLE = ?");
            if ($stmt->execute([$nom])) {
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
