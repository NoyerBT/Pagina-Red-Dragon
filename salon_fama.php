<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Salón de la Fama - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
  <style>
    /* Estilos específicos para el Salón de la Fama */
    #fama-list {
        padding-top: 2rem !important;
        padding-bottom: 0 !important;
        padding-left: clamp(1.5rem, 6vw, 5rem) !important;
        padding-right: clamp(1.5rem, 6vw, 5rem) !important;
    }
    
    .hall-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 2rem;
        margin-bottom: 0;
    }
    
    .footer {
        margin-top: 4rem !important;
        padding: 0.4rem 1rem 0.4rem !important;
        font-size: 0.7rem !important;
        line-height: 1.2 !important;
        border-top: 1px solid rgba(255, 215, 0, 0.2) !important;
    }
    
    .footer p {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.2 !important;
    }

    .fame-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        backdrop-filter: blur(5px);
    }

    .fame-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(220, 20, 60, 0.2);
        border-color: rgba(220, 20, 60, 0.5);
    }

    .fame-photo-container {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #dc143c;
        position: relative;
    }

    .fame-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fame-name {
        color: #fff;
        font-size: 1.5rem;
        margin-bottom: 10px;
        font-family: 'Orbitron', sans-serif;
    }

    .fame-role {
        color: #ccc;
        font-size: 0.9rem;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .steam-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #171a21; /* Steam color */
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s ease;
        gap: 10px;
    }

    .steam-btn:hover {
        background: #2a475e;
    }

    .steam-icon {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    /* Estilos para el banner de invitación a colaborar */
    #invitacion-colaborar {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
        max-width: 900px;
        margin: 0 auto;
    }

    .contributor-banner {
        position: relative;
        display: flex;
        background: linear-gradient(
            to bottom right,
            #b0b0b0,
            #7e7e7e,
            #363636,
            #363636,
            #363636
        );
        border-radius: 20px;
        padding: 1.5px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .contributor-banner::before {
        position: absolute;
        content: "";
        top: -10px;
        left: -10px;
        background: radial-gradient(
            ellipse at center,
            #ffffff,
            rgba(255, 255, 255, 0.4),
            rgba(255, 255, 255, 0.2),
            rgba(255, 255, 255, 0.1),
            rgba(0, 0, 0, 0),
            rgba(0, 0, 0, 0),
            rgba(0, 0, 0, 0)
        );
        width: 40px;
        height: 40px;
        filter: blur(1.5px);
        z-index: 0;
    }

    .contributor-banner::after {
        position: absolute;
        content: "";
        bottom: -10px;
        right: -10px;
        background: radial-gradient(
            ellipse at center,
            rgba(255, 255, 255, 0.3),
            rgba(255, 255, 255, 0.15),
            rgba(255, 255, 255, 0.05),
            rgba(0, 0, 0, 0),
            rgba(0, 0, 0, 0)
        );
        width: 30px;
        height: 30px;
        filter: blur(1px);
        z-index: 0;
    }

    .contributor-banner:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(212, 175, 55, 0.15);
    }

    .contributor-banner__content {
        position: relative;
        z-index: 1;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.95) 0%, rgba(10, 10, 10, 0.98) 100%);
        border-radius: 18px;
        width: 100%;
        padding: 2.5rem 2rem;
        text-align: center;
        backdrop-filter: blur(10px);
    }

    .contributor-banner__icon {
        margin-bottom: 1rem;
        animation: pulse 2s ease-in-out infinite;
        display: inline-block;
    }

    .contributor-logo {
        max-width: 200px;
        width: 100%;
        height: auto;
        object-fit: contain;
        filter: drop-shadow(0 0 15px rgba(212, 175, 55, 0.6));
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .contributor-banner h2 {
        color: #d4af37;
        font-size: 2rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-family: 'Orbitron', sans-serif;
        text-shadow: 0 0 20px rgba(212, 175, 55, 0.6);
    }

    .contributor-banner__text {
        color: #e0e0e0;
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 0.8rem;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .contributor-banner__subtext {
        color: #b0b0b0;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        font-style: italic;
    }

    .contributor-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 2.5rem;
        background: linear-gradient(270deg, rgba(212, 175, 55, 0.9) 0%, rgba(184, 134, 11, 0.9) 100%);
        color: #000;
        text-decoration: none;
        border-radius: 50px;
        font-weight: bold;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(212, 175, 55, 0.5);
    }

    .contributor-btn:hover {
        background: linear-gradient(270deg, rgba(212, 175, 55, 1) 0%, rgba(184, 134, 11, 1) 100%);
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(212, 175, 55, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        border-color: rgba(212, 175, 55, 0.8);
        color: #000;
    }

    .contributor-btn span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .contributor-banner {
            padding: 2rem 1.5rem;
        }

        .contributor-banner h2 {
            font-size: 1.5rem;
        }

        .contributor-banner__text {
            font-size: 1rem;
        }

        .contributor-logo {
            max-width: 150px;
        }

        .contributor-btn {
            padding: 0.9rem 2rem;
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
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="torneo.php">TORNEO</a>
      <?php endif; ?>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <a href="salon_fama.php" class="active">SALÓN DE LA FAMA</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero">
    <section class="hero-content">
      <h1>SALÓN DE LA FAMA</h1>
      <p class="subtitle">Reconociendo a quienes apoyan la pagina</p>
    </section>
  </main>

  <section class="section" id="fama-list">
    <h2>Colaboradores</h2>
    <div class="hall-grid">
      
      <!-- Ejemplo de colaborador 1 -->
      <div class="fame-card">
        <div class="fame-photo-container">
          <img src="Img/fotoadministrador.jpg" alt="Whiting" class="fame-photo" />
        </div>
        <h3 class="fame-name">WHITING</h3>
        <a href="https://steamcommunity.com/id/Whiting21/" target="_blank" rel="noopener noreferrer" class="steam-btn">
            <img src="Img/logo steam.png" alt="Steam" class="steam-icon" />
            Perfil de Steam
        </a>
      </div>

      <!-- Ejemplo de colaborador 2 -->
      <div class="fame-card">
        <div class="fame-photo-container">
          <img src="Img/fotosoporte.jpg" alt="NH" class="fame-photo" />
        </div>
        <h3 class="fame-name">NH</h3>
        <a href="https://steamcommunity.com/profiles/76561199176974317" target="_blank" rel="noopener noreferrer" class="steam-btn">
            <img src="Img/logo steam.png" alt="Steam" class="steam-icon" />
            Perfil de Steam
        </a>
      </div>

       <!-- Ejemplo de colaborador 3 -->
       <div class="fame-card">
        <div class="fame-photo-container">
          <img src="Img/fotodiseñadora.jpg" alt="Vallolet" class="fame-photo" />
        </div>
        <h3 class="fame-name">Vallolet</h3>
        <a href="https://steamcommunity.com/profiles/76561199638892858" target="_blank" rel="noopener noreferrer" class="steam-btn">
            <img src="Img/logo steam.png" alt="Steam" class="steam-icon" />
            Perfil de Steam
        </a>
      </div>

    </div>
  </section>

  <!-- Aviso de invitación a colaborar -->
  <section class="section" id="invitacion-colaborar">
    <div class="contributor-banner">
      <div class="contributor-banner__content">
        <div class="contributor-banner__icon">
          <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="contributor-logo" />
        </div>
        <h2>¿Quieres aparecer aquí?</h2>
        <p class="contributor-banner__text">
          Apoya a la pagina con tu contribución y forma parte del Salón de la Fama. 
          Tu aporte nos ayuda a seguir mejorando y expandiendo nuestros servicios.
        </p>
        <p class="contributor-banner__subtext">
          Contacta con nosotros para más información sobre cómo puedes colaborar.
        </p>
        <a href="contacto.php" class="contributor-btn">
          <span>📧 Contactar para Colaborar</span>
        </a>
      </div>
    </div>
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>

