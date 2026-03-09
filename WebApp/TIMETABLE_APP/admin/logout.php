<?php session_start(); session_destroy(); header("Location: login.php"); ?><?php
session_start();
session_destroy();
header("Location: login.php");
?>
db.php
<?php
$conn = oci_connect('HR', 'HR', 'localhost/ORCLPDB', 'AL32UTF8');
 
if (!$conn) {
   $e = oci_error();
   die($e['message']);
}
 
/* =====================================
  FUNCTION : Vérifier conflits
===================================== */
function existeConflit($conn, $jour, $hd, $hf, $champ, $id, $id_et = null) {
 
   $sql = "
       SELECT COUNT(*) AS NB
       FROM emploi_temps
       WHERE jour = :jour
         AND $champ = :id
         AND (
               TO_DATE(:hd,'HH24:MI') < heure_fin
           AND TO_DATE(:hf,'HH24:MI') > heure_debut
         )
   ";
 
   if ($id_et !== null) {
       $sql .= " AND id_et <> :id_et";
   }
 
   $st = oci_parse($conn, $sql);
   oci_bind_by_name($st, ":jour", $jour);
   oci_bind_by_name($st, ":id", $id);
   oci_bind_by_name($st, ":hd", $hd);
   oci_bind_by_name($st, ":hf", $hf);
 
   if ($id_et !== null) {
       oci_bind_by_name($st, ":id_et", $id_et);
   }
 
   oci_execute($st);
   $row = oci_fetch_assoc($st);
 
   return $row['NB'] > 0;
}
?>
emploi_classe.php
<?php
include "../config/db.php";
 
/* 🔴 FORMATAGE DES HEURES DANS ORACLE avec tri logique des jours */
$sql = "
SELECT
   nom_classe,
   jour,
   TO_CHAR(heure_debut, 'HH24:MI') AS heure_debut,
   TO_CHAR(heure_fin, 'HH24:MI') AS heure_fin,
   nom_prof,
   numero_salle,
   nom_cours
FROM vue_emploi_classe
ORDER BY nom_classe,
   CASE
       WHEN jour = 'Lundi' THEN 1
       WHEN jour = 'Mardi' THEN 2
       WHEN jour = 'Mercredi' THEN 3
       WHEN jour = 'Jeudi' THEN 4
       WHEN jour = 'Vendredi' THEN 5
       WHEN jour = 'Samedi' THEN 6
   END, heure_debut
";
 
$stid = oci_parse($conn, $sql);
oci_execute($stid);
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Emploi du temps par classe – Chronos-SIIA</title>
   <style>
       :root {
           --primary: #0056b3;
           --bg-light: #f8f9fa;
           --white: #ffffff;
           --text-dark: #2d3436;
       }
 
       body {
           font-family: 'Segoe UI', Arial, sans-serif;
           background-color: var(--bg-light);
           color: var(--text-dark);
           margin: 0;
           padding: 20px;
       }
 
       header {
           text-align: center;
           margin-bottom: 40px;
           padding: 20px;
           background: var(--white);
           border-radius: 10px;
           box-shadow: 0 4px 6px rgba(0,0,0,0.05);
       }
 
       header h1 {
           color: var(--primary);
           margin: 0;
           text-transform: uppercase;
           letter-spacing: 2px;
       }
 
       /* --- CONTAINER DE CLASSE --- */
       .classe-card {
           background: var(--white);
           max-width: 1000px;
           margin: 0 auto 40px auto;
           padding: 25px;
           border-radius: 15px;
           box-shadow: 0 10px 25px rgba(0,0,0,0.08);
           border-top: 5px solid var(--primary);
       }
 
       .classe-title {
           font-size: 1.5rem;
           color: var(--primary);
           margin-bottom: 20px;
           display: flex;
           align-items: center;
           gap: 10px;
           border-bottom: 1px solid #eee;
           padding-bottom: 10px;
       }
 
       /* --- TABLE STYLE --- */
       table {
           width: 100%;
           border-collapse: collapse;
           background: var(--white);
       }
 
       th {
           background-color: #f1f4f8;
           color: #555;
           font-weight: 600;
           padding: 15px;
           text-align: left;
           text-transform: uppercase;
           font-size: 0.85rem;
       }
 
       td {
           padding: 15px;
           border-bottom: 1px solid #f1f1f1;
           font-size: 0.95rem;
       }
 
       tr:last-child td { border-bottom: none; }
       
       tr:hover { background-color: #fcfdfe; }
 
       /* --- BADGES --- */
       .time-badge {
           background: #e9ecef;
           padding: 5px 10px;
           border-radius: 5px;
           font-weight: bold;
           color: #495057;
           font-size: 0.85rem;
       }
 
       .salle-badge {
           color: var(--primary);
           font-weight: 600;
       }
 
       .cours-name {
           font-weight: bold;
           color: #2d3436;
       }
 
       /* --- BOUTON RETOUR --- */
       .footer-nav {
           text-align: center;
           margin-top: 40px;
           padding-bottom: 40px;
       }
 
       .btn-back {
           text-decoration: none;
           color: #777;
           font-weight: 600;
           transition: 0.3s;
       }
 
       .btn-back:hover { color: var(--primary); }
 
       /* Impression */
       @media print {
           .footer-nav, .btn-back { display: none; }
           body { background: white; }
           .classe-card { box-shadow: none; border: 1px solid #eee; page-break-after: always; }
       }
   </style>
</head>
<body>
 
<header>
   <h1>📅 Emplois du Temps par Classe</h1>
</header>
 
<main>
<?php
$classe_precedente = null;
 
while ($row = oci_fetch_assoc($stid)) {
 
   /* 🔹 Changement de classe → On ferme la table précédente et on ouvre un nouveau bloc */
   if ($classe_precedente !== $row['NOM_CLASSE']) {
 
       if ($classe_precedente !== null) {
           echo "</tbody></table></div>"; // Fermeture du bloc précédent
       }
 
       echo "<div class='classe-card'>";
       echo "<div class='classe-title'>🎓 Classe : " . htmlspecialchars($row['NOM_CLASSE']) . "</div>";
       echo "<table>
               <thead>
                   <tr>
                       <th>Jour</th>
                       <th>Horaire</th>
                       <th>Cours</th>
                       <th>Enseignant</th>
                       <th>Salle</th>
                   </tr>
               </thead>
               <tbody>";
 
       $classe_precedente = $row['NOM_CLASSE'];
   }
   ?>
 
   <tr>
       <td style="font-weight: 600;"><?php echo htmlspecialchars($row['JOUR']); ?></td>
       <td>
           <span class="time-badge">
               <?php echo $row['HEURE_DEBUT'] . " - " . $row['HEURE_FIN']; ?>
           </span>
       </td>
       <td class="cours-name"><?php echo htmlspecialchars($row['NOM_COURS']); ?></td>
       <td><?php echo htmlspecialchars($row['NOM_PROF']); ?></td>
       <td class="salle-badge">📍 <?php echo htmlspecialchars($row['NUMERO_SALLE']); ?></td>
   </tr>
 
<?php } ?>
 
<?php if ($classe_precedente !== null): ?>
   </tbody></table></div>
<?php endif; ?>
</main>
 
<div class="footer-nav">
   <a href="../index.php" class="btn-back">⬅ Retour à la page d'acceuil</a>
</div>
 
</body>
</html>
emploi_prof.php
<?php
include "../config/db.php";
 
/* 🔴 Requête avec tri logique des jours (Lundi -> Samedi) */
$sql = "
SELECT
   nom_prof,
   jour,
   TO_CHAR(heure_debut, 'HH24:MI') AS heure_debut,
   TO_CHAR(heure_fin, 'HH24:MI') AS heure_fin,
   nom_classe,
   numero_salle,
   nom_cours
FROM vue_emploi_prof
ORDER BY nom_prof,
   CASE
       WHEN jour = 'Lundi' THEN 1
       WHEN jour = 'Mardi' THEN 2
       WHEN jour = 'Mercredi' THEN 3
       WHEN jour = 'Jeudi' THEN 4
       WHEN jour = 'Vendredi' THEN 5
       WHEN jour = 'Samedi' THEN 6
   END, heure_debut
";
 
$stid = oci_parse($conn, $sql);
oci_execute($stid);
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Emploi par Professeur – Chronos-SIIA</title>
   <style>
       :root {
           --accent-teal: #16a085;
           --bg-light: #f4f7f6;
           --white: #ffffff;
           --text-dark: #2c3e50;
       }
 
       body {
           font-family: 'Segoe UI', system-ui, sans-serif;
           background-color: var(--bg-light);
           color: var(--text-dark);
           margin: 0;
           padding: 30px;
       }
 
       header {
           text-align: center;
           margin-bottom: 50px;
       }
 
       header h1 {
           color: var(--accent-teal);
           font-size: 2rem;
           text-transform: uppercase;
           border-bottom: 3px solid var(--accent-teal);
           display: inline-block;
           padding-bottom: 10px;
       }
 
       /* --- CARD STYLE --- */
       .prof-card {
           background: var(--white);
           max-width: 1000px;
           margin: 0 auto 50px auto;
           border-radius: 12px;
           overflow: hidden;
           box-shadow: 0 15px 35px rgba(0,0,0,0.05);
           border: 1px solid #eaeaea;
       }
 
       .prof-header {
           background: var(--accent-teal);
           color: white;
           padding: 20px 30px;
           display: flex;
           align-items: center;
           justify-content: space-between;
       }
 
       .prof-header h3 {
           margin: 0;
           font-size: 1.3rem;
           font-weight: 500;
       }
 
       /* --- TABLE STYLE --- */
       table {
           width: 100%;
           border-collapse: collapse;
       }
 
       th {
           background-color: #f9fafb;
           color: #7f8c8d;
           font-size: 0.8rem;
           text-transform: uppercase;
           letter-spacing: 1px;
           padding: 15px 30px;
           text-align: left;
           border-bottom: 1px solid #eee;
       }
 
       td {
           padding: 15px 30px;
           border-bottom: 1px solid #f1f1f1;
           font-size: 0.95rem;
       }
 
       tr:hover { background-color: #fdfdfd; }
 
       .time-box {
           font-family: monospace;
           background: #f0f3f4;
           padding: 4px 8px;
           border-radius: 4px;
           font-weight: bold;
           color: #34495e;
       }
 
       .badge-classe {
           background: #e8f6f3;
           color: #16a085;
           padding: 3px 10px;
           border-radius: 20px;
           font-weight: bold;
           font-size: 0.85rem;
       }
 
       /* --- FOOTER --- */
       .footer-link {
           text-align: center;
           margin-top: 30px;
       }
 
       .btn-return {
           color: #95a5a6;
           text-decoration: none;
           font-weight: 600;
           transition: 0.2s;
       }
 
       .btn-return:hover { color: var(--accent-teal); }
 
       /* Impression */
       @media print {
           .footer-link { display: none; }
           body { padding: 0; background: white; }
           .prof-card { box-shadow: none; page-break-after: always; border: 1px solid #ddd; }
       }
   </style>
</head>
<body>
 
<header>
   <h1>👨‍🏫 Emplois du Temps par Professeur</h1>
</header>
 
<main>
<?php
$prof_precedent = null;
 
while ($row = oci_fetch_assoc($stid)) {
 
   if ($prof_precedent !== $row['NOM_PROF']) {
       if ($prof_precedent !== null) {
           echo "</tbody></table></div>";
       }
 
       echo "<div class='prof-card'>";
       echo "<div class='prof-header'>
               <h3>M./Mme " . htmlspecialchars($row['NOM_PROF']) . "</h3>
               <span>Planning Hebdomadaire</span>
             </div>";
       echo "<table>
               <thead>
                   <tr>
                       <th>Jour</th>
                       <th>Horaire</th>
                       <th>Module / Cours</th>
                       <th>Classe</th>
                       <th>Salle</th>
                   </tr>
               </thead>
               <tbody>";
 
       $prof_precedent = $row['NOM_PROF'];
   }
   ?>
 
   <tr>
       <td style="font-weight: bold; color: #16a085;"><?php echo $row['JOUR']; ?></td>
       <td>
           <span class="time-box"><?php echo $row['HEURE_DEBUT']; ?></span>
           <small>→</small>
           <span class="time-box"><?php echo $row['HEURE_FIN']; ?></span>
       </td>
       <td style="font-weight: 600;"><?php echo htmlspecialchars($row['NOM_COURS']); ?></td>
       <td><span class="badge-classe"><?php echo htmlspecialchars($row['NOM_CLASSE']); ?></span></td>
       <td style="color: #7f8c8d;">🏢 Salle <?php echo htmlspecialchars($row['NUMERO_SALLE']); ?></td>
   </tr>
 
<?php } ?>
 
<?php if ($prof_precedent !== null): ?>
   </tbody></table></div>
<?php endif; ?>
</main>
 
<div class="footer-link">
   <a href="../index.php" class="btn-back">⬅ Retour à la page d'acceuil</a>
</div>
 
</body>
</html>