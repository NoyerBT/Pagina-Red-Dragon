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

  <main class="hero">
    <section class="hero-content">
      <h1>Contacto</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">¿Tienes dudas? Contáctanos por cualquiera de estos medios.</p>
    </section>
  </main>

  <section class="section" id="contacto-info">
    <h2>Nuestro Equipo</h2>
    
    <div class="team-grid">
      <!-- Administrador Card (Cúspide) -->
      <div class="team-card main-card">
        <div class="team-card__header">
          <div class="team-card__photo">
            <img src="Img/fotoadministrador.jpg" alt="Administrador" style="object-position: 60% center;" />
          </div>
          <div class="team-card__badge admin">ADMINISTRADOR</div>
        </div>
        <div class="team-card__body">
          <h3>WHITING</h3>
          <p class="team-card__role">Fundador & Administrador Principal</p>
          <div class="team-card__contacts">
            <a href="tel:+51959214173" class="team-contact-item">
              <span class="contact-icon">📱</span>
              <span>+51 959214173</span>
            </a>
             <a href="https://guns.lol/whiting" class="team-contact-item">
              <span class="contact-icon">🔗</span>
              <span>https://guns.lol/whiting</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Soporte Card (Base Izquierda) -->
      <div class="team-card">
        <div class="team-card__header">
          <div class="team-card__photo">
            <img src="Img/fotosoporte.jpg" alt="Soporte" />
          </div>
          <div class="team-card__badge support">SOPORTE</div>
        </div>
        <div class="team-card__body">
          <h3>NH</h3>
          <p class="team-card__role">Soporte Técnico</p>
          <div class="team-card__contacts">
            <a href="https://guns.lol/neelsonh" class="team-contact-item" target="_blank" rel="noopener noreferrer">
              <span class="contact-icon">🔗</span>
              <span>guns.lol/neelsonh</span>
            </a>
            <div class="team-contact-item">
              <span class="contact-icon">DISC 💬</span>
              <span>hq27_27364</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Diseñadora Card (Base Derecha) -->
      <div class="team-card">
        <div class="team-card__header">
          <div class="team-card__photo">
            <img src="Img/fotodiseñadora.jpg" alt="Diseñadora" />
          </div>
          <div class="team-card__badge designer">DISEÑADORA</div>
        </div>
        <div class="team-card__body">
          <h3>Vallolet</h3>
          <p class="team-card__role">Diseño Grafico M&N Studio</p>
          <div class="team-card__contacts">
            <a href="mailto:iamvallolet07@gmail.com" class="team-contact-item">
              <span class="contact-icon">📧</span>
              <span>iamvallolet07@gmail.com</span>
            </a>
            <div class="team-contact-item">
              <span class="contact-icon"> DISC 💬</span>
              <span>you_vallolet</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="formulario-contacto">
    <h2>Envíanos un Mensaje</h2>
    <div class="neumorphic-container">
      <form class="neumorphic-form" action="#" method="POST">
        <div class="form-content">
          <div class="form-details">Contacto</div>
          
          <input placeholder="Nombre de Usuario" class="neumorphic-input" type="text" name="nombre" required>
          
          <input placeholder="Correo Electrónico" class="neumorphic-input" type="email" name="email" required>
          
          <select class="neumorphic-input" name="asunto" required>
            <option value="">Selecciona un tema</option>
            <option value="torneo">Consulta sobre el torneo</option>
            <option value="anticheat">Soporte anticheat</option>
            <option value="tecnico">Problema técnico</option>
            <option value="otro">Otro</option>
          </select>
          
          <textarea placeholder="Escribe tu mensaje aquí..." class="neumorphic-input" name="mensaje" rows="5" required></textarea>
          
          <button class="neumorphic-btn">Enviar Mensaje</button>
        </div>
      </form>
    </div>
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
    <p>&copy; <span id="year"></span> 2025 Red Dragons Corporation. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
