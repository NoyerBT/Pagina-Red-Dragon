<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
  <style>
    /* Login único y elegante para Red Dragons Cup */
    #login-form {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 3rem 1rem;
      min-height: 60vh;
    }

    .login-container {
      width: 100%;
      max-width: 500px;
      position: relative;
    }

    .login-card {
      background: rgba(0, 0, 0, 0.85);
      border: 2px solid transparent;
      border-radius: 24px;
      padding: 3.5rem 3rem;
      position: relative;
      backdrop-filter: blur(10px);
      box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .login-card::before {
      content: "";
      position: absolute;
      inset: -2px;
      border-radius: 24px;
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

    .login-card:hover::before {
      opacity: 1;
    }

    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: 
        0 30px 80px rgba(0, 0, 0, 0.6),
        0 0 40px rgba(212, 175, 55, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .login-title {
      text-align: center;
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      background: linear-gradient(135deg, #ffffff 0%, #d4af37 50%, #c0c0c0 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: 0.05em;
    }

    .login-subtitle {
      text-align: center;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.95rem;
      margin-bottom: 2.5rem;
      font-weight: 400;
    }

    .input-group {
      margin-bottom: 1.8rem;
      position: relative;
    }

    .input-wrapper {
      display: flex;
      align-items: center;
      gap: 1rem;
      background: rgba(23, 23, 23, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 1rem 1.25rem;
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .input-wrapper:focus-within {
      border-color: rgba(212, 175, 55, 0.6);
      background: rgba(23, 23, 23, 0.95);
      box-shadow: 
        inset 0 2px 8px rgba(0, 0, 0, 0.3),
        0 0 20px rgba(212, 175, 55, 0.15);
    }

    .input-icon {
      width: 24px;
      height: 24px;
      fill: #d4af37;
      flex-shrink: 0;
      transition: all 0.3s ease;
    }

    .input-wrapper:focus-within .input-icon {
      fill: #ffd700;
      filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.6));
    }

    .input-field {
      flex: 1;
      background: none;
      border: none;
      outline: none;
      color: #ffffff;
      font-size: 1rem;
      font-weight: 400;
      padding: 0;
    }

    .input-field::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .button-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 2.5rem;
    }

    .btn-login {
      width: 100%;
      padding: 1rem 2rem;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
      color: #000000;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      box-shadow: 
        0 4px 15px rgba(212, 175, 55, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      position: relative;
      overflow: hidden;
    }

    .btn-login::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.5s ease;
    }

    .btn-login:hover::before {
      left: 100%;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, #ffd700 0%, #d4af37 100%);
      transform: translateY(-2px);
      box-shadow: 
        0 6px 25px rgba(212, 175, 55, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-register {
      width: 100%;
      padding: 1rem 2rem;
      border: 2px solid rgba(192, 192, 192, 0.3);
      border-radius: 12px;
      background: rgba(23, 23, 23, 0.6);
      color: #ffffff;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      text-decoration: none;
      text-align: center;
      display: inline-block;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-register:hover {
      border-color: rgba(192, 192, 192, 0.6);
      background: rgba(192, 192, 192, 0.1);
      color: #c0c0c0;
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(192, 192, 192, 0.2);
    }

    .error-message {
      width: 100%;
      padding: 1rem;
      border-radius: 12px;
      background: rgba(255, 68, 68, 0.15);
      color: #ff6b6b;
      font-size: 0.95rem;
      text-align: center;
      border: 1px solid rgba(255, 68, 68, 0.3);
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .login-container {
        max-width: 100%;
      }
      
      .login-card {
        padding: 2.5rem 2rem;
      }

      .login-title {
        font-size: 2rem;
      }

      .input-wrapper {
        padding: 0.875rem 1rem;
      }

      .btn-login,
      .btn-register {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
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
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="salon_fama.php">SALÓN DE LA FAMA</a>
      <a href="registro.php">REGISTRARSE</a>
    </nav>
  </header>

  <main class="hero">
    <section class="hero-content">
      <h1>Iniciar Sesión</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Accede a tu cuenta para gestionar tu suscripción.</p>
    </section>
  </main>

  <section class="section" id="login-form">
    <div class="login-container">
      <div class="login-card">
        <form action="procesar_login.php" method="POST">
          <h2 class="login-title">Iniciar Sesión</h2>
          <p class="login-subtitle">Accede a tu cuenta con tu usario y contraseña</p>
          
          <?php
          if (isset($_SESSION['login_error'])) {
              echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
              unset($_SESSION['login_error']);
          }
          ?>

          <div class="input-group">
            <div class="input-wrapper">
              <svg
                viewBox="0 0 16 16"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
                class="input-icon"
              >
                <path
                  d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"
                ></path>
              </svg>
              <input
                type="text"
                id="usuario"
                name="usuario"
                class="input-field"
                placeholder="Usuario"
                autocomplete="username"
                required
              />
            </div>
          </div>

          <div class="input-group">
            <div class="input-wrapper">
              <svg
                viewBox="0 0 16 16"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
                class="input-icon"
              >
                <path
                  d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"
                ></path>
              </svg>
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="input-field" 
                placeholder="Contraseña"
                autocomplete="current-password"
                required
              />
            </div>
          </div>

          <div class="button-group">
            <button type="submit" class="btn-login">
              Iniciar Sesión
            </button>
            <a href="registro.php" class="btn-register">
              Crear Cuenta
            </a>
          </div>
        </form>
      </div>
    </div>
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
