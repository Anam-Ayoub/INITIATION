<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../config/db.php";
include "../config/functions.php";

$status = null; $msg_text = "";

if (isset($_POST['add'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $status = "error"; $msg_text = "Erreur de sécurité (jeton CSRF invalide).";
    } else {
        $jour = $_POST['jour']; $hd = $_POST['heure_debut']; $hf = $_POST['heure_fin'];
        $id_classe = !empty($_POST['new_classe']) ? getOrCreateId($conn, 'CLASSE', 'NUMERO', 'ID_CLASSE', $_POST['new_classe']) : $_POST['id_classe'];
        $id_prof   = !empty($_POST['new_prof'])   ? getOrCreateId($conn, 'PROF', 'NOM_PROF', 'ID_PROF', $_POST['new_prof']) : $_POST['id_prof'];
        $id_salle  = !empty($_POST['id_salle'])   ? $_POST['id_salle'] : null;
        $id_cours  = !empty($_POST['new_cours'])  ? getOrCreateId($conn, 'COURS', 'NOM_COURS', 'ID_COURS', $_POST['new_cours']) : $_POST['id_cours'];

        $conflits = [];
        if (existeConflit($conn, $jour, $hd, $hf, 'ID_CLASSE', $id_classe)) { $conflits[] = "Classe occupée"; }
        if (existeConflit($conn, $jour, $hd, $hf, 'ID_PROF', $id_prof))     { $conflits[] = "Professeur occupé"; }
        if (existeConflit($conn, $jour, $hd, $hf, 'ID_SALLE', $id_salle))   { $conflits[] = "Salle occupée"; }

        if (!empty($conflits)) {
            $status = "error"; $msg_text = "Conflit : " . implode(", ", $conflits);
        } else {
            $st = $conn->prepare("INSERT INTO EMPLOI_DU_TEMPS (JOUR, HEURE_DEB, HEURE_FIN, ID_CLASSE, ID_PROF, ID_SALLE, ID_COURS) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $st->bind_param("sssiiii", $jour, $hd, $hf, $id_classe, $id_prof, $id_salle, $id_cours);
            if ($st->execute()) { $status = "success"; $msg_text = "Séance enregistrée avec succès !"; }
            else { $status = "error"; $msg_text = "Erreur : " . $conn->error; }
        }
    }
}

$classes   = $conn->query("SELECT ID_CLASSE, NUMERO FROM CLASSE ORDER BY NUMERO");
$profs     = $conn->query("SELECT ID_PROF, NOM_PROF FROM PROF ORDER BY NOM_PROF");
$salles    = $conn->query("SELECT ID_SALLE, NOM_SALLE FROM SALLE ORDER BY NOM_SALLE");
$coursList = $conn->query("SELECT ID_COURS, NOM_COURS FROM COURS ORDER BY NOM_COURS");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
    <?php $current_page = 'add'; include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Planifier une séance</h2>
                <p class="subtitle">Créer un nouveau créneau dans l'emploi du temps</p>
            </div>
        </div>

        <?php if($status): ?>
            <div class="alert alert-<?= $status ?>"><?= $msg_text ?></div>
        <?php endif; ?>

        <div class="container">
            <form method="POST">
                <?php csrfField(); ?>
                <div class="form-grid">
                    <div class="input-group">
                        <label>Jour</label>
                        <select name="jour" required>
                            <option>Lundi</option><option>Mardi</option><option>Mercredi</option>
                            <option>Jeudi</option><option>Vendredi</option><option>Samedi</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Horaires</label>
                        <div style="display:flex;gap:10px;">
                            <input type="time" name="heure_debut" required style="flex:1">
                            <input type="time" name="heure_fin" required style="flex:1">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Classe</label>
                        <select name="id_classe">
                            <option value="">— Sélectionner —</option>
                            <?php while($row = $classes->fetch_assoc()): ?>
                                <option value="<?= $row['ID_CLASSE'] ?>"><?= htmlspecialchars($row['NUMERO']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">ou ajouter</div>
                        <input type="text" name="new_classe" placeholder="Ex: Classe C3">
                    </div>

                    <div class="input-group">
                        <label>Professeur</label>
                        <select name="id_prof">
                            <option value="">— Sélectionner —</option>
                            <?php while($row = $profs->fetch_assoc()): ?>
                                <option value="<?= $row['ID_PROF'] ?>"><?= htmlspecialchars($row['NOM_PROF']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">ou ajouter</div>
                        <input type="text" name="new_prof" placeholder="Ex: M. Khalid">
                    </div>

                    <div class="input-group">
                        <label>Salle</label>
                        <select name="id_salle" required>
                            <option value="">— Sélectionner —</option>
                            <?php while($row = $salles->fetch_assoc()): ?>
                                <option value="<?= $row['ID_SALLE'] ?>"><?= htmlspecialchars($row['NOM_SALLE']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">ou ajouter sur la carte</div>
                        <a href="carte.php" class="btn-map-add">🗺️ Ajouter une salle</a>
                    </div>

                    <div class="input-group">
                        <label>Cours</label>
                        <select name="id_cours">
                            <option value="">— Sélectionner —</option>
                            <?php while($row = $coursList->fetch_assoc()): ?>
                                <option value="<?= $row['ID_COURS'] ?>"><?= htmlspecialchars($row['NOM_COURS']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="or-text">ou ajouter</div>
                        <input type="text" name="new_cours" placeholder="Ex: PHP Avancé">
                    </div>

                    <button type="submit" name="add" class="btn-submit">Enregistrer la séance</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>