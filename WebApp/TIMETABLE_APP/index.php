<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Accueil – Gestion des emplois du temps</title>
   <style>
       :root {
           --primary: #0056b3;
           --bg-light: #f8f9fa;
           --text-dark: #343a40;
           --accent: #28a745;
           --white: #ffffff;
       }
 
       body {
           font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
           margin: 0;
           padding: 0;
           background-color: var(--bg-light);
           display: flex;
           flex-direction: column;
           /* Fixe la hauteur à 100% de l'écran */
           height: 100vh;
           overflow: hidden;
       }
 
       /* --- HEADER --- */
       header {
           background-color: var(--white);
           padding: 8px 5%;
           display: flex;
           justify-content: space-between;
           align-items: center;
           box-shadow: 0 2px 10px rgba(0,0,0,0.1);
           z-index: 1000;
       }
 
       .logo-area {
           display: flex;
           align-items: center;
           gap: 12px;
       }
 
       .logo-area img { height: 40px; }
       .logo-area h1 { font-size: 1rem; color: var(--primary); margin: 0; text-transform: uppercase; }
 
       nav a {
           text-decoration: none;
           color: var(--text-dark);
           font-weight: 600;
           margin-left: 15px;
           font-size: 0.9rem;
       }
 
       /* --- HERO SECTION (Remplissage de l'espace) --- */
       .hero-container {
           position: relative;
           width: 100%;
           /* Force l'image à prendre tout le milieu de la page */
           flex: 1;
           overflow: hidden;
       }
 
       .hero-container img {
           width: 100%;
           height: 100%;
           /* L'image est étirée pour toucher le footer sans déformation */
           object-fit: cover;
           object-position: center;
       }
 
       .overlay {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           display: flex;
           align-items: flex-start;
           justify-content: flex-end;
           /* Ajuste ici pour monter/descendre le texte */
           padding-top: 40px;
           padding-right: 5%;
       }
 
       .hero-text {
           background: rgba(255, 255, 255, 0.85);
           padding: 15px 20px;
           border-radius: 8px;
           max-width: 280px;
           text-align: right;
           border-right: 5px solid var(--primary);
           box-shadow: -5px 5px 15px rgba(0,0,0,0.1);
       }
 
       .hero-text h2 {
           color: var(--primary);
           font-size: 1.2rem;
           margin: 0;
           font-weight: 700;
           line-height: 1.4;
       }
 
       /* --- FOOTER COMPACT --- */
       footer {
           background-color: #1a1d20;
           color: #adb5bd;
           text-align: center;
           padding: 10px 0;
           border-top: 3px solid var(--primary);
           font-size: 0.8rem;
           z-index: 1000;
       }
 
       footer p { margin: 2px 0; }
       footer .members { color: var(--accent); font-weight: bold; }
   </style>
</head>
<body>
 
   <header>
       <div class="logo-area">
           <img src="logo.jpeg" alt="Logo">
           <h1>S-Timetabling</h1>
       </div>
       <nav>
           <a href="admin/login.php">Administration</a>
           <a href="views/emploi_prof.php">Emploi Professeur</a>
           <a href="views/emploi_classe.php">Emploi Classe</a>
       </nav>
   </header>
 
   <div class="hero-container">
       <img src="acceuil.jpeg" alt="Background">
       
       <div class="overlay">
           <div class="hero-text">
               <h2>Plateforme intelligente pour consulter les emplois du temps par classe ou professeur</h2>
           </div>
       </div>
   </div>
 
   <footer>
       <p>Projet : <b>Système de Gestion d'Emploi du Temps</b></p>
       <p class="members">Zahira Habyby & Fatima Zahra Elhoussi</p>
       <p>Master SIIA - Promotion 2025/2026</p>
   </footer>
 
</body>
</html>