<?php
/* =============================================================
   Fonctions Partagées — Chronos-SIIA
   ============================================================= */

/**
 * Obtenir un ID existant ou créer un nouvel enregistrement dans une table de référence.
 * Utilisé pour la création automatique de professeurs, classes, salles, cours.
 */
function getOrCreateId($conn, $table, $column_name, $id_column, $value) {
    if (empty($value)) return null;

    // Vérifier si l'élément existe déjà
    $stmt = $conn->prepare("SELECT $id_column FROM $table WHERE $column_name = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row[$id_column];
    } else {
        // Le créer
        $stmt_ins = $conn->prepare("INSERT INTO $table ($column_name) VALUES (?)");
        $stmt_ins->bind_param("s", $value);
        $stmt_ins->execute();
        return $conn->insert_id;
    }
}

/**
 * Vérifier les conflits de planification (pour l'insertion).
 * Retourne vrai si un conflit existe.
 */
function existeConflit($conn, $jour, $hd, $hf, $colonne, $valeur) {
    if (!$valeur) return false;
    $sql = "SELECT COUNT(*) as total FROM EMPLOI_DU_TEMPS 
            WHERE JOUR = ? AND $colonne = ? 
            AND (? < HEURE_FIN AND ? > HEURE_DEB)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $jour, $valeur, $hd, $hf);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] > 0;
}

/**
 * Vérifier les conflits de planification (pour la mise à jour).
 * Exclut l'enregistrement actuel en cours de mise à jour.
 */
function existeConflitUpdate($conn, $jour, $hd, $hf, $colonne, $valeur, $id_actuel) {
    if (!$valeur) return false;
    
    $sql = "SELECT COUNT(*) as total FROM EMPLOI_DU_TEMPS 
            WHERE JOUR = ? 
            AND $colonne = ? 
            AND ID_EMPLOI != ?
            AND (? < HEURE_FIN AND ? > HEURE_DEB)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiss", $jour, $valeur, $id_actuel, $hd, $hf);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] > 0;
}

/**
 * Génère de manière sécurisée les jetons CSRF et le stocke dans la session.
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un jeton CSRF soumis par rapport au jeton de la session.
 */
function validateCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Alias for Users_interface backward compatibility
function verifyCsrfToken($token) {
    return validateCsrfToken($token);
}

/**
 * Affiche un champ d'entrée CSRF caché pour une utilisation dans les formulaires.
 */
function csrfField() {
    $token = generateCsrfToken();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Vérifie si un étudiant est connecté, redirige vers l'index (connexion) sinon.
 */
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['STUDENT_ID'])) {
        header("Location: index.php");
        exit;
    }
}

/**
 * Mappe une chaîne de jour en un nombre pour le tri ou la mise en page.
 */
function getDayNumber($dayStr) {
    $days = [
        'Lundi' => 1,
        'Mardi' => 2,
        'Mercredi' => 3,
        'Jeudi' => 4,
        'Vendredi' => 5,
        'Samedi' => 6
    ];
    return $days[$dayStr] ?? 7;
}

?>
