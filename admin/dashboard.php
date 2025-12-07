<?php
session_start();

if (!isset($_SESSION['admin_usuario'])) {
    header("Location: index.php");
    exit();
}

require_once '../cnt/conexion.php';

// Limpiar IPs de usuarios VIP expirados automáticamente
$check_table = $conn->query("SHOW TABLES LIKE 'usuarios_vip_ips'");
if ($check_table && $check_table->num_rows > 0) {
    $sql_cleanup = "DELETE vip FROM usuarios_vip_ips vip
                   INNER JOIN usuarios u ON vip.usuario_id = u.id
                   WHERE u.fecha_expiracion IS NOT NULL 
                   AND u.fecha_expiracion < CURDATE()";
    $conn->query($sql_cleanup);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Las acciones de equipos y brackets han sido eliminadas
    // Solo se mantiene la gestión de usuarios

    header('Location: dashboard.php');
    exit();
}

// Obtener término de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Verificar si existe la tabla usuarios_vip_ips
$check_table = $conn->query("SHOW TABLES LIKE 'usuarios_vip_ips'");
$table_exists = $check_table && $check_table->num_rows > 0;

// Fetch all non-admin users con su IP si existe
if ($table_exists) {
    if (!empty($busqueda)) {
        $busqueda_like = "%" . $conn->real_escape_string($busqueda) . "%";
        $sql = "SELECT u.id, u.usuario, u.nombre, u.email, u.fecha_registro, u.estado, u.fecha_expiracion, u.vip,
                       vip.ip_servidor
                FROM usuarios u
                LEFT JOIN usuarios_vip_ips vip ON u.id = vip.usuario_id
                WHERE u.nombre LIKE ? OR u.usuario LIKE ? OR u.email LIKE ?
                ORDER BY u.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $busqueda_like, $busqueda_like, $busqueda_like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT u.id, u.usuario, u.nombre, u.email, u.fecha_registro, u.estado, u.fecha_expiracion, u.vip,
                       vip.ip_servidor
                FROM usuarios u
                LEFT JOIN usuarios_vip_ips vip ON u.id = vip.usuario_id
                ORDER BY u.id DESC";
        $result = $conn->query($sql);
    }
} else {
    if (!empty($busqueda)) {
        $busqueda_like = "%" . $conn->real_escape_string($busqueda) . "%";
        $sql = "SELECT id, usuario, nombre, email, fecha_registro, estado, fecha_expiracion 
                FROM usuarios 
                WHERE nombre LIKE ? OR usuario LIKE ? OR email LIKE ?
                ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $busqueda_like, $busqueda_like, $busqueda_like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT id, usuario, nombre, email, fecha_registro, estado, fecha_expiracion FROM usuarios ORDER BY id DESC";
        $result = $conn->query($sql);
    }
}

// Verificar si la consulta fue exitosa
if ($result === false) {
    error_log("Error en consulta SQL: " . $conn->error);
    $sql = "SELECT id, usuario, nombre, email, fecha_registro, estado, fecha_expiracion FROM usuarios ORDER BY id DESC";
    $result = $conn->query($sql);
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
      gap: 1.5rem;
    }

    .nav-links a {
      color: var(--text-light);
      text-decoration: none;
      padding: 0.5rem 1.5rem;
      border-radius: 20px;
      transition: var(--transition);
      font-weight: 500;
    }

    .nav-links a:first-child {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #000;
      font-weight: 700;
      padding: 0.75rem 2rem;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
      border: 2px solid rgba(212, 175, 55, 0.5);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .nav-links a:first-child:hover {
      background: linear-gradient(135deg, var(--primary-dark), #9a7a1f);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(212, 175, 55, 0.4);
    }

    .nav-links a:first-child i {
      font-size: 1.1em;
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

    .search-container {
      flex: 1;
      max-width: 400px;
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 3rem;
      background: rgba(0, 0, 0, 0.3);
      border: 2px solid rgba(212, 175, 55, 0.3);
      border-radius: 25px;
      color: var(--text-light);
      font-size: 0.95rem;
      transition: all 0.3s ease;
      outline: none;
    }

    .search-input:focus {
      border-color: rgba(212, 175, 55, 0.6);
      background: rgba(0, 0, 0, 0.5);
      box-shadow: 0 0 12px rgba(212, 175, 55, 0.3);
    }

    .search-input::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      color: var(--primary);
      font-size: 1.1rem;
      pointer-events: none;
    }

    .search-clear {
      position: absolute;
      right: 1rem;
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.5);
      cursor: pointer;
      font-size: 1.2rem;
      padding: 0.25rem;
      display: none;
      align-items: center;
      justify-content: center;
      transition: color 0.3s ease;
    }

    .search-clear:hover {
      color: var(--text-light);
    }

    .search-clear.visible {
      display: flex;
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
      grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }

    .user-card {
      background: linear-gradient(135deg, #1e1e1e 0%, #252525 100%);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(212, 175, 55, 0.1);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(212, 175, 55, 0.15);
      position: relative;
    }

    .user-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, var(--primary), transparent);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .user-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(212, 175, 55, 0.3);
      border-color: rgba(212, 175, 55, 0.4);
    }

    .user-card:hover::before {
      opacity: 1;
    }

    .user-header {
      background: linear-gradient(135deg, #2a2a2a 0%, #1f1f1f 100%);
      padding: 1.75rem 1.5rem;
      border-bottom: 2px solid rgba(212, 175, 55, 0.2);
      position: relative;
    }

    .user-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.5), transparent);
    }

    .user-name {
      margin: 0;
      color: var(--primary);
      font-size: 1.35rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-shadow: 0 2px 4px rgba(212, 175, 55, 0.2);
    }

    .user-name i {
      font-size: 1.1em;
      opacity: 0.8;
    }

    .user-email {
      margin: 0.5rem 0 0;
      color: var(--text-muted);
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      opacity: 0.85;
    }

    .user-details {
      padding: 1.75rem 1.5rem;
      background: rgba(0, 0, 0, 0.2);
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      transition: all 0.2s ease;
    }

    .detail-row:hover {
      border-bottom-color: rgba(212, 175, 55, 0.2);
      padding-left: 0.5rem;
    }

    .detail-row:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .detail-label {
      color: var(--text-muted);
      font-size: 0.875rem;
      font-weight: 500;
      letter-spacing: 0.3px;
    }

    .detail-value {
      font-weight: 600;
      text-align: right;
      color: var(--text-light);
      font-size: 0.9rem;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.4rem 0.9rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow: hidden;
    }

    .status-badge::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    .status-badge:hover::before {
      left: 100%;
    }

    .status-active {
      background: linear-gradient(135deg, rgba(40, 167, 69, 0.25), rgba(40, 167, 69, 0.15));
      color: #4ade80;
      border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-inactive {
      background: linear-gradient(135deg, rgba(220, 53, 69, 0.25), rgba(220, 53, 69, 0.15));
      color: #f87171;
      border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .action-buttons {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      margin-top: 1.75rem;
      padding-top: 1.5rem;
      border-top: 2px solid rgba(212, 175, 55, 0.15);
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
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.08));
      color: var(--text-light);
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary:hover {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.15));
      border-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .btn-danger {
      background: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }

    .btn-danger:hover {
      background: rgba(220, 53, 69, 0.3);
    }
    
    .btn-vip {
      background: linear-gradient(135deg, #f39c12, #e67e22);
      color: #fff;
      border: 2px solid rgba(243, 156, 18, 0.5);
      box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
      font-weight: 600;
      width: 100%;
      padding: 0.75rem 1rem;
      border-radius: 8px;
    }
    
    .btn-vip:hover {
      background: linear-gradient(135deg, #e67e22, #d35400);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(243, 156, 18, 0.5);
      border-color: rgba(243, 156, 18, 0.8);
    }
    
    .btn-vip i {
      margin-right: 0.5rem;
    }
    
    .btn-delete {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      color: #fff;
      border: 2px solid rgba(231, 76, 60, 0.5);
      box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
      font-weight: 600;
      width: 100%;
      padding: 0.75rem 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
    }
    
    .btn-delete:hover {
      background: linear-gradient(135deg, #c0392b, #a93226);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(231, 76, 60, 0.5);
      border-color: rgba(231, 76, 60, 0.8);
    }
    
    .btn-delete i {
      margin-right: 0.5rem;
    }
    
    .expiration-form {
      display: flex;
      gap: 0.75rem;
      align-items: stretch;
      background: rgba(0, 0, 0, 0.2);
      padding: 0.5rem;
      border-radius: 8px;
      border: 1px solid rgba(243, 156, 18, 0.2);
    }
    
    .date-input-vip {
      flex: 1;
      padding: 0.7rem;
      background: rgba(0, 0, 0, 0.4);
      border: 2px solid rgba(243, 156, 18, 0.3);
      border-radius: 8px;
      color: var(--text-light);
      font-size: 0.9rem;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    
    .date-input-vip:focus {
      outline: none;
      border-color: rgba(243, 156, 18, 0.7);
      box-shadow: 0 0 12px rgba(243, 156, 18, 0.4);
      background: rgba(0, 0, 0, 0.5);
    }
    
    .date-input-vip::-webkit-calendar-picker-indicator {
      filter: invert(1);
      cursor: pointer;
      opacity: 0.8;
    }
    
    .date-input-vip::-webkit-calendar-picker-indicator:hover {
      opacity: 1;
    }
    
    .ip-form {
      display: flex;
      gap: 0.75rem;
      align-items: stretch;
      background: rgba(0, 0, 0, 0.2);
      padding: 0.5rem;
      border-radius: 8px;
      border: 1px solid rgba(52, 152, 219, 0.2);
    }
    
    .ip-input {
      flex: 1;
      padding: 0.7rem;
      background: rgba(0, 0, 0, 0.4);
      border: 2px solid rgba(52, 152, 219, 0.3);
      border-radius: 8px;
      color: var(--text-light);
      font-size: 0.9rem;
      transition: all 0.3s ease;
      font-family: 'Courier New', monospace;
      font-weight: 500;
    }
    
    .ip-input:focus {
      outline: none;
      border-color: rgba(52, 152, 219, 0.7);
      box-shadow: 0 0 12px rgba(52, 152, 219, 0.4);
      background: rgba(0, 0, 0, 0.5);
    }
    
    .ip-input::placeholder {
      color: rgba(255, 255, 255, 0.4);
      font-style: italic;
    }
    
    .ip-display {
      padding: 0.6rem 1rem;
      background: linear-gradient(135deg, rgba(52, 152, 219, 0.15), rgba(52, 152, 219, 0.08));
      border: 1px solid rgba(52, 152, 219, 0.4);
      border-radius: 8px;
      font-family: 'Courier New', monospace;
      color: #60a5fa;
      font-weight: 700;
      font-size: 0.95rem;
      display: inline-block;
      box-shadow: 0 2px 6px rgba(52, 152, 219, 0.2);
      letter-spacing: 0.5px;
    }
    
    .admin-alert {
      background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.2));
      border: 2px solid rgba(52, 152, 219, 0.5);
      border-radius: 8px;
      padding: 1rem 1.5rem;
      margin: 1rem 0 2rem;
      color: #3498db;
      font-weight: 500;
      text-align: center;
      box-shadow: 0 2px 8px rgba(52, 152, 219, 0.2);
      animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .no-users {
      text-align: center;
      padding: 3rem;
      color: var(--text-muted);
    }
    
    .no-users i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    @media (max-width: 768px) {
      .users-grid {
        grid-template-columns: 1fr;
      }
      
      .nav-links {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-links a:first-child,
      .nav-links a:last-child {
        width: 100%;
        text-align: center;
      }

      .search-container {
        max-width: 100%;
        width: 100%;
      }
    }
    .action-buttons .btn {
        width: 100%;
        white-space: nowrap;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        border-radius: 8px;
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
      <a href="dashboard.php">
        <i class="fas fa-users"></i>
        Usuarios
      </a>
      <form method="GET" action="dashboard.php" class="search-container" id="searchForm">
        <i class="fas fa-search search-icon"></i>
        <input 
          type="text" 
          name="buscar" 
          class="search-input" 
          placeholder="Buscar por nombre, usuario o email..."
          value="<?php echo htmlspecialchars($busqueda); ?>"
          id="searchInput"
          autocomplete="off">
        <?php if (!empty($busqueda)): ?>
        <button type="button" class="search-clear visible" id="clearSearch" title="Limpiar búsqueda">
          <i class="fas fa-times"></i>
        </button>
        <?php endif; ?>
      </form>
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
      <?php if ($result && $result->num_rows > 0): ?>
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
              
              <?php 
                // Verificar si el usuario tiene IP asignada y si su VIP no ha expirado
                $ip_mostrar = '';
                $vip_activo = false;
                if (isset($row['ip_servidor']) && !empty($row['ip_servidor'])) {
                    if (isset($row['fecha_expiracion']) && !empty($row['fecha_expiracion'])) {
                        $fecha_expiracion = new DateTime($row['fecha_expiracion']);
                        $fecha_actual = new DateTime();
                        if ($fecha_expiracion >= $fecha_actual) {
                            $ip_mostrar = $row['ip_servidor'];
                            $vip_activo = true;
                        }
                    }
                }
              ?>
              
              <?php if ($ip_mostrar): ?>
              <div class="detail-row">
                <span class="detail-label">IP Servidor</span>
                <span class="detail-value ip-display">
                  <?php echo htmlspecialchars($ip_mostrar); ?>
                </span>
              </div>
              <?php endif; ?>
              
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
                         title="Asignar fecha y convertir en VIP"
                         class="date-input-vip">
                  <button type="submit" class="btn btn-vip" title="Asignar fecha y convertir en VIP">
                    <i class="fas fa-star"></i>
                    <span>VIP</span>
                  </button>
                </form>
                
                <form action="gestionar_usuario.php" method="POST" class="ip-form">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="accion" value="asignar_ip">
                  <input type="text" 
                         name="ip_servidor" 
                         value="<?php echo $ip_mostrar ? htmlspecialchars($ip_mostrar) : ''; ?>"
                         placeholder="Ej: 192.168.1.100:27015"
                         class="ip-input"
                         pattern="^(\d{1,3}\.){3}\d{1,3}(:\d{1,5})?$"
                         title="Formato: IP:Puerto (ej: 192.168.1.100:27015)">
                  <button type="submit" class="btn btn-provide-ip" title="Proporcionar IP del servidor">
                    <i class="fas fa-server"></i>
                    <span>Proporcionar</span>
                  </button>
                </form>
                
                <a href="gestionar_usuario.php?accion=eliminar&id=<?php echo $row['id']; ?>" 
                   class="btn btn-delete" 
                   onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?\n\nEsta acción no se puede deshacer.');"
                   title="Eliminar usuario permanentemente">
                  <i class="fas fa-trash-alt"></i>
                  <span>Eliminar</span>
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-users">
          <i class="fas fa-<?php echo !empty($busqueda) ? 'search' : 'users-slash'; ?>"></i>
          <p>
            <?php if (!empty($busqueda)): ?>
              No se encontraron usuarios que coincidan con "<?php echo htmlspecialchars($busqueda); ?>"
            <?php else: ?>
              No hay usuarios registrados
            <?php endif; ?>
          </p>
          <?php if (!empty($busqueda)): ?>
            <a href="dashboard.php" style="color: var(--primary); text-decoration: none; margin-top: 1rem; display: inline-block;">
              <i class="fas fa-arrow-left"></i> Ver todos los usuarios
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
  
  <script>
    // Manejar búsqueda al presionar Enter
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const searchValue = this.value.trim();
        if (searchValue) {
          document.getElementById('searchForm').submit();
        } else {
          window.location.href = 'dashboard.php';
        }
      }
    });

    // Mostrar/ocultar botón de limpiar
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');
    
    if (searchInput && clearButton) {
      searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
          clearButton.classList.add('visible');
        } else {
          clearButton.classList.remove('visible');
        }
      });

      // Limpiar búsqueda
      clearButton.addEventListener('click', function() {
        searchInput.value = '';
        clearButton.classList.remove('visible');
        window.location.href = 'dashboard.php';
      });
    }

    // Agregar botón de limpiar dinámicamente si no existe
    if (searchInput && !clearButton) {
      searchInput.addEventListener('input', function() {
        let clearBtn = document.getElementById('clearSearch');
        if (!clearBtn && this.value.length > 0) {
          clearBtn = document.createElement('button');
          clearBtn.type = 'button';
          clearBtn.className = 'search-clear visible';
          clearBtn.id = 'clearSearch';
          clearBtn.title = 'Limpiar búsqueda';
          clearBtn.innerHTML = '<i class="fas fa-times"></i>';
          clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            window.location.href = 'dashboard.php';
          });
          searchInput.parentElement.appendChild(clearBtn);
        } else if (clearBtn && this.value.length === 0) {
          clearBtn.remove();
        }
      });
    }
  </script>
</body>
</html>
<?php
$conn->close();
?>
