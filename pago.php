<?php
// Página de Pago - Red Dragons Cup
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'cnt/conexion.php';

// Verificar si existe la columna pais
$check_pais = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'pais'");
$pais_column_exists = $check_pais && $check_pais->num_rows > 0;

// Obtener información del usuario
if ($pais_column_exists) {
    $sql = "SELECT nombre, email, usuario, pais FROM usuarios WHERE usuario = ? LIMIT 1";
} else {
    $sql = "SELECT nombre, email, usuario FROM usuarios WHERE usuario = ? LIMIT 1";
}
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Determinar si es de Perú
$es_peru = false;
if ($pais_column_exists && isset($user['pais']) && $user['pais'] === 'PE') {
    $es_peru = true;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pago - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
      <h1>Proceder al Pago</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle hero-tagline">Completa tu pago para acceder al plan anticheat premium.</p>
    </section>
  </main>

  <section class="section" id="pago-form">
    <div class="registro-container">
      <div class="registro-card">
        <div class="registro-card__logo" aria-hidden="true">
          <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="presentation">
            <circle cx="32" cy="20" r="12" stroke="currentColor" stroke-width="2.5" fill="none" />
            <path d="M12 54c0-10.5 9-19 20-19s20 8.5 20 19" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" />
          </svg>
        </div>
        <p class="registro-card__title">Información de Pago</p>
        
        <div class="user-info" style="background: rgba(212, 175, 55, 0.1); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid rgba(212, 175, 55, 0.3);">
          <h3 style="color: #d4af37; margin-bottom: 1rem; font-size: 1.1rem;">👤 Usuario: <?php echo htmlspecialchars($user['usuario']); ?></h3>
          <p style="color: rgba(255, 255, 255, 0.8); margin: 0.5rem 0;"><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
          <p style="color: rgba(255, 255, 255, 0.8); margin: 0.5rem 0;"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <div class="plan-summary" style="background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid rgba(255, 255, 255, 0.1);">
          <h3 style="color: #d4af37; margin-bottom: 1rem; font-size: 1.2rem;">📋 Resumen del Plan</h3>
          <div class="plan-details">
            <p style="font-size: 1.1rem; margin-bottom: 0.5rem;"><strong>Plan Anticheat Premium</strong></p>
            <p style="font-size: 1.3rem; color: #d4af37; font-weight: bold; margin: 1rem 0;">Precio: <span class="price-highlight"><?php echo $es_peru ? 'S/ 40.00' : '$ 12.00'; ?> / mes</span></p>
            <ul style="list-style: none; padding: 0; margin-top: 1rem;">
              <li style="margin: 0.5rem 0; color: rgba(255, 255, 255, 0.8);">✓ Soporte 24/7</li>
              <li style="margin: 0.5rem 0; color: rgba(255, 255, 255, 0.8);">✓ Acceso a canal privado de Whatsapp</li>
              <li style="margin: 0.5rem 0; color: rgba(255, 255, 255, 0.8);">✓ Uso de futuras actualizaciones</li>
              <li style="margin: 0.5rem 0; color: rgba(255, 255, 255, 0.8);">✓ Servidor privado</li>
              <li style="margin: 0.5rem 0; color: rgba(255, 255, 255, 0.8);">✓ Organiza tu torneo</li>
            </ul>
          </div>
        </div>
        
        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" name="terminos" required>
            Acepto los <a href="terminos.php" target="_blank">términos y condiciones</a>
          </label>
        </div>
        
        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" name="privacidad" required>
            Acepto la <a href="privacidad.php" target="_blank">política de privacidad</a>
          </label>
        </div>
        
        <button type="button" class="registro-btn" onclick="procederPago()" style="width: 100%; margin-top: 1.5rem;">
          <span>Proceder al Pago</span>
        </button>
        
        <p class="login-link" style="text-align: center; margin-top: 1.5rem;">
          <a href="anticheats.php" style="color: #d4af37;">← Volver a Anticheats</a>
        </p>
      </div>
    </div>
  </section>

  <section class="section" id="metodos-pago">
    <h2 style="color: #d4af37; text-align: center; margin-bottom: 2rem; font-size: 2rem; text-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);">💳 Métodos de Pago Disponibles</h2>
    <div class="payment-methods">
      <?php if ($es_peru): ?>
        <!-- Métodos para usuarios de Perú -->
        <div class="payment-card">
          <h3>💳 Deposito Lemon Card - Cualquier banco</h3>
          <p>Lemon Card CCI</p>
        </div>
        <div class="payment-card">
          <h3>📱 Lemon Card</h3>
          <p>Pago móvil QR instantáneo</p>
        </div>
        <div class="payment-card">
          <h3>🏦 Transferencia Bancaria solo BCP</h3>
          <p>BCP</p>
        </div>
        <div class="payment-card">
          <h3>💳 Pagos con PAY PAL</h3>
          <p>QR de Pay Pal</p>
        </div>
      <?php else: ?>
        <!-- Solo PayPal para usuarios extranjeros -->
        <div class="payment-card">
          <h3>💳 PayPal</h3>
          <p>Pago seguro con PayPal</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Modal de Métodos de Pago -->
  <div id="modalPago" class="modal-pago" style="display: none;">
    <div class="modal-pago-content">
      <div class="modal-pago-header">
        <h2 style="color: #d4af37; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
          <i class="fas fa-credit-card"></i>
          Métodos de Pago
        </h2>
        <button class="modal-pago-close" onclick="cerrarModalPago()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-pago-body">
        <div class="pago-info-header" style="background: rgba(212, 175, 55, 0.1); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid rgba(212, 175, 55, 0.3);">
          <p style="color: rgba(255, 255, 255, 0.9); margin: 0.5rem 0;"><strong>Usuario:</strong> <span id="modalUsuario"><?php echo htmlspecialchars($user['usuario']); ?></span></p>
          <p style="color: rgba(255, 255, 255, 0.9); margin: 0.5rem 0;"><strong>Monto a Pagar:</strong> <span style="color: #d4af37; font-size: 1.2rem; font-weight: bold;"><?php echo $es_peru ? 'S/ 40.00' : '$ 12.00'; ?></span></p>
        </div>

        <div id="metodosPagoPeru" style="<?php echo $es_peru ? '' : 'display: none;'; ?>">
          <!-- Método BCP -->
          <div class="metodo-pago-card" style="background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="color: #d4af37; margin-bottom: 1rem; font-size: 1.2rem;">🏦 Transferencia Bancaria BCP</h3>
            <div style="background: rgba(0, 0, 0, 0.4); padding: 1rem; border-radius: 6px; margin-top: 1rem;">
              <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 0.5rem; font-size: 0.9rem;">Número de Cuenta:</p>
              <p style="color: #d4af37; font-size: 1.3rem; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 2px; margin: 0;">19199426737045</p>
            </div>
          </div>

          <!-- Método Lemon Card -->
          <div class="metodo-pago-card" style="background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="color: #d4af37; margin-bottom: 1rem; font-size: 1.2rem;">📱 Lemon Card - QR</h3>
            <div style="text-align: center; margin: 1.5rem 0;">
              <img src="Img/lemonQR.png" alt="QR Lemon Card" style="max-width: 250px; width: 100%; border-radius: 8px; border: 2px solid rgba(212, 175, 55, 0.3);">
            </div>
            <p style="color: rgba(255, 255, 255, 0.8); text-align: center; font-size: 0.95rem; margin-top: 1rem; margin-bottom: 1rem; line-height: 1.5; padding: 0 0.5rem;">
              Puedes pagar desde cualquier billetera digital como <strong style="color: #d4af37;">Yape</strong> o <strong style="color: #d4af37;">Plin</strong>
            </p>
            <div style="background: rgba(0, 0, 0, 0.4); padding: 1rem; border-radius: 6px; margin-top: 1rem;">
              <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 0.5rem; font-size: 0.9rem;">CCI Lemon Card:</p>
              <p style="color: #d4af37; font-size: 1.1rem; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 1px; margin: 0; word-break: break-all;">92200300000319842292</p>
            </div>
          </div>

          <!-- Método PayPal -->
          <div class="metodo-pago-card" style="background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="color: #d4af37; margin-bottom: 1rem; font-size: 1.2rem;">💳 PayPal</h3>
            <div style="text-align: center; margin: 1.5rem 0;">
              <img src="Img/paypalNoy.png" alt="QR PayPal" style="max-width: 250px; width: 100%; border-radius: 8px; border: 2px solid rgba(212, 175, 55, 0.3);">
            </div>
            <p style="color: rgba(255, 255, 255, 0.7); text-align: center; font-size: 0.9rem; margin-top: 1rem;">Escanea el código QR con PayPal para realizar el pago</p>
          </div>
        </div>

        <div id="metodosPagoExtranjero" style="<?php echo $es_peru ? 'display: none;' : ''; ?>">
          <!-- Solo PayPal para extranjeros -->
          <div class="metodo-pago-card" style="background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="color: #d4af37; margin-bottom: 1rem; font-size: 1.2rem;">💳 PayPal</h3>
            <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1.5rem;">Para usuarios fuera de Perú, el método de pago disponible es PayPal.</p>
            <div style="text-align: center; margin: 1.5rem 0;">
              <img src="Img/paypalNoy.png" alt="QR PayPal" style="max-width: 250px; width: 100%; border-radius: 8px; border: 2px solid rgba(212, 175, 55, 0.3);">
            </div>
            <p style="color: rgba(255, 255, 255, 0.7); text-align: center; font-size: 0.9rem; margin-top: 1rem;">Escanea el código QR con PayPal para realizar el pago</p>
          </div>
        </div>

        <div style="background: rgba(52, 152, 219, 0.1); padding: 1rem; border-radius: 8px; border: 1px solid rgba(52, 152, 219, 0.3); margin-top: 2rem;">
          <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 0.9rem; text-align: center;">
            <i class="fas fa-info-circle" style="color: #3498db; margin-right: 0.5rem;"></i>
            AL MOMENTO DE REALIZAR EL PAGO DEJAR EL NOMBRE DE USUARIO, EL ADMINISTRADOR LE DESIGNARA EL VIP EN EL TRANSCURSO DE 24 HORAS O CONTACTALO PARA MAS RAPIDES.
          </p>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
          <button type="button" id="btnNotificarPago" class="notificar-pago-btn" onclick="notificarPago()" style="
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            color: #000;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
          ">
            <i class="fas fa-bell" style="margin-right: 0.5rem;"></i>
            Notificar Pago
          </button>
          <div id="mensajeNotificacion" style="margin-top: 1rem; display: none;"></div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .modal-pago {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.85);
      backdrop-filter: blur(5px);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      padding: 2rem;
      overflow-y: auto;
    }

    .modal-pago-content {
      background: linear-gradient(135deg, #1e1e1e 0%, #252525 100%);
      border-radius: 16px;
      max-width: 600px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
      border: 2px solid rgba(212, 175, 55, 0.3);
      position: relative;
    }

    .modal-pago-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 2rem;
      border-bottom: 2px solid rgba(212, 175, 55, 0.2);
      background: rgba(0, 0, 0, 0.3);
    }

    .modal-pago-close {
      background: rgba(231, 76, 60, 0.2);
      border: 1px solid rgba(231, 76, 60, 0.4);
      color: #e74c3c;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      font-size: 1.2rem;
    }

    .modal-pago-close:hover {
      background: rgba(231, 76, 60, 0.4);
      transform: rotate(90deg);
    }

    .modal-pago-body {
      padding: 2rem;
    }

    .metodo-pago-card {
      transition: all 0.3s ease;
    }

    .metodo-pago-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      border-color: rgba(212, 175, 55, 0.4) !important;
    }

    .notificar-pago-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
      background: linear-gradient(135deg, #e5c649 0%, #c9a521 100%);
    }

    .notificar-pago-btn:active {
      transform: translateY(0);
    }

    .notificar-pago-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    @media (max-width: 768px) {
      .modal-pago-content {
        max-width: 95%;
        margin: 1rem;
      }

      .modal-pago-header,
      .modal-pago-body {
        padding: 1rem;
      }
    }
  </style>

  <script>
    function procederPago() {
      const terminos = document.querySelector('input[name="terminos"]').checked;
      const privacidad = document.querySelector('input[name="privacidad"]').checked;
      
      if (!terminos || !privacidad) {
        alert('Por favor, acepta los términos y condiciones y la política de privacidad para continuar.');
        return;
      }
      
      // Abrir modal de métodos de pago
      abrirModalPago();
    }

    function abrirModalPago() {
      const modal = document.getElementById('modalPago');
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function cerrarModalPago() {
      const modal = document.getElementById('modalPago');
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalPago').addEventListener('click', function(e) {
      if (e.target === this) {
        cerrarModalPago();
      }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        cerrarModalPago();
      }
    });

    function notificarPago() {
      const btn = document.getElementById('btnNotificarPago');
      const mensajeDiv = document.getElementById('mensajeNotificacion');
      
      // Deshabilitar botón mientras se procesa
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i> Enviando notificación...';
      mensajeDiv.style.display = 'none';

      // Enviar petición AJAX
      fetch('ajax_notificar_pago.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'usuario=' + encodeURIComponent('<?php echo htmlspecialchars($user['usuario']); ?>')
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          mensajeDiv.style.display = 'block';
          mensajeDiv.innerHTML = '<div style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(46, 204, 113, 0.4);"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
          btn.innerHTML = '<i class="fas fa-check" style="margin-right: 0.5rem;"></i> Notificación Enviada';
          
          // Ocultar mensaje después de 5 segundos
          setTimeout(() => {
            mensajeDiv.style.display = 'none';
          }, 5000);
        } else {
          mensajeDiv.style.display = 'block';
          mensajeDiv.innerHTML = '<div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(231, 76, 60, 0.4);"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-bell" style="margin-right: 0.5rem;"></i> Notificar Pago';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        mensajeDiv.style.display = 'block';
        mensajeDiv.innerHTML = '<div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(231, 76, 60, 0.4);"><i class="fas fa-exclamation-circle"></i> Error al enviar la notificación. Por favor, intenta nuevamente.</div>';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-bell" style="margin-right: 0.5rem;"></i> Notificar Pago';
      });
    }
  </script>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
