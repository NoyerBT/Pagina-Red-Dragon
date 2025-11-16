<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login - Red Dragons Cup</title>
  <link rel="stylesheet" href="../styles.css" />
</head>
<body>
  <div class="bg-overlay"></div>
  <main class="hero">
    <section class="hero-content">
      <h1>Panel de Administración</h1>
      <img src="../Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
    </section>
  </main>

  <section class="section" id="login-form">
    <div class="registro-container">
      <div class="registro-card">
        <h2>🔑 Acceso de Administrador</h2>
        <?php
        if (isset($_SESSION['admin_login_error'])) {
            echo '<p class="error-message">' . $_SESSION['admin_login_error'] . '</p>';
            unset($_SESSION['admin_login_error']);
        }
        ?>
        <form class="registro-form" action="procesar_login_admin.php" method="POST">
          <div class="form-group">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" required>
          </div>
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" class="btn primary registro-btn">Ingresar</button>
        </form>
      </div>
    </div>
  </section>
</body>
</html>
