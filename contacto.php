<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contacto - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
</head>
<body>
  <div class="bg-overlay"></div>
  <div class="top-logo-section">
    <img src="Img/Logo left 4.png" alt="Logo Left 4 Dead" class="top-logo" />
  </div>
  <header class="top-bar">
    <nav class="nav-links">
      <a href="index.php">Inicio</a>
      <a href="torneo.php">Torneo</a>
      <a href="anticheats.php">Anticheats</a>
      <a href="contacto.php">Contacto</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">Mi Cuenta</a>
        <a href="logout.php">Cerrar Sesión</a>
      <?php else: ?>
        <a href="login.php">Iniciar Sesión</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero">
    <section class="hero-content">
      <h1>Contacto</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">¿Tienes dudas? Contáctanos por cualquiera de estos medios.</p>
    </section>
  </main>

  <section class="section" id="contacto-info">
    <h2>Información de Contacto</h2>
    
    <div class="contact-grid">
      <div class="contact-card">
        <h3>💬 Discord</h3>
        <p>Únete a nuestro servidor oficial</p>
        <a href="https://discord.gg/k2xmHEST" class="contact-link" target="_blank" rel="noopener noreferrer">https://discord.gg/k2xmHEST</a>
      </div>
      
      <div class="contact-card">
        <h3>📧 Email</h3>
        <p>Soporte técnico y consultas</p>
        <a href="mailto:info@reddragons.com" class="contact-link">info@reddragons.com</a>
      </div>
      
      <div class="contact-card">
        <h3>📱 WhatsApp</h3>
        <p>Atención directa</p>
        <a href="#" class="contact-link">+51 XXX XXX XXX</a>
      </div>
      
      <div class="contact-card">
        <h3>🐦 Twitter</h3>
        <p>Síguenos para noticias</p>
        <a href="#" class="contact-link">@RedDragonsCup</a>
      </div>
    </div>
  </section>

  <section class="section" id="formulario-contacto">
    <h2>Envíanos un Mensaje</h2>
    <form class="contact-form" action="#" method="POST">
      <div class="form-group">
        <label for="nombre">Nombre Completo</label>
        <input type="text" id="nombre" name="nombre" required>
      </div>
      
      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" required>
      </div>
      
      <div class="form-group">
        <label for="asunto">Asunto</label>
        <select id="asunto" name="asunto" required>
          <option value="">Selecciona un tema</option>
          <option value="torneo">Consulta sobre el torneo</option>
          <option value="anticheat">Soporte anticheat</option>
          <option value="tecnico">Problema técnico</option>
          <option value="otro">Otro</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="mensaje">Mensaje</label>
        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
      </div>
      
      <button type="submit" class="btn primary">Enviar Mensaje</button>
    </form>
  </section>

  <section class="section" id="horarios">
    <h2>Horarios de Atención</h2>
    <div class="horarios-info">
      <p><strong>Lunes a Viernes:</strong> 9:00 AM - 6:00 PM</p>
      <p><strong>Sábados:</strong> 10:00 AM - 4:00 PM</p>
      <p><strong>Domingos:</strong> Solo emergencias</p>
      <p><em>Horario: UTC-5 (Perú)</em></p>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
