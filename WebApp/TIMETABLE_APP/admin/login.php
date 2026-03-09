<?php
session_start();

// 1. Connexion à MySQL (Assurez-vous que les infos sont correctes)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "timetable_system"; // Smiyet la base de données dyalk

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// 2. Traitement du formulaire
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête sécurisée avec MySQLi (Prepare)
    $sql = "SELECT * FROM admin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['admin'] = $row['username'];
        header("Location: dashboard.php");
        exit();
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
    <title>Connexion Admin – Chronos-SIIA</title>
    <style>
        :root {
            --primary: #0056b3;
            --bg-light: #f4f7f9;
            --text-dark: #333;
            --white: #ffffff;
            --error: #dc3545;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-card {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
            border-top: 5px solid var(--primary);
        }

        .logo-container img {
            height: 150px;
            width: auto;
            filter: drop-shadow(0px 8px 12px rgba(0,0,0,0.15));
            transition: transform 0.3s ease;
        }

        h2 { color: var(--primary); margin: 10px 0; font-weight: 800; }
        p { color: #777; margin-bottom: 25px; }

        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-dark); }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #fafafa;
            font-size: 1rem;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #004494;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,86,179,0.3);
        }

        .error-msg {
            background-color: #fff5f5;
            color: var(--error);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid var(--error);
            text-align: left;
        }

        .back-home { margin-top: 25px; display: inline-block; text-decoration: none; color: #888; }
    </style>
</head>
<body>

<div class="login-card">
   <div class="logo-container">
       <img src="../logo.jpeg" alt="Logo Chronos-SIIA">
   </div>
   
   <h2>Connexion</h2>
   <p>Espace d'administration</p>

   <?php if(isset($error)): ?>
       <div class="error-msg">
           <strong>Erreur :</strong> <?php echo $error; ?>
       </div>
   <?php endif; ?>

   <form method="POST">
       <div class="form-group">
           <label for="username">Nom d'utilisateur</label>
           <input type="text" name="username" id="username" placeholder="Pseudo (ex: admin)" required>
       </div>

       <div class="form-group">
           <label for="password">Mot de passe</label>
           <input type="password" name="password" id="password" placeholder="••••••••" required>
       </div>

       <button type="submit" name="login" class="btn-login">S'authentifier maintenant</button>
   </form>

   <a href="../index.php" class="back-home">← Retour au portail public</a>
</div>

</body>
</html>