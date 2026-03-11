<?php
session_start();
include "../config/db.php";

$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$jour_filtre = $_GET['jour'] ?? 'all';

$sql = "SELECT e.JOUR, TIME_FORMAT(e.HEURE_DEB, '%H:%i') as hd, TIME_FORMAT(e.HEURE_FIN, '%H:%i') as hf,
        s.NOM_SALLE, co.NOM_COURS, c.NUMERO as NOM_CLASSE, p.NOM_PROF
        FROM EMPLOI_DU_TEMPS e
        LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE LEFT JOIN COURS co ON e.ID_COURS = co.ID_COURS
        LEFT JOIN CLASSE c ON e.ID_CLASSE = c.ID_CLASSE LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF";
if ($jour_filtre !== 'all') { $sql .= " WHERE e.JOUR = ?"; }
$sql .= " ORDER BY FIELD(e.JOUR, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'), e.HEURE_DEB";

$stmt = $conn->prepare($sql);
if ($jour_filtre !== 'all') { $stmt->bind_param("s", $jour_filtre); }
$stmt->execute();
$emplois = $stmt->get_result();

$grouped = [];
while($row = $emplois->fetch_assoc()) { $grouped[$row['JOUR']][] = $row; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurité — CHRONOS</title>
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
            <a href="emploi_prof.php">👨‍🏫 Emploi par Prof</a>
            <a href="securite.php" class="active">🔐 Sécurité / Salles</a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Sécurité — Salles à ouvrir</h2>
                <p class="subtitle">Consultez les salles qui doivent être ouvertes selon le planning</p>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET">
                <label><strong>Filtrer par jour :</strong></label>
                <select name="jour" onchange="this.form.submit()" style="margin-left:10px;">
                    <option value="all" <?= $jour_filtre === 'all' ? 'selected' : '' ?>>Toute la semaine</option>
                    <?php foreach($jours as $j): ?>
                        <option value="<?= $j ?>" <?= $jour_filtre === $j ? 'selected' : '' ?>><?= $j ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if (!empty($grouped)): ?>
            <?php foreach($grouped as $day => $sessions): ?>
                <div class="day-group">
                    <div class="day-group-header">
                        <span class="day-name"><?= htmlspecialchars($day) ?></span>
                        <span class="day-count"><?= count($sessions) ?> salle<?= count($sessions)>1?'s':'' ?></span>
                    </div>
                    <div class="schedule-list">
                        <?php foreach($sessions as $s): ?>
                            <div class="session-card">
                                <div class="session-main">
                                    <span class="session-chip chip-time"><?= $s['hd'] ?> — <?= $s['hf'] ?></span>
                                    <span class="session-chip chip-room">🏢 <?= htmlspecialchars($s['NOM_SALLE']) ?></span>
                                    <span class="session-chip chip-course"><?= htmlspecialchars($s['NOM_COURS']) ?></span>
                                    <span class="session-chip chip-class">🎓 <?= htmlspecialchars($s['NOM_CLASSE']) ?></span>
                                    <span class="session-chip chip-prof">👨‍🏫 <?= htmlspecialchars($s['NOM_PROF']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">Aucune salle programmée pour <?= $jour_filtre === 'all' ? 'cette semaine' : 'le ' . htmlspecialchars($jour_filtre) ?>.</div>
        <?php endif; ?>
    </div>
</body>
</html>
