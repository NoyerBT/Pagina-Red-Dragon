<?php
// Página de Registro - Red Dragons Cup
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro - Red Dragons Cup</title>
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
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="salon_fama.php">SALÓN DE LA FAMA</a>
      <a href="login.php">INICIAR SESIÓN</a>
    </nav>
  </header>

  <main class="hero hero--anticheat">
    <section class="hero-content">
      <h1>Crear Cuenta</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle hero-tagline">Regístrate para acceder al plan anticheat premium.</p>
    </section>
  </main>

  <section class="section" id="registro-form">
    <div class="registro-container">
      <div class="registro-card">
        <div class="registro-card__logo" aria-hidden="true">
          <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="presentation">
            <circle cx="32" cy="20" r="12" stroke="currentColor" stroke-width="2.5" fill="none" />
            <path d="M12 54c0-10.5 9-19 20-19s20 8.5 20 19" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" />
          </svg>
        </div>
        <p class="registro-card__title">Rellena tu información</p>
        <form class="registro-form" action="procesar_registro.php" method="POST">
          
          <div class="form-row">
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
              <label for="apellido">Apellido</label>
              <input type="text" id="apellido" name="apellido" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
          </div>
          
          <div class="form-group">
            <label for="usuario">Nombre de Usuario</label>
            <input type="text" id="usuario" name="usuario" required>
            <small>Mínimo 4 caracteres, solo letras y números</small>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="password">Contraseña</label>
              <input type="password" id="password" name="password" required>
              <small>Mínimo 8 caracteres</small>
            </div>
            <div class="form-group">
              <label for="confirm_password">Confirmar Contraseña</label>
              <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="telefono">Teléfono (opcional)</label>
            <input type="tel" id="telefono" name="telefono">
          </div>
          
          <div class="form-group">
            <label for="pais">País</label>
            <select id="pais" name="pais" required>
              <option value="">Selecciona tu país</option>
              <option value="PE">Perú</option>
              <option value="CO">Colombia</option>
              <option value="MX">México</option>
              <option value="AR">Argentina</option>
              <option value="CL">Chile</option>
              <option value="EC">Ecuador</option>
              <option value="BO">Bolivia</option>
              <option value="VE">Venezuela</option>
              <option value="UY">Uruguay</option>
              <option value="PY">Paraguay</option>
              <option value="OTHER">Otro</option>
            </select>
          </div>
          
          <div class="plan-summary">
            <h3>📋 Resumen del Plan</h3>
            <div class="plan-details">
              <p><strong>Plan Anticheat Premium</strong></p>
              <p>Precio: <span class="price-highlight">S/ 30.00 / mes</span></p>
              <ul>
                <li>✓ Soporte 24/7</li>
                <li>✓ Acceso a canal privado de Whatsapp</li>
                <li>✓ Uso de futuras actualizaciones</li>
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
          
          <button type="submit" class="registro-btn">
            <span>Crear Cuenta y Proceder al Pago</span>
          </button>
          
          <p class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
          </p>
        </form>
      </div>
    </div>
  </section>

  <section class="section" id="metodos-pago">
    <h2>💳 Métodos de Pago Disponibles</h2>
    <div class="payment-methods">
      <div class="payment-card">
        <h3>💳 Tarjeta de Crédito/Débito</h3>
        <p>Visa, Mastercard, American Express</p>
      </div>
      <div class="payment-card">
        <h3>📱 Yape</h3>
        <p>Pago móvil instantáneo</p>
      </div>
      <div class="payment-card">
        <h3>🏦 Transferencia Bancaria</h3>
        <p>BCP</p>
      </div>
      <div class="payment-card">
        <h3>💰 PagoEfectivo</h3>
        <p>"Aun en desarrollo"</p>
      </div>
    </div>
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
  <script src="registro-validation.js"></script>
</body>
</html>
