<?php
// Página de Inicio - Red Dragons Cup
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Torneo Red Dragons</title>
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
    </nav>
  </header>

  <main class="hero" id="torneo">
    <section class="hero-content">
      <h1>RED DRAGON´S | WHITING</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Torneo para verdaderas leyendas.</p>
      <div class="hero-buttons">
        <a href="#registro" class="btn primary">Mas Informacion</a>
        <a href="#equipos" class="btn secondary">Ver reglas</a>
      </div>
      <div class="info-tags">
        <span>Premio: $50</span>
        <span>Modo: 4v4</span>
        <span>Plataforma: PC</span>
      </div>
    </section>
  </main>

  <section class="section" id="equipos">
    <h2>Equipos y Formato</h2>
    <p>Describe aquí cuántos equipos participan, formato (grupos, eliminación directa, Bo1/Bo3), horarios, etc.</p>
  </section>

  <section class="section" id="registro">
    <h2>Registro</h2>
    <p>Indica el enlace o las instrucciones para que los jugadores/equipos se inscriban al torneo.</p>
  </section>

  <section class="section" id="contacto">
    <h2>Contacto</h2>
    <p>Agrega tu Discord, correo o redes sociales para resolver dudas de los participantes.</p>
  </section>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
