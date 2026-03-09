<?php
session_start();
include "../config/db.php";

// 1. Récupérer la liste des professeurs
$profs_res = $conn->query("SELECT * FROM PROF ORDER BY NOM_PROF");

$id_prof = $_GET['id_prof'] ?? null;
$emplois = [];

// 2. Si un prof est sélectionné
if ($id_prof) {
    $sql = "SELECT e.*, c.NUMERO as NOM_CLASSE, co.NOM_COURS, s.NOM_SALLE,
            TIME_FORMAT(e.HEURE_DEB, '%H:%i') as hd,
            TIME_FORMAT(e.HEURE_FIN, '%H:%i') as hf
            FROM EMPLOI_DU_TEMPS e
            JOIN CLASSE c ON e.ID_CLASSE = c.ID_CLASSE
            JOIN COURS co ON e.ID_COURS = co.ID_COURS
            JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
            WHERE e.ID_PROF = ?
            ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.HEURE_DEB";
    
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
    <title>Emploi du Temps par Professeur</title>
    <style>
        /* Même style que emploi_classe.php */
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f9; margin: 0; display: flex; }
        .sidebar { width: 260px; background: #1a1d20; color: white; height: 100vh; position: fixed; }
        .sidebar a { display: block; padding: 15px; color: #adb5bd; text-decoration: none; }
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #eee; text-align: left; }
        th { background: #28a745; color: white; }
        .filter-section { margin-bottom: 20px; background: #e8f5e9; padding: 15px; border-radius: 8px; }
        select { padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="padding:20px;"><h2>Chronos</h2></div>
        <a href="../index.php">🏠 page acceuil</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>👨‍🏫 Emploi du Temps par Professeur</h2>
            
            <div class="filter-section">
                <form method="GET">
                    <label>Choisir un Professeur :</label>
                    <select name="id_prof" onchange="this.form.submit()">
                        <option value="">-- Sélectionner --</option>
                        <?php while($p = $profs_res->fetch_assoc()): ?>
                            <option value="<?= $p['ID_PROF'] ?>" <?= $id_prof == $p['ID_PROF'] ? 'selected' : '' ?>>
                                <?= $p['NOM_PROF'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>

            <?php if ($id_prof && $emplois->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Horaire</th>
                            <th>Cours</th>
                            <th>Classe</th>
                            <th>Salle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $emplois->fetch_assoc()): ?>
                            <tr>
                                <td><b><?= $row['JOUR'] ?></b></td>
                                <td><?= $row['hd'] ?> - <?= $row['hf'] ?></td>
                                <td><?= $row['NOM_COURS'] ?></td>
                                <td><?= $row['NOM_CLASSE'] ?></td>
                                <td><?= $row['NOM_SALLE'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php elseif($id_prof): ?>
                <p>Ce professeur n'a aucune séance cette semaine.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>