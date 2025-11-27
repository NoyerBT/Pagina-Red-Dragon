<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Política de Privacidad - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="bg-overlay"></div>
  <header class="top-bar">
    <div class="top-logo-section">
      <img src="Img/Logo left 4.png" alt="Logo Left 4 Dead" class="top-logo" />
    </div>
    <nav class="nav-links">
      <a href="index.php">INICIO</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="torneo.php">TORNEO</a>
      <?php endif; ?>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="section legal-section">
    <h1>Política de Privacidad</h1>
    <p>Última actualización: <?php echo date('d/m/Y'); ?></p>

    <article>
      <h2>1. Información que recopilamos</h2>
      <p>Solicitamos datos personales como nombre, correo electrónico, usuario de juego y país para administrar tu registro y participación. También recopilamos métricas técnicas básicas para mejorar el servicio.</p>
    </article>

    <article>
      <h2>2. Uso de la información</h2>
      <p>La información se usa para validar tu identidad, gestionar tu suscripción al plan anticheat, enviar avisos sobre el torneo y responder consultas de soporte.</p>
    </article>

    <article>
      <h2>3. Protección de datos</h2>
      <p>Mantenemos medidas de seguridad administrativas y técnicas para evitar accesos no autorizados. Solo el personal autorizado del staff puede acceder a tus datos.</p>
    </article>

    <article>
      <h2>4. Compartición con terceros</h2>
      <p>No vendemos la información de los jugadores. Solo compartimos datos con proveedores tecnológicos estrictamente necesarios para operar la plataforma o cumplir con leyes vigentes.</p>
    </article>

    <article>
      <h2>5. Derechos del usuario</h2>
      <p>Puedes solicitar la actualización o eliminación de tu información, así como revocar tu consentimiento escribiendo a nuestro canal de soporte.</p>
    </article>

    <article>
      <h2>6. Cookies y tecnologías similares</h2>
      <p>Utilizamos cookies funcionales para recordar sesiones e identificar actividad sospechosa. Puedes deshabilitarlas desde tu navegador, aunque ciertas funciones podrían verse limitadas.</p>
    </article>

    <article>
      <h2>7. Cambios a esta política</h2>
      <p>Podemos modificar esta política para reflejar mejoras en la seguridad o cambios legales. Notificaremos cualquier actualización relevante dentro del portal.</p>
    </article>
  </main>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
</body>
</html>
