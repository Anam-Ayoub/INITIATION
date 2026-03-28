<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include __DIR__ . "/../../config/db.php";
include __DIR__ . "/../../config/functions.php";

$id_et = $_GET['id_et'] ?? null;
if (!$id_et) die("Séance invalide");

$stmt_info = $pdo->prepare("SELECT ID_EMPLOI, JOUR, TIME_FORMAT(HEURE_DEB, '%H:%i') AS hd, TIME_FORMAT(HEURE_FIN, '%H:%i') AS hf, ID_CLASSE, ID_PROF, ID_SALLE, ID_COURS FROM EMPLOI_DU_TEMPS WHERE ID_EMPLOI = ?");
$stmt_info->execute([$id_et]);
$row = $stmt_info->fetch();
if (!$row) die("Séance introuvable");

$classes_list = $pdo->query("SELECT * FROM CLASSE ORDER BY NUMERO")->fetchAll();
$profs_list   = $pdo->query("SELECT * FROM PROF ORDER BY NOM_PROF")->fetchAll();
$salles_list  = $pdo->query("SELECT * FROM SALLE ORDER BY NOM_SALLE")->fetchAll();
$cours_list   = $pdo->query("SELECT * FROM COURS ORDER BY NOM_COURS")->fetchAll();

$error = "";

if (isset($_POST['update'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Erreur de sécurité (jeton CSRF invalide).";
    } else {
        $jour = $_POST['jour']; $hd = $_POST['hd']; $hf = $_POST['hf'];
        $id_classe = !empty($_POST['new_classe']) ? getOrCreateId($pdo, 'CLASSE', 'NUMERO', 'ID_CLASSE', $_POST['new_classe']) : $_POST['classe'];
        $id_prof   = !empty($_POST['new_prof'])   ? getOrCreateId($pdo, 'PROF', 'NOM_PROF', 'ID_PROF', $_POST['new_prof']) : $_POST['prof'];
        $id_salle  = !empty($_POST['salle'])      ? $_POST['salle'] : null;
        $id_cours  = !empty($_POST['new_cours'])  ? getOrCreateId($pdo, 'COURS', 'NOM_COURS', 'ID_COURS', $_POST['new_cours']) : $_POST['cours'];

        if (existeConflitUpdate($pdo, $jour, $hd, $hf, 'ID_CLASSE', $id_classe, $id_et)) { $error = "Conflit : Classe occupée."; }
        elseif (existeConflitUpdate($pdo, $jour, $hd, $hf, 'ID_PROF', $id_prof, $id_et)) { $error = "Conflit : Professeur occupé."; }
        elseif (existeConflitUpdate($pdo, $jour, $hd, $hf, 'ID_SALLE', $id_salle, $id_et)) { $error = "Conflit : Salle occupée."; }
        else {
            try {
                $up = $pdo->prepare("UPDATE EMPLOI_DU_TEMPS SET JOUR=?, HEURE_DEB=?, HEURE_FIN=?, ID_CLASSE=?, ID_PROF=?, ID_SALLE=?, ID_COURS=? WHERE ID_EMPLOI=?");
                if($up->execute([$jour, $hd, $hf, $id_classe, $id_prof, $id_salle, $id_cours, $id_et])) { 
                    header("Location: list_et.php"); exit; 
                }
            } catch (PDOException $e) {
                $error = "Erreur SQL : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
    <?php $current_page = 'list'; include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Modifier la séance #<?= htmlspecialchars($id_et) ?></h2>
                <p class="subtitle">Mettre à jour les informations du créneau</p>
            </div>
            <a href="list_et.php" class="admin-badge" style="text-decoration:none;">← Retour</a>
        </div>

        <?php if($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <div class="container">
            <form method="POST">
                <?php csrfField(); ?>
                <div class="form-grid">
                    <div class="input-group">
                        <label>Jour</label>
                        <select name="jour">
                            <?php foreach(["Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"] as $j): ?>
                                <option value="<?= $j ?>" <?= $row['JOUR']==$j?'selected':'' ?>><?= $j ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Horaires</label>
                        <div style="display:flex;gap:10px;">
                            <input type="time" name="hd" value="<?= $row['hd'] ?>" required style="flex:1">
                            <input type="time" name="hf" value="<?= $row['hf'] ?>" required style="flex:1">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Classe</label>
                        <select name="classe">
                            <?php foreach($classes_list as $c): ?>
                                <option value="<?= $c['ID_CLASSE'] ?>" <?= $c['ID_CLASSE']==$row['ID_CLASSE']?'selected':'' ?>><?= htmlspecialchars($c['NUMERO']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="or-text">ou nouvelle</div>
                        <input type="text" name="new_classe" placeholder="Ex: C4">
                    </div>

                    <div class="input-group">
                        <label>Professeur</label>
                        <select name="prof">
                            <?php foreach($profs_list as $p): ?>
                                <option value="<?= $p['ID_PROF'] ?>" <?= $p['ID_PROF']==$row['ID_PROF']?'selected':'' ?>><?= htmlspecialchars($p['NOM_PROF']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="or-text">ou nouveau</div>
                        <input type="text" name="new_prof" placeholder="Ex: Mme. Amina">
                    </div>

                    <div class="input-group">
                        <label>Salle</label>
                        <select name="salle" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach($salles_list as $s): ?>
                                <option value="<?= $s['ID_SALLE'] ?>" <?= $s['ID_SALLE']==$row['ID_SALLE']?'selected':'' ?>><?= htmlspecialchars($s['NOM_SALLE']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="or-text">ou ajouter sur la carte</div>
                        <a href="carte.php" class="btn-map-add">🗺️ Ajouter une salle</a>
                    </div>

                    <div class="input-group">
                        <label>Cours</label>
                        <select name="cours">
                            <?php foreach($cours_list as $co): ?>
                                <option value="<?= $co['ID_COURS'] ?>" <?= $co['ID_COURS']==$row['ID_COURS']?'selected':'' ?>><?= htmlspecialchars($co['NOM_COURS']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="or-text">ou nouveau</div>
                        <input type="text" name="new_cours" placeholder="Ex: Big Data">
                    </div>

                    <button type="submit" name="update" class="btn-submit">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>