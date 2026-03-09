<?php
session_start();
if(!isset($_SESSION['admin'])){
   header("Location: login.php");
   exit();
}
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard Admin – Chronos-SIIA</title>
   <style>
       :root {
           --primary: #0056b3;
           --sidebar-bg: #1a1d20;
           --bg-light: #f4f7f9;
           --white: #ffffff;
           --text-muted: #6c757d;
           --accent: #28a745;
           --danger: #dc3545;
       }
 
       body {
           font-family: 'Segoe UI', Tahoma, sans-serif;
           margin: 0;
           display: flex;
           background-color: var(--bg-light);
           height: 100vh;
       }
 
       /* --- SIDEBAR --- */
       .sidebar {
           width: 260px;
           background-color: var(--sidebar-bg);
           color: white;
           display: flex;
           flex-direction: column;
           padding: 20px 0;
           box-shadow: 2px 0 10px rgba(0,0,0,0.1);
       }
 
       .sidebar-header {
           text-align: center;
           padding: 0 20px 20px 20px;
           border-bottom: 1px solid #333;
       }
 
       .sidebar-header img {
           height: 80px;
           border-radius: 10px;
           margin-bottom: 10px;
       }
 
       .sidebar-header h3 {
           font-size: 1rem;
           margin: 0;
           color: var(--primary);
           text-transform: uppercase;
       }
 
       .sidebar-menu {
           flex: 1;
           padding: 20px 0;
       }
 
       .sidebar-menu a {
           display: flex;
           align-items: center;
           padding: 12px 25px;
           color: #adb5bd;
           text-decoration: none;
           transition: 0.3s;
           font-size: 0.95rem;
       }
 
       .sidebar-menu a:hover {
           background-color: #2c3136;
           color: white;
           border-left: 4px solid var(--primary);
       }
 
       .sidebar-menu a.active {
           background-color: var(--primary);
           color: white;
       }
 
       .logout-btn {
           padding: 15px 25px;
           background-color: #c82333;
           color: white;
           text-decoration: none;
           text-align: center;
           font-weight: bold;
           margin: 10px 20px;
           border-radius: 5px;
       }
 
       /* --- MAIN CONTENT --- */
       .main-content {
           flex: 1;
           padding: 30px;
           overflow-y: auto;
       }
 
       .header-top {
           display: flex;
           justify-content: space-between;
           align-items: center;
           margin-bottom: 30px;
       }
 
       .header-top h2 {
           margin: 0;
           color: #333;
       }
 
       .admin-profile {
           background: var(--white);
           padding: 8px 20px;
           border-radius: 50px;
           box-shadow: 0 2px 5px rgba(0,0,0,0.05);
           font-weight: 600;
           color: var(--primary);
       }
 
       /* --- CARDS --- */
       .stats-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
           gap: 20px;
           margin-bottom: 40px;
       }
 
       .card {
           background: var(--white);
           padding: 25px;
           border-radius: 12px;
           box-shadow: 0 4px 15px rgba(0,0,0,0.05);
           border-left: 5px solid var(--primary);
       }
 
       .card h4 {
           margin: 0;
           color: var(--text-muted);
           font-size: 0.9rem;
           text-transform: uppercase;
       }
 
       .card p {
           margin: 10px 0 0 0;
           font-size: 1.8rem;
           font-weight: bold;
           color: #212529;
       }
 
       .actions-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 15px;
       }
 
       .btn-action {
           display: block;
           padding: 20px;
           background: var(--white);
           text-decoration: none;
           color: var(--text-dark);
           border-radius: 10px;
           text-align: center;
           font-weight: 600;
           box-shadow: 0 2px 8px rgba(0,0,0,0.05);
           border: 1px solid #eee;
           transition: 0.3s;
       }
 
       .btn-action:hover {
           transform: translateY(-5px);
           border-color: var(--primary);
           color: var(--primary);
       }
 
   </style>
</head>
<body>
 
   <div class="sidebar">
       <div class="sidebar-header">
           <img src="../logo.jpeg" alt="Logo">
           <h3>Chronos-SIIA</h3>
       </div>
       
       <div class="sidebar-menu">
           <a href="dashboard.php" class="active">🏠 Tableau de Bord</a>
           <a href="add_et.php">➕ Ajouter une séance</a>
           <a href="list_et.php">📝 Modifier / Lister</a>
           <a href="delete_et.php">🗑️ Supprimer séance</a>
           <hr style="border: 0.5px solid #333; margin: 10px 20px;">
           <a href="../views/emploi_classe.php" target="_blank">📅 Emploi par Classe</a>
           <a href="../views/emploi_prof.php" target="_blank">👨‍🏫 Emploi par Prof</a>
       </div>
 
       <a href="logout.php" class="logout-btn">Déconnexion</a>
   </div>
 
   <div class="main-content">
       <div class="header-top">
           <h2>Bienvenue dans votre gestionnaire</h2>
           <div class="admin-profile">
               👤 Admin : <?php echo htmlspecialchars($_SESSION['admin']); ?>
           </div>
       </div>
 
       <div class="stats-grid">
           <div class="card">
               <h4>Total Séances</h4>
               <p>2</p>
           </div>
           <div class="card" style="border-left-color: var(--accent);">
               <h4>Classes Gérées</h4>
               <p>2</p>
           </div>
           <div class="card" style="border-left-color: #f39c12;">
               <h4>Professeurs</h4>
               <p>2</p>
           </div>
       </div>
 
       <h3>Actions Rapides</h3>
       <div class="actions-grid">
           <a href="add_et.php" class="btn-action">Nouvelle Séance</a>
           <a href="list_et.php" class="btn-action">Gérer Planning</a>
           <a href="../views/emploi_classe.php" class="btn-action">Consulter Public</a>
       </div>
   </div>
 
</body>
</html>