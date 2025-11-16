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
      <h1>Sistema Anticheat</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Protección avanzada para un juego limpio y competitivo.</p>
    </section>
  </main>

<section class="section" id="cta-anticheat">
    <div class="plan-actions" style="text-align: center; padding: 2rem 0;">
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="generar_claves.php" class="btn primary plan-btn glow-on-hover" style="margin-right: 10px;">Generar Claves</a>
            <a href="https://github.com/HQ27x/anticheatRDC/releases/download/v3/RDC_VerifierV3.exe" class="btn secondary plan-btn glow-on-hover">Descargar Anticheat</a>
        <?php else: ?>
            <a href="https://github.com/HQ27x/anticheatRDC/releases/download/v3/RDC_VerifierV3.exe" class="btn secondary plan-btn glow-on-hover">Descargar Anticheat</a>
            <a href="registro.php" class="btn primary plan-btn glow-on-hover">Adquirir Plan</a>
            <p class="plan-note" style="margin-top: 1rem;">Necesitas crear una cuenta para continuar</p>
        <?php endif; ?>
    </div>
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
      <div class="plan-card premium">
        <div class="plan-header">
          <h3>🏆 Plan Premium</h3>
          <div class="price">
            <span class="currency">S/</span>
            <span class="amount">30</span>
            <span class="period">/mes</span>
          </div>
        </div>
        
        <div class="plan-features">
          <h4>✅ Incluye:</h4>
          <ul>
            <li>✓ Protección anticheat 24/7</li>
            <li>✓ Acceso a torneos oficiales</li>
            <li>✓ Soporte técnico prioritario</li>
            <li>✓ Estadísticas detalladas</li>
            <li>✓ Badge exclusivo de jugador verificado</li>
            <li>✓ Actualizaciones automáticas</li>
          </ul>
        </div>
        
      </div>
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
