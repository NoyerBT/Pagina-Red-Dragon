<?php
session_start();
require_once 'cnt/conexion.php';

// Verificar que el usuario accedió mediante el panel de administración
if (!isset($_SESSION['admin_usuario'])) {
    header('Location: admin/index.php');
    exit();
}

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Añadir equipo
    if ($accion === 'agregar_equipo') {
        $nombre = $conn->real_escape_string($_POST['nombre_equipo']);
        $seed = intval($_POST['seed']);
        $sql = "INSERT INTO equipos (nombre, seed) VALUES ('$nombre', $seed)";
        $conn->query($sql);
    }
    
    // Editar equipo
    if ($accion === 'editar_equipo') {
        $id = intval($_POST['equipo_id']);
        $nombre = $conn->real_escape_string($_POST['nombre_equipo']);
        $seed = intval($_POST['seed']);
        $sql = "UPDATE equipos SET nombre='$nombre', seed=$seed WHERE id=$id";
        $conn->query($sql);
    }
    
    // Eliminar equipo
    if ($accion === 'eliminar_equipo') {
        $id = intval($_POST['equipo_id']);
        $sql = "DELETE FROM equipos WHERE id=$id";
        $conn->query($sql);
    }
    
    // Actualizar puntajes de match
    if ($accion === 'actualizar_match') {
        $match_id = intval($_POST['match_id']);
        $puntos1 = intval($_POST['puntos_equipo1']);
        $puntos2 = intval($_POST['puntos_equipo2']);
        
        // Determinar ganador
        $ganador_id = 'NULL';
        if ($puntos1 > $puntos2 && isset($_POST['equipo1_id'])) {
            $ganador_id = intval($_POST['equipo1_id']);
        } elseif ($puntos2 > $puntos1 && isset($_POST['equipo2_id'])) {
            $ganador_id = intval($_POST['equipo2_id']);
        }
        
        $sql = "UPDATE matches SET 
                puntos_equipo1=$puntos1, 
                puntos_equipo2=$puntos2, 
                ganador_id=$ganador_id,
                completado=1 
                WHERE id=$match_id";
        $conn->query($sql);
    }
    
    // Generar matches automáticamente
    if ($accion === 'generar_ronda1') {
        // Obtener equipos ordenados por seed
        $result = $conn->query("SELECT id FROM equipos WHERE activo=1 ORDER BY seed ASC");
        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = $row['id'];
        }
        
        // Crear matches de la ronda 1 (Winners Bracket)
        $match_num = 1;
        for ($i = 0; $i < count($equipos); $i += 2) {
            if (isset($equipos[$i]) && isset($equipos[$i + 1])) {
                $eq1 = $equipos[$i];
                $eq2 = $equipos[$i + 1];
                $sql = "INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                        VALUES ('winners', 1, $match_num, $eq1, $eq2)";
                $conn->query($sql);
                $match_num++;
            }
        }
    }
    
    header('Location: admin_brackets.php');
    exit();
}

// Obtener todos los equipos
$equipos_query = $conn->query("SELECT * FROM equipos ORDER BY seed ASC");
$equipos = [];
while ($row = $equipos_query->fetch_assoc()) {
    $equipos[] = $row;
}

// Obtener todos los matches
$matches_query = $conn->query("
    SELECT m.*, 
           e1.nombre as equipo1_nombre, 
           e2.nombre as equipo2_nombre,
           eg.nombre as ganador_nombre
    FROM matches m
    LEFT JOIN equipos e1 ON m.equipo1_id = e1.id
    LEFT JOIN equipos e2 ON m.equipo2_id = e2.id
    LEFT JOIN equipos eg ON m.ganador_id = eg.id
    ORDER BY m.bracket_tipo, m.ronda, m.numero_match
");
$matches = [];
while ($row = $matches_query->fetch_assoc()) {
    $matches[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Brackets</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(20, 20, 20, 0.9);
            border-radius: 15px;
            border: 2px solid #d4af37;
        }
        
        .admin-section {
            margin-bottom: 3rem;
            padding: 1.5rem;
            background: rgba(30, 30, 30, 0.8);
            border-radius: 10px;
            border: 1px solid #555;
        }
        
        .admin-section h2 {
            color: #d4af37;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            color: #d0d0d0;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(40, 40, 40, 0.9);
            border: 2px solid #555;
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
        }
        
        .btn-admin {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #d4af37, #c09b2d);
            color: #000;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
        }
        
        .equipos-table, .matches-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .equipos-table th, .matches-table th {
            background: rgba(212, 175, 55, 0.2);
            color: #d4af37;
            padding: 1rem;
            text-align: left;
            border: 1px solid #555;
        }
        
        .equipos-table td, .matches-table td {
            padding: 0.8rem;
            border: 1px solid #555;
            color: #d0d0d0;
        }
        
        .equipos-table tr:hover, .matches-table tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }
        
        .score-winner {
            color: #3498db !important;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .score-loser {
            color: #e74c3c !important;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .match-form {
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .match-form input {
            width: 60px;
            padding: 0.4rem;
            text-align: center;
        }
        
        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: rgba(30, 30, 30, 0.98);
            padding: 2rem;
            border-radius: 10px;
            border: 2px solid #d4af37;
            max-width: 500px;
            width: 90%;
        }
        
        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        
        .badge-winners {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }
        
        .badge-losers {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    
    <header class="top-bar">
        <nav class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="torneo.php">Torneo</a>
            <a href="brackets.php">Brackets</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="admin_brackets.php" style="color: #d4af37;">Admin Brackets</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <div class="admin-container">
        <h1 style="color: #d4af37; text-align: center; margin-bottom: 2rem;">
            Panel de Administración de Brackets
        </h1>

        <!-- Sección: Gestión de Equipos -->
        <div class="admin-section">
            <h2>📋 Gestión de Equipos</h2>
            
            <form method="POST" style="margin-bottom: 2rem;">
                <input type="hidden" name="accion" value="agregar_equipo">
                <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Nombre del Equipo</label>
                        <input type="text" name="nombre_equipo" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Seed (Posición)</label>
                        <input type="number" name="seed" required min="1" max="48">
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn-admin">➕ Agregar Equipo</button>
                    </div>
                </div>
            </form>

            <table class="equipos-table">
                <thead>
                    <tr>
                        <th>Seed</th>
                        <th>Nombre del Equipo</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><strong>#<?php echo $equipo['seed']; ?></strong></td>
                        <td><?php echo htmlspecialchars($equipo['nombre']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($equipo['fecha_registro'])); ?></td>
                        <td>
                            <button onclick="editarEquipo(<?php echo $equipo['id']; ?>, '<?php echo htmlspecialchars($equipo['nombre']); ?>', <?php echo $equipo['seed']; ?>)" 
                                    class="btn-admin btn-edit btn-small">✏️ Editar</button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este equipo?');">
                                <input type="hidden" name="accion" value="eliminar_equipo">
                                <input type="hidden" name="equipo_id" value="<?php echo $equipo['id']; ?>">
                                <button type="submit" class="btn-admin btn-danger btn-small">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="POST" style="margin-top: 2rem;">
                <input type="hidden" name="accion" value="generar_ronda1">
                <button type="submit" class="btn-admin" onclick="return confirm('¿Generar matches de Ronda 1 con los equipos actuales?');">
                    🎲 Generar Matches de Ronda 1
                </button>
            </form>
        </div>

        <!-- Sección: Gestión de Matches -->
        <div class="admin-section">
            <h2>🎮 Gestión de Matches y Puntajes</h2>
            
            <table class="matches-table">
                <thead>
                    <tr>
                        <th>Bracket</th>
                        <th>Ronda</th>
                        <th>Match #</th>
                        <th>Equipo 1</th>
                        <th>Puntos</th>
                        <th>VS</th>
                        <th>Puntos</th>
                        <th>Equipo 2</th>
                        <th>Ganador</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                    <tr>
                        <td>
                            <span class="badge <?php echo $match['bracket_tipo'] === 'winners' ? 'badge-winners' : 'badge-losers'; ?>">
                                <?php echo strtoupper($match['bracket_tipo']); ?>
                            </span>
                        </td>
                        <td>R<?php echo $match['ronda']; ?></td>
                        <td><strong>#<?php echo $match['numero_match']; ?></strong></td>
                        <td><?php echo htmlspecialchars($match['equipo1_nombre'] ?? 'TBD'); ?></td>
                        <td>
                            <span class="<?php 
                                if ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null) {
                                    echo $match['puntos_equipo1'] > $match['puntos_equipo2'] ? 'score-winner' : 'score-loser';
                                }
                            ?>">
                                <?php echo $match['puntos_equipo1'] ?? '-'; ?>
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">VS</td>
                        <td>
                            <span class="<?php 
                                if ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null) {
                                    echo $match['puntos_equipo2'] > $match['puntos_equipo1'] ? 'score-winner' : 'score-loser';
                                }
                            ?>">
                                <?php echo $match['puntos_equipo2'] ?? '-'; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($match['equipo2_nombre'] ?? 'TBD'); ?></td>
                        <td style="color: #2ecc71; font-weight: bold;">
                            <?php echo htmlspecialchars($match['ganador_nombre'] ?? '-'); ?>
                        </td>
                        <td>
                            <button onclick="actualizarMatch(<?php echo $match['id']; ?>, '<?php echo htmlspecialchars($match['equipo1_nombre'] ?? ''); ?>', '<?php echo htmlspecialchars($match['equipo2_nombre'] ?? ''); ?>', <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>, <?php echo $match['puntos_equipo1'] ?? 0; ?>, <?php echo $match['puntos_equipo2'] ?? 0; ?>)" 
                                    class="btn-admin btn-small">📊 Actualizar Puntaje</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Editar Equipo -->
    <div id="modalEditarEquipo" class="modal">
        <div class="modal-content">
            <h2 style="color: #d4af37; margin-bottom: 1.5rem;">✏️ Editar Equipo</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="editar_equipo">
                <input type="hidden" name="equipo_id" id="edit_equipo_id">
                <div class="form-group">
                    <label>Nombre del Equipo</label>
                    <input type="text" name="nombre_equipo" id="edit_nombre_equipo" required>
                </div>
                <div class="form-group">
                    <label>Seed (Posición)</label>
                    <input type="number" name="seed" id="edit_seed" required min="1" max="48">
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-admin">💾 Guardar</button>
                    <button type="button" onclick="cerrarModal('modalEditarEquipo')" class="btn-admin btn-danger">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Actualizar Match -->
    <div id="modalActualizarMatch" class="modal">
        <div class="modal-content">
            <h2 style="color: #d4af37; margin-bottom: 1.5rem;">📊 Actualizar Puntaje del Match</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="actualizar_match">
                <input type="hidden" name="match_id" id="match_id">
                <input type="hidden" name="equipo1_id" id="match_equipo1_id">
                <input type="hidden" name="equipo2_id" id="match_equipo2_id">
                
                <div class="form-group">
                    <label id="label_equipo1">Equipo 1</label>
                    <input type="number" name="puntos_equipo1" id="match_puntos1" required min="0">
                </div>
                
                <div class="form-group">
                    <label id="label_equipo2">Equipo 2</label>
                    <input type="number" name="puntos_equipo2" id="match_puntos2" required min="0">
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-admin">💾 Guardar Puntaje</button>
                    <button type="button" onclick="cerrarModal('modalActualizarMatch')" class="btn-admin btn-danger">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editarEquipo(id, nombre, seed) {
            document.getElementById('edit_equipo_id').value = id;
            document.getElementById('edit_nombre_equipo').value = nombre;
            document.getElementById('edit_seed').value = seed;
            document.getElementById('modalEditarEquipo').classList.add('active');
        }

        function actualizarMatch(matchId, equipo1, equipo2, equipo1Id, equipo2Id, puntos1, puntos2) {
            document.getElementById('match_id').value = matchId;
            document.getElementById('match_equipo1_id').value = equipo1Id;
            document.getElementById('match_equipo2_id').value = equipo2Id;
            document.getElementById('label_equipo1').textContent = equipo1 || 'Equipo 1';
            document.getElementById('label_equipo2').textContent = equipo2 || 'Equipo 2';
            document.getElementById('match_puntos1').value = puntos1;
            document.getElementById('match_puntos2').value = puntos2;
            document.getElementById('modalActualizarMatch').classList.add('active');
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModal(this.id);
                }
            });
        });
    </script>
</body>
</html>
