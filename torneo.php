<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Torneo - Red Dragons Cup</title>
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
      <h1>Torneo Red Dragons Cup</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Torneo RDC reúne a los equipos más destacados de la comunidad competitiva de left 4 dead 2 en una experiencia competitiva diseñada 
        para destacar el talento, la disciplina y el juego limpio. Aquí encontrarás todos los detalles esenciales del evento, desde las reglas hasta 
        los premios y requisitos de participación. Prepárate para formar parte de una competencia creada para vibrar, crecer y demostrar de qué está 
        hecho tu equipo en los futuros torneos y eventos.</p>
    </section>
  </main>

  <section class="section" id="torneo-info">
    <h2>Información del Torneo</h2>
    <div class="torneo-grid">
      <div class="torneo-card">
        <h3>🏆 Premio</h3>
        <p>50 USD</p>
      </div>
      <div class="torneo-card">
        <h3>🎮 Modalidad</h3>
        <p>4v4 Zonemod Competitivo</p>
      </div>
      <div class="torneo-card">
        <h3>📅 Fecha</h3>
        <p>INICIO 1RO DE NOVIEMBRE 2025</p>
      </div>
      <div class="torneo-card">
        <h3>⚡ Plataforma</h3>
        <p>PC</p>
      </div>
    </div>
  </section>

  <section class="section" id="equipos">
    <h2>Equipos Participantes</h2>
    <p>Aquí se mostrarán los equipos registrados una vez que comience la inscripción.</p>
    <div class="equipos-placeholder">
      <p>🔄 Próximamente: Lista de equipos inscritos</p>
    </div>
    <div class="brackets-button-container">
      <a href="brackets.php" class="btn-brackets">
        <span>📋</span> Ver Brackets del Torneo
      </a>
    </div>
  </section>

  <section class="section" id="formato">
    <h2>Formato del Torneo</h2>
    <div class="formato-info">
      <h3>📋 Estructura</h3>
      <ul>
        <li>Fase de grupos (Bo1)</li>
        <li>Eliminatorias (Bo3)</li>
        <li>Final (Bo5)</li>
      </ul>
      
      <h3>⏰ Horarios</h3>
      <p>Los horarios se definirán según la cantidad de equipos inscritos.</p>
      
      <h3>📜 Reglas</h3>
      <p>Las reglas detalladas se publicarán próximamente.</p>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
