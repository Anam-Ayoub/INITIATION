<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../config/functions.php";

$classes = $pdo->query("SELECT * FROM CLASSE ORDER BY NUMERO")->fetchAll();
$profs   = $pdo->query("SELECT * FROM PROF ORDER BY NOM_PROF")->fetchAll();
$salles  = $pdo->query("SELECT * FROM SALLE ORDER BY NOM_SALLE")->fetchAll();
$cours   = $pdo->query("SELECT * FROM COURS ORDER BY NOM_COURS")->fetchAll();

$days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$phases = [
    ['label' => '08:30 - 10:30', 'hd' => '08:30', 'hf' => '10:30'],
    ['label' => '10:45 - 12:30', 'hd' => '10:45', 'hf' => '12:30'],
    ['label' => '14:00 - 16:00', 'hd' => '14:00', 'hf' => '16:00'],
    ['label' => '16:15 - 18:15', 'hd' => '16:15', 'hf' => '18:15']
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une séance — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=5">
    <style>
        .grid-container {
            margin-top: 30px;
            overflow-x: auto;
        }
        .timetable-grid {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        .timetable-grid th, .timetable-grid td {
            border: 1px solid var(--border);
            padding: 15px;
            text-align: center;
            min-width: 150px;
        }
        .timetable-grid th {
            background: var(--bg-glass);
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .day-cell {
            font-weight: 700;
            color: var(--accent-light);
            background: var(--bg-glass);
            width: 120px;
            min-width: 120px;
        }
        .slot-cell {
            cursor: pointer;
            transition: all var(--transition-fast);
            position: relative;
            height: 100px;
        }
        .slot-cell:hover {
            background: var(--bg-glass-hover);
        }
        .slot-cell.assigned {
            background: var(--success-bg);
            border-color: rgba(16, 185, 129, 0.2);
        }
        .slot-cell.assigned:hover {
            filter: brightness(0.95);
        }
        .break-cell {
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 0.75rem;
            font-style: italic;
            width: 80px;
            min-width: 80px;
        }
        .session-info {
            font-size: 0.85rem;
            color: var(--success);
        }
        .session-info .course { font-weight: 700; display: block; }
        .session-info .prof { font-size: 0.75rem; color: var(--text-secondary); }
        .session-info .room { font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 4px;}

        /* Modal Styles */
        #sessionModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background: var(--bg-card);
            width: 450px;
            padding: 30px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            animation: fadeIn 0.3s ease;
        }
        .modal-header {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }
        .modal-footer {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn-delete-session {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php $current_page = 'add'; include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Gestion de l'Emploi du Temps</h2>
                <p class="subtitle">Sélectionnez une classe pour gérer ses séances</p>
            </div>
        </div>

        <div class="filter-section">
            <label><strong>Classe :</strong></label>
            <select id="classSelect" style="margin-left:10px; width: 300px;">
                <option value="">— Sélectionner une classe —</option>
                <?php foreach($classes as $c): ?>
                    <option value="<?= $c['ID_CLASSE'] ?>"><?= htmlspecialchars($c['NUMERO']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="alertContainer"></div>

        <div class="grid-container" id="gridContainer" style="display:none;">
            <table class="timetable-grid">
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>08:30 - 10:30</th>
                        <th>10:45 - 12:30</th>
                        <th class="break-cell">Pause</th>
                        <th>14:00 - 16:00</th>
                        <th>16:15 - 18:15</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($days as $day): ?>
                    <tr>
                        <td class="day-cell"><?= $day ?></td>
                        <td class="slot-cell" data-day="<?= $day ?>" data-phase="0"></td>
                        <td class="slot-cell" data-day="<?= $day ?>" data-phase="1"></td>
                        <td class="break-cell">12:30 - 14:00</td>
                        <td class="slot-cell" data-day="<?= $day ?>" data-phase="2"></td>
                        <td class="slot-cell" data-day="<?= $day ?>" data-phase="3"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Session Modal -->
    <div id="sessionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Détails de la séance</h3>
                <p id="modalSubtitle" class="subtitle"></p>
            </div>
            <form id="sessionForm">
                <input type="hidden" id="modalIdEmploi" name="id_emploi">
                <input type="hidden" id="modalJour" name="jour">
                <input type="hidden" id="modalHd" name="hd">
                <input type="hidden" id="modalHf" name="hf">

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Cours</label>
                    <select name="id_cours" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach($cours as $co): ?>
                            <option value="<?= $co['ID_COURS'] ?>"><?= htmlspecialchars($co['NOM_COURS']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Professeur</label>
                    <select name="id_prof" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach($profs as $p): ?>
                            <option value="<?= $p['ID_PROF'] ?>"><?= htmlspecialchars($p['NOM_PROF']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Salle</label>
                    <select name="id_salle" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach($salles as $s): ?>
                            <option value="<?= $s['ID_SALLE'] ?>"><?= htmlspecialchars($s['NOM_SALLE']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-delete-session" id="deleteBtn" style="display:none;">Supprimer</button>
                    <button type="button" class="btn-primary" style="background:var(--bg-glass); color:var(--text-primary); border:1px solid var(--border);" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const classSelect = document.getElementById('classSelect');
        const gridContainer = document.getElementById('gridContainer');
        const sessionModal = document.getElementById('sessionModal');
        const sessionForm = document.getElementById('sessionForm');
        const alertContainer = document.getElementById('alertContainer');
        
        const phases = <?= json_encode($phases) ?>;
        let currentSessions = [];

        classSelect.addEventListener('change', function() {
            if (this.value) {
                gridContainer.style.display = 'block';
                loadSessions(this.value);
            } else {
                gridContainer.style.display = 'none';
            }
        });

        function loadSessions(idClasse) {
            fetch(`api_sessions.php?action=get_sessions&id_classe=${idClasse}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentSessions = data.sessions;
                        renderGrid();
                    }
                });
        }

        function renderGrid() {
            document.querySelectorAll('.slot-cell').forEach(cell => {
                const day = cell.dataset.day;
                const phaseIdx = cell.dataset.phase;
                const phase = phases[phaseIdx];
                
                cell.classList.remove('assigned');
                cell.innerHTML = '';
                cell.onclick = () => openModal(day, phaseIdx);

                const session = currentSessions.find(s => s.JOUR === day && s.hd === phase.hd);
                if (session) {
                    cell.classList.add('assigned');
                    cell.innerHTML = `
                        <div class="session-info">
                            <span class="course">${session.NOM_COURS}</span>
                            <span class="prof">${session.NOM_PROF}</span>
                            <span class="room">🏢 ${session.NOM_SALLE}</span>
                        </div>
                    `;
                    cell.onclick = () => openModal(day, phaseIdx, session);
                }
            });
        }

        function openModal(day, phaseIdx, session = null) {
            const phase = phases[phaseIdx];
            document.getElementById('modalJour').value = day;
            document.getElementById('modalHd').value = phase.hd;
            document.getElementById('modalHf').value = phase.hf;
            document.getElementById('modalSubtitle').textContent = `${day} | ${phase.label}`;
            
            if (session) {
                document.getElementById('modalTitle').textContent = "Modifier la séance";
                document.getElementById('modalIdEmploi').value = session.ID_EMPLOI;
                sessionForm.id_cours.value = session.ID_COURS;
                sessionForm.id_prof.value = session.ID_PROF;
                sessionForm.id_salle.value = session.ID_SALLE;
                document.getElementById('deleteBtn').style.display = 'block';
                document.getElementById('deleteBtn').onclick = () => deleteSession(session.ID_EMPLOI);
            } else {
                document.getElementById('modalTitle').textContent = "Nouvelle séance";
                document.getElementById('modalIdEmploi').value = "";
                sessionForm.reset();
                document.getElementById('deleteBtn').style.display = 'none';
            }
            
            sessionModal.style.display = 'flex';
        }

        function closeModal() {
            sessionModal.style.display = 'none';
        }

        sessionForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(sessionForm);
            const data = Object.fromEntries(formData.entries());
            data.id_classe = classSelect.value;

            fetch('api_sessions.php?action=save_session', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Séance enregistrée avec succès');
                    closeModal();
                    loadSessions(classSelect.value);
                } else {
                    showAlert('error', data.message);
                }
            });
        };

        function deleteSession(idEmploi) {
            if (!confirm('Voulez-vous vraiment supprimer cette séance ?')) return;

            fetch('api_sessions.php?action=delete_session', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_emploi: idEmploi })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Séance supprimée');
                    closeModal();
                    loadSessions(classSelect.value);
                } else {
                    showAlert('error', data.message);
                }
            });
        }

        function showAlert(type, message) {
            alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => alertContainer.innerHTML = '', 3000);
        }
    </script>
</body>
</html>
