<?php
session_start();
require_once 'cnt/conexion.php';

$userHasAccount = isset($_SESSION['usuario']);
$plan_activo = false;
$dias_restantes = null;
$usuario_id = null;
$mis_torneos = [];

if ($userHasAccount) {
    $stmt = $conn->prepare("SELECT id, estado, fecha_expiracion FROM usuarios WHERE usuario = ? LIMIT 1");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $usuario_id = $user['id'];
        if ($user['estado'] === 'activo' && !empty($user['fecha_expiracion'])) {
            try {
                $hoy = new DateTime('today');
                $expiracion = new DateTime($user['fecha_expiracion']);
                if ($expiracion >= $hoy) {
                    $plan_activo = true;
                    $dias_restantes = (int)$hoy->diff($expiracion)->format('%a');
                }
            } catch (Exception $e) {
                error_log('Error evaluando expiración de plan: ' . $e->getMessage());
            }
        }
    }

    $stmt->close();
    
    // Cargar torneos del usuario si tiene plan activo
    if ($plan_activo && $usuario_id) {
        // Verificar si la tabla existe
        $check_table = $conn->query("SHOW TABLES LIKE 'torneos'");
        if ($check_table->num_rows > 0) {
            $stmt = $conn->prepare("SELECT t.id, t.nombre_torneo, t.logo, t.fecha_creacion, 
                                   COUNT(e.id) as total_equipos 
                                   FROM torneos t 
                                   LEFT JOIN equipos_torneo e ON t.id = e.torneo_id 
                                   WHERE t.usuario_id = ? 
                                   GROUP BY t.id 
                                   ORDER BY t.fecha_creacion DESC");
            if ($stmt !== false) {
                $stmt->bind_param("i", $usuario_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($torneo = $result->fetch_assoc()) {
                    $mis_torneos[] = $torneo;
                }
                $stmt->close();
            }
        }
    }
}

// No cerrar la conexión aquí, se cerrará al final del archivo después de usarla
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Organizar mi torneo - Red Dragons Cup</title>
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
      <?php if ($userHasAccount): ?>
        <a href="torneo.php">TORNEO</a>
      <?php endif; ?>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="salon_fama.php">SALÓN DE LA FAMA</a>
      <?php if ($userHasAccount): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero hero--organizador">
    <section class="hero-content">
      <h1>Organiza tu torneo</h1>
      <?php if ($userHasAccount && $plan_activo): ?>
        <p class="subtitle">Genera brackets, matches y lleva el control de tus llaves en minutos. <span class="text-success">Plan Anticheat activo<?php if ($dias_restantes !== null) { echo ' • ' . $dias_restantes . ' días restantes'; } ?></span></p>
      <?php elseif ($userHasAccount): ?>
        <p class="subtitle">Activa tu plan Anticheat para desbloquear el generador de llaves y gestionar tus encuentros privados.</p>
      <?php else: ?>
        <p class="subtitle">Esta herramienta está disponible únicamente para usuarios registrados con un plan Anticheat vigente.</p>
      <?php endif; ?>

      <div class="hero-buttons plan-actions">
        <?php if (!$userHasAccount): ?>
          <div class="btn-wrapper plan-actions__btn">
            <a href="registro.php" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">C</span><span class="btn-letter">r</span><span class="btn-letter">e</span><span class="btn-letter">a</span><span class="btn-letter">r</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">C</span><span class="btn-letter">r</span><span class="btn-letter">e</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
                </div>
              </div>
            </a>
          </div>
          <div class="btn-wrapper plan-actions__btn">
            <a href="login.php" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">I</span><span class="btn-letter">n</span><span class="btn-letter">i</span><span class="btn-letter">c</span><span class="btn-letter">i</span><span class="btn-letter">a</span><span class="btn-letter">r</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">I</span><span class="btn-letter">n</span><span class="btn-letter">i</span><span class="btn-letter">c</span><span class="btn-letter">i</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
                </div>
              </div>
            </a>
          </div>
        <?php elseif (!$plan_activo): ?>
          <div class="btn-wrapper plan-actions__btn">
            <a href="anticheats.php" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">V</span><span class="btn-letter">e</span><span class="btn-letter">r</span><span class="btn-letter">P</span><span class="btn-letter">l</span><span class="btn-letter">a</span><span class="btn-letter">n</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">V</span><span class="btn-letter">i</span><span class="btn-letter">s</span><span class="btn-letter">u</span><span class="btn-letter">a</span><span class="btn-letter">l</span><span class="btn-letter">i</span><span class="btn-letter">z</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
                </div>
              </div>
            </a>
          </div>
          <div class="btn-wrapper plan-actions__btn">
            <a href="dashboard.php" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">M</span><span class="btn-letter">i</span><span class="btn-letter">C</span><span class="btn-letter">u</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">t</span><span class="btn-letter">a</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">A</span><span class="btn-letter">c</span><span class="btn-letter">c</span><span class="btn-letter">e</span><span class="btn-letter">d</span><span class="btn-letter">i</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
                </div>
              </div>
            </a>
          </div>
        <?php else: ?>
          <div class="btn-wrapper plan-actions__btn">
            <a href="crear_torneo.php" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">E</span><span class="btn-letter">m</span><span class="btn-letter">p</span><span class="btn-letter">e</span><span class="btn-letter">z</span><span class="btn-letter">a</span><span class="btn-letter">r</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">C</span><span class="btn-letter">o</span><span class="btn-letter">m</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">z</span><span class="btn-letter">a</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
                </div>
              </div>
            </a>
          </div>
          <div class="btn-wrapper plan-actions__btn">
            <a href="#" onclick="abrirModalMiServidor(); return false;" class="btn">
              <svg class="btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path>
              </svg>
              <div class="txt-wrapper">
                <div class="txt-1">
                  <span class="btn-letter">M</span><span class="btn-letter">i</span>
                  <span class="btn-letter"> </span>
                  <span class="btn-letter">S</span><span class="btn-letter">e</span><span class="btn-letter">r</span><span class="btn-letter">v</span><span class="btn-letter">i</span><span class="btn-letter">d</span><span class="btn-letter">o</span><span class="btn-letter">r</span>
                </div>
                <div class="txt-2">
                  <span class="btn-letter">A</span><span class="btn-letter">c</span><span class="btn-letter">c</span><span class="btn-letter">e</span><span class="btn-letter">d</span><span class="btn-letter">i</span><span class="btn-letter">e</span><span class="btn-letter">n</span><span class="btn-letter">d</span><span class="btn-letter">o</span>
                </div>
              </div>
            </a>
          </div>
        <?php endif; ?>
      </div>

      <div class="info-tags">
        <span>Integracion de equipos</span>
        <span>Brackets personalizables o aleatorios</span>
        <span>Servidor privado</span>
      </div>
    </section>
  </main>

  <section class="section organizer-steps">
    <h2>Capacidades del organizador</h2>
    <div class="organizer-steps-grid">
      <article class="organizer-card">
        <span class="organizer-card__icon">🧩</span>
        <h3>Libertad</h3>
        <p>Agrega y elimina equipos, define matchs y usa tu servidor como y cuando quieras.</p>
      </article>
      <article class="organizer-card">
        <span class="organizer-card__icon">🎮</span>
        <h3>Controla cada match</h3>
        <p>Registra puntajes, marca ganadores y reinicia partidas sin necesidad de hojas externas.</p>
      </article>
      <article class="organizer-card">
        <span class="organizer-card__icon">🛡️</span>
        <h3>Anticheat</h3>
        <p>El organizador con suscripción activa, puede generar tokens de revision y contar con las actualizaciones del anticheats.</p>
      </article>
    </div>
  </section>

  <?php if ($userHasAccount && $plan_activo): ?>
    <section class="section" style="margin-bottom: 3rem; max-width: 1200px; margin-left: auto; margin-right: auto; padding: 2rem 1rem;">
      <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="color: #d4af37; font-size: 2.5rem; margin-bottom: 1rem;">Mis Torneos</h2>
        <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1rem;">Gestiona y organiza todos tus torneos desde un solo lugar</p>
      </div>
      
      <?php if (count($mis_torneos) > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
          <?php foreach ($mis_torneos as $torneo): ?>
            <div class="torneo-card" style="background: rgba(0, 0, 0, 0.7); border: 2px solid rgba(212, 175, 55, 0.3); border-radius: 20px; padding: 1.5rem; transition: all 0.3s ease; position: relative; overflow: hidden;">
              <?php if (!empty($torneo['logo'])): ?>
                <div style="text-align: center; margin-bottom: 1rem;">
                  <img src="<?php echo htmlspecialchars($torneo['logo']); ?>" alt="<?php echo htmlspecialchars($torneo['nombre_torneo']); ?>" style="max-width: 120px; max-height: 120px; border-radius: 12px; border: 2px solid rgba(212, 175, 55, 0.3); object-fit: cover;">
                </div>
              <?php endif; ?>
              <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <h3 style="color: #d4af37; margin: 0; font-size: 1.5rem;"><?php echo htmlspecialchars($torneo['nombre_torneo']); ?></h3>
                <span style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">
                  <?php 
                    $fecha = new DateTime($torneo['fecha_creacion']);
                    echo $fecha->format('d/m/Y');
                  ?>
                </span>
              </div>
              <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(255, 255, 255, 0.8); margin-bottom: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span><?php echo $torneo['total_equipos']; ?> equipos</span>
              </div>
              <div style="padding-top: 1rem; border-top: 1px solid rgba(212, 175, 55, 0.2); display: flex; flex-direction: column; gap: 0.75rem;">
                <button type="button" onclick="window.location.href='editar_torneo.php?torneo_id=<?php echo $torneo['id']; ?>'" style="background: transparent; border: 1px solid rgba(212, 175, 55, 0.4); color: #d4af37; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-align: center; width: 100%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                  Editar torneo
                </button>
                <button type="button" onclick="window.location.href='crear_torneo.php?torneo_id=<?php echo $torneo['id']; ?>'" style="background: transparent; border: 1px solid rgba(52, 152, 219, 0.4); color: #3498db; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-align: center; width: 100%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  </svg>
                  Editar equipos
                </button>
                <button type="button" onclick="window.location.href='brackets_torneo.php?torneo_id=<?php echo $torneo['id']; ?>'" style="background: transparent; border: 1px solid rgba(155, 89, 182, 0.4); color: #9b59b6; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-align: center; width: 100%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M9 14l2 2 4-4"></path>
                  </svg>
                  Brackets
                </button>
                <button type="button" onclick="eliminarTorneo(<?php echo $torneo['id']; ?>, '<?php echo htmlspecialchars($torneo['nombre_torneo'], ENT_QUOTES); ?>')" style="background: transparent; border: 1px solid rgba(231, 76, 60, 0.4); color: #e74c3c; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-align: center; width: 100%;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                  </svg>
                  Eliminar torneo
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 4rem 2rem; background: rgba(0, 0, 0, 0.5); border: 2px dashed rgba(212, 175, 55, 0.3); border-radius: 20px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="rgba(212, 175, 55, 0.5)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 2rem;">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
            <path d="M9 14l2 2 4-4"></path>
          </svg>
          <h3 style="color: #d4af37; font-size: 1.8rem; margin-bottom: 1rem;">Aquí verás tus torneos</h3>
          <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Cuando crees tu primer torneo, aparecerá en esta sección. Podrás gestionar equipos, generar llaves y administrar todos los detalles de tus competencias.
          </p>
          <a href="crear_torneo.php" style="display: inline-block; background: linear-gradient(135deg, #d4af37, #c09b2d); color: #000; padding: 1rem 2.5rem; border-radius: 10px; font-weight: bold; text-decoration: none; transition: all 0.3s ease;">
            🏆 Crear Mi Primer Torneo
          </a>
        </div>
      <?php endif; ?>
    </section>
  <?php elseif ($userHasAccount): ?>
    <?php if (!$plan_activo): ?>
      <section class="section" style="max-width: 800px; margin: 0 auto; padding: 3rem 2rem;">
        <div style="text-align: center; padding: 3rem 2rem; background: rgba(0, 0, 0, 0.5); border: 2px dashed rgba(212, 175, 55, 0.3); border-radius: 20px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="rgba(212, 175, 55, 0.5)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 2rem;">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          <h3 style="color: #d4af37; font-size: 1.8rem; margin-bottom: 1rem;">Plan Anticheat Requerido</h3>
          <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1rem; margin-bottom: 2rem;">
            Necesitas un plan Anticheat activo para crear y gestionar tus torneos.
          </p>
          <a href="anticheats.php" style="display: inline-block; background: linear-gradient(135deg, #d4af37, #c09b2d); color: #000; padding: 1rem 2.5rem; border-radius: 10px; font-weight: bold; text-decoration: none; transition: all 0.3s ease;">
            Ver Planes Disponibles
          </a>
        </div>
      </section>
    <?php endif; ?>
  <?php endif; ?>

  <style>
    .torneo-card:hover {
      transform: translateY(-5px);
      border-color: rgba(212, 175, 55, 0.6);
      box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
    }
    
    .torneo-card button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    .torneo-card button[onclick*="eliminarTorneo"]:hover {
      background: rgba(231, 76, 60, 0.1) !important;
      border-color: rgba(231, 76, 60, 0.6) !important;
    }
    
    .torneo-card button[onclick*="editar_torneo"]:hover {
      background: rgba(212, 175, 55, 0.1) !important;
      border-color: rgba(212, 175, 55, 0.6) !important;
    }
    
    .torneo-card button[onclick*="crear_torneo"]:hover {
      background: rgba(52, 152, 219, 0.1) !important;
      border-color: rgba(52, 152, 219, 0.6) !important;
    }
    
    .torneo-card button[onclick*="brackets_torneo"]:hover {
      background: rgba(155, 89, 182, 0.1) !important;
      border-color: rgba(155, 89, 182, 0.6) !important;
    }
    
    @media (max-width: 768px) {
      .torneo-card {
        min-width: 100%;
      }
    }
  </style>
  
  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
  
  <script>
    function eliminarTorneo(torneoId, nombreTorneo) {
      if (!confirm(`¿Estás seguro de que deseas eliminar el torneo "${nombreTorneo}"?\n\nEsta acción no se puede deshacer y se eliminarán todos los equipos asociados.`)) {
        return;
      }

      const formData = new FormData();
      formData.append('torneo_id', torneoId);

      fetch('ajax_eliminar_torneo.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          // Recargar la página para actualizar la lista
          window.location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        alert('Error al eliminar el torneo: ' + error);
      });
    }
  </script>
  
  <!-- Modal MI SERVIDOR -->
  <div id="modalMiServidor" class="modal-servidor" style="display: none;">
    <div class="modal-servidor-content">
      <div class="modal-servidor-header">
        <h2>🖥️ Mi Servidor Privado</h2>
        <button class="modal-servidor-close" onclick="cerrarModalMiServidor()">&times;</button>
      </div>
      <div class="modal-servidor-body" id="modalServidorBody">
        <div class="loading-servidor">
          <p>Cargando información del servidor...</p>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    .modal-servidor {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.85);
      z-index: 10000;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .modal-servidor-content {
      background: linear-gradient(135deg, rgba(20, 20, 20, 0.98), rgba(30, 30, 30, 0.98));
      border: 2px solid #d4af37;
      border-radius: 15px;
      width: 90%;
      max-width: 600px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
      animation: slideUp 0.3s ease;
    }
    
    @keyframes slideUp {
      from { transform: translateY(50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    .modal-servidor-header {
      padding: 1.5rem;
      border-bottom: 2px solid rgba(212, 175, 55, 0.3);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .modal-servidor-header h2 {
      color: #d4af37;
      margin: 0;
      font-size: 1.8rem;
    }
    
    .modal-servidor-close {
      background: transparent;
      border: none;
      color: #fff;
      font-size: 2rem;
      cursor: pointer;
      padding: 0;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.3s ease;
    }
    
    .modal-servidor-close:hover {
      background: rgba(212, 175, 55, 0.2);
      color: #d4af37;
    }
    
    .modal-servidor-body {
      padding: 2rem;
      color: #fff;
    }
    
    .loading-servidor {
      text-align: center;
      padding: 2rem;
      color: #d4af37;
    }
    
    .servidor-info {
      text-align: center;
    }
    
    .servidor-ip {
      background: rgba(212, 175, 55, 0.1);
      border: 2px solid #d4af37;
      border-radius: 10px;
      padding: 1.5rem;
      margin: 1rem 0;
    }
    
    .servidor-ip-label {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
    }
    
    .servidor-ip-value {
      color: #d4af37;
      font-size: 1.8rem;
      font-weight: bold;
      font-family: 'Courier New', monospace;
      word-break: break-all;
    }
    
    .servidor-no-vip {
      text-align: center;
      padding: 2rem;
      color: rgba(255, 255, 255, 0.7);
    }
    
    .servidor-no-vip h3 {
      color: #d4af37;
      margin-bottom: 1rem;
    }
    
    .servidor-error {
      text-align: center;
      padding: 2rem;
      color: #e74c3c;
    }
  </style>
  
  <script>
    function abrirModalMiServidor() {
      const modal = document.getElementById('modalMiServidor');
      const body = document.getElementById('modalServidorBody');
      
      modal.style.display = 'flex';
      body.innerHTML = '<div class="loading-servidor"><p>Cargando información del servidor...</p></div>';
      
      // Cargar IP del servidor
      fetch('ajax_obtener_ip_servidor.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (data.es_vip) {
            // Usuario VIP
            if (data.ip_servidor) {
              // VIP con IP asignada
              body.innerHTML = `
                <div class="servidor-info">
                  <div class="servidor-ip">
                    <div class="servidor-ip-label">Dirección IP de tu Servidor Privado</div>
                    <div class="servidor-ip-value">${data.ip_servidor}</div>
                  </div>
                  <p style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem; margin-top: 1rem;">
                    Esta IP es exclusiva para tu cuenta VIP. No la compartas con otros usuarios.
                  </p>
                </div>
              `;
            } else {
              // VIP sin IP asignada aún
              body.innerHTML = `
                <div class="servidor-no-vip">
                  <h3>⭐ Eres Usuario VIP</h3>
                  <p style="margin-top: 1rem; color: #d4af37; font-size: 1.1rem;">
                    El administrador le designará su IP muy pronto.
                  </p>
                  <p style="margin-top: 1rem; color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">
                    Recibirás tu IP de servidor privado en breve. Gracias por tu paciencia.
                  </p>
                </div>
              `;
            }
          } else {
            // Usuario NO VIP
            body.innerHTML = `
              <div class="servidor-no-vip">
                <h3>⚠️ No eres usuario VIP</h3>
                <p>Los servidores privados están disponibles únicamente para usuarios VIP.</p>
                <p style="margin-top: 1rem; color: #d4af37;">Contacta con un administrador para obtener acceso VIP.</p>
              </div>
            `;
          }
        } else {
          body.innerHTML = `
            <div class="servidor-error">
              <h3>❌ Error</h3>
              <p>${data.message || 'No se pudo cargar la información del servidor.'}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        body.innerHTML = `
          <div class="servidor-error">
            <h3>❌ Error</h3>
            <p>Error al conectar con el servidor. Por favor, intenta nuevamente.</p>
          </div>
        `;
      });
    }
    
    function cerrarModalMiServidor() {
      document.getElementById('modalMiServidor').style.display = 'none';
    }
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('modalMiServidor').addEventListener('click', function(e) {
      if (e.target === this) {
        cerrarModalMiServidor();
      }
    });
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        cerrarModalMiServidor();
      }
    });
  </script>
  
  <?php
  // Cerrar conexión al final
  if (isset($conn)) {
    $conn->close();
  }
  ?>
</body>
</html>
