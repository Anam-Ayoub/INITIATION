<?php
/**
 * Shared Sidebar — CHRONOS
 * Set $current_page before including.
 */
$current_page = $current_page ?? '';
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand">CHRONOS</div>
        <div class="brand-sub">Gestion du temps</div>
    </div>
    
    <div class="sidebar-menu">
        <a href="dashboard.php" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>">🏠 Tableau de Bord</a>
        <a href="add_et.php" class="<?= $current_page === 'add' ? 'active' : '' ?>">➕ Ajouter une séance</a>
        <a href="list_et.php" class="<?= $current_page === 'list' ? 'active' : '' ?>">📋 Modifier / Lister</a>
        <a href="delete_et.php" class="<?= $current_page === 'delete' ? 'active' : '' ?>">🗑️ Supprimer séance</a>
        <div class="sidebar-divider"></div>
        <a href="carte.php" class="<?= $current_page === 'carte' ? 'active' : '' ?>">🗺️ Carte des Salles</a>
        <a href="../views/emploi_classe.php" <?= $current_page !== 'emploi_classe' ? 'target="_blank"' : '' ?> class="<?= $current_page === 'emploi_classe' ? 'active' : '' ?>">📅 Emploi par Classe</a>
        <a href="../views/emploi_prof.php" <?= $current_page !== 'emploi_prof' ? 'target="_blank"' : '' ?> class="<?= $current_page === 'emploi_prof' ? 'active' : '' ?>">👨‍🏫 Emploi par Prof</a>
        <a href="../views/securite.php" <?= $current_page !== 'securite' ? 'target="_blank"' : '' ?> class="<?= $current_page === 'securite' ? 'active' : '' ?>">🔐 Sécurité / Salles</a>
    </div>

    <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
</div>
