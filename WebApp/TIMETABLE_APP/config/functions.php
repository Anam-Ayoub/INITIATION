<?php

/* =============================================================
   Shared Functions — Chronos-SIIA
   ============================================================= */

/**
 * Get an existing ID or create a new record in a lookup table.
 * Used for auto-creating professors, classes, rooms, courses.
 */
function getOrCreateId($conn, $table, $column_name, $id_column, $value) {
    if (empty($value)) return null;

    // Check if the element already exists
    $stmt = $conn->prepare("SELECT $id_column FROM $table WHERE $column_name = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row[$id_column];
    } else {
        // Create it
        $stmt_ins = $conn->prepare("INSERT INTO $table ($column_name) VALUES (?)");
        $stmt_ins->bind_param("s", $value);
        $stmt_ins->execute();
        return $conn->insert_id;
    }
}

/**
 * Check for scheduling conflicts (for INSERT).
 * Returns true if a conflict exists.
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
 * Check for scheduling conflicts (for UPDATE).
 * Excludes the current record being updated.
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
 * Generate a CSRF token and store it in the session.
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a submitted CSRF token against the session token.
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden CSRF input field for use in forms.
 */
function csrfField() {
    $token = generateCsrfToken();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
