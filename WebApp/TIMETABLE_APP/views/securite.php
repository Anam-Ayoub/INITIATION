<?php
session_start();
include "../config/db.php";

// List of days for the dropdown
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

$jour_filtre = $_GET['jour'] ?? 'all';
$emplois = [];

// Build query based on filter
$sql = "SELECT e.JOUR, 
        TIME_FORMAT(e.HEURE_DEB, '%H:%i') as hd,
        TIME_FORMAT(e.HEURE_FIN, '%H:%i') as hf,
        s.NOM_SALLE, co.NOM_COURS, c.NUMERO as NOM_CLASSE, p.NOM_PROF
        FROM EMPLOI_DU_TEMPS e
        JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
        JOIN COURS co ON e.ID_COURS = co.ID_COURS
        JOIN CLASSE c ON e.ID_CLASSE = c.ID_CLASSE
        JOIN PROF p ON e.ID_PROF = p.ID_PROF";

if ($jour_filtre !== 'all') {
    $sql .= " WHERE e.JOUR = ?";
}

$sql .= " ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.HEURE_DEB";

$stmt = $conn->prepare($sql);

if ($jour_filtre !== 'all') {
    $stmt->bind_param("s", $jour_filtre);
}

$stmt->execute();
$emplois = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sécurité – Salles à ouvrir</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f9; margin: 0; display: flex; }
        .sidebar { width: 260px; background: #1a1d20; color: white; height: 100vh; position: fixed; }
        .sidebar a { display: block; padding: 15px; color: #adb5bd; text-decoration: none; }
        .sidebar a:hover { background: #2c3035; color: #fff; }
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #eee; text-align: left; }
        th { background: #e67e22; color: white; }
        .filter-section { margin-bottom: 20px; background: #fef5e7; padding: 15px; border-radius: 8px; display: flex; align-items: center; gap: 12px; }
        select { padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 0.95rem; }
        .day-badge { display: inline-block; background: #e67e22; color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; }
        .info-box { background: #fef9e7; border-left: 4px solid #e67e22; padding: 12px 16px; border-radius: 4px; margin-bottom: 15px; color: #7d6608; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="padding:20px;"><h2>Chronos</h2></div>
        <a href="../index.php">🏠 Page accueil</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>🔐 Sécurité – Salles à ouvrir</h2>

            <div class="info-box">
                📋 Cette page affiche les salles qui doivent être ouvertes selon le planning. Sélectionnez un jour ou consultez toute la semaine.
            </div>

            <div class="filter-section">
                <form method="GET">
                    <label><b>Filtrer par jour :</b></label>
                    <select name="jour" onchange="this.form.submit()">
                        <option value="all" <?= $jour_filtre === 'all' ? 'selected' : '' ?>>📅 Toute la semaine</option>
                        <?php foreach($jours as $j): ?>
                            <option value="<?= $j ?>" <?= $jour_filtre === $j ? 'selected' : '' ?>><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if ($emplois->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Horaire</th>
                            <th>Salle</th>
                            <th>Cours</th>
                            <th>Classe</th>
                            <th>Professeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $current_day = '';
                        while($row = $emplois->fetch_assoc()): 
                            $new_day = ($row['JOUR'] !== $current_day);
                            $current_day = $row['JOUR'];
                        ?>
                            <?php if ($new_day && $jour_filtre === 'all'): ?>
                                <tr>
                                    <td colspan="6" style="background:#fdf2e9; padding:8px 12px; border:none;">
                                        <span class="day-badge"><?= $row['JOUR'] ?></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><b><?= $row['JOUR'] ?></b></td>
                                <td><?= $row['hd'] ?> - <?= $row['hf'] ?></td>
                                <td><b>Salle <?= $row['NOM_SALLE'] ?></b></td>
                                <td><?= $row['NOM_COURS'] ?></td>
                                <td><?= $row['NOM_CLASSE'] ?></td>
                                <td><?= $row['NOM_PROF'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888; font-style:italic;">Aucune salle programmée pour <?= $jour_filtre === 'all' ? 'cette semaine' : 'le ' . $jour_filtre ?>.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
