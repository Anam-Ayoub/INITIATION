<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['PROF_ID'])) {
    header("Location: index.php");
    exit;
}

$profName = $_SESSION['PROF_NAME'];
$idProf = $_SESSION['PROF_ID'];

// Récupérer l'emploi du temps pour ce professeur
$sql = "
    SELECT 
        e.ID_EMPLOI,
        e.JOUR,
        e.HEURE_DEB,
        e.HEURE_FIN,
        c.NOM_COURS,
        cl.NUMERO as NUMERO_CLASSE,
        s.NOM_SALLE
    FROM EMPLOI_DU_TEMPS e
    LEFT JOIN COURS c ON e.ID_COURS = c.ID_COURS
    LEFT JOIN CLASSE cl ON e.ID_CLASSE = cl.ID_CLASSE
    LEFT JOIN SALLE s ON e.ID_SALLE = s.ID_SALLE
    WHERE e.ID_PROF = :id_prof
    ORDER BY FIELD(e.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.HEURE_DEB
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_prof' => $idProf]);
$results = $stmt->fetchAll();

// Organiser les données par jour pour un rendu facile
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

// Créneaux horaires pour la vue hebdomadaire
$timeSlots = [
    '08:30:00 - 10:30:00',
    '10:30:00 - 12:30:00',
    '14:30:00 - 16:30:00',
    '16:30:00 - 18:30:00'
];

// Aide pour formater les chaînes de temps de la base de données
function formatTime($timeStr) {
    return date('H:i', strtotime($timeStr));
}

// Aide pour vérifier si une session correspond à peu près à un créneau horaire
function getSessionForSlot($sessions, $slotIndex) {
    $slotStarts = ['08:30:00', '10:30:00', '14:30:00', '16:30:00'];
    $slotEnds = ['10:30:00', '12:30:00', '16:30:00', '18:30:00'];
    
    $slotStart = $slotStarts[$slotIndex];
    $slotEnd = $slotEnds[$slotIndex];
    
    foreach ($sessions as $session) {
        if ($session['HEURE_DEB'] <= $slotStart && $session['HEURE_FIN'] >= $slotEnd) {
            return $session;
        }
    }
    return null;
}

// Faire correspondre le jour actuel de PHP au jour en français
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
if ($currentDayFrench === 'Dimanche') $currentDayFrench = 'Lundi'; // Par défaut à Lundi si c'est Dimanche
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS - Professor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .day-view { display: none; }
        .week-view { display: none; }
        .active-view { display: block; }
        
        .day-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .empty-day {
            padding: 3rem;
            text-align: center;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-content glass-panel" style="padding: 1rem 1.5rem;">
                <a href="#" class="nav-brand">CHRONOS (Interface Professeur)</a>
                <div class="nav-user">
                    <div class="user-info">
                        👨‍🏫 Bienvenue, <?php echo htmlspecialchars($profName); ?> 
                    </div>
                    <a href="logout.php" class="btn-logout">Se déconnecter</a>
                </div>
            </div>
        </nav>

        <main>
            <div class="dashboard-header">
                <h2>Mon Emploi du Temps</h2>
                <div class="view-controls">
                    <button class="view-btn active" onclick="switchView('day')" id="btn-day">Aujourd'hui</button>
                    <button class="view-btn" onclick="switchView('week')" id="btn-week">Semaine</button>
                </div>
            </div>

            <!-- Zone de vue par jour -->
            <div id="view-day" class="day-view active-view glass-panel" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3><?php echo htmlspecialchars($currentDayFrench); ?></h3>
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
                                Aucun cours programmé pour vous aujourd'hui.
                            </div>
                        <?php else: ?>
                            <?php foreach ($sessions as $session): ?>
                                <div class="schedule-card" style="background: white;">
                                    <div class="schedule-detail" style="font-weight: 600; color: var(--primary); margin-bottom: 0.5rem;">
                                        🕒 <?php echo formatTime($session['HEURE_DEB']) . ' - ' . formatTime($session['HEURE_FIN']); ?>
                                    </div>
                                    <div class="course-name" style="font-size: 1.1rem; margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars($session['NOM_COURS'] ?: 'Unknown Course'); ?>
                                    </div>
                                    <div class="schedule-detail">
                                        🎓 Classe : <?php echo htmlspecialchars($session['NUMERO_CLASSE'] ?: 'Unknown Class'); ?>
                                    </div>
                                    <div class="schedule-detail">
                                        🚪 Salle : <?php echo htmlspecialchars($session['NOM_SALLE'] ?: 'N/A'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Zone de vue hebdomadaire -->
            <div id="view-week" class="week-view glass-panel" style="padding: 2rem;">
                <div class="timetable-container">
                    <table class="timetable-grid">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Heure</th>
                                <?php foreach (array_keys($scheduleByDay) as $day): ?>
                                    <th><?php echo htmlspecialchars($day); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timeSlots as $slotIndex => $timeSlot): ?>
                                <?php list($startStr, $endStr) = explode(' - ', $timeSlot); ?>
                                <tr>
                                    <td style="padding: 1rem 0; font-weight: 500; color: var(--text-muted); font-size: 0.875rem;">
                                        <?php echo formatTime($startStr) . '<br>-<br>' . formatTime($endStr); ?>
                                    </td>
                                    <?php foreach ($scheduleByDay as $day => $sessions): ?>
                                        <td style="padding: 0.5rem;">
                                            <?php 
                                            $session = getSessionForSlot($sessions, $slotIndex);
                                            if ($session): 
                                            ?>
                                                <div class="schedule-card" style="background: white; border-top: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb;">
                                                    <div class="course-name" style="font-size: 0.8rem;">
                                                        <?php echo htmlspecialchars($session['NOM_COURS'] ?: 'Unknown'); ?>
                                                    </div>
                                                    <div class="schedule-detail">
                                                        Classe: <?php echo htmlspecialchars($session['NUMERO_CLASSE'] ?: 'Unknown'); ?>
                                                    </div>
                                                    <div class="schedule-detail">
                                                        Salle: <?php echo htmlspecialchars($session['NOM_SALLE'] ?: 'N/A'); ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="empty-slot">Libre</div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        function switchView(view) {
            // Mettre à jour les boutons
            document.getElementById('btn-day').classList.remove('active');
            document.getElementById('btn-week').classList.remove('active');
            document.getElementById('btn-' + view).classList.add('active');

            // Mettre à jour les vues
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
