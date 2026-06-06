<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — Gestion des emplois du temps</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 30% 0%, rgba(99,102,241,0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 100%, rgba(139,92,246,0.04) 0%, transparent 50%);
            pointer-events: none;
                z-index: 0;
            }

            /* NAVIGATION */
            nav {
                position: relative;
                z-index: 10;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 5%;
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(0,0,0,0.06);
            }

            .brand {
                font-size: 1.3rem;
                font-weight: 800;
                letter-spacing: 4px;
                background: linear-gradient(135deg, #6366f1, #a78bfa);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .nav-links { display: flex; gap: 8px; }

            .nav-links a {
                text-decoration: none;
                color: #64748b;
                font-weight: 500;
                font-size: 0.85rem;
                padding: 8px 16px;
                border-radius: 8px;
                transition: all 0.25s ease;
                border: 1px solid transparent;
            }

            .nav-links a:hover {
                color: #1e293b;
                background: rgba(0,0,0,0.03);
                border-color: rgba(0,0,0,0.06);
            }

            /* SECTION PRINCIPALE */
            .hero {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                z-index: 1;
                text-align: center;
            }

            .hero-content {
                max-width: 600px;
                animation: fadeUp 0.8s ease;
                position: relative;
                z-index: 10;
            }

            .hero-icon {
                font-size: 3.5rem;
                margin-bottom: 20px;
            }

            .hero h1 {
                font-size: 3rem;
                font-weight: 900;
                letter-spacing: 6px;
                margin-bottom: 16px;
                background: linear-gradient(135deg, #1e293b, #6366f1);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero p {
                font-size: 1.1rem;
                color: #64748b;
                line-height: 1.7;
                margin-bottom: 32px;
            }

            .hero-actions {
                display: flex; gap: 12px;
                justify-content: center;
            }

            .btn-hero {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 14px 28px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.9rem;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-hero.primary {
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                color: white;
            }

            .btn-hero.primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 20px rgba(99,102,241,0.25);
            }

            .btn-hero.secondary {
                background: white;
                color: #64748b;
                border: 1px solid rgba(0,0,0,0.1);
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .btn-hero.secondary:hover {
                color: #1e293b;
                border-color: rgba(0,0,0,0.15);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            }

            /* Orbes floues */
            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(100px);
                opacity: 0.25;
                animation: float 8s ease-in-out infinite;
                z-index: 0;
                pointer-events: none;
            }
            .orb-1 { width: 300px; height: 300px; background: #6366f1; top: 10%; left: 10%; }
            .orb-2 { width: 200px; height: 200px; background: #8b5cf6; bottom: 20%; right: 15%; animation-delay: 3s; }
            .orb-3 { width: 150px; height: 150px; background: #3b82f6; top: 60%; left: 50%; animation-delay: 5s; }

            /* PIED DE PAGE */
            footer {
                position: relative;
                z-index: 10;
                text-align: center;
                padding: 14px;
                background: rgba(255, 255, 255, 0.6);
                border-top: 1px solid rgba(0,0,0,0.04);
                font-size: 0.75rem;
                color: #94a3b8;
            }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, -20px); }
        }
    </style>
</head>
<body>

    <nav>
        <div class="brand">CHRONOS</div>
        <div class="nav-links">
            <a href="admin/login.php">Administration</a>
            <a href="views/emploi_prof.php">Emploi Professeur</a>
            <a href="views/emploi_classe.php">Emploi Classe</a>
            <a href="views/securite.php">Sécurité / Salles</a>
        </div>
    </nav>

    <div class="hero">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="hero-content">
            <div class="hero-icon">⏱️</div>
            <h1>CHRONOS</h1>
            <p>Plateforme intelligente pour consulter et gérer les emplois du temps par classe ou professeur</p>
            <div class="hero-actions">
                <a href="views/emploi_classe.php" class="btn-hero primary">📅 Consulter les emplois</a>
                <a href="admin/login.php" class="btn-hero secondary">🔒 Administration</a>
            </div>
        </div>
    </div>

    <footer>
        <p>CHRONOS — Système de Gestion d'Emploi du Temps</p>
    </footer>

</body>
</html>