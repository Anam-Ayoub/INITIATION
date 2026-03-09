<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// 1. Connexion
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "timetable_system"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { 
    die("Connexion échouée: " . $conn->connect_error); 
}

$status = null;
$msg_text = "";

/* =============================================================
   Fonction pour récupérer l'ID ou créer un nouvel élément
   ============================================================= */
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
        // Sinon, le créer
        $stmt_ins = $conn->prepare("INSERT INTO $table ($column_name) VALUES (?)");
        $stmt_ins->bind_param("s", $value);
        $stmt_ins->execute();
        return $conn->insert_id;
    }
}

/* ==========================================
   Fonction pour vérifier les conflits (MySQL)
   ========================================== */
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

/* =========================
   Traitement du formulaire
========================= */
if (isset($_POST['add'])) {
    $jour = $_POST['jour'];
    $hd   = $_POST['heure_debut'];
    $hf   = $_POST['heure_fin'];

    // Récupérer les IDs (soit du Select, soit du texte "Nouveau")
    $id_classe = !empty($_POST['new_classe']) ? getOrCreateId($conn, 'CLASSE', 'NUMERO', 'ID_CLASSE', $_POST['new_classe']) : $_POST['id_classe'];
    $id_prof   = !empty($_POST['new_prof'])   ? getOrCreateId($conn, 'PROF', 'NOM_PROF', 'ID_PROF', $_POST['new_prof']) : $_POST['id_prof'];
    $id_salle  = !empty($_POST['new_salle'])  ? getOrCreateId($conn, 'SALLE', 'NOM_SALLE', 'ID_SALLE', $_POST['new_salle']) : $_POST['id_salle'];
    $id_cours  = !empty($_POST['new_cours'])  ? getOrCreateId($conn, 'COURS', 'NOM_COURS', 'ID_COURS', $_POST['new_cours']) : $_POST['id_cours'];

    // Vérification des conflits
    $conflits = [];
    if (existeConflit($conn, $jour, $hd, $hf, 'ID_CLASSE', $id_classe)) { $conflits[] = "⚠ Classe occupée."; }
    if (existeConflit($conn, $jour, $hd, $hf, 'ID_PROF', $id_prof)) { $conflits[] = "⚠ Professeur occupé."; }
    if (existeConflit($conn, $jour, $hd, $hf, 'ID_SALLE', $id_salle)) { $conflits[] = "⚠ Salle occupée."; }

    if (!empty($conflits)) {
        $status = "error";
        $msg_text = implode("<br>", $conflits);
    } else {
        $sql = "INSERT INTO EMPLOI_DU_TEMPS (JOUR, HEURE_DEB, HEURE_FIN, ID_CLASSE, ID_PROF, ID_SALLE, ID_COURS) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $st = $conn->prepare($sql);
        $st->bind_param("sssiiii", $jour, $hd, $hf, $id_classe, $id_prof, $id_salle, $id_cours);

        if ($st->execute()) {
            $status = "success";
            $msg_text = "✅ Séance et nouvelles données enregistrées !";
        } else {
            $status = "error";
            $msg_text = "❌ Erreur : " . $conn->error;
        }
    }
}

// Chargement des listes
$classes   = $conn->query("SELECT ID_CLASSE, NUMERO FROM CLASSE ORDER BY NUMERO");
$profs     = $conn->query("SELECT ID_PROF, NOM_PROF FROM PROF ORDER BY NOM_PROF");
$salles    = $conn->query("SELECT ID_SALLE, NOM_SALLE FROM SALLE ORDER BY NOM_SALLE");
$coursList = $conn->query("SELECT ID_COURS, NOM_COURS FROM COURS ORDER BY NOM_COURS");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planification Avancée - Chronos</title>
    <style>
        :root { --primary: #0056b3; --sidebar-bg: #1a1d20; --bg-light: #f4f7f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', sans-serif; display: flex; background: var(--bg-light); margin: 0; }
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; height: 100vh; position: fixed; }
        .main-content { flex: 1; margin-left: 260px; padding: 40px; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group { background: #f9f9f9; padding: 10px; border-radius: 8px; border: 1px solid #eee; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; font-size: 0.9rem; }
        select, input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .or-text { text-align: center; margin: 5px 0; color: #888; font-size: 0.8rem; font-style: italic; }
        .btn-submit { background: var(--primary); color: white; padding: 15px; border: none; border-radius: 8px; cursor: pointer; grid-column: span 2; font-size: 16px; margin-top: 10px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="padding:20px; text-align:center;"><h2>Chronos</h2></div>
        <a href="dashboard.php" style="color:white; display:block; padding:15px; text-decoration:none;">🏠 Tableau de Bord</a>
        <a href="add_et.php" style="color:white; display:block; padding:15px; background:var(--primary); text-decoration:none;">➕ Ajouter Séance</a>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>➕ Planifier une séance (Ajout auto)</h2>

            <?php if($status): ?>
                <div class="alert alert-<?= $status ?>"><?= $msg_text ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Jour</label>
                        <select name="jour" required>
                            <option>Lundi</option><option>Mardi</option><option>Mercredi</option>
                            <option>Jeudi</option><option>Vendredi</option><option>Samedi</option>
                        </select>
                    </div>
                    <div class="input-group" style="display:flex; gap:10px;">
                        <div style="flex:1;"><label>Début</label><input type="time" name="heure_debut" required></div>
                        <div style="flex:1;"><label>Fin</label><input type="time" name="heure_fin" required></div>
                    </div>

                    <div class="input-group">
                        <label>Classe (Choisir)</label>
                        <select name="id_classe">
                            <option value="">-- Sélectionner --</option>
                            <?php while($row = $classes->fetch_assoc()): ?>
                                <option value="<?= $row['ID_CLASSE'] ?>"><?= $row['NUMERO'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">OU ajouter une nouvelle :</div>
                        <input type="text" name="new_classe" placeholder="Ex: Classe C3">
                    </div>

                    <div class="input-group">
                        <label>Professeur (Choisir)</label>
                        <select name="id_prof">
                            <option value="">-- Sélectionner --</option>
                            <?php while($row = $profs->fetch_assoc()): ?>
                                <option value="<?= $row['ID_PROF'] ?>"><?= $row['NOM_PROF'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">OU ajouter un nouveau :</div>
                        <input type="text" name="new_prof" placeholder="Ex: M. Khalid">
                    </div>

                    <div class="input-group">
                        <label>Salle (Choisir)</label>
                        <select name="id_salle">
                            <option value="">-- Sélectionner --</option>
                            <?php while($row = $salles->fetch_assoc()): ?>
                                <option value="<?= $row['ID_SALLE'] ?>"><?= $row['NOM_SALLE'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">OU ajouter une nouvelle :</div>
                        <input type="text" name="new_salle" placeholder="Ex: Salle Informatique">
                    </div>

                    <div class="input-group">
                        <label>Cours (Choisir)</label>
                        <select name="id_cours">
                            <option value="">-- Sélectionner --</option>
                            <?php while($row = $coursList->fetch_assoc()): ?>
                                <option value="<?= $row['ID_COURS'] ?>"><?= $row['NOM_COURS'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">OU ajouter un nouveau :</div>
                        <input type="text" name="new_cours" placeholder="Ex: PHP Avancé">
                    </div>

                    <button type="submit" name="add" class="btn-submit">🚀 Valider et Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>