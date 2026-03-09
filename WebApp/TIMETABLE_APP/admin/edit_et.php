<?php
session_start();
if (!isset($_SESSION['admin'])) {
   header("Location: login.php");
   exit;
}

// 1. Connexion MySQL
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "timetable_system"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { 
    die("Connexion échouée: " . $conn->connect_error); 
}

$id_et = $_GET['id_et'] ?? null;
if (!$id_et) die("Séance invalide");

/* =============================================================
   Fonction pour récupérer l'ID ou créer un nouvel élément (MySQL)
   ============================================================= */
function getOrCreateId($conn, $table, $column_name, $id_column, $value) {
    if (empty($value)) return null;
    $stmt = $conn->prepare("SELECT $id_column FROM $table WHERE $column_name = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row[$id_column];
    } else {
        $stmt_ins = $conn->prepare("INSERT INTO $table ($column_name) VALUES (?)");
        $stmt_ins->bind_param("s", $value);
        $stmt_ins->execute();
        return $conn->insert_id;
    }
}

/* =============================================================
   CORRECTION : Fonction pour vérifier les conflits
   ============================================================= */
function existeConflitUpdate($conn, $jour, $hd, $hf, $colonne, $valeur, $id_actuel) {
    if (!$valeur) return false;
    
    // Hna l-moushkil: l-query fiha 5 dyal les "?"
    $sql = "SELECT COUNT(*) as total FROM EMPLOI_DU_TEMPS 
            WHERE JOUR = ? 
            AND $colonne = ? 
            AND ID_EMPLOI != ?
            AND (? < HEURE_FIN AND ? > HEURE_DEB)";
            
    $stmt = $conn->prepare($sql);
    
    // "s" pour jour, "i" pour valeur (id), "i" pour id_actuel, "s" pour hd, "s" pour hf
    // Total: siiss (5 variables)
    $stmt->bind_param("siiss", $jour, $valeur, $id_actuel, $hd, $hf);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] > 0;
}

/* =========================
   Infos séance actuelle
========================= */
$stmt_info = $conn->prepare("
   SELECT ID_EMPLOI, JOUR, 
          TIME_FORMAT(HEURE_DEB, '%H:%i') AS hd, 
          TIME_FORMAT(HEURE_FIN, '%H:%i') AS hf, 
          ID_CLASSE, ID_PROF, ID_SALLE, ID_COURS 
   FROM EMPLOI_DU_TEMPS WHERE ID_EMPLOI = ?
");
$stmt_info->bind_param("i", $id_et);
$stmt_info->execute();
$row = $stmt_info->get_result()->fetch_assoc();
if (!$row) die("Séance introuvable");

/* =========================
   Chargement des listes
========================= */
$classes_list = $conn->query("SELECT * FROM CLASSE ORDER BY NUMERO");
$profs_list   = $conn->query("SELECT * FROM PROF ORDER BY NOM_PROF");
$salles_list  = $conn->query("SELECT * FROM SALLE ORDER BY NOM_SALLE");
$cours_list   = $conn->query("SELECT * FROM COURS ORDER BY NOM_COURS");

$error = "";

/* =========================
   Traitement de l'Update
========================= */
if (isset($_POST['update'])) {
    $jour = $_POST['jour'];
    $hd   = $_POST['hd'];
    $hf   = $_POST['hf'];

    // Création dynamique si nécessaire
    $id_classe = !empty($_POST['new_classe']) ? getOrCreateId($conn, 'CLASSE', 'NUMERO', 'ID_CLASSE', $_POST['new_classe']) : $_POST['classe'];
    $id_prof   = !empty($_POST['new_prof'])   ? getOrCreateId($conn, 'PROF', 'NOM_PROF', 'ID_PROF', $_POST['new_prof']) : $_POST['prof'];
    $id_salle  = !empty($_POST['new_salle'])  ? getOrCreateId($conn, 'SALLE', 'NOM_SALLE', 'ID_SALLE', $_POST['new_salle']) : $_POST['salle'];
    $id_cours  = !empty($_POST['new_cours'])  ? getOrCreateId($conn, 'COURS', 'NOM_COURS', 'ID_COURS', $_POST['new_cours']) : $_POST['cours'];

    // Vérification des conflits
    if (existeConflitUpdate($conn, $jour, $hd, $hf, 'ID_CLASSE', $id_classe, $id_et)) {
        $error = "❌ Conflit : La classe est déjà occupée.";
    } elseif (existeConflitUpdate($conn, $jour, $hd, $hf, 'ID_PROF', $id_prof, $id_et)) {
        $error = "❌ Conflit : Le professeur est déjà occupé.";
    } elseif (existeConflitUpdate($conn, $jour, $hd, $hf, 'ID_SALLE', $id_salle, $id_et)) {
        $error = "❌ Conflit : La salle est déjà occupée.";
    } else {
        // Update final
        $up = $conn->prepare("
            UPDATE EMPLOI_DU_TEMPS SET 
                JOUR = ?, HEURE_DEB = ?, HEURE_FIN = ?, 
                ID_CLASSE = ?, ID_PROF = ?, ID_SALLE = ?, ID_COURS = ?
            WHERE ID_EMPLOI = ?
        ");
        $up->bind_param("sssiiiii", $jour, $hd, $hf, $id_classe, $id_prof, $id_salle, $id_cours, $id_et);
        
        if($up->execute()) {
            echo "<script>window.location.href='list_et.php?status=success';</script>";
            exit;
        } else {
            $error = "Erreur SQL : " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <title>Modifier Séance – Chronos</title>
   <style>
       :root { --primary: #0056b3; --sidebar-bg: #1a1d20; --bg-light: #f4f7f9; --white: #ffffff; }
       body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh;}
       .sidebar { width: 260px; background-color: var(--sidebar-bg); color: white; height: 100vh; position: fixed; }
       .sidebar-menu a { display: block; padding: 15px 25px; color: #adb5bd; text-decoration: none; }
       .sidebar-menu a:hover { background: #2c3136; color: white; }
       .main-content { flex: 1; margin-left: 260px; padding: 40px; }
       .form-card { background: var(--white); padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 850px; }
       .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
       .form-group { display: flex; flex-direction: column; }
       label { font-weight: 600; margin-bottom: 5px; color: #555; }
       select, input { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
       input[type="text"] { background-color: #f9f9ff; border-left: 4px solid var(--primary); }
       .btn-save { background: var(--primary); color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; }
       .error-box { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #dc3545; }
   </style>
</head>
<body>
   <div class="sidebar">
       <div style="padding:20px; text-align:center; border-bottom:1px solid #333;"><h2>CHRONOS-SIIA</h2></div>
       <div class="sidebar-menu">
           <a href="dashboard.php">🏠 Tableau de Bord</a>
           <a href="add_et.php">➕ Ajouter une séance</a>
           <a href="list_et.php">📝 Modifier / Lister</a>
           <a href="logout.php">🚪 Déconnexion</a>
       </div>
   </div>

   <div class="main-content">
       <div class="form-card">
           <h2>✏️ Modifier la Séance #<?= htmlspecialchars($id_et) ?></h2>
           <?php if($error): ?><div class="error-box"><?= $error ?></div><?php endif; ?>

           <form method="POST">
               <div class="form-row">
                   <div class="form-group">
                       <label>Jour</label>
                       <select name="jour">
                           <?php foreach(["Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"] as $j): ?>
                               <option value="<?= $j ?>" <?= $row['JOUR']==$j?'selected':'' ?>><?= $j ?></option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   <div class="form-group" style="display:flex; gap:10px; flex-direction:row">
                       <div style="flex:1"><label>Début</label><input type="time" name="hd" value="<?= $row['hd'] ?>" required></div>
                       <div style="flex:1"><label>Fin</label><input type="time" name="hf" value="<?= $row['hf'] ?>" required></div>
                   </div>
               </div>

               <div class="form-row">
                   <div class="form-group">
                       <label>Classe actuelle</label>
                       <select name="classe">
                           <?php while($c=$classes_list->fetch_assoc()): ?>
                               <option value="<?= $c['ID_CLASSE'] ?>" <?= $c['ID_CLASSE']==$row['ID_CLASSE']?'selected':'' ?>><?= $c['NUMERO'] ?></option>
                           <?php endwhile; ?>
                       </select>
                   </div>
                   <div class="form-group"><label>Nouvelle classe ?</label><input type="text" name="new_classe" placeholder="Ex: C4"></div>
               </div>

               <div class="form-row">
                   <div class="form-group">
                       <label>Professeur actuel</label>
                       <select name="prof">
                           <?php while($p=$profs_list->fetch_assoc()): ?>
                               <option value="<?= $p['ID_PROF'] ?>" <?= $p['ID_PROF']==$row['ID_PROF']?'selected':'' ?>><?= $p['NOM_PROF'] ?></option>
                           <?php endwhile; ?>
                       </select>
                   </div>
                   <div class="form-group"><label>Nouveau prof ?</label><input type="text" name="new_prof" placeholder="Ex: Mme. Amina"></div>
               </div>

               <div class="form-row">
                   <div class="form-group">
                       <label>Salle actuelle</label>
                       <select name="salle">
                           <?php while($s=$salles_list->fetch_assoc()): ?>
                               <option value="<?= $s['ID_SALLE'] ?>" <?= $s['ID_SALLE']==$row['ID_SALLE']?'selected':'' ?>><?= $s['NOM_SALLE'] ?></option>
                           <?php endwhile; ?>
                       </select>
                   </div>
                   <div class="form-group"><label>Nouvelle salle ?</label><input type="text" name="new_salle" placeholder="Ex: Salle 15"></div>
               </div>

               <div class="form-row">
                   <div class="form-group">
                       <label>Cours actuel</label>
                       <select name="cours">
                           <?php while($co=$cours_list->fetch_assoc()): ?>
                               <option value="<?= $co['ID_COURS'] ?>" <?= $co['ID_COURS']==$row['ID_COURS']?'selected':'' ?>><?= $co['NOM_COURS'] ?></option>
                           <?php endwhile; ?>
                       </select>
                   </div>
                   <div class="form-group"><label>Nouveau cours ?</label><input type="text" name="new_cours" placeholder="Ex: Big Data"></div>
               </div>

               <div style="margin-top:20px;">
                   <button type="submit" name="update" class="btn-save">💾 Enregistrer les modifications</button>
                   <a href="list_et.php" style="margin-left:15px; text-decoration:none; color:#666;">Annuler</a>
               </div>
           </form>
       </div>
   </div>
</body>
</html>