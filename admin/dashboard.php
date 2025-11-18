<?php
session_start();

if (!isset($_SESSION['admin_usuario'])) {
    header("Location: index.php");
    exit();
}

require_once '../cnt/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'agregar_equipo':
            $nombre = trim($_POST['nombre_equipo'] ?? '');
            $seed = intval($_POST['seed'] ?? 0);
            if ($nombre !== '' && $seed > 0) {
                $nombre = $conn->real_escape_string($nombre);
                if ($conn->query("INSERT INTO equipos (nombre, seed) VALUES ('$nombre', $seed)")) {
                    $_SESSION['admin_flash'] = 'Equipo agregado correctamente.';
                } else {
                    $_SESSION['admin_flash'] = 'No se pudo agregar el equipo. Verifica que el seed no esté repetido.';
                }

    .admin-alert {
      background: rgba(40, 167, 69, 0.15);
      border: 1px solid rgba(40, 167, 69, 0.4);
      color: #9fe2b1;
      padding: 0.75rem 1.25rem;
      border-radius: var(--border-radius);
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    .admin-modules {
      display: flex;
      flex-direction: column;
      gap: 2rem;
      margin-top: 3rem;
    }

    .admin-card {
      background: var(--card-bg);
      border-radius: var(--border-radius);
      padding: 1.5rem;
      box-shadow: var(--box-shadow);
      border: 1px solid rgba(212, 175, 55, 0.15);
    }

    .admin-card h2 {
      color: var(--primary);
      margin-top: 0;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      font-size: 1.4rem;
    }

    .admin-card form:not(.expiration-form) {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    .admin-card label {
      display: block;
      margin-bottom: 0.35rem;
      color: var(--text-muted);
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }

    .admin-card input[type="text"],
    .admin-card input[type="number"],
    .admin-card input[type="date"],
    .admin-card select {
      width: 100%;
      padding: 0.65rem;
      border-radius: 6px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(0, 0, 0, 0.3);
      color: var(--text-light);
      font-size: 0.95rem;
    }

    .admin-card button[type="submit"],
    .admin-card .btn {
      justify-self: start;
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      font-size: 0.95rem;
    }

    .admin-table th,
    .admin-table td {
      border: 1px solid rgba(255, 255, 255, 0.08);
      padding: 0.75rem;
      text-align: left;
    }

    .admin-table th {
      background: rgba(212, 175, 55, 0.12);
      color: var(--primary);
      font-weight: 600;
    }

    .admin-table tr:nth-child(every) {}

    .admin-table tr:nth-child(even) {
      background: rgba(255, 255, 255, 0.02);
    }

    .admin-actions {
      display: inline-flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .admin-actions form {
      display: inline;
    }

    .btn-small {
      padding: 0.35rem 0.75rem;
      font-size: 0.8rem;
    }

    .btn-ghost {
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: var(--text-light);
    }

    .tag {
      display: inline-block;
      padding: 0.2rem 0.7rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .tag-winners {
      background: rgba(46, 204, 113, 0.15);
      color: #2ecc71;
      border: 1px solid rgba(46, 204, 113, 0.4);
    }

    .tag-losers {
      background: rgba(231, 76, 60, 0.15);
      color: #e74c3c;
      border: 1px solid rgba(231, 76, 60, 0.4);
    }

    .score-winner {
      color: #2ecc71;
      font-weight: 600;
    }

    .score-loser {
      color: #e74c3c;
      font-weight: 600;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.75);
      align-items: center;
      justify-content: center;
      z-index: 2000;
      padding: 1rem;
    }

    .modal.active {
      display: flex;
    }

    .modal-content {
      background: #1c1c1c;
      border-radius: 10px;
      padding: 1.5rem;
      border: 1px solid rgba(212, 175, 55, 0.3);
      width: min(500px, 100%);
    }

    .modal-content h3 {
      margin-top: 0;
      color: var(--primary);
    }

    .modal-actions {
      display: flex;
      gap: 0.75rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }
            } else {
                $_SESSION['admin_flash'] = 'Nombre y seed son obligatorios.';
            }
            break;

        case 'editar_equipo':
            $id = intval($_POST['equipo_id'] ?? 0);
            $nombre = trim($_POST['nombre_equipo'] ?? '');
            $seed = intval($_POST['seed'] ?? 0);
            if ($id > 0 && $nombre !== '' && $seed > 0) {
                $nombre = $conn->real_escape_string($nombre);
                if ($conn->query("UPDATE equipos SET nombre='$nombre', seed=$seed WHERE id=$id")) {
                    $_SESSION['admin_flash'] = 'Equipo actualizado correctamente.';
                } else {
                    $_SESSION['admin_flash'] = 'No se pudo actualizar el equipo.';
                }
            } else {
                $_SESSION['admin_flash'] = 'Datos insuficientes para actualizar el equipo.';
            }
            break;

        case 'eliminar_equipo':
            $id = intval($_POST['equipo_id'] ?? 0);
            if ($id > 0) {
                if ($conn->query("DELETE FROM equipos WHERE id=$id")) {
                    $_SESSION['admin_flash'] = 'Equipo eliminado.';
                } else {
                    $_SESSION['admin_flash'] = 'No se pudo eliminar el equipo.';
                }
            }
            break;

        case 'actualizar_match':
            $match_id = intval($_POST['match_id'] ?? 0);
            $puntos1 = intval($_POST['puntos_equipo1'] ?? 0);
            $puntos2 = intval($_POST['puntos_equipo2'] ?? 0);
            $equipo1_id = isset($_POST['equipo1_id']) ? intval($_POST['equipo1_id']) : null;
            $equipo2_id = isset($_POST['equipo2_id']) ? intval($_POST['equipo2_id']) : null;

            if ($match_id > 0) {
                $ganador_id = 'NULL';
                if ($puntos1 > $puntos2 && $equipo1_id) {
                    $ganador_id = $equipo1_id;
                } elseif ($puntos2 > $puntos1 && $equipo2_id) {
                    $ganador_id = $equipo2_id;
                }

                $conn->query("UPDATE matches SET 
                                puntos_equipo1=$puntos1,
                                puntos_equipo2=$puntos2,
                                ganador_id=$ganador_id,
                                completado=1
                              WHERE id=$match_id");
                $_SESSION['admin_flash'] = 'Match actualizado.';
            }
            break;

        case 'generar_ronda1':
            $equipos_result = $conn->query("SELECT id FROM equipos WHERE activo=1 ORDER BY seed ASC");
            $equipos_ids = [];
            if ($equipos_result) {
                while ($row = $equipos_result->fetch_assoc()) {
                    $equipos_ids[] = intval($row['id']);
                }
            }

            $match_num = 1;
            for ($i = 0; $i < count($equipos_ids); $i += 2) {
                if (isset($equipos_ids[$i]) && isset($equipos_ids[$i + 1])) {
                    $eq1 = $equipos_ids[$i];
                    $eq2 = $equipos_ids[$i + 1];
                    $conn->query("INSERT INTO matches (bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                  VALUES ('winners', 1, $match_num, $eq1, $eq2)");
                    $match_num++;
                }
            }
            $_SESSION['admin_flash'] = 'Ronda 1 generada con los equipos actuales.';
            break;
    }

    header('Location: dashboard.php');
    exit();
}

// Fetch all non-admin users
$sql = "SELECT id, usuario, nombre, email, fecha_registro, estado, fecha_expiracion FROM usuarios";
$result = $conn->query($sql);

// Fetch equipos
$equipos = [];
$equipos_query = $conn->query("SELECT * FROM equipos ORDER BY seed ASC");
if ($equipos_query) {
    while ($row = $equipos_query->fetch_assoc()) {
        $equipos[] = $row;
    }
}

// Fetch matches
$matches = [];
$matches_query = $conn->query("SELECT m.*, 
                                      e1.nombre AS equipo1_nombre,
                                      e2.nombre AS equipo2_nombre,
                                      eg.nombre AS ganador_nombre
                               FROM matches m
                               LEFT JOIN equipos e1 ON m.equipo1_id = e1.id
                               LEFT JOIN equipos e2 ON m.equipo2_id = e2.id
                               LEFT JOIN equipos eg ON m.ganador_id = eg.id
                               ORDER BY m.bracket_tipo, m.ronda, m.numero_match");
if ($matches_query) {
    while ($row = $matches_query->fetch_assoc()) {
        $matches[] = $row;
    }
}

$admin_flash = $_SESSION['admin_flash'] ?? null;
if ($admin_flash) {
    unset($_SESSION['admin_flash']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Red Dragons Cup</title>
  <link rel="stylesheet" href="../styles.css" />
  <style>
    :root {
      --primary: #d4af37;
      --primary-dark: #b38f2a;
      --bg-dark: #121212;
      --card-bg: #1e1e1e;
      --text-light: #f5f5f5;
      --text-muted: #a0a0a0;
      --border-radius: 8px;
      --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    body {
      background-color: var(--bg-dark);
      color: var(--text-light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      margin: 0;
      padding: 0;
    }

    .top-bar {
      background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
      padding: 1rem 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
      margin-bottom: 2rem;
    }

    .nav-links {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .nav-links a {
      color: var(--text-light);
      text-decoration: none;
      padding: 0.5rem 1.5rem;
      border-radius: 20px;
      transition: var(--transition);
      font-weight: 500;
    }

    .nav-links a:last-child {
      background: var(--primary);
      color: #000;
      font-weight: 600;
    }

    .nav-links a:last-child:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .section {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1.5rem 3rem;
    }

    .page-title {
      color: var(--primary);
      margin-bottom: 2rem;
      font-size: 2rem;
      text-align: center;
      position: relative;
      padding-bottom: 1rem;
    }

    .page-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 3px;
      background: var(--primary);
      border-radius: 3px;
    }

    .users-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .user-card {
      background: var(--card-bg);
      border-radius: var(--border-radius);
      overflow: hidden;
      box-shadow: var(--box-shadow);
      transition: var(--transition);
      border: 1px solid rgba(212, 175, 55, 0.1);
    }

    .user-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
      border-color: rgba(212, 175, 55, 0.3);
    }

    .user-header {
      background: linear-gradient(135deg, #2a2a2a 0%, #1f1f1f 100%);
      padding: 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .user-name {
      margin: 0;
      color: var(--primary);
      font-size: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .user-email {
      margin: 0.25rem 0 0;
      color: var(--text-muted);
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .user-details {
      padding: 1.5rem;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.75rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .detail-row:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .detail-label {
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    .detail-value {
      font-weight: 500;
      text-align: right;
    }

    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-active {
      background: rgba(40, 167, 69, 0.2);
      color: #28a745;
    }

    .status-inactive {
      background: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }

    .action-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 4px;
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      gap: 0.5rem;
    }

    .btn i {
      font-size: 0.9em;
    }

    .btn-primary {
      background: var(--primary);
      color: #000;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      color: var(--text-light);
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .btn-danger {
      background: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }

    .btn-danger:hover {
      background: rgba(220, 53, 69, 0.3);
    }

    .expiration-form {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .expiration-form input[type="date"] {
      flex: 1;
      padding: 0.5rem;
      background: rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 4px;
      color: var(--text-light);
    }

    .expiration-form button {
      padding: 0.5rem;
      min-width: 36px;
    }

    @media (max-width: 768px) {
      .users-grid {
        grid-template-columns: 1fr;
      }
      
      .nav-links {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-links a:last-child {
        width: 100%;
        text-align: center;
      }
    }
    .action-buttons .btn {
        margin: 2px 0;
        white-space: nowrap;
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
    .expiration-form {
        display: flex;
        flex-wrap: nowrap;
        gap: 5px;
        align-items: center;
        min-width: 250px;
    }
    .expiration-form input[type="date"] {
        padding: 0.4rem;
        border: 1px solid rgba(255, 215, 0, 0.3);
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        border-radius: 4px;
        min-width: 150px;
    }
    @media (max-width: 1200px) {
        .admin-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
  </style>
</head>
<body>
  <header class="top-bar">
    <nav class="nav-links">
      <a href="#">Usuarios</a>
      <a href="logout.php">Cerrar Sesión</a>
    </nav>
  </header>

  <main class="section">
    <h1 class="page-title">Gestión de Usuarios</h1>
    <?php if ($admin_flash): ?>
      <div class="admin-alert">
        <?php echo htmlspecialchars($admin_flash); ?>
      </div>
    <?php endif; ?>
    
    <div class="users-grid">
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="user-card">
            <div class="user-header">
              <h3 class="user-name">
                <i class="fas fa-user"></i>
                <?php echo htmlspecialchars($row['nombre']); ?>
              </h3>
              <p class="user-email">
                <i class="fas fa-envelope"></i>
                <?php echo htmlspecialchars($row['email']); ?>
              </p>
            </div>
            
            <div class="user-details">
              <div class="detail-row">
                <span class="detail-label">ID de Usuario</span>
                <span class="detail-value">#<?php echo $row['id']; ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Nombre de Usuario</span>
                <span class="detail-value"><?php echo htmlspecialchars($row['usuario']); ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Fecha de Registro</span>
                <span class="detail-value"><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Estado</span>
                <span class="status-badge status-<?php echo $row['estado'] === 'activo' ? 'active' : 'inactive'; ?>">
                  <?php echo ucfirst($row['estado']); ?>
                </span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Expiración</span>
                <span class="detail-value">
                  <?php 
                    echo $row['fecha_expiracion'] 
                      ? date('d/m/Y', strtotime($row['fecha_expiracion'])) 
                      : 'N/A'; 
                  ?>
                </span>
              </div>
              
              <div class="action-buttons">
                <a href="gestionar_usuario.php?accion=bloquear&id=<?php echo $row['id']; ?>" 
                   class="btn btn-secondary">
                  <i class="fas <?php echo $row['estado'] === 'activo' ? 'fa-lock' : 'fa-unlock'; ?>"></i>
                  <?php echo $row['estado'] === 'activo' ? 'Bloquear' : 'Desbloquear'; ?>
                </a>
                
                <form action="gestionar_usuario.php" method="POST" class="expiration-form">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="accion" value="expiracion">
                  <input type="date" name="fecha_expiracion" 
                         value="<?php echo $row['fecha_expiracion'] ? $row['fecha_expiracion'] : ''; ?>"
                         title="Fecha de expiración">
                  <button type="submit" class="btn btn-primary" title="Guardar fecha">
                    <i class="fas fa-save"></i>
                  </button>
                </form>
                
                <a href="gestionar_usuario.php?accion=eliminar&id=<?php echo $row['id']; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');"
                   title="Eliminar usuario">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-users">
          <i class="fas fa-users-slash"></i>
          <p>No hay usuarios registrados</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="admin-modules">
      <div class="admin-card" id="gestion-equipos">
        <h2>📋 Gestión de Equipos</h2>
        <form method="POST">
          <input type="hidden" name="accion" value="agregar_equipo">
          <div>
            <label>Nombre del Equipo</label>
            <input type="text" name="nombre_equipo" placeholder="Ej: Red Dragons" required>
          </div>
          <div>
            <label>Seed</label>
            <input type="number" name="seed" min="1" max="64" required>
          </div>
          <button type="submit" class="btn btn-primary">➕ Agregar Equipo</button>
        </form>

        <table class="admin-table">
          <thead>
            <tr>
              <th>Seed</th>
              <th>Equipo</th>
              <th>Registro</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($equipos) === 0): ?>
              <tr>
                <td colspan="4">No hay equipos registrados todavía.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($equipos as $equipo): ?>
                <tr>
                  <td>#<?php echo $equipo['seed']; ?></td>
                  <td><?php echo htmlspecialchars($equipo['nombre']); ?></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($equipo['fecha_registro'])); ?></td>
                  <td>
                    <div class="admin-actions">
                      <button type="button"
                              class="btn btn-primary btn-small"
                              onclick="abrirModalEquipo(<?php echo $equipo['id']; ?>, '<?php echo htmlspecialchars($equipo['nombre']); ?>', <?php echo $equipo['seed']; ?>)">✏️ Editar</button>
                      <form method="POST" onsubmit="return confirm('¿Eliminar este equipo?');">
                        <input type="hidden" name="accion" value="eliminar_equipo">
                        <input type="hidden" name="equipo_id" value="<?php echo $equipo['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-small">🗑️ Eliminar</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <form method="POST" style="margin-top: 1rem;">
          <input type="hidden" name="accion" value="generar_ronda1">
          <button class="btn btn-secondary">🎲 Generar Matches Ronda 1</button>
        </form>
      </div>

      <div class="admin-card" id="gestion-brackets">
        <h2>🎮 Matches y Brackets</h2>
        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Bracket</th>
                <th>Ronda</th>
                <th>Match</th>
                <th>Equipo 1</th>
                <th>P1</th>
                <th>vs</th>
                <th>P2</th>
                <th>Equipo 2</th>
                <th>Ganador</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($matches) === 0): ?>
                <tr>
                  <td colspan="10">Todavía no hay matches generados.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($matches as $match): ?>
                  <tr>
                    <td>
                      <span class="tag <?php echo $match['bracket_tipo'] === 'winners' ? 'tag-winners' : 'tag-losers'; ?>">
                        <?php echo strtoupper($match['bracket_tipo']); ?>
                      </span>
                    </td>
                    <td>R<?php echo $match['ronda']; ?></td>
                    <td>#<?php echo $match['numero_match']; ?></td>
                    <td><?php echo htmlspecialchars($match['equipo1_nombre'] ?? 'TBD'); ?></td>
                    <td class="<?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] > $match['puntos_equipo2']) ? 'score-winner' : 'score-loser'; ?>">
                      <?php echo $match['puntos_equipo1'] ?? '-'; ?>
                    </td>
                    <td style="text-align:center;">vs</td>
                    <td class="<?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] > $match['puntos_equipo1']) ? 'score-winner' : 'score-loser'; ?>">
                      <?php echo $match['puntos_equipo2'] ?? '-'; ?>
                    </td>
                    <td><?php echo htmlspecialchars($match['equipo2_nombre'] ?? 'TBD'); ?></td>
                    <td><?php echo htmlspecialchars($match['ganador_nombre'] ?? '-'); ?></td>
                    <td>
                      <button class="btn btn-primary btn-small" onclick="abrirModalMatch(<?php echo $match['id']; ?>, '<?php echo htmlspecialchars($match['equipo1_nombre'] ?? ''); ?>', '<?php echo htmlspecialchars($match['equipo2_nombre'] ?? ''); ?>', <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>, <?php echo $match['puntos_equipo1'] ?? 0; ?>, <?php echo $match['puntos_equipo2'] ?? 0; ?>)">Actualizar</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <div class="modal" id="modalEquipo">
    <div class="modal-content">
      <h3>Editar Equipo</h3>
      <form method="POST" id="formEditarEquipo">
        <input type="hidden" name="accion" value="editar_equipo">
        <input type="hidden" name="equipo_id" id="editEquipoId">
        <div>
          <label>Nombre</label>
          <input type="text" name="nombre_equipo" id="editNombreEquipo" required>
        </div>
        <div>
          <label>Seed</label>
          <input type="number" name="seed" id="editSeed" min="1" required>
        </div>
        <div class="modal-actions">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
          <button type="button" class="btn btn-ghost" onclick="cerrarModal('modalEquipo')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="modalMatch">
    <div class="modal-content">
      <h3>Actualizar match</h3>
      <form method="POST">
        <input type="hidden" name="accion" value="actualizar_match">
        <input type="hidden" name="match_id" id="matchId">
        <input type="hidden" name="equipo1_id" id="matchEquipo1Id">
        <input type="hidden" name="equipo2_id" id="matchEquipo2Id">
        <div>
          <label id="labelEquipo1">Equipo 1</label>
          <input type="number" min="0" name="puntos_equipo1" id="matchPuntos1" required>
        </div>
        <div>
          <label id="labelEquipo2">Equipo 2</label>
          <input type="number" min="0" name="puntos_equipo2" id="matchPuntos2" required>
        </div>
        <div class="modal-actions">
          <button type="submit" class="btn btn-primary">Guardar match</button>
          <button type="button" class="btn btn-ghost" onclick="cerrarModal('modalMatch')">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</body>
<script>
  function abrirModalEquipo(id, nombre, seed) {
    document.getElementById('editEquipoId').value = id;
    document.getElementById('editNombreEquipo').value = nombre;
    document.getElementById('editSeed').value = seed;
    document.getElementById('modalEquipo').classList.add('active');
  }

  function abrirModalMatch(matchId, equipo1Nombre, equipo2Nombre, equipo1Id, equipo2Id, puntos1, puntos2) {
    document.getElementById('matchId').value = matchId;
    document.getElementById('matchEquipo1Id').value = equipo1Id || '';
    document.getElementById('matchEquipo2Id').value = equipo2Id || '';
    document.getElementById('labelEquipo1').textContent = equipo1Nombre || 'Equipo 1';
    document.getElementById('labelEquipo2').textContent = equipo2Nombre || 'Equipo 2';
    document.getElementById('matchPuntos1').value = puntos1 ?? '';
    document.getElementById('matchPuntos2').value = puntos2 ?? '';
    document.getElementById('modalMatch').classList.add('active');
  }

  function cerrarModal(id) {
    document.getElementById(id).classList.remove('active');
  }

  window.addEventListener('click', function (event) {
    document.querySelectorAll('.modal').forEach(function(modal) {
      if (event.target === modal) {
        cerrarModal(modal.id);
      }
    });
  });
</script>
</html>
<?php
$conn->close();
?>
