<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$token_data = null;
$tokens_file = 'tokens_database.json';

// Función para cargar tokens desde el archivo JSON
function load_tokens($file) {
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        return json_decode($json_data, true) ?: [];
    } 
    return [];
}
si
// Función para guardar tokens en el archivo JSON
function save_tokens($file, $tokens) {
    $json_data = json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $json_data);
}

// Lógica para generar el token cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokens = load_tokens($tokens_file);
    
    // Generar un token único y seguro
    $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    
    // Crear hash del token para verificación
    $token_hash = hash('sha256', $token);
    
    // Fechas de creación y expiración
    $created_date = new DateTime();
    $expires_date = (new DateTime())->add(new DateInterval('P2D')); // Expira en 2 días

    // Obtener nombre de usuario del formulario
    $player_name = $_POST['player_name'];

    // Crear la estructura de datos del token
    $token_data = [
        'player_name' => $player_name,
        'tournament_name' => 'Red Dragons Cup',
        'token' => $token,
        'token_hash' => $token_hash,
        'created_date' => $created_date->format(DateTime::ISO8601),
        'expires_date' => $expires_date->format(DateTime::ISO8601),
        'is_active' => true,
        'used_count' => 0,
        'last_used' => null
    ];
    
    // Guardar el token en la base de datos (el array de tokens)
    $tokens[$token_hash] = $token_data;
    save_tokens($tokens_file, $tokens);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Generador de Claves - Red Dragons Cup</title>
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
      <h1>Generador de Claves</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Genera una nueva clave de acceso para el anticheat.</p>
    </section>
  </main>

  <section class="section" id="key-generator">
    <div class="registro-container">
      <div class="registro-card">
        <h2>🔑 Tu Clave</h2>
        <div class="key-display-area" style="padding: 20px; background-color: #1a1a1a; border-radius: 8px; margin-bottom: 20px; text-align: left; font-family: monospace; word-wrap: break-word;">
          <?php if ($token_data): ?>
            <p><strong>Jugador:</strong> <?php echo htmlspecialchars($token_data['player_name']); ?></p>
            <p><strong>Token:</strong> <?php echo htmlspecialchars($token_data['token']); ?></p>
            <p><strong>Expira:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($token_data['expires_date']))); ?></p>
          <?php else: ?>
            <p style="text-align: center;">Haz clic en el botón para generar una nueva clave.</p>
          <?php endif; ?>
        </div>
        <form action="generar_claves.php" method="POST" class="registro-form">
          <div class="form-group">
            <label for="player_name">Nombre del Jugador</label>
            <input type="text" id="player_name" name="player_name" required>
          </div>
          <button type="submit" class="btn primary registro-btn glow-on-hover">Generar Nueva Clave</button>
        </form>
      </div>
    </div>
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
