<?php
session_start();

// Verificar si el usuario es VIP
$es_vip = false;
$vip_activo = false;

if (isset($_SESSION['usuario'])) {
    require_once 'cnt/conexion.php';
    
    $sql = "SELECT vip, fecha_expiracion FROM usuarios WHERE usuario = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Verificar explícitamente que vip sea exactamente 1 (no null, no 0, no string)
        if (isset($user['vip']) && $user['vip'] == 1 && $user['vip'] !== '0' && $user['vip'] !== 0) {
            $es_vip = true;
            // Verificar si la fecha de expiración no ha pasado
            if (!empty($user['fecha_expiracion'])) {
                $fecha_expiracion = new DateTime($user['fecha_expiracion']);
                $fecha_actual = new DateTime();
                if ($fecha_expiracion >= $fecha_actual) {
                    $vip_activo = true;
                }
            } else {
                // Si no tiene fecha de expiración pero es VIP, considerarlo activo
                $vip_activo = true;
            }
        } else {
            // Asegurarse de que si no es VIP, las variables estén en false
            $es_vip = false;
            $vip_activo = false;
        }
    }
    
    $stmt->close();
    $conn->close();
}
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
      <?php if (isset($_SESSION['usuario']) && $vip_activo): ?>
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
          <a href="https://github.com/HQ27x/anticheatRDC/releases/download/ver3/RDC-ATv3.exe" class="btn">
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
        <!-- Botón Betas (Nuevo VIP) -->
        <div class="btn-wrapper plan-actions__btn">
          <button onclick="openModal()" class="btn" style="border: none; cursor: pointer;">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">B</span><span class="btn-letter">e</span><span class="btn-letter">t</span><span class="btn-letter">a</span><span class="btn-letter">s</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">B</span><span class="btn-letter">e</span><span class="btn-letter">t</span><span class="btn-letter">a</span><span class="btn-letter">s</span>
              </div>
            </div>
          </button>
        </div>
        <!-- Botón OPF (Nuevo VIP) -->
        <div class="btn-wrapper plan-actions__btn">
          <a href="https://github.com/HQ27x/anticheatRDC/releases/download/updates/OPF-decoder.exe" class="btn">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.794-.43l-8.916 8.916a2.25 2.25 0 00-1.932 5.861" />
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">O</span><span class="btn-letter">P</span><span class="btn-letter">F</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">O</span><span class="btn-letter">P</span><span class="btn-letter">F</span>
              </div>
            </div>
          </a>
        </div>
      <?php elseif (isset($_SESSION['usuario']) && !$vip_activo): ?>
        <div style="text-align: center; padding: 2rem; color: rgba(255, 255, 255, 0.7);">
          <h3 style="color: #d4af37; margin-bottom: 1rem;">⭐ Acceso VIP Requerido</h3>
          <p style="margin-bottom: 1rem;">Los botones de generar claves y descargar el anticheat están disponibles únicamente para usuarios VIP.</p>
          <div class="btn-wrapper plan-actions__btn" style="margin-top: 2rem; display: inline-block;">
            <a href="pago.php" class="btn">
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
          <!-- Botón OPF (Nuevo No-VIP) -->
          <div class="btn-wrapper plan-actions__btn" style="margin-top: 2rem; display: inline-block; margin-left: 10px;">
            <a href="https://github.com/HQ27x/anticheatRDC/releases/download/updates/OPF-decoder.exe" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.794-.43l-8.916 8.916a2.25 2.25 0 00-1.932 5.861" />
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">O</span><span class="btn-letter">P</span><span class="btn-letter">F</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">O</span><span class="btn-letter">P</span><span class="btn-letter">F</span>
                </div>
              </div>
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="btn-wrapper plan-actions__btn">
          <a href="https://github.com/HQ27x/anticheatRDC/releases/download/ver3/RDC-ATv3.exe" class="btn">
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
          <a href="<?php echo isset($_SESSION['usuario']) ? 'pago.php' : 'registro.php'; ?>" class="btn">
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
        <!-- Botón OPF (Nuevo Visitante) -->
        <div class="btn-wrapper plan-actions__btn">
          <a href="https://github.com/HQ27x/anticheatRDC/releases/download/updates/OPF-decoder.exe" class="btn">
            <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.794-.43l-8.916 8.916a2.25 2.25 0 00-1.932 5.861" />
            </svg>
            <div class="txt-wrapper">
              <div class="txt-1">
                <span class="btn-letter">O</span><span class="btn-letter">P</span><span class="btn-letter">F</span>
              </div>
              <div class="txt-2">
                <span class="btn-letter">O</span><span class="btn-letter">P</span><span class="btn-letter">F</span>
              </div>
            </div>
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php if (!isset($_SESSION['usuario'])): ?>
      <p class="plan-note plan-note--highlight">Necesitas crear una cuenta para continuar</p>
    <?php elseif (isset($_SESSION['usuario']) && !$vip_activo): ?>
      <p class="plan-note plan-note--highlight">Necesitas ser usuario VIP para acceder a estas funciones</p>
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
        <a href="<?php echo isset($_SESSION['usuario']) ? 'pago.php' : 'registro.php'; ?>" class="Btn<?php echo (isset($_SESSION['usuario']) && $es_vip) ? ' Btn-renovar' : ''; ?>" style="margin-top: 1.4rem;"></a>
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
  <!-- Modal para Betas -->
  <div id="betaModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h3>Elige cuál probar</h3>
      <p>Selecciona una versión beta para descargar:</p>
      <a href="https://github.com/HQ27x/anticheatRDC/releases/download/beta/rdcbetav5.exe" class="btn-modal">Versión Beta V5</a>
    </div>
  </div>

  <style>
    /* Estilos del Modal */
    .modal {
      display: none; 
      position: fixed; 
      z-index: 1000; 
      left: 0; 
      top: 0; 
      width: 100%; 
      height: 100%; 
      overflow: auto; 
      background-color: rgba(0,0,0,0.8); 
      backdrop-filter: blur(5px);
    }
    .modal-content {
      background-color: #1a1a1a;
      margin: 15% auto; 
      padding: 30px; 
      border: 1px solid #d4af37; 
      width: 90%; 
      max-width: 400px; 
      color: #fff; 
      text-align: center; 
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
      animation: modalFadeIn 0.3s;
    }
    .close {
      color: #aaa; 
      float: right; 
      font-size: 28px; 
      font-weight: bold; 
      cursor: pointer;
      line-height: 1;
    }
    .close:hover,
    .close:focus {
      color: #d4af37; 
      text-decoration: none; 
      cursor: pointer;
    }
    .btn-modal {
      display: inline-block; 
      padding: 12px 25px; 
      background: linear-gradient(45deg, #d4af37, #f2d06b);
      color: #000; 
      text-decoration: none; 
      border-radius: 30px; 
      margin-top: 20px; 
      font-weight: bold;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-modal:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
    }
    @keyframes modalFadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>

  <script>
    // Lógica del Modal
    function openModal() {
      document.getElementById("betaModal").style.display = "block";
    }
    function closeModal() {
      document.getElementById("betaModal").style.display = "none";
    }
    window.onclick = function(event) {
      if (event.target == document.getElementById("betaModal")) {
        closeModal();
      }
    }
  </script>
</body>
</html>
