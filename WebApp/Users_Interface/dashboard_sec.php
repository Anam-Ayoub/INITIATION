<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['SEC_ID'])) {
    header("Location: index.php");
    exit;
}

$secName = $_SESSION['SEC_NAME'];
$idSec = $_SESSION['SEC_ID'];

// Fetch all schedules for security (rooms to be opened)
$sql = "
    SELECT 
        e.ID_EMPLOI,
        e.JOUR,
        e.HEURE_DEB,
        e.HEURE_FIN,
        c.NOM_COURS,
        cl.NUMERO as NUMERO_CLASSE,
        p.NOM_PROF,
        s.NOM_SALLE
    FROM EMPLOI_DU_TEMPS e
    LEFT JOIN COURS c ON e.ID_COURS = c.ID_COURS
    LEFT JOIN CLASSE cl ON e.ID_CLASSE = cl.ID_CLASSE
    LEFT JOIN PROF p ON e.ID_PROF = p.ID_PROF
    LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
    ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), s.NOM_SALLE, e.HEURE_DEB
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll();

// Organize data by day
$scheduleByDay = [
    'Lundi' => [],
    'Mardi' => [],
    'Mercredi' => [],
    'Jeudi' => [],
    'Vendredi' => [],
    'Samedi' => []
];

foreach ($results as $row) {
    if (isset($scheduleByDay[$row['JOUR']])) {
        $scheduleByDay[$row['JOUR']][] = $row;
    }
}

// Time slots for week view
$timeSlots = [
    '08:30:00 - 10:30:00',
    '10:30:00 - 12:30:00',
    '14:30:00 - 16:30:00',
    '16:30:00 - 18:30:00'
];

// Helper to format time strings from DB
function formatTime($timeStr) {
    return date('H:i', strtotime($timeStr));
}

// Map current PHP day to French day
$englishToFrenchDay = [
    'Monday' => 'Lundi',
    'Tuesday' => 'Mardi',
    'Wednesday' => 'Mercredi',
    'Thursday' => 'Jeudi',
    'Friday' => 'Vendredi',
    'Saturday' => 'Samedi',
    'Sunday' => 'Dimanche'
];
$currentDayEnglish = date('l');
$currentDayFrench = $englishToFrenchDay[$currentDayEnglish] ?? 'Lundi';
if ($currentDayFrench === 'Dimanche') $currentDayFrench = 'Lundi'; // Default to Monday if Sunday
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS - Interface de Sécurité</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .day-view { display: none; }
        .week-view { display: none; }
        .active-view { display: block; }
        
        .day-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .empty-day {
            padding: 3rem;
            text-align: center;
            color: var(--text-muted);
        }

        .badge-warning {
            background-color: #FEF3C7;
            color: #D97706;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #059669;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #DC2626;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-content glass-panel" style="padding: 1rem 1.5rem;">
                <a href="#" class="nav-brand">CHRONOS (Interface de Sécurité)</a>
                <div class="nav-user">
                    <div class="user-info">
                        🛡️ Agent <?php echo htmlspecialchars($secName); ?> 
                    </div>
                    <a href="logout.php" class="btn-logout">Se déconnecter</a>
                </div>
            </div>
        </nav>

        <main>
            <div class="dashboard-header">
                <h2>Planning des Salles</h2>
                <div class="view-controls">
                    <button class="view-btn active" onclick="switchView('day')" id="btn-day">Aujourd'hui</button>
                    <button class="view-btn" onclick="switchView('week')" id="btn-week">Historique Hebdomadaire</button>
                </div>
            </div>

            <!-- Day View Area -->
            <div id="view-day" class="day-view active-view glass-panel" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3>Salles à ouvrir / sécuriser : <?php echo htmlspecialchars($currentDayFrench); ?></h3>
                    <select id="day-selector" class="form-control" style="width: auto;" onchange="changeDay()">
                        <?php foreach (array_keys($scheduleByDay) as $day): ?>
                            <option value="<?php echo htmlspecialchars($day); ?>" <?php echo $day === $currentDayFrench ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($day); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php foreach ($scheduleByDay as $day => $sessions): ?>
                    <div id="day-content-<?php echo htmlspecialchars($day); ?>" class="day-grid" style="display: <?php echo $day === $currentDayFrench ? 'grid' : 'none'; ?>">
                        <?php if (empty($sessions)): ?>
                            <div class="empty-day" style="grid-column: 1 / -1; background: rgba(255,255,255,0.4); border-radius: 0.5rem; border: 1px dashed #D1D5DB;">
                                Aucune salle programmée pour ce jour.
                            </div>
                        <?php else: ?>
                            <?php foreach ($sessions as $session): ?>
                                <div class="schedule-card" style="background: white; border-left-color: #F59E0B;">
                                    <?php
                                        // Simple logic to show active or upcoming vs finished for today
                                        $status = "À venir";
                                        $badgeClass = "badge-warning";
                                        if ($day === $currentDayFrench) {
                                            $now = date('H:i:s');
                                            $start = $session['HEURE_DEB'];
                                            $end = $session['HEURE_FIN'];
                                            if ($now >= $start && $now <= $end) {
                                                $status = "En cours";
                                                $badgeClass = "badge-success";
                                            } elseif ($now > $end) {
                                                $status = "Terminé (À fermer)";
                                                $badgeClass = "badge-danger";
                                            }
                                        }
                                    ?>
                                    <div>
                                        <span class="<?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                    </div>
                                    <div class="course-name" style="font-size: 1.25rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                        🚪 Salle <?php echo htmlspecialchars($session['NOM_SALLE'] ?: 'N/A'); ?>
                                    </div>
                                    <div class="schedule-detail" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">
                                        🕒 <?php echo formatTime($session['HEURE_DEB']) . ' - ' . formatTime($session['HEURE_FIN']); ?>
                                    </div>
                                    <div class="schedule-detail">
                                        🎓 Class: <?php echo htmlspecialchars($session['NUMERO_CLASSE'] ?: 'Unknown Class'); ?>
                                    </div>
                                    <div class="schedule-detail">
                                        👨‍🏫 Prof: <?php echo htmlspecialchars($session['NOM_PROF'] ?: 'Unknown Prof'); ?>
                                    </div>
                                    <div class="schedule-detail">
                                        📚 Course: <?php echo htmlspecialchars($session['NOM_COURS'] ?: 'Unknown Course'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Week View Area -->
            <div id="view-week" class="week-view glass-panel" style="padding: 2rem;">
                <p style="color: var(--text-muted); margin-bottom: 1rem;">Liste chronologique complète de l'activité du campus pour la semaine (pour archivage sécurité).</p>
                <div class="timetable-container" style="max-height: 600px; overflow-y: auto;">
                    <table class="timetable-grid" style="min-width: 1000px;">
                        <thead>
                            <tr style="position: sticky; top: 0; background: var(--glass-bg); z-index: 10;">
                                <th>Jour</th>
                                <th>Heure</th>
                                <th>Salle</th>
                                <th>Classe</th>
                                <th>Professeur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $weekResults = $results; // already sorted by Day, then Salle, then Hour
                            if (empty($weekResults)): 
                            ?>
                                <tr><td colspan="5" style="text-align:center; padding: 2rem;">Aucun emploi du temps trouvé pour cette semaine.</td></tr>
                            <?php else: ?>
                                <?php foreach ($weekResults as $row): ?>
                                    <tr style="border-bottom: 1px solid #E5E7EB;">
                                        <td style="padding: 1rem; font-weight: 600;"><?php echo htmlspecialchars($row['JOUR']); ?></td>
                                        <td style="padding: 1rem; color: var(--text-muted);">
                                            <?php echo formatTime($row['HEURE_DEB']) . ' - ' . formatTime($row['HEURE_FIN']); ?>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span style="background: #F3F4F6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 500;">
                                                <?php echo htmlspecialchars($row['NOM_SALLE'] ?: 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;"><?php echo htmlspecialchars($row['NUMERO_CLASSE'] ?: 'N/A'); ?></td>
                                        <td style="padding: 1rem;"><?php echo htmlspecialchars($row['NOM_PROF'] ?: 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        function switchView(view) {
            // Update buttons
            document.getElementById('btn-day').classList.remove('active');
            document.getElementById('btn-week').classList.remove('active');
            document.getElementById('btn-' + view).classList.add('active');

            // Update views
            document.getElementById('view-day').classList.remove('active-view');
            document.getElementById('view-week').classList.remove('active-view');
            document.getElementById('view-' + view).classList.add('active-view');
        }

        function changeDay() {
            var selectedDay = document.getElementById('day-selector').value;
            var days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            
            days.forEach(function(day) {
                document.getElementById('day-content-' + day).style.display = 'none';
            });
            
            document.getElementById('day-content-' + selectedDay).style.display = 'grid';
        }
    </script>
</body>
</html>
