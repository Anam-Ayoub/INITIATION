<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include __DIR__ . "/../../config/db.php";
include __DIR__ . "/../../config/functions.php";

$message = "";

if (isset($_POST['delete'])) {
   if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
       $message = "<div class='alert alert-error'>Erreur de sécurité (jeton CSRF invalide).</div>";
   } else {
       try {
           $stmt = $pdo->prepare("DELETE FROM EMPLOI_DU_TEMPS WHERE ID_EMPLOI = ?");
           if ($stmt->execute([$_POST['delete']])) { 
               $message = "<div class='alert alert-success'>Séance supprimée avec succès.</div>"; 
           }
       } catch (PDOException $e) {
           $message = "<div class='alert alert-error'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
       }
   }
   }

   $sql = "SELECT e.ID_EMPLOI as id_et, e.JOUR as jour, TIME_FORMAT(e.HEURE_DEB, '%H:%i') AS hd, TIME_FORMAT(e.HEURE_FIN, '%H:%i') AS hf,
   c.NUMERO as nom_classe, p.NOM_PROF as nom_prof, s.NOM_SALLE as numero_salle, co.NOM_COURS as nom_cours
   FROM EMPLOI_DU_TEMPS e LEFT JOIN CLASSE c ON e.ID_CLASSE=c.ID_CLASSE LEFT JOIN PROF p ON e.ID_PROF=p.ID_PROF
   LEFT JOIN SALLE s ON e.ID_SALLE=s.ID_SALLE LEFT JOIN COURS co ON e.ID_COURS=co.ID_COURS
   ORDER BY CASE WHEN e.JOUR='Lundi' THEN 1 WHEN e.JOUR='Mardi' THEN 2 WHEN e.JOUR='Mercredi' THEN 3
   WHEN e.JOUR='Jeudi' THEN 4 WHEN e.JOUR='Vendredi' THEN 5 WHEN e.JOUR='Samedi' THEN 6 END, e.HEURE_DEB";
   $sessions = $pdo->query($sql)->fetchAll();
   ?>
   <!DOCTYPE html>
   <html lang="fr">
   <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=2">
   </head>
   <body>
    <?php $current_page = 'delete'; include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Supprimer des séances</h2>
                <p class="subtitle">Sélectionnez les séances à retirer du planning</p>
            </div>
        </div>

        <?= $message; ?>

        <div class="schedule-list">
            <?php if (count($sessions) > 0): ?>
                <?php foreach ($sessions as $row): ?>
                    <div class="session-card">
                        <div class="session-main">
                            <span class="session-chip chip-day"><?= htmlspecialchars($row['jour']) ?></span>
                            <span class="session-chip chip-time"><?= $row['hd'] ?> — <?= $row['hf'] ?></span>
                            <span class="session-chip chip-course"><?= htmlspecialchars($row['nom_cours']) ?></span>
                            <span class="session-chip chip-prof">👨‍🏫 <?= htmlspecialchars($row['nom_prof']) ?></span>
                            <span class="session-chip chip-class">🎓 <?= htmlspecialchars($row['nom_classe']) ?></span>
                            <span class="session-chip chip-room">🏢 <?= htmlspecialchars($row['numero_salle']) ?></span>
                        </div>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette séance ?');">
                            <?php csrfField(); ?>
                            <button type="submit" name="delete" value="<?= $row['id_et'] ?>" class="btn-delete">Supprimer</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">Aucune séance à supprimer.</div>
            <?php endif; ?>
        </div>
    </div>
   </body>
   </html>