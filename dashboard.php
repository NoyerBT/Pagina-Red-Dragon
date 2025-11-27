<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'cnt/conexion.php';

// Obtener información del usuario
$sql = "SELECT nombre, fecha_expiracion FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Calcular días restantes
$dias_restantes = null;
if ($user['fecha_expiracion']) {
    $hoy = new DateTime();
    $fecha_expiracion = new DateTime($user['fecha_expiracion']);
    $dias_restantes = $hoy->diff($fecha_expiracion)->format('%r%a');
    $dias_restantes = intval($dias_restantes) > 0 ? $dias_restantes : 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
</head>
<body>
  <div class="bg-overlay"></div>
  <header class="top-bar">
    <div class="top-logo-section">
      <img src="Img/Logo left 4.png" alt="Logo Left 4 Dead" class="top-logo" />
    </div>
    <nav class="nav-links">
      <a href="index.php">INICIO</a>
      <a href="torneo.php">TORNEO</a>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="dashboard.php">MI CUENTA</a>
      <a href="logout.php">CERRAR SESIÓN</a>
    </nav>
  </header>

  <main class="hero">
    <section class="hero-content">
      <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Aquí podrás gestionar tu suscripción y ver información del torneo.</p>
      
      <div class="plan-info" style="margin-top: 2rem; padding: 1.5rem; background: rgba(0, 0, 0, 0.7); border-radius: 10px; max-width: 500px; margin-left: auto; margin-right: auto;">
        <h3>📅 Estado de tu Plan</h3>
        <?php if ($user['fecha_expiracion']): ?>
            <p><strong>Tu plan vence el:</strong> <?php echo date('d/m/Y', strtotime($user['fecha_expiracion'])); ?></p>
            <?php if ($dias_restantes !== null): ?>
                <?php if ($dias_restantes > 0): ?>
                    <p><strong>Días restantes:</strong> <span style="color: #4CAF50;"><?php echo $dias_restantes; ?> días</span></p>
                <?php else: ?>
                    <p><strong>Estado:</strong> <span style="color: #f44336;">Plan vencido</span></p>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <p><strong>No tienes un plan activo.</strong></p>
            <p>Contacta con el administrador para activar tu suscripción.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
