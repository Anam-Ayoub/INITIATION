<?php
session_start();
include "../config/db.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer.";
        }
    } else {
        $error = "Identifiants incorrects. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — CHRONOS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 50% 0%, rgba(99,102,241,0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(139,92,246,0.03) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: white;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
            padding: 48px 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: fadeUp 0.6s ease;
        }

        .login-brand {
            font-size: 1.8rem;
            font-weight: 900;
            letter-spacing: 5px;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .login-sub {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 36px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }

        input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(99,102,241,0.2);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.12);
            text-align: left;
            font-size: 0.88rem;
        }

        .back-home {
            margin-top: 28px;
            display: inline-block;
            text-decoration: none;
            color: #94a3b8;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .back-home:hover { color: #6366f1; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-brand">CHRONOS</div>
    <p class="login-sub">Espace d'administration</p>

    <?php if(isset($error)): ?>
        <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" placeholder="admin" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn-login">Se connecter</button>
    </form>

    <a href="../index.php" class="back-home">← Retour au portail</a>
</div>

</body>
</html>