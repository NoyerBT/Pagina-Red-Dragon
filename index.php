<?php
session_start();
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
      <a href="brackets.php">Clasificación</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">Mi Cuenta</a>
        <a href="logout.php">Cerrar Sesión</a>
      <?php else: ?>
        <a href="login.php">Iniciar Sesión</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero" id="torneo">
    <section class="hero-content">
      <h1>RED DRAGON´S | WHITING</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Torneo para verdaderas leyendas.</p>
      <div class="hero-buttons">
        <a href="#equipos" class="btn primary">Mas Informacion</a>
        <a href="#equipos" class="btn secondary">Ver reglas</a>
      </div>
      <div class="info-tags">
        <span>Premio: $50</span>
        <span>Modo: 4v4</span>
        <span>Plataforma: PC</span>
      </div>
    </section>
  </main>

  <section class="section about-section" id="equipos">
    <h2>SOBRE NUESTRA PAGINA</h2>
    <div class="about-wrapper">
      <article class="about-item">
        <h3>¿QUÉ ES RDC?</h3>
        <p>
          Esta página es una plataforma de la Red Dragon Cup, torneo del videojuego Left 4 Dead 2 diseñado para la comunidad y los jugadores que buscan una experiencia seria, organizada y con un ambiente de alto nivel. Nuestra página reúne toda la información oficial del torneo, desde reglas y clasificación hasta anuncios importantes y contenido exclusivo del torneo.
        </p>
      </article>

      <article class="about-item">
        <h3>NUESTRA MISIÓN</h3>
        <p>
          Queremos ofrecer un torneo transparente, justo y emocionante, donde cada equipo tenga la oportunidad de demostrar su habilidad y competir en igualdad de condiciones. RDC nace con el objetivo de fortalecer la escena competitiva y brindar un espacio donde la comunidad pueda crecer, participar y disfrutar.
        </p>
      </article>

      <article class="about-item">
        <h3>¿QUÉ OFRECEMOS?</h3>
        <ul class="about-list">
          <li>Torneos organizados profesionalmente con formatos claros, grupos o eliminación directa.</li>
          <li>Clasificaciones y estadísticas actualizadas para mantener a la comunidad informada.</li>
          <li>Reglamento detallado para garantizar un ambiente justo y competitivo.</li>
          <li>Sistema de contacto directo para resolver dudas o enviar documentos.</li>
          <li>Premios y reconocimientos para los mejores equipos.</li>
        </ul>
      </article>

      <article class="about-item">
        <h3>COMUNIDAD Y TRANSPARENCIA</h3>
        <p>
          Creemos en la comunicación abierta con los participantes, por eso nuestro portal está diseñado para que cualquier jugador o equipo pueda acceder de forma rápida a la información más relevante. Cada cambio, actualización o anuncio se publicará directamente aquí.
        </p>
      </article>

      <article class="about-item">
        <h3>ÚNETE A LA EXPERIENCIA</h3>
        <p>
          Si eres competitivo, si disfrutas de los desafíos y si buscas un torneo donde tu habilidad realmente importe, entonces estás en el lugar correcto. RDC no es solo un torneo: es una arena para leyendas.
        </p>
      </article>
    </div>
  </section>
  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
