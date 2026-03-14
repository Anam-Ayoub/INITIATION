<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte des Salles — CHRONOS</title>
    <link rel="stylesheet" href="../assets/style.css?v=4">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .map-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: calc(100vh - 120px); /* Fill remaining space */
        }
        
        .toolbar {
            display: flex;
            gap: 15px;
            background: var(--bg-card);
            padding: 16px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            align-items: center;
        }

        .tool-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: 1px solid var(--border);
            background: var(--bg-primary);
            color: var(--text-secondary);
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .tool-btn:hover {
            background: var(--bg-glass-hover);
            color: var(--text-primary);
        }

        .tool-btn.active {
            background: var(--accent-glow);
            color: var(--accent-light);
            border-color: var(--border-accent);
        }

        .tool-btn.save-btn {
            margin-left: auto;
            background: var(--success-bg);
            color: var(--success);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .tool-btn.save-btn:hover {
            background: rgba(16, 185, 129, 0.15);
        }

        .map-grid-wrapper {
            flex: 1;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            overflow: auto;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* The Grid layout */
        .map-grid {
            display: grid;
            /* 20x20 grid, 40px cells */
            grid-template-columns: repeat(30, 40px);
            grid-template-rows: repeat(20, 40px);
            gap: 1px;
            background: var(--border); /* Grid lines color */
            border: 1px solid var(--border);
            user-select: none;
        }

        .cell {
            background: var(--bg-secondary);
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: var(--text-primary);
            transition: filter 0.1s;
        }

        .cell:hover {
            filter: brightness(0.95);
        }

        /* Cell Types */
        .cell[data-type="road"] {
            background: #e2e8f0; /* Light grey for roads */
        }

        .cell[data-type="entrance"] {
            background: var(--success-bg);
            border: 2px solid var(--success);
            color: var(--success);
            border-radius: 4px;
            font-size: 8px;
        }

        .cell[data-type="classroom"] {
            background: var(--accent-glow);
            border: 2px solid var(--accent);
            color: var(--accent-light);
            border-radius: 4px;
        }

        /* Custom Modal for Classroom Name */
        #nameModal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: var(--bg-card);
            padding: 30px;
            border-radius: var(--radius-lg);
            width: 350px;
            box-shadow: var(--shadow-lg);
        }
        .modal-content h3 { margin-bottom: 15px; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
    </style>
</head>
<body>
    <?php $current_page = 'carte'; include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2>Carte des Salles</h2>
                <div class="subtitle">Dessinez votre établissement et ajoutez les salles</div>
            </div>
            <div class="admin-badge">Admin: <?php echo htmlspecialchars($_SESSION['admin']); ?></div>
        </div>

        <div id="alert-container"></div>

        <div class="map-container">
            <div class="toolbar">
                <button class="tool-btn active" data-tool="road">🛣️ Route</button>
                <button class="tool-btn" data-tool="classroom">🚪 Salle</button>
                <button class="tool-btn" data-tool="entrance">🏛️ Entrée</button>
                <button class="tool-btn" data-tool="eraser">🧹 Gomme</button>
                
                <button class="tool-btn save-btn" id="saveBtn">💾 Sauvegarder la carte</button>
            </div>

            <div class="map-grid-wrapper">
                <div class="map-grid" id="mapGrid">
                    <!-- Grid cells spawned via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Classroom Name -->
    <div id="nameModal">
        <div class="modal-content">
            <h3>Nom de la Salle</h3>
            <input type="text" id="classroomNameInput" placeholder="Ex: Salle 12, Amphi A..." class="form-group" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm);">
            <div class="modal-actions">
                <button class="btn-primary" style="background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border);" onclick="closeModal()">Annuler</button>
                <button class="btn-primary" onclick="confirmClassroom()">Ajouter</button>
            </div>
        </div>
    </div>

    <script>
        const grid = document.getElementById('mapGrid');
        const cols = 30;
        const rows = 20;
        let currentTool = 'road';
        let isDrawing = false;
        
        // Modal state
        let pendingCell = null;

        // Initialize empty grid
        for (let i = 0; i < cols * rows; i++) {
            const cell = document.createElement('div');
            cell.classList.add('cell');
            cell.dataset.index = i;
            cell.dataset.type = 'empty';
            
            // Mouse events for drawing
            cell.addEventListener('mousedown', (e) => startDrawing(e, cell));
            cell.addEventListener('mouseenter', (e) => continueDrawing(e, cell));
            
            grid.appendChild(cell);
        }

        // Global mouse up to stop drawing
        document.addEventListener('mouseup', () => isDrawing = false);

        // Tool selection
        document.querySelectorAll('.tool-btn[data-tool]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelector('.tool-btn.active').classList.remove('active');
                btn.classList.add('active');
                currentTool = btn.dataset.tool;
            });
        });

        // Drawing logic
        function startDrawing(e, cell) {
            isDrawing = true;
            applyTool(cell);
        }

        function continueDrawing(e, cell) {
            if (isDrawing) applyTool(cell);
        }

        function applyTool(cell) {
            if (currentTool === 'road') {
                if (cell.dataset.type === 'classroom') removeClassroom(cell);
                cell.dataset.type = 'road';
                cell.textContent = '';
            } else if (currentTool === 'entrance') {
                if (cell.dataset.type === 'classroom') removeClassroom(cell);
                cell.dataset.type = 'entrance';
                cell.textContent = 'ENTRÉE';
            } else if (currentTool === 'eraser') {
                if (cell.dataset.type === 'classroom') removeClassroom(cell);
                cell.dataset.type = 'empty';
                cell.textContent = '';
            } else if (currentTool === 'classroom' && cell.dataset.type !== 'classroom') {
                // Open modal for classroom name
                isDrawing = false; // Stop dragging for classrooms
                pendingCell = cell;
                document.getElementById('nameModal').style.display = 'flex';
                document.getElementById('classroomNameInput').focus();
            }
        }

        function closeModal() {
            document.getElementById('nameModal').style.display = 'none';
            document.getElementById('classroomNameInput').value = '';
            pendingCell = null;
        }

        function confirmClassroom() {
            const name = document.getElementById('classroomNameInput').value.trim();
            if (!name) return;

            // Call API to ensure it's in DB
            fetch('api_carte.php?action=add_salle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nom: name })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    pendingCell.dataset.type = 'classroom';
                    pendingCell.dataset.name = name;
                    pendingCell.dataset.id = data.id_salle; // Store DB ID
                    pendingCell.textContent = name;
                    closeModal();
                } else {
                    alert("Erreur: " + data.error);
                }
            });
        }

        function removeClassroom(cell) {
            const name = cell.dataset.name;
            if (name) {
                // Call API to remove from DB if we want, but usually we just want to remove from map visually
                // Only delete if user explicitly erases it. Let's delete it from DB for true sync.
                fetch('api_carte.php?action=delete_salle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nom: name })
                });
            }
            cell.dataset.type = 'empty';
            cell.removeAttribute('data-name');
            cell.removeAttribute('data-id');
            cell.textContent = '';
        }

        // Save layout
        document.getElementById('saveBtn').addEventListener('click', () => {
            const cells = Array.from(document.querySelectorAll('.cell'));
            const mapData = cells.map(c => ({
                index: c.dataset.index,
                type: c.dataset.type,
                name: c.dataset.name || null,
                id: c.dataset.id || null
            }));

            fetch('api_carte.php?action=save_map', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ grid_data: JSON.stringify(mapData) })
            })
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('alert-container');
                if (data.success) {
                    container.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                } else {
                    container.innerHTML = `<div class="alert alert-error">${data.error}</div>`;
                }
                setTimeout(() => container.innerHTML = '', 3000);
            });
        });

        // Load layout on page load
        window.addEventListener('load', () => {
            fetch('api_carte.php?action=get_map')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data !== '{}') {
                    const parsedData = JSON.parse(data.data);
                    parsedData.forEach(cellData => {
                        const cell = document.querySelector(`.cell[data-index="${cellData.index}"]`);
                        if (cell) {
                            cell.dataset.type = cellData.type;
                            if (cellData.type === 'classroom') {
                                cell.dataset.name = cellData.name;
                                cell.dataset.id = cellData.id;
                                cell.textContent = cellData.name;
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
