<?php
session_start();
require_once '../cnt/conexion.php';

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
    
    // Eliminar match
    if ($accion === 'eliminar_match') {
        $match_id = intval($_POST['match_id']);
        $sql = "DELETE FROM matches WHERE id=$match_id";
        $conn->query($sql);
    }
    
    // Crear match manual
    if ($accion === 'crear_match_manual') {
        $bracket_tipo = $_POST['bracket_tipo'];
        $ronda = intval($_POST['ronda']);
        $equipo1_id = !empty($_POST['equipo1_id']) ? intval($_POST['equipo1_id']) : null;
        $equipo2_id = !empty($_POST['equipo2_id']) ? intval($_POST['equipo2_id']) : null;
        
        // Validar que no exista ya un match con estos equipos en esta ronda
        $duplicado = false;
        if ($equipo1_id && $equipo2_id) {
            $check = $conn->query("
                SELECT * FROM matches 
                WHERE bracket_tipo='$bracket_tipo' 
                AND ronda=$ronda 
                AND (
                    (equipo1_id=$equipo1_id AND equipo2_id=$equipo2_id) OR
                    (equipo1_id=$equipo2_id AND equipo2_id=$equipo1_id)
                )
            ");
            if ($check->num_rows > 0) {
                $duplicado = true;
            }
        }
        
        if (!$duplicado) {
            // Obtener el siguiente número de match para esa ronda
            $result = $conn->query("SELECT MAX(numero_match) as max_num FROM matches WHERE bracket_tipo='$bracket_tipo' AND ronda=$ronda");
            $row = $result->fetch_assoc();
            $numero_match = ($row['max_num'] ?? 0) + 1;
            
            $equipo1_sql = $equipo1_id ? $equipo1_id : 'NULL';
            $equipo2_sql = $equipo2_id ? $equipo2_id : 'NULL';
            
            $sql = "INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                    VALUES ('$bracket_tipo', $ronda, $numero_match, $equipo1_sql, $equipo2_sql)";
            $conn->query($sql);
        }
    }
    
    // Editar equipos de un match
    if ($accion === 'editar_match_equipos') {
        $match_id = intval($_POST['match_id']);
        $equipo1_id = !empty($_POST['equipo1_id']) ? intval($_POST['equipo1_id']) : 'NULL';
        $equipo2_id = !empty($_POST['equipo2_id']) ? intval($_POST['equipo2_id']) : 'NULL';
        
        $sql = "UPDATE matches SET equipo1_id=$equipo1_id, equipo2_id=$equipo2_id WHERE id=$match_id";
        $conn->query($sql);
    }
    
    // Actualizar puntajes de match CON AVANCE AUTOMÁTICO
    if ($accion === 'actualizar_match') {
        $match_id = intval($_POST['match_id']);
        $puntos1 = intval($_POST['puntos_equipo1']);
        $puntos2 = intval($_POST['puntos_equipo2']);
        $equipo1_id = !empty($_POST['equipo1_id']) ? intval($_POST['equipo1_id']) : null;
        $equipo2_id = !empty($_POST['equipo2_id']) ? intval($_POST['equipo2_id']) : null;
        
        // Obtener información del match actual
        $match_info = $conn->query("SELECT * FROM matches WHERE id=$match_id")->fetch_assoc();
        $bracket_actual = $match_info['bracket_tipo'];
        $ronda_actual = $match_info['ronda'];
        $numero_match_actual = $match_info['numero_match'];
        
        // Determinar ganador y perdedor
        $ganador_id = null;
        $perdedor_id = null;
        
        if ($equipo1_id && $equipo2_id) {
            if ($puntos1 > $puntos2) {
                $ganador_id = $equipo1_id;
                $perdedor_id = $equipo2_id;
            } elseif ($puntos2 > $puntos1) {
                $ganador_id = $equipo2_id;
                $perdedor_id = $equipo1_id;
            }
        }
        
        // Actualizar el match actual
        $ganador_sql = $ganador_id ? $ganador_id : 'NULL';
        $sql = "UPDATE matches SET 
                puntos_equipo1=$puntos1, 
                puntos_equipo2=$puntos2, 
                ganador_id=$ganador_sql,
                completado=1 
                WHERE id=$match_id";
        $conn->query($sql);
        
        // AVANCE AUTOMÁTICO: Si hay ganador, avanzarlo a la siguiente ronda
        if ($ganador_id) {
            $ronda_siguiente = $ronda_actual + 1;
            
            // Calcular en qué match de la siguiente ronda debe ir el ganador
            // Los matches se emparejan de 2 en 2: match 1 y 2 van al match 1, match 3 y 4 al match 2, etc.
            $numero_match_siguiente = ceil($numero_match_actual / 2);
            
            // Determinar si va a equipo1 o equipo2 del siguiente match
            // Si el número de match actual es impar (1,3,5...) → equipo1
            // Si es par (2,4,6...) → equipo2
            $es_equipo1 = ($numero_match_actual % 2 == 1);
            
            // Buscar si ya existe el match de la siguiente ronda
            $siguiente_match = $conn->query("
                SELECT * FROM matches 
                WHERE bracket_tipo='$bracket_actual' 
                AND ronda=$ronda_siguiente 
                AND numero_match=$numero_match_siguiente
            ")->fetch_assoc();
            
            if ($siguiente_match) {
                // El match ya existe, actualizar el equipo correspondiente
                if ($es_equipo1) {
                    $conn->query("UPDATE matches SET equipo1_id=$ganador_id WHERE id={$siguiente_match['id']}");
                } else {
                    $conn->query("UPDATE matches SET equipo2_id=$ganador_id WHERE id={$siguiente_match['id']}");
                }
            } else {
                // El match no existe, crearlo
                if ($es_equipo1) {
                    $conn->query("INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id) 
                                  VALUES ('$bracket_actual', $ronda_siguiente, $numero_match_siguiente, $ganador_id)");
                } else {
                    $conn->query("INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo2_id) 
                                  VALUES ('$bracket_actual', $ronda_siguiente, $numero_match_siguiente, $ganador_id)");
                }
            }
            
            // Si es Winners Bracket, enviar al perdedor al Losers Bracket
            if ($bracket_actual === 'winners' && $perdedor_id) {
                // Calcular la ronda del losers bracket
                // Ronda 1 de winners → Ronda 1 de losers
                // Ronda 2 de winners → Ronda 3 de losers
                // Ronda 3 de winners → Ronda 5 de losers
                $losers_ronda = ($ronda_actual == 1) ? 1 : (($ronda_actual - 1) * 2 + 1);
                
                // Contar cuántos matches ya existen en esa ronda del losers
                $count_result = $conn->query("SELECT COUNT(*) as total FROM matches WHERE bracket_tipo='losers' AND ronda=$losers_ronda");
                $count = $count_result->fetch_assoc()['total'];
                $losers_match_num = $count + 1;
                
                // Buscar si hay un match en losers con un slot vacío en esa ronda
                $losers_match_vacio = $conn->query("
                    SELECT * FROM matches 
                    WHERE bracket_tipo='losers' 
                    AND ronda=$losers_ronda 
                    AND (equipo1_id IS NULL OR equipo2_id IS NULL)
                    ORDER BY numero_match ASC
                    LIMIT 1
                ")->fetch_assoc();
                
                if ($losers_match_vacio) {
                    // Hay un match con slot vacío, agregar al perdedor ahí
                    if ($losers_match_vacio['equipo1_id'] === null) {
                        $conn->query("UPDATE matches SET equipo1_id=$perdedor_id WHERE id={$losers_match_vacio['id']}");
                    } else {
                        $conn->query("UPDATE matches SET equipo2_id=$perdedor_id WHERE id={$losers_match_vacio['id']}");
                    }
                } else {
                    // No hay match vacío, crear uno nuevo con el perdedor
                    $conn->query("INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id) 
                                  VALUES ('losers', $losers_ronda, $losers_match_num, $perdedor_id)");
                }
            }
        }
    }
    
    // Avanzar ganadores a siguiente ronda
    if ($accion === 'avanzar_ronda') {
        $bracket_tipo = $_POST['bracket_tipo'];
        $ronda_actual = intval($_POST['ronda_actual']);
        
        // Obtener todos los matches completados de la ronda actual
        $result = $conn->query("
            SELECT * FROM matches 
            WHERE bracket_tipo='$bracket_tipo' 
            AND ronda=$ronda_actual 
            AND completado=1 
            AND ganador_id IS NOT NULL
            ORDER BY numero_match ASC
        ");
        
        $ganadores = [];
        $perdedores = [];
        while ($row = $result->fetch_assoc()) {
            $ganadores[] = $row['ganador_id'];
            // Determinar perdedor
            if ($row['equipo1_id'] == $row['ganador_id']) {
                if ($row['equipo2_id']) $perdedores[] = $row['equipo2_id'];
            } else {
                if ($row['equipo1_id']) $perdedores[] = $row['equipo1_id'];
            }
        }
        
        // Crear matches de la siguiente ronda
        $ronda_siguiente = $ronda_actual + 1;
        $match_num = 1;
        
        // Avanzar ganadores al mismo bracket (siguiente ronda)
        for ($i = 0; $i < count($ganadores); $i += 2) {
            if (isset($ganadores[$i])) {
                $eq1 = $ganadores[$i];
                $eq2 = isset($ganadores[$i + 1]) ? $ganadores[$i + 1] : 'NULL';
                
                $sql = "INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                        VALUES ('$bracket_tipo', $ronda_siguiente, $match_num, $eq1, $eq2)";
                $conn->query($sql);
                $match_num++;
            }
        }
        
        // Si es Winners Bracket, enviar perdedores al Losers Bracket
        if ($bracket_tipo === 'winners' && count($perdedores) > 0) {
            // Calcular ronda del losers bracket (depende de la ronda actual del winners)
            $losers_ronda = ($ronda_actual == 1) ? 1 : (($ronda_actual - 1) * 2);
            
            // Obtener el último número de match en esa ronda del losers
            $result = $conn->query("SELECT MAX(numero_match) as max_num FROM matches WHERE bracket_tipo='losers' AND ronda=$losers_ronda");
            $row = $result->fetch_assoc();
            $match_num_losers = ($row['max_num'] ?? 0) + 1;
            
            // Crear matches en losers bracket
            for ($i = 0; $i < count($perdedores); $i += 2) {
                if (isset($perdedores[$i])) {
                    $eq1 = $perdedores[$i];
                    $eq2 = isset($perdedores[$i + 1]) ? $perdedores[$i + 1] : 'NULL';
                    
                    $sql = "INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                            VALUES ('losers', $losers_ronda, $match_num_losers, $eq1, $eq2)";
                    $conn->query($sql);
                    $match_num_losers++;
                }
            }
        }
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
    
    header('Location: brackets.php');
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

// Detectar equipos sin match asignado
$equipos_en_matches = [];
foreach ($matches as $match) {
    if ($match['equipo1_id']) $equipos_en_matches[] = $match['equipo1_id'];
    if ($match['equipo2_id']) $equipos_en_matches[] = $match['equipo2_id'];
}
$equipos_en_matches = array_unique($equipos_en_matches);

$equipos_sin_match = [];
foreach ($equipos as $equipo) {
    if (!in_array($equipo['id'], $equipos_en_matches)) {
        $equipos_sin_match[] = $equipo;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Brackets</title>
    <link rel="stylesheet" href="../styles.css">
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
            <a href="../index.php">Inicio</a>
            <a href="../torneo.php">Torneo</a>
            <a href="../brackets.php">Brackets</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="brackets.php" style="color: #d4af37;">Admin Brackets</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <div class="admin-container">
        <h1 style="color: #d4af37; text-align: center; margin-bottom: 2rem;">
            Panel de Administración de Brackets
        </h1>

        <?php if (count($equipos_sin_match) > 0): ?>
        <!-- Alerta: Equipos sin Match -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(243, 156, 18, 0.15); border: 2px solid #f39c12; border-radius: 10px;">
            <h3 style="color: #f39c12; margin-top: 0; display: flex; align-items: center; gap: 0.5rem;">
                ⚠️ Equipos sin Match Asignado (<?php echo count($equipos_sin_match); ?>)
            </h3>
            <p style="color: #d0d0d0; margin-bottom: 1rem;">
                Los siguientes equipos <strong>NO</strong> están asignados a ningún match. 
                Usa el botón <strong>"✏️ Editar Match"</strong> para agregarlos manualmente a matches con slots TBD.
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <?php foreach ($equipos_sin_match as $equipo): ?>
                <span style="padding: 0.5rem 1rem; background: rgba(243, 156, 18, 0.2); border: 1px solid #f39c12; border-radius: 5px; color: #f39c12; font-weight: bold;">
                    #<?php echo $equipo['seed']; ?> - <?php echo htmlspecialchars($equipo['nombre']); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

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

            <form method="POST" style="margin-top: 2rem; display: flex; gap: 1rem;">
                <input type="hidden" name="accion" value="generar_ronda1">
                <button type="submit" class="btn-admin" onclick="return confirm('¿Generar matches de Ronda 1 con los equipos actuales?');">
                    🎲 Generar Matches de Ronda 1 (Automático)
                </button>
                <button type="button" onclick="abrirModalCrearMatch()" class="btn-admin" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                    ➕ Crear Match Manual
                </button>
            </form>
        </div>

        <!-- Sección: Gestión de Matches -->
        <div class="admin-section">
            <h2>🎮 Gestión de Matches y Puntajes</h2>
            
            <?php
            // Agrupar matches por bracket y ronda
            $matches_agrupados = [];
            foreach ($matches as $match) {
                $bracket = $match['bracket_tipo'];
                $ronda = $match['ronda'];
                if (!isset($matches_agrupados[$bracket])) {
                    $matches_agrupados[$bracket] = [];
                }
                if (!isset($matches_agrupados[$bracket][$ronda])) {
                    $matches_agrupados[$bracket][$ronda] = [];
                }
                $matches_agrupados[$bracket][$ronda][] = $match;
            }
            
            // Mostrar por bracket
            $bracket_nombres = [
                'winners' => '🏆 Winners Bracket',
                'losers' => '💔 Losers Bracket',
                'grand_final' => '👑 Gran Final'
            ];
            
            foreach ($matches_agrupados as $bracket_tipo => $rondas):
                $bracket_nombre = $bracket_nombres[$bracket_tipo] ?? $bracket_tipo;
            ?>
            
            <div style="margin-bottom: 3rem;">
                <h3 style="color: <?php echo $bracket_tipo === 'winners' ? '#2ecc71' : ($bracket_tipo === 'losers' ? '#e74c3c' : '#f39c12'); ?>; margin-bottom: 1.5rem; padding: 0.8rem; background: rgba(<?php echo $bracket_tipo === 'winners' ? '46, 204, 113' : ($bracket_tipo === 'losers' ? '231, 76, 60' : '243, 156, 18'); ?>, 0.1); border-radius: 5px; text-align: center;">
                    <?php echo $bracket_nombre; ?>
                </h3>
                
                <?php foreach ($rondas as $ronda_num => $matches_ronda): ?>
                
                <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(255, 255, 255, 0.02); border-radius: 8px;">
                    <h4 style="color: #d4af37; margin-bottom: 1rem;">
                        📍 Ronda <?php echo $ronda_num; ?> (<?php echo count($matches_ronda); ?> Matches)
                    </h4>
                    
                    <table class="matches-table">
                        <thead>
                            <tr>
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
                            <?php foreach ($matches_ronda as $match): ?>
                            <tr>
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
                                    <button onclick="editarMatchEquipos(<?php echo $match['id']; ?>, <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>)" 
                                            class="btn-admin btn-edit btn-small">✏️ Editar Match</button>
                                    <button onclick="actualizarMatch(<?php echo $match['id']; ?>, '<?php echo htmlspecialchars($match['equipo1_nombre'] ?? ''); ?>', '<?php echo htmlspecialchars($match['equipo2_nombre'] ?? ''); ?>', <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>, <?php echo $match['puntos_equipo1'] ?? 0; ?>, <?php echo $match['puntos_equipo2'] ?? 0; ?>)" 
                                            class="btn-admin btn-small">📊 Actualizar Puntaje</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ ¿Eliminar este match? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="accion" value="eliminar_match">
                                        <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                        <button type="submit" class="btn-admin btn-danger btn-small">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php endforeach; ?>
            </div>
            
            <?php endforeach; ?>
            
            <!-- Sección: Avanzar Rondas -->
            <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(46, 204, 113, 0.1); border: 2px solid #2ecc71; border-radius: 10px;">
                <h3 style="color: #2ecc71; margin-top: 0;">🎯 Avanzar Ganadores a Siguiente Ronda</h3>
                <p style="color: #d0d0d0; margin-bottom: 1.5rem;">
                    Selecciona el bracket y la ronda completada para avanzar automáticamente a los ganadores.
                    Los perdedores del Winners Bracket caerán al Losers Bracket.
                </p>
                
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <input type="hidden" name="accion" value="avanzar_ronda">
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Tipo de Bracket</label>
                        <select name="bracket_tipo" required style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                            <option value="winners">Winners Bracket</option>
                            <option value="losers">Losers Bracket</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Ronda Completada</label>
                        <input type="number" name="ronda_actual" required min="1" max="10" placeholder="Ej: 1" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                    </div>
                    
                    <div>
                        <button type="submit" class="btn-admin" style="background: linear-gradient(135deg, #2ecc71, #27ae60);" onclick="return confirm('¿Avanzar ganadores de esta ronda? Asegúrate de que todos los matches estén completados.');">
                            🎯 Avanzar Ganadores
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Crear Match Manual -->
    <div id="modalCrearMatch" class="modal">
        <div class="modal-content">
            <h2 style="color: #d4af37; margin-bottom: 1.5rem;">➕ Crear Match Manual</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="crear_match_manual">
                
                <div class="form-group">
                    <label>Tipo de Bracket</label>
                    <select name="bracket_tipo" required style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                        <option value="winners">Winners Bracket</option>
                        <option value="losers">Losers Bracket</option>
                        <option value="grand_final">Gran Final</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Ronda</label>
                    <input type="number" name="ronda" required min="1" max="10" value="1" placeholder="Ej: 1" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                </div>
                
                <div class="form-group">
                    <label>Equipo 1 (opcional)</label>
                    <select name="equipo1_id" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                        <option value="">-- Sin equipo (TBD) --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo['id']; ?>">
                                #<?php echo $equipo['seed']; ?> - <?php echo htmlspecialchars($equipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Equipo 2 (opcional)</label>
                    <select name="equipo2_id" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                        <option value="">-- Sin equipo (TBD) --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo['id']; ?>">
                                #<?php echo $equipo['seed']; ?> - <?php echo htmlspecialchars($equipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-admin">💾 Crear Match</button>
                    <button type="button" onclick="cerrarModal('modalCrearMatch')" class="btn-admin btn-danger">❌ Cancelar</button>
                </div>
            </form>
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

    <!-- Modal Editar Equipos del Match -->
    <div id="modalEditarMatchEquipos" class="modal">
        <div class="modal-content">
            <h2 style="color: #d4af37; margin-bottom: 1.5rem;">✏️ Editar Equipos del Match</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="editar_match_equipos">
                <input type="hidden" name="match_id" id="edit_match_id">
                
                <div class="form-group">
                    <label>Equipo 1</label>
                    <select name="equipo1_id" id="edit_match_equipo1" class="form-group input" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                        <option value="">-- Sin equipo (TBD) --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo['id']; ?>">
                                #<?php echo $equipo['seed']; ?> - <?php echo htmlspecialchars($equipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Equipo 2</label>
                    <select name="equipo2_id" id="edit_match_equipo2" class="form-group input" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
                        <option value="">-- Sin equipo (TBD) --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo['id']; ?>">
                                #<?php echo $equipo['seed']; ?> - <?php echo htmlspecialchars($equipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-admin">💾 Guardar Cambios</button>
                    <button type="button" onclick="cerrarModal('modalEditarMatchEquipos')" class="btn-admin btn-danger">❌ Cancelar</button>
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
        function abrirModalCrearMatch() {
            document.getElementById('modalCrearMatch').classList.add('active');
        }

        function editarEquipo(id, nombre, seed) {
            document.getElementById('edit_equipo_id').value = id;
            document.getElementById('edit_nombre_equipo').value = nombre;
            document.getElementById('edit_seed').value = seed;
            document.getElementById('modalEditarEquipo').classList.add('active');
        }

        function editarMatchEquipos(matchId, equipo1Id, equipo2Id) {
            document.getElementById('edit_match_id').value = matchId;
            document.getElementById('edit_match_equipo1').value = equipo1Id || '';
            document.getElementById('edit_match_equipo2').value = equipo2Id || '';
            document.getElementById('modalEditarMatchEquipos').classList.add('active');
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
