<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/functions.php';

header('Content-Type: application/json');

// Check if user is logged in as admin
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_sessions':
        $id_classe = $_GET['id_classe'] ?? 0;
        if (!$id_classe) {
            echo json_encode(['success' => false, 'message' => 'Classe non spécifiée']);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT e.ID_EMPLOI, e.JOUR, 
                   TIME_FORMAT(e.HEURE_DEB, '%H:%i') as hd, 
                   TIME_FORMAT(e.HEURE_FIN, '%H:%i') as hf,
                   e.ID_PROF, p.NOM_PROF,
                   e.ID_COURS, co.NOM_COURS,
                   e.ID_SALLE, s.NOM_SALLE
            FROM EMPLOI_DU_TEMPS e
            LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF
            LEFT JOIN COURS co ON e.ID_COURS = co.ID_COURS
            LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
            WHERE e.ID_CLASSE = ?
        ");
        $stmt->execute([$id_classe]);
        $sessions = $stmt->fetchAll();

        echo json_encode(['success' => true, 'sessions' => $sessions]);
        break;

    case 'save_session':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            exit;
        }

        $id_emploi = $data['id_emploi'] ?? null;
        $id_classe = $data['id_classe'];
        $jour = $data['jour'];
        $hd = $data['hd'];
        $hf = $data['hf'];
        $id_prof = $data['id_prof'];
        $id_cours = $data['id_cours'];
        $id_salle = $data['id_salle'];

        // Conflict checking
        if ($id_emploi) {
            if (existeConflitUpdate($pdo, $jour, $hd, $hf, 'ID_CLASSE', $id_classe, $id_emploi)) {
                echo json_encode(['success' => false, 'message' => 'Conflit: Classe occupée']); exit;
            }
            if (existeConflitUpdate($pdo, $jour, $hd, $hf, 'ID_PROF', $id_prof, $id_emploi)) {
                echo json_encode(['success' => false, 'message' => 'Conflit: Professeur occupé']); exit;
            }
            if (existeConflitUpdate($pdo, $jour, $hd, $hf, 'ID_SALLE', $id_salle, $id_emploi)) {
                echo json_encode(['success' => false, 'message' => 'Conflit: Salle occupée']); exit;
            }

            // Update
            $sql = "UPDATE EMPLOI_DU_TEMPS SET JOUR=?, HEURE_DEB=?, HEURE_FIN=?, ID_CLASSE=?, ID_PROF=?, ID_SALLE=?, ID_COURS=? WHERE ID_EMPLOI=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$jour, $hd, $hf, $id_classe, $id_prof, $id_salle, $id_cours, $id_emploi])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } else {
            if (existeConflit($pdo, $jour, $hd, $hf, 'ID_CLASSE', $id_classe)) {
                echo json_encode(['success' => false, 'message' => 'Conflit: Classe occupée']); exit;
            }
            if (existeConflit($pdo, $jour, $hd, $hf, 'ID_PROF', $id_prof)) {
                echo json_encode(['success' => false, 'message' => 'Conflit: Professeur occupé']); exit;
            }
            if (existeConflit($pdo, $jour, $hd, $hf, 'ID_SALLE', $id_salle)) {
                echo json_encode(['success' => false, 'message' => 'Conflit: Salle occupée']); exit;
            }

            // Insert
            $sql = "INSERT INTO EMPLOI_DU_TEMPS (JOUR, HEURE_DEB, HEURE_FIN, ID_CLASSE, ID_PROF, ID_SALLE, ID_COURS) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$jour, $hd, $hf, $id_classe, $id_prof, $id_salle, $id_cours])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion']);
            }
        }
        break;

    case 'delete_session':
        $data = json_decode(file_get_contents('php://input'), true);
        $id_emploi = $data['id_emploi'] ?? null;
        if (!$id_emploi) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM EMPLOI_DU_TEMPS WHERE ID_EMPLOI = ?");
        if ($stmt->execute([$id_emploi])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action inconnue']);
        break;
}
