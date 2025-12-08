<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'TokenGenerator.php';
require_once 'cnt/conexion.php';

$generator = new TokenGenerator();
$message = "";
$messageType = "";
$activeTab = 'generate';

// Función para obtener el nombre del torneo del usuario
function obtener_torneo_usuario($usuario) {
    global $conn;
    
    // Obtener el ID del usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || !isset($user['id'])) {
        return null;
    }
    
    $usuario_id = $user['id'];
    
    // Verificar si la tabla torneos existe
    $check_table = $conn->query("SHOW TABLES LIKE 'torneos'");
    if ($check_table->num_rows == 0) {
        return null;
    }
    
    // Obtener el torneo más reciente del usuario
    $stmt = $conn->prepare("SELECT nombre_torneo FROM torneos WHERE usuario_id = ? ORDER BY fecha_creacion DESC LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $torneo = $result->fetch_assoc();
    $stmt->close();
    
    return $torneo ? $torneo['nombre_torneo'] : null;
}

// Datos para la vista
$generated_token = null;
$validation_result = null;
$token_stats = null;
$all_tokens = null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'generate') {
        $player_name = trim($_POST['player_name'] ?? '');
        if (!empty($player_name)) {
            $current_user = $_SESSION['usuario']; // Obtener el usuario actual
            // Obtener el nombre del torneo del usuario, o null si no tiene
            $tournament_name = obtener_torneo_usuario($current_user);
            // Si no tiene torneo, usar null (se mostrará como vacío o mensaje)
            $generated_token = $generator->generate_token($player_name, $tournament_name, $current_user);
            if ($generated_token) {
                $message = "Token generado exitosamente.";
                $messageType = "success";
            } else {
                $message = "Error al generar el token.";
                $messageType = "error";
            }
        } else {
            $message = "Por favor ingrese el nombre del jugador.";
            $messageType = "error";
        }
        $activeTab = 'generate';

    } elseif ($action === 'validate') {
        // Esta acción ahora se maneja vía AJAX, pero mantenemos el código por compatibilidad
        $token_to_validate = trim($_POST['token_to_validate'] ?? '');
        if (!empty($token_to_validate)) {
            list($isValid, $result) = $generator->validate_token($token_to_validate);
            $validation_result = [
                'valid' => $isValid,
                'data' => $result
            ];
        }
        $activeTab = 'generate'; // Cambiar a generate ya que la pestaña validate ya no existe

    } elseif ($action === 'deactivate') {
        $token_hash = $_POST['token_hash'] ?? '';
        $current_user = $_SESSION['usuario']; // Obtener el usuario actual
        if ($generator->deactivate_token($token_hash, $current_user)) {
            $message = "Token desactivado correctamente.";
            $messageType = "success";
        } else {
            $message = "Error al desactivar el token. Verifica que el token te pertenezca.";
            $messageType = "error";
        }
        $activeTab = 'manage';
    }
}

// Cargar datos para la pestaña de gestión siempre si es solicitada o por defecto
if ($activeTab === 'manage' || isset($_GET['tab']) && $_GET['tab'] === 'manage') {
    $current_user = $_SESSION['usuario']; // Obtener el usuario actual
    $token_stats = $generator->get_token_stats($current_user); // Filtrar por usuario
    $all_tokens = $generator->list_tokens($current_user); // Filtrar por usuario
    $activeTab = 'manage';
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
  <!-- Librería para QR -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    /* Estilos adicionales para la nueva interfaz */
    .tabs {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
        border-bottom: 2px solid #333;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .tab-btn {
        background: none;
        border: none;
        color: #888;
        padding: 1rem 2rem;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: bold;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }
    .tab-btn:hover {
        color: #ccc;
    }
    .tab-btn.active {
        color: #d4af37;
        border-bottom-color: #d4af37;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease;
        width: 100%;
        max-width: 100%;
    }
    .tab-content.active {
        display: block;
    }
    .result-box {
        background: #1a1a1a;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1.5rem;
        border: 1px solid #333;
    }
    .token-display {
        font-family: monospace;
        font-size: 1.2rem;
        color: #d4af37;
        word-break: break-all;
        margin: 1rem 0;
        padding: 1rem;
        background: #000;
        border-radius: 4px;
    }
    .qr-container {
        display: flex;
        justify-content: center;
        margin: 1.5rem 0;
        padding: 1rem;
        background: white;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
        border-radius: 8px;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: #222;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #333;
    }
    .stat-number {
        font-size: 2.5rem;
        color: #d4af37;
        font-weight: bold;
    }
    .stat-label {
        color: #888;
        margin-top: 0.5rem;
    }
    .tokens-table-container {
        overflow-x: auto;
    }
    .tokens-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    .tokens-table th, .tokens-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #333;
    }
    .tokens-table th {
        color: #d4af37;
        font-weight: bold;
    }
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    .status-active { background: rgba(0, 255, 0, 0.2); color: #00ff00; }
    .status-expired { background: rgba(255, 0, 0, 0.2); color: #ff4444; }
    .status-inactive { background: rgba(128, 128, 128, 0.2); color: #aaa; }
    
    /* Toast Notification Styles - Minimalista y Centrado */
    .toast-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10000;
        pointer-events: none;
    }
    .toast {
        min-width: 280px;
        max-width: 350px;
        padding: 0.9rem 1.4rem;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        animation: toastFadeIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), toastFadeOut 0.3s ease 2.5s forwards;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        pointer-events: auto;
    }
    .toast-success {
        background: rgba(0, 180, 0, 0.85);
        color: white;
    }
    .toast-error {
        background: rgba(220, 0, 0, 0.85);
        color: white;
    }
    .toast-icon {
        font-size: 1.3rem;
        font-weight: bold;
    }
    .toast-message {
        font-weight: 500;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }
    
    @keyframes toastFadeIn {
        from {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }
    
    @keyframes toastFadeOut {
        from {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        to {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.9);
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Asegurar layout vertical */
    .registro-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    .tab-content-wrapper {
        width: 100%;
        max-width: 1000px;
        display: flex;
        flex-direction: column;
        margin: 0 auto;
    }
    
    .registro-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    /* Asegurar que las tarjetas ocupen el ancho completo disponible */
    .tab-content .registro-card {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* Estilos para botones deshabilitados */
    .registro-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .registro-btn:disabled:hover {
        transform: none;
        box-shadow: none;
    }
    
    /* Estilos del nuevo botón - From Uiverse.io by Spacious74 */
    .button {
        cursor: pointer;
        border: solid 3px #0a0a0a;
        border-top: none;
        border-radius: 16px;
        position: relative;
        box-shadow: 0px 3px 8px #00000062, 0px 8px 30px -10px #000000a6, 0px 10px 35px -15px #00000071;
        transition: all 0.3s ease;
        background: transparent;
        width: auto;
        max-width: 400px;
        margin: 1rem auto 0;
        display: block;
    }
    
    .button .inner {
        padding: 10px 24px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 600;
        letter-spacing: 0.8px;
        border-bottom: solid 2px #1a2a3a;
        border-radius: 13px;
        background: linear-gradient(180deg, #2a3f5a, #0a0a0a);
        color: #fff;
        text-shadow: 1px 1px #000, 0 0 7px #fff;
    }
    
    .button .svgs {
        position: relative;
        margin-top: 7px;
        z-index: 10;
    }
    
    .button .svgs > * {
        filter: drop-shadow(0 0 5px #fff) drop-shadow(1px 1px 0px #000);
    }
    
    .button .svgs .svg-s {
        position: absolute;
        font-size: 0.7rem;
        left: 16px;
        top: -3px;
    }
    
    .button .svgs .svg-l {
        font-size: 0.9rem;
    }
    
    .button:active {
        box-shadow: none;
    }
    
    .button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .button:disabled:active {
        box-shadow: 0px 4px 10px #00000062, 0px 10px 40px -10px #000000a6, 0px 12px 45px -15px #00000071;
    }
    
    /* Mejorar responsive */
    @media (max-width: 768px) {
        .tabs {
            flex-direction: column;
            align-items: stretch;
        }
        .tab-btn {
            width: 100%;
            text-align: center;
        }
        .toast {
            min-width: 250px;
            max-width: 90%;
            padding: 0.8rem 1.2rem;
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
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero" style="min-height: 40vh;">
    <section class="hero-content">
      <h1>Panel de Control de Tokens</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <p class="subtitle">Genera, valida y administra tokens para el torneo</p>
    </section>
  </main>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

  <section class="section">
    <div class="registro-container">
      
      <div class="tabs">
        <button class="tab-btn <?php echo $activeTab === 'generate' ? 'active' : ''; ?>" onclick="switchTab('generate')">Generar Token</button>
        <button class="tab-btn <?php echo $activeTab === 'manage' ? 'active' : ''; ?>" onclick="switchTab('manage')">Administrar</button>
      </div>

      <div class="tab-content-wrapper">

      <!-- Tab: Generar -->
      <div id="tab-generate" class="tab-content <?php echo $activeTab === 'generate' ? 'active' : ''; ?>">
        <div class="registro-card">
          <h2>Generar Nuevo Token</h2>
          <p class="plan-note" style="margin-bottom: 20px;">El token tendrá una validez fija de <strong>2 días</strong>.</p>
          
          <form id="generateTokenForm" class="registro-form">
            <div class="form-group">
              <label for="player_name">Nombre del Jugador</label>
              <input type="text" id="player_name" name="player_name" required placeholder="Ingrese nickname del jugador">
            </div>
            <button type="submit" class="button" id="generateBtn">
              <div class="inner">
                <div class="svgs">
                  <svg viewBox="0 0 256 256" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg" class="svg-l">
                    <path d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0l-23.06-63.44L26.5 143a16 16 0 0 1 0-30l63.44-23.06L113 26.5a16 16 0 0 1 30 0l23.07 63.44L229.5 113a15.79 15.79 0 0 1 10.5 15" fill="currentColor"></path>
                  </svg>
                  <svg viewBox="0 0 256 256" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg" class="svg-s">
                    <path d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0l-23.06-63.44L26.5 143a16 16 0 0 1 0-30l63.44-23.06L113 26.5a16 16 0 0 1 30 0l23.07 63.44L229.5 113a15.79 15.79 0 0 1 10.5 15" fill="currentColor"></path>
                  </svg>
                </div>
                <span id="generateBtnText">Generar Token</span>
              </div>
            </button>
          </form>

          <!-- Contenedor para mostrar el token generado -->
          <div id="tokenResultContainer" style="display: none;">
            <div class="result-box">
                <h3 style="color: #d4af37; margin-bottom: 1rem;">Token Generado Exitosamente</h3>
                
                <div class="token-details">
                    <p><strong>Jugador:</strong> <span id="token-player-name"></span></p>
                    <p><strong>Torneo:</strong> <span id="token-tournament-name"></span></p>
                    <p><strong>Expira:</strong> <span id="token-expires-date"></span></p>
                    
                    <div class="token-display" id="token-text"></div>
                    
                    <!-- Sección de validación integrada -->
                    <div style="margin-top: 1.5rem;">
                      <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="token_to_validate" style="color: #ffe8ba; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Validar Token</label>
                        <input type="text" id="token_to_validate" name="token_to_validate" placeholder="Pegue el token aquí para validar" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255, 170, 63, 0.4); background: rgba(0, 0, 0, 0.65); color: #fefefe; box-sizing: border-box;">
                      </div>
                      
                      <div class="btn-group" style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; align-items: stretch;">
                        <button type="button" onclick="copyToken()" class="btn" style="padding: 0.75rem 1.5rem; font-size: 0.9rem; flex: 1; min-width: 150px; background: rgba(100, 100, 100, 0.8); color: white; border: 1px solid rgba(255, 255, 255, 0.2); cursor: pointer; border-radius: 8px; font-weight: bold; transition: all 0.3s ease;">Copiar Token</button>
                        <button type="button" onclick="validateTokenFromInput()" class="btn" style="padding: 0.75rem 1.5rem; font-size: 0.9rem; flex: 1; min-width: 150px; background: linear-gradient(45deg, #d4af37, #c09b2d); color: #000; border: none; cursor: pointer; border-radius: 8px; font-weight: bold; transition: all 0.3s ease;" id="validateBtnInline">
                          <span id="validateBtnTextInline">Validar Token</span>
                        </button>
                      </div>
                    </div>

                    <!-- Contenedor para mostrar el resultado de la validación -->
                    <div id="validateResultContainer" style="display: none; margin-top: 1.5rem;">
                      <div class="result-box" id="validateResultBox"></div>
                    </div>

                    <div class="qr-container" id="qrcode"></div>
                </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Administrar -->
      <div id="tab-manage" class="tab-content <?php echo $activeTab === 'manage' ? 'active' : ''; ?>">
        <div class="registro-card" style="max-width: 100%;">
          <h2>Gestión de Tokens</h2>
          
          <?php if (!$token_stats): ?>
             <form action="generar_claves.php" method="GET">
                <input type="hidden" name="tab" value="manage">
                <button type="submit" class="button">
                  <div class="inner">
                    <div class="svgs">
                      <svg viewBox="0 0 256 256" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg" class="svg-l">
                        <path d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0l-23.06-63.44L26.5 143a16 16 0 0 1 0-30l63.44-23.06L113 26.5a16 16 0 0 1 30 0l23.07 63.44L229.5 113a15.79 15.79 0 0 1 10.5 15" fill="currentColor"></path>
                      </svg>
                      <svg viewBox="0 0 256 256" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg" class="svg-s">
                        <path d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0l-23.06-63.44L26.5 143a16 16 0 0 1 0-30l63.44-23.06L113 26.5a16 16 0 0 1 30 0l23.07 63.44L229.5 113a15.79 15.79 0 0 1 10.5 15" fill="currentColor"></path>
                      </svg>
                    </div>
                    Cargar Datos
                  </div>
                </button>
             </form>
          <?php else: ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $token_stats['total_tokens']; ?></div>
                    <div class="stat-label">Total Tokens</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #00ff00;"><?php echo $token_stats['active_tokens']; ?></div>
                    <div class="stat-label">Activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #ff4444;"><?php echo $token_stats['expired_tokens']; ?></div>
                    <div class="stat-label">Expirados</div>
                </div>
            </div>

            <div class="tokens-table-container">
                <table class="tokens-table">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th>Estado</th>
                            <th>Expira</th>
                            <th>Usos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($all_tokens) as $hash => $t): 
                            $is_active = $t['is_active'] ?? true;
                            $expires_date_obj = new DateTime($t['expires_date']);
                            $now_obj = new DateTime();
                            $is_expired = $now_obj > $expires_date_obj;
                            
                            $status_class = 'status-active';
                            $status_text = 'Activo';
                            
                            if (!$is_active) {
                                $status_class = 'status-inactive';
                                $status_text = 'Inactivo';
                            } elseif ($is_expired) {
                                $status_class = 'status-expired';
                                $status_text = 'Expirado';
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['player_name']); ?></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($t['expires_date'])); ?></td>
                            <td><?php echo $t['used_count'] ?? 0; ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <?php if ($is_active && !$is_expired): ?>
                                        <button type="button" onclick="deactivateToken('<?php echo $hash; ?>', this)" class="btn exception" style="padding: 0.2rem 0.5rem; font-size: 0.8rem; background: #660000; color: white; border: none; cursor: pointer; border-radius: 4px;">Desactivar</button>
                                    <?php endif; ?>
                                    <button type="button" onclick="deleteToken('<?php echo $hash; ?>', this)" class="btn exception" style="padding: 0.2rem 0.5rem; font-size: 0.8rem; background: #8B0000; color: white; border: none; cursor: pointer; border-radius: 4px;">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      </div>
    </div>
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
  <script>
    // Función para mostrar toast
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = type === 'success' ? '✓' : '✕';
        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <span class="toast-message">${message}</span>
        `;
        
        // Limpiar toasts anteriores antes de agregar uno nuevo
        container.innerHTML = '';
        container.appendChild(toast);
        
        // Remover automáticamente después de 2.8 segundos
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'toastFadeOut 0.3s ease forwards';
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 2800);
    }

    // Mostrar toast si hay mensaje del servidor
    <?php if ($message): ?>
        window.addEventListener('DOMContentLoaded', function() {
            showToast('<?php echo htmlspecialchars($message, ENT_QUOTES); ?>', '<?php echo $messageType; ?>');
        });
    <?php endif; ?>

    function switchTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab
        document.getElementById('tab-' + tabId).classList.add('active');
        // Activate button
        event.currentTarget.classList.add('active');
    }

    function copyToken() {
        const tokenText = document.getElementById('token-text').innerText;
        navigator.clipboard.writeText(tokenText).then(() => {
            showToast('Token copiado al portapapeles', 'success');
        }).catch(err => {
            // Fallback para navegadores que no soportan clipboard API o no están en contexto seguro
            const textArea = document.createElement("textarea");
            textArea.value = tokenText;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast('Token copiado al portapapeles', 'success');
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                showToast('Error al copiar el token', 'error');
            }
            document.body.removeChild(textArea);
        });
    }

    // Manejar formulario de generar token con AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const generateForm = document.getElementById('generateTokenForm');
        if (generateForm) {
            generateForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'generate');
                
                const generateBtn = document.getElementById('generateBtn');
                const generateBtnText = document.getElementById('generateBtnText');
                const originalText = generateBtnText ? generateBtnText.textContent : 'Generar Token';
                
                // Deshabilitar botón y mostrar loading
                generateBtn.disabled = true;
                if (generateBtnText) {
                    generateBtnText.textContent = 'Generando...';
                }
                
                fetch('ajax_tokens.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    generateBtn.disabled = false;
                    if (generateBtnText) {
                        generateBtnText.textContent = originalText;
                    }
                    
                    if (data.success) {
                        showToast(data.message, 'success');
                        displayGeneratedToken(data.token);
                        // Limpiar formulario
                        generateForm.reset();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    generateBtn.disabled = false;
                    if (generateBtnText) {
                        generateBtnText.textContent = originalText;
                    }
                    showToast('Error al generar el token', 'error');
                    console.error('Error:', error);
                });
            });
        }

        // Permitir validar con Enter en el input
        const tokenToValidateInput = document.getElementById('token_to_validate');
        if (tokenToValidateInput) {
            tokenToValidateInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    validateTokenFromInput();
                }
            });
        }
    });

    // Función para mostrar el token generado
    function displayGeneratedToken(tokenData) {
        const container = document.getElementById('tokenResultContainer');
        const playerName = document.getElementById('token-player-name');
        const tournamentName = document.getElementById('token-tournament-name');
        const expiresDate = document.getElementById('token-expires-date');
        const tokenText = document.getElementById('token-text');
        const qrContainer = document.getElementById('qrcode');
        
        // Llenar datos
        playerName.textContent = tokenData.player_name;
        // Mostrar el nombre del torneo o un mensaje si no tiene
        if (tokenData.tournament_name && tokenData.tournament_name.trim() !== '') {
            tournamentName.textContent = tokenData.tournament_name;
        } else {
            tournamentName.textContent = 'Sin torneo asignado';
            tournamentName.style.color = '#888';
            tournamentName.style.fontStyle = 'italic';
        }
        
        // Formatear fecha
        const expires = new Date(tokenData.expires_date);
        const formattedDate = expires.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        expiresDate.textContent = formattedDate;
        
        tokenText.textContent = tokenData.token;
        
        // Limpiar QR anterior y generar nuevo
        qrContainer.innerHTML = '';
        const qrcode = new QRCode(qrContainer, {
            text: tokenData.token,
            width: 128,
            height: 128
        });
        
        // Mostrar contenedor
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Función para mostrar el resultado de la validación
    function displayValidationResult(data, isValid) {
        const container = document.getElementById('validateResultContainer');
        const resultBox = document.getElementById('validateResultBox');
        
        if (isValid) {
            const formattedCreated = new Date(data.created_date).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            const formattedExpires = new Date(data.expires_date).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const tournamentDisplay = (data.tournament_name && data.tournament_name.trim() !== '') 
                ? escapeHtml(data.tournament_name) 
                : '<span style="color: #888; font-style: italic;">Sin torneo asignado</span>';
            
            resultBox.innerHTML = `
                <h3 style="color: #00ff00; margin-bottom: 1rem;">✅ Token Válido</h3>
                <div style="margin-top: 1rem; line-height: 1.6;">
                    <p><strong>Jugador:</strong> ${escapeHtml(data.player_name)}</p>
                    <p><strong>Torneo:</strong> ${tournamentDisplay}</p>
                    <p><strong>Creado:</strong> ${formattedCreated}</p>
                    <p><strong>Expira:</strong> ${formattedExpires}</p>
                    <p><strong>Usos:</strong> ${data.used_count || 0}</p>
                </div>
            `;
        } else {
            resultBox.innerHTML = `
                <h3 style="color: #ff4444; margin-bottom: 1rem;">❌ Token Inválido</h3>
                <p style="margin-top: 1rem;">${escapeHtml(data)}</p>
            `;
        }
        
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Función para validar token desde el input integrado
    function validateTokenFromInput() {
        const tokenInput = document.getElementById('token_to_validate');
        const validateBtn = document.getElementById('validateBtnInline');
        const validateBtnText = document.getElementById('validateBtnTextInline');
        
        if (!tokenInput || !tokenInput.value.trim()) {
            showToast('Por favor ingrese un token para validar', 'error');
            return;
        }
        
        const originalText = validateBtnText.textContent;
        const formData = new FormData();
        formData.append('action', 'validate');
        formData.append('token_to_validate', tokenInput.value.trim());
        
        // Deshabilitar botón y mostrar loading
        validateBtn.disabled = true;
        validateBtnText.textContent = 'Validando...';
        
        fetch('ajax_tokens.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            validateBtn.disabled = false;
            validateBtnText.textContent = originalText;
            
            if (data.success) {
                if (data.valid) {
                    showToast('Token válido', 'success');
                    displayValidationResult(data.data, true);
                } else {
                    showToast(data.message, 'error');
                    displayValidationResult(data.message, false);
                }
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            validateBtn.disabled = false;
            validateBtnText.textContent = originalText;
            showToast('Error al validar el token', 'error');
            console.error('Error:', error);
        });
    }

    // Función para desactivar token (sin confirmación)
    function deactivateToken(tokenHash, buttonElement) {
        const formData = new FormData();
        formData.append('action', 'deactivate');
        formData.append('token_hash', tokenHash);
        
        // Deshabilitar botón
        const originalText = buttonElement.textContent;
        buttonElement.disabled = true;
        buttonElement.textContent = 'Desactivando...';
        
        fetch('ajax_tokens.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Recargar la página para actualizar la tabla
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showToast(data.message, 'error');
                buttonElement.disabled = false;
                buttonElement.textContent = originalText;
            }
        })
        .catch(error => {
            showToast('Error al desactivar el token', 'error');
            buttonElement.disabled = false;
            buttonElement.textContent = originalText;
            console.error('Error:', error);
        });
    }

    // Función para eliminar token (sin confirmación)
    function deleteToken(tokenHash, buttonElement) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('token_hash', tokenHash);
        
        // Deshabilitar botón
        const originalText = buttonElement.textContent;
        buttonElement.disabled = true;
        buttonElement.textContent = 'Eliminando...';
        
        fetch('ajax_tokens.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Recargar la página para actualizar la tabla
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showToast(data.message, 'error');
                buttonElement.disabled = false;
                buttonElement.textContent = originalText;
            }
        })
        .catch(error => {
            showToast('Error al eliminar el token', 'error');
            buttonElement.disabled = false;
            buttonElement.textContent = originalText;
            console.error('Error:', error);
        });
    }

    // Función auxiliar para escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
  </script>
</body>
</html>
