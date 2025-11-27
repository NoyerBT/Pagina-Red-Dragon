<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Términos y Condiciones - Red Dragons Cup</title>
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
    <h1>Términos y Condiciones</h1>
    <p>Última actualización: <?php echo date('d/m/Y'); ?></p>

    <article>
      <h2>1. Aceptación del servicio</h2>
      <p>Al registrarte en la Red Dragons Cup aceptas participar bajo las reglas del torneo y los lineamientos de nuestra comunidad. El uso indebido del servicio o la entrega de información falsa puede derivar en la suspensión de tu cuenta.</p>
    </article>

    <article>
      <h2>2. Requisitos de participación</h2>
      <p>Todos los jugadores deben proporcionar datos verídicos, mantener sus cuentas personales y cumplir los mínimos de horas de juego según la modalidad del torneo. Cualquier cuenta detectada con trampas o comportamientos antideportivos será descalificada.</p>
    </article>

    <article>
      <h2>3. Pagos y suscripciones</h2>
      <p>El pago del plan anticheat es mensual y no reembolsable una vez activado el servicio. El incumplimiento de pago puede conllevar a la pérdida de beneficios y acceso a herramientas exclusivas.</p>
    </article>

    <article>
      <h2>4. Conducta y sanciones</h2>
      <p>Los jugadores deben mantener un comportamiento respetuoso. El equipo organizador puede sancionar a cualquier participante que incumpla normas de convivencia, utilice hacks o afecte la experiencia de otros usuarios.</p>
    </article>

    <article>
      <h2>5. Propiedad intelectual</h2>
      <p>Los logotipos, contenidos y materiales del torneo son propiedad de Red Dragons Cup. Queda prohibida su reproducción sin autorización previa por escrito.</p>
    </article>

    <article>
      <h2>6. Modificaciones del documento</h2>
      <p>Nos reservamos el derecho de actualizar estos términos en cualquier momento. Los cambios se publicarán en esta página y su uso continuado del servicio implicará la aceptación de las nuevas condiciones.</p>
    </article>
  </main>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
</body>
</html>
