<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión - Red Dragons Cup</title>
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
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="registro.php">REGISTRARSE</a>
    </nav>
  </header>

  <main class="hero">
    <section class="hero-content">
      <h1>Iniciar Sesión</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Accede a tu cuenta para gestionar tu suscripción.</p>
    </section>
  </main>

  <section class="section" id="login-form">
    <div class="registro-container">
      <div class="registro-card">
        <h2>🔑 Ingresa tus Credenciales :D</h2>
        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<p class="error-message">' . $_SESSION['login_error'] . '</p>';
            unset($_SESSION['login_error']);
        }
        ?>
        <form class="registro-form" action="procesar_login.php" method="POST">
          <div class="form-group">
            <label for="usuario">Nombre de Usuario o Correo</label>
            <input type="text" id="usuario" name="usuario" required>
          </div>
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" class="btn primary registro-btn">Iniciar Sesión</button>
          <p class="login-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
          </p>
        </form>
      </div>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
