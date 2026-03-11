<?php
session_start();
include "../config/db.php";

$classes_res = $conn->query("SELECT * FROM CLASSE ORDER BY NUMERO");
$id_classe = $_GET['id_classe'] ?? null;
$emplois = null;

if ($id_classe) {
    $sql = "SELECT e.*, p.NOM_PROF, co.NOM_COURS, s.NOM_SALLE,
            TIME_FORMAT(e.HEURE_DEB, '%H:%i') as hd, TIME_FORMAT(e.HEURE_FIN, '%H:%i') as hf
            FROM EMPLOI_DU_TEMPS e
            JOIN PROF p ON e.ID_PROF = p.ID_PROF JOIN COURS co ON e.ID_COURS = co.ID_COURS
            JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
            WHERE e.ID_CLASSE = ?
            ORDER BY FIELD(e.JOUR, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'), e.HEURE_DEB";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_classe);
    $stmt->execute();
    $emplois = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi par Classe — CHRONOS</title>
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
            <a href="emploi_classe.php" class="active">📅 Emploi par Classe</a>
            <a href="emploi_prof.php">👨‍🏫 Emploi par Prof</a>
            <a href="securite.php">🔐 Sécurité / Salles</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Emploi du Temps par Classe</h2>
                <p class="subtitle">Sélectionnez une classe pour afficher son planning</p>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET">
                <label><strong>Classe :</strong></label>
                <select name="id_classe" onchange="this.form.submit()" style="margin-left:10px;">
                    <option value="">— Sélectionner —</option>
                    <?php while($c = $classes_res->fetch_assoc()): ?>
                        <option value="<?= $c['ID_CLASSE'] ?>" <?= $id_classe == $c['ID_CLASSE'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['NUMERO']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <?php if ($id_classe && $emplois && $emplois->num_rows > 0): ?>
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
                                    <span class="session-chip chip-prof">👨‍🏫 <?= htmlspecialchars($s['NOM_PROF']) ?></span>
                                    <span class="session-chip chip-room">🏢 <?= htmlspecialchars($s['NOM_SALLE']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif($id_classe): ?>
            <div class="empty-state">Aucune séance programmée pour cette classe.</div>
        <?php endif; ?>
    </div>
</body>
</html>