<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'TokenGenerator.php';

$generator = new TokenGenerator();
$message = "";
$messageType = "";
$activeTab = 'generate';

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
            $generated_token = $generator->generate_token($player_name);
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
        $token_to_validate = trim($_POST['token_to_validate'] ?? '');
        if (!empty($token_to_validate)) {
            list($isValid, $result) = $generator->validate_token($token_to_validate);
            $validation_result = [
                'valid' => $isValid,
                'data' => $result
            ];
        }
        $activeTab = 'validate';

    } elseif ($action === 'deactivate') {
        $token_hash = $_POST['token_hash'] ?? '';
        if ($generator->deactivate_token($token_hash)) {
            $message = "Token desactivado correctamente.";
            $messageType = "success";
        } else {
            $message = "Error al desactivar el token.";
            $messageType = "error";
        }
        $activeTab = 'manage';
    }
}

// Cargar datos para la pestaña de gestión siempre si es solicitada o por defecto
if ($activeTab === 'manage' || isset($_GET['tab']) && $_GET['tab'] === 'manage') {
    $token_stats = $generator->get_token_stats();
    $all_tokens = $generator->list_tokens();
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
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
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

  <section class="section">
    <div class="registro-container" style="max-width: 1000px;">
      
      <?php if ($message): ?>
        <div class="alert <?php echo $messageType === 'success' ? 'alert-success' : 'alert-error'; ?>" style="padding: 1rem; margin-bottom: 1rem; border-radius: 4px; background: <?php echo $messageType === 'success' ? 'rgba(0,255,0,0.1)' : 'rgba(255,0,0,0.1)'; ?>; border: 1px solid <?php echo $messageType === 'success' ? '#00ff00' : '#ff0000'; ?>; color: white;">
            <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <div class="tabs">
        <button class="tab-btn <?php echo $activeTab === 'generate' ? 'active' : ''; ?>" onclick="switchTab('generate')">Generar Token</button>
        <button class="tab-btn <?php echo $activeTab === 'validate' ? 'active' : ''; ?>" onclick="switchTab('validate')">Validar Token</button>
        <button class="tab-btn <?php echo $activeTab === 'manage' ? 'active' : ''; ?>" onclick="switchTab('manage')">Administrar</button>
      </div>

      <!-- Tab: Generar -->
      <div id="tab-generate" class="tab-content <?php echo $activeTab === 'generate' ? 'active' : ''; ?>">
        <div class="registro-card">
          <h2>Generar Nuevo Token</h2>
          <p class="plan-note" style="margin-bottom: 20px;">El token tendrá una validez fija de <strong>2 días</strong>.</p>
          
          <form action="generar_claves.php" method="POST" class="registro-form">
            <input type="hidden" name="action" value="generate">
            <div class="form-group">
              <label for="player_name">Nombre del Jugador</label>
              <input type="text" id="player_name" name="player_name" required placeholder="Ingrese nickname del jugador">
            </div>
            <button type="submit" class="btn primary registro-btn glow-on-hover">Generar Token</button>
          </form>

          <?php if ($generated_token): ?>
            <div class="result-box">
                <h3 style="color: #d4af37; margin-bottom: 1rem;">Token Generado Exitosamente</h3>
                
                <div class="token-details">
                    <p><strong>Jugador:</strong> <?php echo htmlspecialchars($generated_token['player_name']); ?></p>
                    <p><strong>Torneo:</strong> <?php echo htmlspecialchars($generated_token['tournament_name']); ?></p>
                    <p><strong>Expira:</strong> <?php echo date('d/m/Y H:i', strtotime($generated_token['expires_date'])); ?></p>
                    
                    <div class="token-display" id="token-text"><?php echo htmlspecialchars($generated_token['token']); ?></div>
                    
                    <div class="btn-group" style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                        <button type="button" onclick="copyToken()" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Copiar Token</button>
                    </div>

                    <div class="qr-container" id="qrcode"></div>
                    <script type="text/javascript">
                        var qrcode = new QRCode(document.getElementById("qrcode"), {
                            text: "<?php echo htmlspecialchars($generated_token['token']); ?>",
                            width: 128,
                            height: 128
                        });
                    </script>
                </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Tab: Validar -->
      <div id="tab-validate" class="tab-content <?php echo $activeTab === 'validate' ? 'active' : ''; ?>">
        <div class="registro-card">
          <h2>Validar Token</h2>
          <form action="generar_claves.php" method="POST" class="registro-form">
            <input type="hidden" name="action" value="validate">
            <div class="form-group">
              <label for="token_to_validate">Ingrese Token</label>
              <input type="text" id="token_to_validate" name="token_to_validate" required placeholder="Pegue el token aquí">
            </div>
            <button type="submit" class="btn primary registro-btn glow-on-hover">Validar</button>
          </form>

          <?php if ($validation_result): ?>
            <div class="result-box">
                <?php if ($validation_result['valid']): ?>
                    <h3 style="color: #00ff00;">✅ Token Válido</h3>
                    <?php $data = $validation_result['data']; ?>
                    <div style="margin-top: 1rem; line-height: 1.6;">
                        <p><strong>Jugador:</strong> <?php echo htmlspecialchars($data['player_name']); ?></p>
                        <p><strong>Torneo:</strong> <?php echo htmlspecialchars($data['tournament_name']); ?></p>
                        <p><strong>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($data['created_date'])); ?></p>
                        <p><strong>Expira:</strong> <?php echo date('d/m/Y H:i', strtotime($data['expires_date'])); ?></p>
                        <p><strong>Usos:</strong> <?php echo $data['used_count']; ?></p>
                    </div>
                <?php else: ?>
                    <h3 style="color: #ff4444;">❌ Token Inválido</h3>
                    <p style="margin-top: 1rem;"><?php echo htmlspecialchars($validation_result['data']); ?></p>
                <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Tab: Administrar -->
      <div id="tab-manage" class="tab-content <?php echo $activeTab === 'manage' ? 'active' : ''; ?>">
        <div class="registro-card" style="max-width: 100%;">
          <h2>Gestión de Tokens</h2>
          
          <?php if (!$token_stats): ?>
             <form action="generar_claves.php" method="GET">
                <input type="hidden" name="tab" value="manage">
                <button type="submit" class="btn primary registro-btn glow-on-hover">Cargar Datos</button>
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
                                <?php if ($is_active && !$is_expired): ?>
                                    <form action="generar_claves.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="deactivate">
                                        <input type="hidden" name="token_hash" value="<?php echo $hash; ?>">
                                        <button type="submit" class="btn exception" style="padding: 0.2rem 0.5rem; font-size: 0.8rem; background: #660000; color: white; border: none; cursor: pointer;">Desactivar</button>
                                    </form>
                                <?php endif; ?>
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
  </section>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
  <script>
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
            alert('Token copiado al portapapeles');
        }).catch(err => {
            // Fallback para navegadores que no soportan clipboard API o no están en contexto seguro
            const textArea = document.createElement("textarea");
            textArea.value = tokenText;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                alert('Token copiado al portapapeles');
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
            }
            document.body.removeChild(textArea);
        });
    }
  </script>
</body>
</html>
