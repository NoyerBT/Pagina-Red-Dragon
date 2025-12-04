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
      <h1>RED DRAGONS CORPORATION</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">ANTICHEATS | SERVIDOR PRIVADO | ORGANIZA TU TORNEO.</p>

  </main>



  <section class="section about-section" id="equipos">
    <h2>SOBRE NUESTRA PÁGINA / ABOUT OUR PAGE</h2>
    <div class="about-wrapper">
      <article class="about-item">
        <h3>¿QUÉ ES RDC? / WHAT IS RDC?</h3>
        <p>
          Esta página es una plataforma de la Red Dragon Cup, torneo del videojuego Left 4 Dead 2 diseñado para la comunidad y los jugadores que buscan una experiencia seria, organizada y con un ambiente de alto nivel. Nuestra página reúne toda la información oficial del torneo, desde reglas y clasificación hasta anuncios importantes y contenido exclusivo del torneo.
        </p>
        <p class="text-en">
          This page is a platform for the Red Dragon Cup, a Left 4 Dead 2 tournament designed for the community and players seeking a serious, organized experience with a high-level environment. Our page gathers all official tournament information, from rules and standings to important announcements and exclusive tournament content.
        </p>
      </article>

      <article class="about-item">
        <h3>NUESTRA MISIÓN / OUR MISSION</h3>
        <p>
          Queremos ofrecer un torneo transparente, justo y emocionante, donde cada equipo tenga la oportunidad de demostrar su habilidad y competir en igualdad de condiciones. RDC nace con el objetivo de fortalecer la escena competitiva y brindar un espacio donde la comunidad pueda crecer, participar y disfrutar.
        </p>
        <p class="text-en">
          We want to offer a transparent, fair, and exciting tournament where every team has the opportunity to demonstrate their skill and compete on equal terms. RDC was born with the objective of strengthening the competitive scene and providing a space where the community can grow, participate, and enjoy.
        </p>
      </article>

      <article class="about-item">
        <h3>¿QUÉ OFRECEMOS? / WHAT DO WE OFFER?</h3>
        <ul class="about-list">
          <li>
            Torneos organizados profesionalmente con formatos claros, grupos o eliminación directa.
            <div class="text-en">Professionally organized tournaments with clear formats, groups, or direct elimination.</div>
          </li>
          <li>
            Clasificaciones y estadísticas actualizadas para mantener a la comunidad informada.
            <div class="text-en">Updated standings and statistics to keep the community informed.</div>
          </li>
          <li>
            Reglamento detallado para garantizar un ambiente justo y competitivo.
            <div class="text-en">Detailed regulations to ensure a fair and competitive environment.</div>
          </li>
          <li>
            Sistema de contacto directo para resolver dudas o enviar documentos.
            <div class="text-en">Direct contact system to resolve doubts or send documents.</div>
          </li>
          <li>
            Premios y reconocimientos para los mejores equipos.
            <div class="text-en">Prizes and recognition for the best teams.</div>
          </li>
        </ul>
      </article>

      <article class="about-item">
        <h3>COMUNIDAD Y TRANSPARENCIA / COMMUNITY & TRANSPARENCY</h3>
        <p>
          Creemos en la comunicación abierta con los participantes, por eso nuestro portal está diseñado para que cualquier jugador o equipo pueda acceder de forma rápida a la información más relevante. Cada cambio, actualización o anuncio se publicará directamente aquí.
        </p>
        <p class="text-en">
          We believe in open communication with participants, which is why our portal is designed so that any player or team can quickly access the most relevant information. Every change, update, or announcement will be published directly here.
        </p>
      </article>

      <article class="about-item">
        <h3>ÚNETE A LA EXPERIENCIA / JOIN THE EXPERIENCE</h3>
        <p>
          Si eres competitivo, si disfrutas de los desafíos y si buscas un torneo donde tu habilidad realmente importe, entonces estás en el lugar correcto. RDC no es solo un torneo: es una arena para leyendas.
        </p>
        <p class="text-en">
          If you are competitive, if you enjoy challenges, and if you are looking for a tournament where your skill really matters, then you are in the right place. RDC is not just a tournament: it is an arena for legends.
        </p>
      </article>
    </div>
  </section>
  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
