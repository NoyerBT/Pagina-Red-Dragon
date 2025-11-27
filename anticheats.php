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
        <a href="generar_claves.php" class="btn primary plan-btn glow-on-hover plan-actions__btn">Generar Claves</a>
        <a href="https://github.com/HQ27x/anticheatRDC/releases/download/v3/RDC_VerifierV3.exe" class="cta-download plan-actions__btn">
          <span>Descargar Anticheat</span>
          <svg fill="none" viewBox="0 0 24 24" class="cta-download__arrow" aria-hidden="true">
            <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2" stroke="currentColor" d="M5 12h14M13 6l6 6-6 6"></path>
          </svg>
        </a>
      <?php else: ?>
        <a href="https://github.com/HQ27x/anticheatRDC/releases/download/v3/RDC_VerifierV3.exe" class="cta-download plan-actions__btn">
          <span>Descargar Anticheat</span>
          <svg fill="none" viewBox="0 0 24 24" class="cta-download__arrow" aria-hidden="true">
            <path stroke-linejoin="round" stroke-linecap="round" stroke-width="2" stroke="currentColor" d="M5 12h14M13 6l6 6-6 6"></path>
          </svg>
        </a>
        <a href="registro.php" class="cta-glow plan-actions__btn">
          <div class="cta-glow__display">
            <div class="cta-glow__text">Adquirir Plan</div>
          </div>
          <span></span>
          <span></span>
        </a>
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
        <h3>🛡️ Protección en Tiempo Real</h3>
        <p>Detección instantánea de cheats y hacks durante las partidas.</p>
      </div>
      <div class="feature-card">
        <h3>🔍 Análisis Avanzado</h3>
        <p>Algoritmos de última generación para identificar comportamientos sospechosos.</p>
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
            <span class="amount">30</span>
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
        <a href="registro.php" class="plan-card-modern__cta">Adquirir Plan</a>
      </article>
    </div>
  </section>

  <section class="section" id="como-funciona">
    <h2>¿Cómo Funciona?</h2>
    <div class="steps-container">
      <div class="step">
        <div class="step-number">1</div>
        <h3>Registro</h3>
        <p>Crea tu cuenta en nuestra plataforma</p>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <h3>Pago</h3>
        <p>Adquiere el plan premium por S/ 30</p>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <h3>Instalación</h3>
        <p>Descarga e instala nuestro cliente</p>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <h3>¡Juega!</h3>
        <p>Disfruta de partidas limpias y competitivas</p>
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
