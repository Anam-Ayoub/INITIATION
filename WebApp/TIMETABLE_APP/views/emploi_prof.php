<?php
session_start();
include "../config/db.php";

$profs_res = $conn->query("SELECT * FROM PROF ORDER BY NOM_PROF");
$id_prof = $_GET['id_prof'] ?? null;
$emplois = null;

if ($id_prof) {
    $sql = "SELECT e.*, c.NUMERO as NOM_CLASSE, co.NOM_COURS, s.NOM_SALLE,
            TIME_FORMAT(e.HEURE_DEB, '%H:%i') as hd, TIME_FORMAT(e.HEURE_FIN, '%H:%i') as hf
            FROM EMPLOI_DU_TEMPS e
            JOIN CLASSE c ON e.ID_CLASSE = c.ID_CLASSE JOIN COURS co ON e.ID_COURS = co.ID_COURS
            JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
            WHERE e.ID_PROF = ?
            ORDER BY FIELD(e.JOUR, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'), e.HEURE_DEB";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_prof);
    $stmt->execute();
    $emplois = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi par Professeur — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="brand">CHRONOS</div>
            <div class="brand-sub">Gestion du temps</div>
        </div>
        <div class="sidebar-menu">
            <a href="../index.php">🏠 Page d'accueil</a>
            <a href="emploi_classe.php">📅 Emploi par Classe</a>
            <a href="emploi_prof.php" class="active">👨‍🏫 Emploi par Prof</a>
            <a href="securite.php">🔐 Sécurité / Salles</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Emploi du Temps par Professeur</h2>
                <p class="subtitle">Sélectionnez un professeur pour afficher son planning</p>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET">
                <label><strong>Professeur :</strong></label>
                <select name="id_prof" onchange="this.form.submit()" style="margin-left:10px;">
                    <option value="">— Sélectionner —</option>
                    <?php while($p = $profs_res->fetch_assoc()): ?>
                        <option value="<?= $p['ID_PROF'] ?>" <?= $id_prof == $p['ID_PROF'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['NOM_PROF']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <?php if ($id_prof && $emplois && $emplois->num_rows > 0): ?>
            <?php
            $grouped = [];
            while($row = $emplois->fetch_assoc()) { $grouped[$row['JOUR']][] = $row; }
            ?>
            <?php foreach($grouped as $day => $sessions): ?>
                <div class="day-group">
                    <div class="day-group-header">
                        <span class="day-name"><?= htmlspecialchars($day) ?></span>
                        <span class="day-count"><?= count($sessions) ?> séance<?= count($sessions)>1?'s':'' ?></span>
                    </div>
                    <div class="schedule-list">
                        <?php foreach($sessions as $s): ?>
                            <div class="session-card">
                                <div class="session-main">
                                    <span class="session-chip chip-time"><?= $s['hd'] ?> — <?= $s['hf'] ?></span>
                                    <span class="session-chip chip-course"><?= htmlspecialchars($s['NOM_COURS']) ?></span>
                                    <span class="session-chip chip-class">🎓 <?= htmlspecialchars($s['NOM_CLASSE']) ?></span>
                                    <span class="session-chip chip-room">🏢 <?= htmlspecialchars($s['NOM_SALLE']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif($id_prof): ?>
            <div class="empty-state">Ce professeur n'a aucune séance cette semaine.</div>
        <?php endif; ?>
    </div>
</body>
</html>