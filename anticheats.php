<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Anticheats - Red Dragons Cup</title>
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
      <a href="salon_fama.php">SALÓN DE LA FAMA</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero hero--anticheat">
    <section class="hero-content">
      <h1>Sistema Anticheat</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle hero-tagline">Protección avanzada para un juego limpio y competitivo.</p>
    </section>
  </main>

  <section class="section" id="cta-anticheat">
    <div class="plan-actions">
      <?php if (isset($_SESSION['usuario'])): ?>
        <div class="btn-wrapper plan-actions__btn">
          <a href="generar_claves.php" class="btn">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">G</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">e</span><span class="btn-letter">r</span><span class="btn-letter">a</span><span class="btn-letter">r</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">G</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">e</span><span class="btn-letter">r</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
              </div>
            </div>
          </a>
        </div>
        <div class="btn-wrapper plan-actions__btn">
          <a href="https://github.com/HQ27x/anticheatRDC/releases/download/v3/RDC_VerifierV3.exe" class="btn">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">D</span><span class="btn-letter">e</span><span class="btn-letter">s</span><span class="btn-letter">c</span><span class="btn-letter">a</span><span class="btn-letter">r</span><span class="btn-letter">g</span><span class="btn-letter">a</span><span class="btn-letter">r</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">D</span><span class="btn-letter">e</span><span class="btn-letter">s</span><span class="btn-letter">c</span><span class="btn-letter">a</span><span class="btn-letter">r</span><span class="btn-letter">g</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
              </div>
            </div>
          </a>
        </div>
      <?php else: ?>
        <div class="btn-wrapper plan-actions__btn">
          <a href="https://github.com/HQ27x/anticheatRDC/releases/download/v3/RDC_VerifierV3.exe" class="btn">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">D</span><span class="btn-letter">e</span><span class="btn-letter">s</span><span class="btn-letter">c</span><span class="btn-letter">a</span><span class="btn-letter">r</span><span class="btn-letter">g</span><span class="btn-letter">a</span><span class="btn-letter">r</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">D</span><span class="btn-letter">e</span><span class="btn-letter">s</span><span class="btn-letter">c</span><span class="btn-letter">a</span><span class="btn-letter">r</span><span class="btn-letter">g</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
              </div>
            </div>
          </a>
        </div>
        <div class="btn-wrapper plan-actions__btn">
          <a href="registro.php" class="btn">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">A</span><span class="btn-letter">d</span><span class="btn-letter">q</span><span class="btn-letter">u</span><span class="btn-letter">i</span><span class="btn-letter">r</span><span class="btn-letter">i</span><span class="btn-letter">r</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">A</span><span class="btn-letter">d</span><span class="btn-letter">q</span><span class="btn-letter">u</span><span class="btn-letter">i</span><span class="btn-letter">r</span><span class="btn-letter">i</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
              </div>
            </div>
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php if (!isset($_SESSION['usuario'])): ?>
      <p class="plan-note plan-note--highlight">Necesitas crear una cuenta para continuar</p>
    <?php endif; ?>
  </section>

  <section class="section" id="anticheat-info">
    <h2>¿Qué es nuestro Anticheat?</h2>
    <p>Nuestro sistema anticheat garantiza un entorno de juego justo y competitivo para todos los participantes del torneo Red Dragons Cup.</p>
    
    <div class="features-grid">
      <div class="feature-card">
        <h3>🛡️ Protección ante tramposos</h3>
        <p>Detección de programas y hacks en dispositivo de losusuarios.</p>
      </div>
      <div class="feature-card">
        <h3>⚡ Rendimiento Optimizado</h3>
        <p>Mínimo impacto en el rendimiento de tu juego.</p>
      </div>
      <div class="feature-card">
        <h3>🔒 Seguridad Total</h3>
        <p>Protección de datos y privacidad garantizada.</p>
      </div>
    </div>
  </section>

  <section class="section" id="plan-anticheat">
    <h2>Plan Anticheat Premium</h2>
    <div class="plan-container">
      <article class="plan-card-modern">
        <div class="plan-card-modern__border"></div>
        <div class="plan-card-modern__title-group">
          <span class="plan-card-modern__eyebrow">Protección total</span>
          <h3>🏆 Plan Premium</h3>
          <p class="plan-card-modern__price">
            <span class="currency">S/</span>
            <span class="amount">40</span>
            <span class="period">/mes</span>
          </p>
          <p class="plan-card-modern__subtitle">La mejor defensa contra cheats para equipos competitivos.</p>
        </div>
        <hr class="plan-card-modern__divider" />
        <ul class="plan-card-modern__list">
          <?php $planFeatures = [
            'Soporte 24/7',
            'Acceso a canal privado de whatssapp',
            'Soporte técnico prioritario',
            'Rol en discord',
            'Uso de futuras actualizaciones'
          ]; ?>
          <?php foreach ($planFeatures as $feature): ?>
            <li class="plan-card-modern__list-item">
              <span class="plan-card-modern__check">
                <svg class="plan-card-modern__check-icon" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" />
                </svg>
              </span>
              <span class="plan-card-modern__list-text"><?= $feature ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <a href="registro.php" class="Btn" style="margin-top: 1.4rem;"></a>
      </article>
    </div>
  </section>

  <section class="section" id="como-funciona">
    <h2>¿Cómo Funciona?</h2>
    <div class="steps-container">
      <div class="step">
        <div class="mac-header">
          <span class="red"></span>
          <span class="yellow"></span>
          <span class="green"></span>
        </div>
        <div class="step-number">1</div>
        <h3>Registro</h3>
        <p>Crea tu cuenta en nuestra plataforma</p>
      </div>
      <div class="step">
        <div class="mac-header">
          <span class="red"></span>
          <span class="yellow"></span>
          <span class="green"></span>
        </div>
        <div class="step-number">2</div>
        <h3>Pago</h3>
        <p>Adquiere el plan premium por S/ 40</p>
      </div>
      <div class="step">
        <div class="mac-header">
          <span class="red"></span>
          <span class="yellow"></span>
          <span class="green"></span>
        </div>
        <div class="step-number">3</div>
        <h3>Instalación</h3>
        <p>Descarga e instala nuestro cliente</p>
      </div>
      <div class="step">
        <div class="mac-header">
          <span class="red"></span>
          <span class="yellow"></span>
          <span class="green"></span>
        </div>
        <div class="step-number">4</div>
        <h3>¡Juega!</h3>
        <p>Disfruta de partidas limpias y competitivas</p>
      </div>
    </div>
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
