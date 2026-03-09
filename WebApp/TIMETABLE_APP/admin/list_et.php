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
$dbname = "timetable_system"; // T-akked mn s-miya li drti f phpMyAdmin

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier la connexion
if ($conn->connect_error) { 
    die("Connexion échouée: " . $conn->connect_error); 
}

/* =========================
   Récupérer toutes les séances avec tri logique des jours
========================= */
// MySQLi query
$sql_text = "
   SELECT
       e.ID_EMPLOI,
       e.JOUR,
       TIME_FORMAT(e.HEURE_DEB, '%H:%i') AS hd,
       TIME_FORMAT(e.HEURE_FIN, '%H:%i') AS hf,
       c.NUMERO AS NOM_CLASSE,
       p.NOM_PROF,
       s.NOM_SALLE AS NUMERO_SALLE,
       co.NOM_COURS
   FROM EMPLOI_DU_TEMPS e
   JOIN CLASSE c ON e.ID_CLASSE = c.ID_CLASSE
   JOIN PROF p ON e.ID_PROF = p.ID_PROF
   JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
   JOIN COURS co ON e.ID_COURS = co.ID_COURS
   ORDER BY
       CASE
           WHEN e.JOUR = 'Lundi' THEN 1
           WHEN e.JOUR = 'Mardi' THEN 2
           WHEN e.JOUR = 'Mercredi' THEN 3
           WHEN e.JOUR = 'Jeudi' THEN 4
           WHEN e.JOUR = 'Vendredi' THEN 5
           WHEN e.JOUR = 'Samedi' THEN 6
       END, e.HEURE_DEB
";

$result = $conn->query($sql_text);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des séances – Chronos-SIIA</title>
    <style>
        :root {
            --primary: #0056b3;
            --sidebar-bg: #1a1d20;
            --bg-light: #f4f7f9;
            --white: #ffffff;
            --text-dark: #333;
            --accent-blue: #e7f1ff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 0;
            display: flex;
            background-color: var(--bg-light);
            min-height: 100vh;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #333; }
        .sidebar-header p { font-weight: bold; color: var(--primary); margin-top: 10px; }
        
        .sidebar-menu { flex: 1; padding: 20px 0; }
        .sidebar-menu a {
            display: block; padding: 12px 25px; color: #adb5bd; text-decoration: none; transition: 0.3s;
        }
        .sidebar-menu a:hover { background: #2c3136; color: white; }
        .active { color: white !important; background: var(--primary); }

        /* --- MAIN CONTENT --- */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 40px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h2 { color: var(--text-dark); margin: 0; font-size: 1.8rem; }

        .table-container {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            background-color: #f8f9fa;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 18px 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.95rem;
            color: #444;
        }

        tr:hover { background-color: var(--accent-blue); }

        .time-tag {
            background: #343a40;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-family: monospace;
        }

        .edit-btn {
            padding: 6px 14px;
            color: var(--primary);
            border: 1px solid var(--primary);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: 0.3s;
        }

        .edit-btn:hover { background-color: var(--primary); color: white; }

        .add-quick-btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

   <div class="sidebar">
       <div class="sidebar-header">
           <p>CHRONOS-SIIA</p>
       </div>
       <div class="sidebar-menu">
           <a href="dashboard.php">🏠 Tableau de Bord</a>
           <a href="add_et.php">➕ Ajouter une séance</a>
           <a href="list_et.php" class="active">📝 Modifier / Lister</a>
           <a href="logout.php">🚪 Déconnexion</a>
       </div>
   </div>

   <div class="main-content">
       <div class="header-section">
           <h2>📋 Liste des Séances Planifiées</h2>
           <a href="add_et.php" class="add-quick-btn">+ Nouvelle Séance</a>
       </div>

       <div class="table-container">
           <table>
               <thead>
                   <tr>
                       <th>Jour</th>
                       <th>Début</th>
                       <th>Fin</th>
                       <th>Classe</th>
                       <th>Professeur</th>
                       <th>Salle</th>
                       <th>Cours</th>
                       <th style="text-align: center;">Action</th>
                   </tr>
               </thead>
               <tbody>
                   <?php 
                   if ($result->num_rows > 0) {
                       while ($row = $result->fetch_assoc()) { 
                   ?>
                   <tr>
                       <td style="font-weight: bold; color: var(--primary);"><?= htmlspecialchars($row['JOUR']) ?></td>
                       <td><span class="time-tag"><?= $row['hd'] ?></span></td>
                       <td><span class="time-tag"><?= $row['hf'] ?></span></td>
                       <td style="font-weight: 500;"><?= htmlspecialchars($row['NOM_CLASSE']) ?></td>
                       <td><?= htmlspecialchars($row['NOM_PROF']) ?></td>
                       <td><strong><?= htmlspecialchars($row['NUMERO_SALLE']) ?></strong></td>
                       <td><?= htmlspecialchars($row['NOM_COURS']) ?></td>
                       <td style="text-align: center;">
                           <a class="edit-btn" href="edit_et.php?id_et=<?= $row['ID_EMPLOI'] ?>">✏️ Modifier</a>
                       </td>
                   </tr>
                   <?php 
                       } 
                   } else {
                       echo "<tr><td colspan='8' style='text-align:center; padding:20px;'>Aucune séance trouvée.</td></tr>";
                   }
                   ?>
               </tbody>
           </table>
       </div>
   </div>

</body>
</html>