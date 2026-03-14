<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['STUDENT_ID'])) {
    header("Location: dashboard_student.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic CSRF verification
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = "Session expired or invalid token. Please try again.";
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = "Veuillez saisir votre email et mot de passe.";
        } else {
            // First check if it's a student
            $stmt = $pdo->prepare("SELECT `ID_STUDENT`, `FULL_NAME`, `PASSWORD`, `ID_CLASSE` FROM `STUDENT` WHERE `EMAIL` = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $student = $stmt->fetch();

            if ($student && password_verify($password, $student['PASSWORD'])) {
                // Successful student login
                $_SESSION['STUDENT_ID'] = $student['ID_STUDENT'];
                $_SESSION['STUDENT_NAME'] = $student['FULL_NAME'];
                $_SESSION['ID_CLASSE'] = $student['ID_CLASSE'];
                session_regenerate_id(true);
                header("Location: dashboard_student.php");
                exit;
            } else {
                // If not a student, check if it's a professor
                $stmtProf = $pdo->prepare("SELECT `ID_PROF`, `NOM_PROF`, `PASSWORD` FROM `PROF` WHERE `EMAIL` = :email LIMIT 1");
                $stmtProf->execute(['email' => $email]);
                $prof = $stmtProf->fetch();

                if ($prof && password_verify($password, $prof['PASSWORD'])) {
                    // Successful professor login
                    $_SESSION['PROF_ID'] = $prof['ID_PROF'];
                    $_SESSION['PROF_NAME'] = $prof['NOM_PROF'];
                    session_regenerate_id(true);
                    header("Location: dashboard_prof.php");
                    exit;
                } else {
                    // If not a prof, check if it's a security guard
                    $stmtSec = $pdo->prepare("SELECT `ID_SEC`, `FULL_NAME`, `PASSWORD` FROM `SECURITY` WHERE `EMAIL` = :email LIMIT 1");
                    $stmtSec->execute(['email' => $email]);
                    $sec = $stmtSec->fetch();

                    if ($sec && password_verify($password, $sec['PASSWORD'])) {
                        // Successful security login
                        $_SESSION['SEC_ID'] = $sec['ID_SEC'];
                        $_SESSION['SEC_NAME'] = $sec['FULL_NAME'];
                        session_regenerate_id(true);
                        header("Location: dashboard_sec.php");
                        exit;
                    } else {
                        $error = "Email ou mot de passe invalide.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS - Connexion Étudiant</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card glass-panel">
            <div class="auth-header">
                <div class="auth-logo">CHRONOS</div>
                <div class="auth-subtitle">Portail Utilisateur</div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="nom@chronos.edu">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
