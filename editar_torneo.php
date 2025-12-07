<?php
session_start();
require_once 'cnt/conexion.php';

// Verificar que el usuario esté logueado y tenga plan activo
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$torneo_id = isset($_GET['torneo_id']) ? intval($_GET['torneo_id']) : 0;

if ($torneo_id <= 0) {
    header("Location: torneo.php");
    exit();
}

// Verificar que el usuario tenga plan activo
$plan_activo = false;
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
            }
        } catch (Exception $e) {
            error_log('Error evaluando expiración de plan: ' . $e->getMessage());
        }
    }
}
$stmt->close();

if (!$plan_activo) {
    header("Location: torneo.php");
    exit();
}

// Cargar datos del torneo
$torneo = null;
$stmt = $conn->prepare("SELECT id, nombre_torneo, logo, modalidad FROM torneos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $torneo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($torneo = $result->fetch_assoc()) {
    // Si no tiene modalidad, asignar Single Elimination por defecto
    if (empty($torneo['modalidad'])) {
        $torneo['modalidad'] = 'Single Elimination';
    }
} else {
    header("Location: torneo.php");
    exit();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Editar Torneo - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
  <style>
    .crear-torneo-container {
      max-width: 700px;
      margin: 2rem auto;
      padding: 2rem;
    }

    .crear-torneo-card {
      background: rgba(0, 0, 0, 0.7);
      border: 2px solid rgba(212, 175, 55, 0.3);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .crear-torneo-card h2 {
      color: #d4af37;
      margin-bottom: 1.5rem;
      font-size: 2rem;
      text-align: center;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      color: #fff;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .form-group input[type="text"] {
      width: 100%;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 8px;
      color: #fff;
      font-size: 1rem;
    }

    .form-group input[type="text"]:focus {
      outline: none;
      border-color: #d4af37;
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
    }

    .logo-preview {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid rgba(212, 175, 55, 0.3);
      margin-top: 0.5rem;
      display: none;
    }

    .logo-preview.visible {
      display: block;
    }

    .logo-actions {
      display: flex;
      gap: 1rem;
      margin-top: 0.5rem;
    }

    .btn-eliminar-logo {
      background: #e74c3c;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .btn-eliminar-logo:hover {
      background: #c0392b;
    }

    .btn-guardar {
      background: linear-gradient(135deg, #27ae60, #229954);
      color: #fff;
      border: none;
      padding: 1rem 3rem;
      border-radius: 10px;
      font-size: 1.2rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      margin-top: 1rem;
    }

    .btn-guardar:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 20px rgba(39, 174, 96, 0.4);
    }

    .btn-cancelar {
      background: #95a5a6;
      color: #fff;
      border: none;
      padding: 1rem 3rem;
      border-radius: 10px;
      font-size: 1.2rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      margin-top: 1rem;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-cancelar:hover {
      background: #7f8c8d;
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

  <main class="hero hero--organizador">
    <section class="hero-content">
      <h1>Editar Torneo</h1>
      <p class="subtitle">Modifica el nombre y logo de tu torneo</p>
    </section>
  </main>

  <section class="section">
    <div class="crear-torneo-container">
      <div class="crear-torneo-card">
        <h2>Editar Torneo</h2>
        
        <form id="form-editar-torneo" enctype="multipart/form-data">
          <div class="form-group">
            <label for="nombre_torneo">Nombre del Torneo *</label>
            <input type="text" id="nombre_torneo" name="nombre_torneo" required maxlength="200" value="<?php echo htmlspecialchars($torneo['nombre_torneo']); ?>" placeholder="Ej: Red Dragons Cup 2024">
          </div>
          
          <div class="form-group">
            <label for="modalidad_torneo">Modalidad del Torneo *</label>
            <select id="modalidad_torneo" name="modalidad_torneo" required style="width: 100%; padding: 0.75rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; color: #fff; font-size: 1rem;">
              <option value="Single Elimination" <?php echo ($torneo['modalidad'] === 'Single Elimination') ? 'selected' : ''; ?>>Single Elimination</option>
              <option value="Double Elimination" <?php echo ($torneo['modalidad'] === 'Double Elimination') ? 'selected' : ''; ?>>Double Elimination</option>
            </select>
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-top: 0.5rem;">
              ⚠️ Cambiar la modalidad solo generará el bracket B si no hay matches completados. Si ya hay resultados, se mantendrá la estructura actual.
            </p>
          </div>
          
          <div class="form-group">
            <label for="logo_torneo">Logo del Torneo (PNG o JPG)</label>
            <input type="file" id="logo_torneo" name="logo" accept="image/png,image/jpeg,image/jpg" onchange="previewLogo(this)">
            <?php if (!empty($torneo['logo'])): ?>
              <div style="margin-top: 1rem;">
                <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 0.5rem;">Logo actual:</p>
                <img src="<?php echo htmlspecialchars($torneo['logo']); ?>" alt="Logo actual" class="logo-preview visible" id="logo-actual" style="max-width: 200px; max-height: 200px;">
                <div class="logo-actions">
                  <button type="button" class="btn-eliminar-logo" onclick="eliminarLogo()">Eliminar Logo</button>
                </div>
              </div>
            <?php endif; ?>
            <img class="logo-preview" id="logo-preview" alt="Vista previa">
          </div>
          
          <button type="submit" class="btn-guardar" id="btn-guardar">
            💾 Guardar Cambios
          </button>
          <a href="torneo.php" class="btn-cancelar">
            Cancelar
          </a>
        </form>
      </div>
    </div>
  </section>

  <script>
    const TORNEO_ID = <?php echo $torneo_id; ?>;
    let eliminarLogoActual = false;

    function previewLogo(input) {
      const preview = document.getElementById('logo-preview');
      const logoActual = document.getElementById('logo-actual');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.add('visible');
          preview.style.maxWidth = '200px';
          preview.style.maxHeight = '200px';
          if (logoActual) {
            logoActual.style.display = 'none';
          }
          eliminarLogoActual = false; // Si sube uno nuevo, no eliminar el anterior
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function eliminarLogo() {
      if (confirm('¿Estás seguro de que deseas eliminar el logo actual?')) {
        const logoActual = document.getElementById('logo-actual');
        const preview = document.getElementById('logo-preview');
        const logoInput = document.getElementById('logo_torneo');
        
        if (logoActual) {
          logoActual.style.display = 'none';
        }
        preview.classList.remove('visible');
        logoInput.value = '';
        eliminarLogoActual = true;
      }
    }

    document.getElementById('form-editar-torneo').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const nombre = document.getElementById('nombre_torneo').value.trim();
      const modalidad = document.getElementById('modalidad_torneo').value;
      
      if (!nombre) {
        alert('Debes ingresar un nombre para el torneo');
        return;
      }
      
      if (!modalidad) {
        alert('Debes seleccionar una modalidad para el torneo');
        return;
      }

      const btn = document.getElementById('btn-guardar');
      btn.disabled = true;
      btn.textContent = 'Guardando...';

      const formData = new FormData();
      formData.append('torneo_id', TORNEO_ID);
      formData.append('nombre_torneo', nombre);
      formData.append('modalidad', modalidad);
      formData.append('eliminar_logo', eliminarLogoActual ? '1' : '0');
      
      const logoInput = document.getElementById('logo_torneo');
      if (logoInput.files && logoInput.files[0]) {
        formData.append('logo', logoInput.files[0]);
      }

      fetch('ajax_editar_torneo.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = '💾 Guardar Cambios';
        
        if (data.success) {
          alert(data.message);
          window.location.href = 'torneo.php';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = '💾 Guardar Cambios';
        alert('Error al guardar los cambios: ' + error);
      });
    });
  </script>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
