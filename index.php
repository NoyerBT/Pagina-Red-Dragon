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

  <main class="hero" id="torneo">
    <section class="hero-content">
      <h1>RED DRAGON´S | WHITING</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">ANTICHEATS | SERVIDOR PRIVADO | ORGANIZA TU TORNEO.</p>
      <div class="hero-buttons">
        <a href="#equipos" class="btn primary">Mas Informacion</a>
        <button type="button" class="btn secondary" id="open-rules">Ver reglas</button>
      </div>
  </main>

  <section class="rules-modal" id="rules-modal" aria-hidden="true" role="dialog" aria-label="Reglas del torneo">
    <div class="rules-modal__backdrop" data-close-modal></div>
    <div class="rules-modal__dialog" role="document">
      <button type="button" class="rules-modal__close" aria-label="Cerrar" data-close-modal>&times;</button>
      <p class="rules-modal__eyebrow">𝙍𝙀𝙂𝙇𝘼𝙎 𝘿𝙀𝙇 𝙏𝙊𝙍𝙉𝙀𝙊 𝙍𝘿𝘾 2025</p>
      <h3>𝙍𝙀𝙂𝙇𝘼𝙎 𝙋𝘼𝙍𝘼 𝙄𝙉𝙎𝘾𝙍𝙄𝘽𝙄𝙍𝙎𝙀</h3>
      <ol class="rules-list">
        <li>El mínimo de horas para participar es de 600 hrs a más.</li>
        <li>Los participantes no deberán tener baneos por cheats en ninguna comunidad.</li>
        <li>Debes registrarte con tu cuenta principal.</li>
        <li>Durante el periodo de inscripciones los perfiles de los jugadores tienen que ser públicos en todos los ámbitos para poder confirmar que cumplen los requisitos.</li>
        <li>Un participante no debe estar inscrito en más de un equipo al mismo tiempo.</li>
        <li>Cada equipo debe tener un nombre y logo de manera obligatoria, con un máximo de 6 integrantes.</li>
        <li>El nombre del equipo y el logo no deben contener imágenes implícitas, sexuales, racistas, homofóbicas o referencias; esto también aplica a los nombres de los participantes registrados.</li>
      </ol>
    </div>
  </section>

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
