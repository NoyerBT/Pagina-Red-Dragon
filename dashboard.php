<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'cnt/conexion.php';

// Obtener información del usuario
$sql = "SELECT nombre, fecha_expiracion FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Calcular días restantes
$dias_restantes = null;
if ($user['fecha_expiracion']) {
    $hoy = new DateTime();
    $fecha_expiracion = new DateTime($user['fecha_expiracion']);
    $dias_restantes = $hoy->diff($fecha_expiracion)->format('%r%a');
    $dias_restantes = intval($dias_restantes) > 0 ? $dias_restantes : 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
  <style>
    .welcome-section {
      text-align: center;
      padding: 2rem 0;
      animation: fadeInUp 0.8s ease-out;
    }

    .welcome-title {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #ffffff 0%, #d4af37 50%, #c0c0c0 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .welcome-subtitle {
      font-size: 1.3rem;
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 2rem;
      font-weight: 400;
    }

    .dashboard-actions {
      display: flex;
      gap: 1.5rem;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 3rem;
    }

    .dashboard-card {
      background: rgba(0, 0, 0, 0.6);
      border: 2px solid rgba(212, 175, 55, 0.3);
      border-radius: 20px;
      padding: 2rem;
      min-width: 280px;
      max-width: 350px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .dashboard-card::before {
      content: "";
      position: absolute;
      inset: -2px;
      border-radius: 20px;
      padding: 2px;
      background: linear-gradient(135deg, 
        rgba(212, 175, 55, 0.2) 0%, 
        rgba(192, 192, 192, 0.2) 50%, 
        rgba(212, 175, 55, 0.2) 100%);
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      opacity: 0;
      transition: opacity 0.4s ease;
      z-index: -1;
    }

    .dashboard-card:hover::before {
      opacity: 1;
    }

    .dashboard-card:hover {
      transform: translateY(-8px);
      border-color: rgba(212, 175, 55, 0.6);
      box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 0 30px rgba(212, 175, 55, 0.2);
    }

    .card-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      display: block;
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #ffffff;
      margin-bottom: 0.5rem;
    }

    .card-description {
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.6);
      line-height: 1.6;
    }

    .plan-info {
      display: none;
      margin-top: 3rem;
      padding: 2rem;
      background: rgba(0, 0, 0, 0.8);
      border: 2px solid rgba(212, 175, 55, 0.4);
      border-radius: 20px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
      animation: fadeInUp 0.6s ease-out;
    }

    .plan-info.active {
      display: block;
    }

    .plan-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }

    .plan-header h3 {
      font-size: 1.8rem;
      color: #d4af37;
      margin: 0;
      font-weight: 600;
    }

    .plan-detail {
      margin: 1rem 0;
      font-size: 1.1rem;
      color: #ffffff;
    }

    .plan-detail strong {
      color: #d4af37;
      margin-right: 0.5rem;
    }

    .plan-status {
      padding: 0.75rem 1.5rem;
      border-radius: 10px;
      font-weight: 600;
      display: inline-block;
      margin-top: 0.5rem;
    }

    .plan-status.active {
      background: rgba(76, 175, 80, 0.2);
      color: #4CAF50;
      border: 1px solid rgba(76, 175, 80, 0.4);
    }

    .plan-status.expired {
      background: rgba(244, 67, 54, 0.2);
      color: #f44336;
      border: 1px solid rgba(244, 67, 54, 0.4);
    }

    .plan-status.inactive {
      background: rgba(192, 192, 192, 0.2);
      color: #c0c0c0;
      border: 1px solid rgba(192, 192, 192, 0.4);
    }

    .close-plan {
      margin-top: 1.5rem;
      padding: 0.75rem 2rem;
      background: rgba(212, 175, 55, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 10px;
      color: #d4af37;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .close-plan:hover {
      background: rgba(212, 175, 55, 0.2);
      border-color: rgba(212, 175, 55, 0.5);
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      .welcome-title {
        font-size: 2rem;
      }

      .welcome-subtitle {
        font-size: 1.1rem;
      }

      .dashboard-actions {
        flex-direction: column;
        align-items: center;
      }

      .dashboard-card {
        width: 100%;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="bg-overlay"></div>
  <header class="top-bar">
    <div class="top-logo-section">
      <img src="Img/Logo left 4.png" alt="Logo Left 4 Dead" class="top-logo" />
    </div>
    <nav class="nav-links">
      <a href="index.php">INICIO</a>
      <a href="torneo.php">TORNEO</a>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="salon_fama.php">SALÓN DE LA FAMA</a>
      <a href="dashboard.php">MI CUENTA</a>
      <a href="logout.php">CERRAR SESIÓN</a>
    </nav>
  </header>

  <main class="hero">
    <section class="hero-content">
      <div class="welcome-section">
        <h1 class="welcome-title">¡Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h1>
        <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
        <p class="welcome-subtitle">Estamos encantados de tenerte en Red Dragons</p>
        
        <div class="dashboard-actions">
          <div class="dashboard-card" onclick="mostrarPlan()">
            <span class="card-icon">💎</span>
            <h3 class="card-title">Mi Suscripción</h3>
            <p class="card-description">Consulta el estado de tu plan y gestiona tu suscripción</p>
          </div>
          
          <div class="dashboard-card" onclick="window.location.href='torneo.php'">
            <span class="card-icon">🏆</span>
            <h3 class="card-title">Organizar Torneo</h3>
            <p class="card-description">Crea brackets y gestiona tus encuentros privados</p>
          </div>
          
          <div class="dashboard-card" onclick="window.location.href='anticheats.php'">
            <span class="card-icon">🛡️</span>
            <h3 class="card-title">Anticheats RDC</h3>
            <p class="card-description">Conoce nuestros sistemas de protección</p>
          </div>
        </div>
      </div>
      
      <div class="plan-info" id="planInfo">
        <div class="plan-header">
          <span style="font-size: 2rem;">📅</span>
          <h3>Estado de tu Plan</h3>
        </div>
        
        <?php if ($user['fecha_expiracion']): ?>
            <div class="plan-detail">
              <strong>Fecha de vencimiento:</strong>
              <?php echo date('d/m/Y', strtotime($user['fecha_expiracion'])); ?>
            </div>
            
            <?php if ($dias_restantes !== null): ?>
                <?php if ($dias_restantes > 0): ?>
                    <div class="plan-detail">
                      <strong>Días restantes:</strong>
                      <span class="plan-status active"><?php echo $dias_restantes; ?> días activos</span>
                    </div>
                <?php else: ?>
                    <div class="plan-detail">
                      <strong>Estado:</strong>
                      <span class="plan-status expired">Plan vencido</span>
                    </div>
                    <p style="color: rgba(255, 255, 255, 0.7); margin-top: 1rem;">
                      Contacta con el administrador para renovar tu suscripción.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="plan-detail">
              <strong>Estado:</strong>
              <span class="plan-status inactive">Sin plan activo</span>
            </div>
            <p style="color: rgba(255, 255, 255, 0.7); margin-top: 1rem;">
              Contacta con el administrador para activar tu suscripción.
            </p>
        <?php endif; ?>
        
        <button class="close-plan" onclick="ocultarPlan()">Cerrar</button>
      </div>
    </section>
  </main>

  <script>
    function mostrarPlan() {
      const planInfo = document.getElementById('planInfo');
      planInfo.classList.add('active');
      planInfo.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function ocultarPlan() {
      const planInfo = document.getElementById('planInfo');
      planInfo.classList.remove('active');
      setTimeout(() => {
        document.querySelector('.welcome-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 300);
    }
  </script>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
