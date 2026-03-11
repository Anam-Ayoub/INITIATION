<?php
session_start();
if(!isset($_SESSION['admin'])){
   header("Location: login.php");
   exit();
}
include "../config/db.php";

$total_seances  = $conn->query("SELECT COUNT(*) as c FROM EMPLOI_DU_TEMPS")->fetch_assoc()['c'];
$total_classes  = $conn->query("SELECT COUNT(*) as c FROM CLASSE")->fetch_assoc()['c'];
$total_profs    = $conn->query("SELECT COUNT(*) as c FROM PROF")->fetch_assoc()['c'];
$total_salles   = $conn->query("SELECT COUNT(*) as c FROM SALLE")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard — CHRONOS</title>
   <link rel="stylesheet" href="../assets/style.css?v=2">
</head>
<body>
    <?php $current_page = 'dashboard'; include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Tableau de Bord</h2>
                <p class="subtitle">Vue d'ensemble de votre planification</p>
            </div>
            <div class="admin-badge">👤 <?php echo htmlspecialchars($_SESSION['admin']); ?></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-label">Séances</div>
                <div class="stat-value"><?= $total_seances ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div class="stat-label">Classes</div>
                <div class="stat-value"><?= $total_classes ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👨‍🏫</div>
                <div class="stat-label">Professeurs</div>
                <div class="stat-value"><?= $total_profs ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-label">Salles</div>
                <div class="stat-value"><?= $total_salles ?></div>
            </div>
        </div>

        <p class="section-title">Actions rapides</p>
        <div class="actions-grid">
            <a href="add_et.php" class="btn-action"><span class="action-icon">➕</span> Nouvelle Séance</a>
            <a href="list_et.php" class="btn-action"><span class="action-icon">📋</span> Gérer Planning</a>
            <a href="../views/emploi_classe.php" class="btn-action" target="_blank"><span class="action-icon">📅</span> Emplois Publics</a>
            <a href="../views/securite.php" class="btn-action" target="_blank"><span class="action-icon">🔐</span> Sécurité</a>
        </div>
    </div>
</body>
</html>