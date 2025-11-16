<?php
session_start();

if (!isset($_SESSION['admin_usuario'])) {
    header("Location: index.php");
    exit();
}

require_once '../cnt/conexion.php';

// Fetch all non-admin users
$sql = "SELECT id, usuario, nombre, email, fecha_registro, estado, fecha_expiracion FROM usuarios";
$result = $conn->query($sql);

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
      padding: 0 1.5rem;
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
  </main>
</body>
</html>
<?php
$conn->close();
?>
