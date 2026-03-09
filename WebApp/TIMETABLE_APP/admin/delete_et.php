<?php
session_start();
if (!isset($_SESSION['admin'])) {
   header("Location: login.php");
   exit();
}

// Connexion MySQL (Assurez-vous que les identifiants correspondent à votre config/db.php)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "timetable_system"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { 
    die("Connexion échouée: " . $conn->connect_error); 
}

$message = "";

/* =========================
   SUPPRESSION
========================= */
if (isset($_GET['delete'])) {
   $id_et = $_GET['delete'];

   // Utilisation de mysqli avec requête préparée
   $stmt = $conn->prepare("DELETE FROM EMPLOI_DU_TEMPS WHERE ID_EMPLOI = ?");
   $stmt->bind_param("i", $id_et);

   if ($stmt->execute()) {
       $message = "<div class='alert success'>✅ Séance supprimée avec succès.</div>";
   } else {
       $message = "<div class='alert error'>❌ Erreur lors de la suppression : " . $conn->error . "</div>";
   }
}

/* =========================
   LISTE DES SEANCES (Requête adaptée à MySQL)
========================= */
$sql = "
SELECT
   e.ID_EMPLOI as id_et,
   e.JOUR as jour,
   TIME_FORMAT(e.HEURE_DEB, '%H:%i') AS hd,
   TIME_FORMAT(e.HEURE_FIN, '%H:%i') AS hf,
   c.NUMERO as nom_classe,
   p.NOM_PROF as nom_prof,
   s.NOM_SALLE as numero_salle,
   co.NOM_COURS as nom_cours
FROM EMPLOI_DU_TEMPS e
JOIN CLASSE c     ON e.ID_CLASSE = c.ID_CLASSE
JOIN PROF p       ON e.ID_PROF   = p.ID_PROF
JOIN SALLE s      ON e.ID_SALLE  = s.ID_SALLE
JOIN COURS co     ON e.ID_COURS  = co.ID_COURS
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

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <title>Supprimer une séance – Chronos-SIIA</title>
   <style>
       :root { --primary: #0056b3; --sidebar-bg: #1a1d20; --bg-light: #f4f7f9; --white: #ffffff; --danger: #e74c3c; }
       body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background-color: var(--bg-light); min-height: 100vh; }
       .sidebar { width: 260px; background-color: var(--sidebar-bg); color: white; position: fixed; height: 100vh; }
       .sidebar-menu a { display: block; padding: 15px 25px; color: #adb5bd; text-decoration: none; }
       .sidebar-menu a:hover, .active { background: #2c3136; color: white; }
       .active { background: var(--primary) !important; }
       .main-content { flex: 1; margin-left: 260px; padding: 40px; }
       .table-container { background: var(--white); padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
       table { width: 100%; border-collapse: collapse; }
       th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
       th { background: #f8f9fa; color: #555; font-size: 0.8rem; text-transform: uppercase; }
       .badge-time { background: #eef2f7; padding: 4px 8px; border-radius: 4px; color: var(--primary); font-weight: bold; }
       .btn-delete { background: var(--danger); color: white; padding: 8px 12px; text-decoration: none; border-radius: 6px; font-size: 0.85rem; }
       .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
       .success { background: #d4edda; color: #155724; }
       .error { background: #f8d7da; color: #721c24; }
   </style>
   <script>
       function confirmDelete() {
           return confirm("⚠️ Voulez-vous vraiment supprimer cette séance ?");
       }
   </script>
</head>
<body>

   <div class="sidebar">
       <div style="padding:20px; text-align:center; border-bottom:1px solid #333;"><h2>CHRONOS</h2></div>
       <div class="sidebar-menu">
           <a href="dashboard.php">🏠 Tableau de Bord</a>
           <a href="add_et.php">➕ Ajouter une séance</a>
           <a href="list_et.php">📝 Modifier / Lister</a>
           <a href="delete_et.php" class="active">🗑️ Supprimer séance</a>
       </div>
   </div>

   <div class="main-content">
       <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
           <h2>🗑 Suppression des Séances</h2>
           <a href="dashboard.php" style="text-decoration:none; color: var(--primary); font-weight:bold;">← Retour</a>
       </div>

       <?= $message; ?>

       <div class="table-container">
           <table>
               <thead>
                   <tr>
                       <th>Jour</th>
                       <th>Horaire</th>
                       <th>Classe</th>
                       <th>Professeur</th>
                       <th>Salle</th>
                       <th>Cours</th>
                       <th style="text-align: center;">Action</th>
                   </tr>
               </thead>
               <tbody>
                   <?php while ($row = $result->fetch_assoc()) { ?>
                   <tr>
                       <td style="font-weight: 600;"><?= htmlspecialchars($row['jour']) ?></td>
                       <td><span class="badge-time"><?= $row['hd'] ?> - <?= $row['hf'] ?></span></td>
                       <td><?= htmlspecialchars($row['nom_classe']) ?></td>
                       <td><?= htmlspecialchars($row['nom_prof']) ?></td>
                       <td><?= htmlspecialchars($row['numero_salle']) ?></td>
                       <td style="font-style: italic;"><?= htmlspecialchars($row['nom_cours']) ?></td>
                       <td style="text-align: center;">
                           <a class="btn-delete" 
                              href="?delete=<?= $row['id_et'] ?>" 
                              onclick="return confirmDelete();">
                              🗑 Supprimer
                           </a>
                       </td>
                   </tr>
                   <?php } ?>
               </tbody>
           </table>
       </div>
   </div>

</body>
</html>