<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include "../config/db.php";

$sql_text = "
   SELECT e.ID_EMPLOI, e.JOUR, TIME_FORMAT(e.HEURE_DEB, '%H:%i') AS hd, TIME_FORMAT(e.HEURE_FIN, '%H:%i') AS hf,
   c.NUMERO AS NOM_CLASSE, p.NOM_PROF, s.NOM_SALLE AS NUMERO_SALLE, co.NOM_COURS
   FROM EMPLOI_DU_TEMPS e
   LEFT JOIN CLASSE c ON e.ID_CLASSE = c.ID_CLASSE
   LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF
   LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
   LEFT JOIN COURS co ON e.ID_COURS = co.ID_COURS
   ORDER BY CASE
       WHEN e.JOUR='Lundi' THEN 1 WHEN e.JOUR='Mardi' THEN 2 WHEN e.JOUR='Mercredi' THEN 3
       WHEN e.JOUR='Jeudi' THEN 4 WHEN e.JOUR='Vendredi' THEN 5 WHEN e.JOUR='Samedi' THEN 6
   END, e.HEURE_DEB";
$result = $conn->query($sql_text);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des séances — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
    <?php $current_page = 'list'; include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Séances planifiées</h2>
                <p class="subtitle"><?= $result->num_rows ?> séance<?= $result->num_rows > 1 ? 's' : '' ?> trouvée<?= $result->num_rows > 1 ? 's' : '' ?></p>
            </div>
            <a href="add_et.php" class="add-quick-btn">+ Nouvelle</a>
        </div>

        <div class="schedule-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="session-card">
                        <div class="session-main">
                            <span class="session-chip chip-day"><?= htmlspecialchars($row['JOUR']) ?></span>
                            <span class="session-chip chip-time"><?= $row['hd'] ?> — <?= $row['hf'] ?></span>
                            <span class="session-chip chip-course"><?= htmlspecialchars($row['NOM_COURS']) ?></span>
                            <span class="session-chip chip-prof">👨‍🏫 <?= htmlspecialchars($row['NOM_PROF']) ?></span>
                            <span class="session-chip chip-class">🎓 <?= htmlspecialchars($row['NOM_CLASSE']) ?></span>
                            <span class="session-chip chip-room">🏢 <?= htmlspecialchars($row['NUMERO_SALLE']) ?></span>
                        </div>
                        <a class="edit-btn" href="edit_et.php?id_et=<?= $row['ID_EMPLOI'] ?>">Modifier</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">Aucune séance planifiée.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>