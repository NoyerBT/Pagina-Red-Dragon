<?php
session_start();
require_once 'cnt/conexion.php';

// Verificar que el usuario esté logueado y tenga plan activo
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
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

// Verificar si hay un torneo en proceso
$torneo_id = null;
$torneo_nombre = null;
$equipos_existentes = [];

if (isset($_GET['torneo_id'])) {
    $torneo_id = intval($_GET['torneo_id']);
    // Verificar que el torneo pertenezca al usuario
    $stmt = $conn->prepare("SELECT id, nombre_torneo FROM torneos WHERE id = ? AND usuario_id = ?");
    
    if ($stmt === false) {
        // Si prepare falla, verificar si la tabla existe y crearla
        $check_table = $conn->query("SHOW TABLES LIKE 'torneos'");
        if ($check_table->num_rows === 0) {
            $create_table = "CREATE TABLE IF NOT EXISTS torneos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                nombre_torneo VARCHAR(200) NOT NULL,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_usuario (usuario_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $conn->query($create_table);
            $stmt = $conn->prepare("SELECT id, nombre_torneo FROM torneos WHERE id = ? AND usuario_id = ?");
        }
        
        if ($stmt === false) {
            error_log("Error preparando consulta: " . $conn->error);
            $torneo_id = null;
        }
    }
    
    if ($stmt !== false) {
        $stmt->bind_param("ii", $torneo_id, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($torneo = $result->fetch_assoc()) {
            $torneo_nombre = $torneo['nombre_torneo'];
            
            // Verificar si la tabla equipos_torneo existe
            $check_equipos_table = $conn->query("SHOW TABLES LIKE 'equipos_torneo'");
            if ($check_equipos_table->num_rows === 0) {
                // Crear la tabla si no existe
                $create_equipos_table = "CREATE TABLE IF NOT EXISTS equipos_torneo (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    torneo_id INT NOT NULL,
                    nombre_equipo VARCHAR(100) NOT NULL,
                    tag VARCHAR(20) DEFAULT NULL,
                    logo VARCHAR(255) DEFAULT NULL,
                    orden INT NOT NULL,
                    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (torneo_id) REFERENCES torneos(id) ON DELETE CASCADE,
                    INDEX idx_torneo (torneo_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                $conn->query($create_equipos_table);
            }
            
            // Cargar equipos existentes
            $stmt2 = $conn->prepare("SELECT id, nombre_equipo, tag, logo, orden FROM equipos_torneo WHERE torneo_id = ? ORDER BY orden ASC");
            
            if ($stmt2 !== false) {
                $stmt2->bind_param("i", $torneo_id);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                
                while ($equipo = $result2->fetch_assoc()) {
                    $equipos_existentes[] = $equipo;
                }
                $stmt2->close();
            }
        } else {
            $torneo_id = null;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crear Torneo - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="animations.css" />
  <style>
    .crear-torneo-container {
      max-width: 900px;
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

    .form-group select {
      width: 100%;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 8px;
      color: #fff;
      font-size: 1rem;
    }

    .form-group select:focus {
      outline: none;
      border-color: #d4af37;
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
    }

    .form-group select option {
      background: #1a1a1a;
      color: #fff;
    }

    .equipos-section {
      margin-top: 2rem;
    }

    .equipo-form-container {
      background: rgba(30, 30, 30, 0.8);
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .equipo-form-container h3 {
      color: #d4af37;
      margin-bottom: 1.5rem;
      font-size: 1.3rem;
    }

    .equipo-form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .logo-preview {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid rgba(212, 175, 55, 0.3);
      margin-top: 0.5rem;
      display: none;
    }

    .logo-preview.visible {
      display: block;
    }

    .btn-agregar-equipo {
      background: linear-gradient(135deg, #d4af37, #c09b2d);
      color: #000;
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      margin-top: 1rem;
    }

    .btn-agregar-equipo:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }

    .btn-agregar-equipo:disabled {
      background: #555;
      cursor: not-allowed;
      transform: none;
    }

    .editing-mode {
      border-color: #3498db !important;
      box-shadow: 0 0 15px rgba(52, 152, 219, 0.3);
    }

    .equipos-list {
      margin-top: 2rem;
    }

    .equipos-list {
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 2px solid rgba(212, 175, 55, 0.2);
    }

    .equipos-list h3 {
      color: #d4af37;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
    }

    .equipos-list-empty {
      text-align: center;
      padding: 2rem;
      color: rgba(255, 255, 255, 0.5);
      font-style: italic;
    }

    .equipo-card {
      background: rgba(40, 40, 40, 0.8);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 1.5rem;
      transition: all 0.3s ease;
    }

    .equipo-card:hover {
      border-color: rgba(212, 175, 55, 0.5);
      transform: translateX(5px);
    }

    .equipo-card-logo {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid rgba(212, 175, 55, 0.3);
      flex-shrink: 0;
    }

    .equipo-card-info {
      flex: 1;
    }

    .equipo-card-info h4 {
      color: #fff;
      margin: 0 0 0.5rem 0;
      font-size: 1.2rem;
    }

    .equipo-card-info .tag {
      color: #d4af37;
      font-size: 0.95rem;
    }

    .equipo-card-actions {
      display: flex;
      gap: 0.5rem;
      flex-shrink: 0;
    }

    .btn-editar-equipo {
      background: #3498db;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-editar-equipo:hover {
      background: #2980b9;
      transform: translateY(-2px);
    }

    .btn-eliminar-equipo {
      background: #e74c3c;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-eliminar-equipo:hover {
      background: #c0392b;
      transform: translateY(-2px);
    }

    .btn-cancelar-edicion {
      background: #95a5a6;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      margin-left: 0.5rem;
    }

    .btn-cancelar-edicion:hover {
      background: #7f8c8d;
    }

    .btn-crear-torneo {
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
      margin-top: 2rem;
    }

    .btn-crear-torneo:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 20px rgba(39, 174, 96, 0.4);
    }

    .btn-crear-torneo:disabled {
      background: #555;
      cursor: not-allowed;
      transform: none;
    }

    .max-equipos {
      color: #d4af37;
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .alert {
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
    }

    .alert-info {
      background: rgba(52, 152, 219, 0.2);
      border: 1px solid #3498db;
      color: #3498db;
    }

    .alert-error {
      background: rgba(231, 76, 60, 0.2);
      border: 1px solid #e74c3c;
      color: #e74c3c;
    }

    @media (max-width: 768px) {
      .crear-torneo-container {
        padding: 1rem;
      }

      .equipo-form-row {
        grid-template-columns: 1fr;
      }

      .equipo-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
      }

      .equipo-card-actions {
        width: 100%;
        justify-content: center;
      }

      .equipo-card-info {
        width: 100%;
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

  <main class="hero hero--organizador">
    <section class="hero-content">
      <h1>Crear Nuevo Torneo</h1>
      <p class="subtitle">Configura tu torneo, agrega equipos y sus logos</p>
    </section>
  </main>

  <section class="section">
    <div class="crear-torneo-container">
      <?php if (!$torneo_id): ?>
        <!-- Paso 1: Crear Torneo -->
        <div class="alert alert-info">
          <strong>💡 Información:</strong> Primero crea tu torneo con un nombre. Luego podrás agregar los equipos.
        </div>

        <div class="crear-torneo-card">
          <h2>1. Crear Torneo</h2>
          
          <form id="form-crear-torneo" enctype="multipart/form-data">
            <div class="form-group">
              <label for="nombre_torneo">Nombre del Torneo *</label>
              <input type="text" id="nombre_torneo" name="nombre_torneo" required maxlength="200" placeholder="Ej: Red Dragons Cup 2024">
            </div>
            <div class="form-group">
              <label for="modalidad_torneo">Modalidad del Torneo *</label>
              <select id="modalidad_torneo" name="modalidad_torneo" required>
                <option value="">Selecciona una modalidad</option>
                <option value="Single Elimination">Single Elimination</option>
                <option value="Double Elimination">Double Elimination</option>
              </select>
            </div>
            <div class="form-group">
              <label for="logo_torneo">Logo del Torneo (PNG o JPG)</label>
              <input type="file" id="logo_torneo" name="logo" accept="image/png,image/jpeg,image/jpg" onchange="previewTorneoLogo(this)">
              <img class="logo-preview" id="logo-torneo-preview" alt="Vista previa" style="margin-top: 0.5rem;">
            </div>
            <button type="submit" class="btn-crear-torneo" id="btn-crear-torneo-inicial">
              🏆 Crear Torneo
            </button>
          </form>
        </div>
      <?php else: ?>
        <!-- Paso 2: Agregar Equipos -->
        <div class="alert alert-info">
          <strong>💡 Información:</strong> Torneo: <strong><?php echo htmlspecialchars($torneo_nombre); ?></strong>. Puedes agregar equipos sin límite.
        </div>

        <div class="crear-torneo-card">
          <h2>2. Agregar Equipos</h2>
          
          <div class="equipos-section">
            <div class="equipo-form-container" id="equipo-form-container">
              <h3 id="form-title">Agregar Nuevo Equipo</h3>
              <form id="form-agregar-equipo">
                <div class="equipo-form-row">
                  <div class="form-group">
                    <label>Nombre del Equipo *</label>
                    <input type="text" id="equipo-nombre" required maxlength="100" placeholder="Ej: Red Dragons">
                  </div>
                  <div class="form-group">
                    <label>TAG / Signos (Opcional)</label>
                    <input type="text" id="equipo-tag" maxlength="20" placeholder="Ej: #1 / LATAM">
                  </div>
                </div>
                <div class="form-group">
                  <label>Logo del Equipo (PNG o JPG)</label>
                  <input type="file" id="equipo-logo" accept="image/png,image/jpeg,image/jpg" onchange="previewLogo(this)">
                  <img class="logo-preview" id="logo-preview" alt="Vista previa">
                </div>
                <button type="submit" class="btn-agregar-equipo" id="btn-agregar-equipo">
                  + Agregar Equipo
                </button>
                <button type="button" class="btn-cancelar-edicion" id="btn-cancelar-edicion" style="display: none;">
                  Cancelar Edición
                </button>
              </form>
            </div>
          </div>

          <div class="equipos-list" id="equipos-list">
            <h3>Equipos Agregados</h3>
            <div id="equipos-list-container">
              <div class="equipos-list-empty">No hay equipos agregados aún. Agrega tu primer equipo arriba.</div>
            </div>
          </div>
        </div>

        <div class="crear-torneo-card" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
          <button type="button" class="btn-crear-torneo" id="btn-guardar-cambios" style="display: none; background: linear-gradient(135deg, #3498db, #2980b9);">
            🔄 Actualizar Vista
          </button>
          <a href="torneo.php" class="btn-crear-torneo" style="text-decoration: none; display: inline-block; text-align: center;">
            ✅ Finalizar y Ver Torneo
          </a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <script>
    <?php if ($torneo_id): ?>
    const TORNEO_ID = <?php echo $torneo_id; ?>;
    let equipos = <?php echo json_encode($equipos_existentes); ?>;
    let equipoEditando = null;
    
    function previewLogo(input) {
      const preview = document.getElementById('logo-preview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.add('visible');
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function renderEquipos() {
      const container = document.getElementById('equipos-list-container');
      
      if (!equipos || equipos.length === 0) {
        container.innerHTML = '<div class="equipos-list-empty">No hay equipos agregados aún. Agrega tu primer equipo arriba.</div>';
        return;
      }

      container.innerHTML = equipos.map((equipo) => {
        const logoUrl = equipo.logo ? equipo.logo : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect width="80" height="80" fill="%23333"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-size="12"%3ESin logo%3C/text%3E%3C/svg%3E';
        return `
          <div class="equipo-card" data-equipo-id="${equipo.id}">
            <img src="${logoUrl}" alt="${equipo.nombre_equipo}" class="equipo-card-logo">
            <div class="equipo-card-info">
              <h4>${equipo.nombre_equipo}</h4>
              ${equipo.tag ? `<span class="tag">${equipo.tag}</span>` : ''}
            </div>
            <div class="equipo-card-actions">
              <button type="button" class="btn-editar-equipo" onclick="editarEquipo(${equipo.id})">Editar</button>
              <button type="button" class="btn-eliminar-equipo" onclick="eliminarEquipo(${equipo.id})">Eliminar</button>
            </div>
          </div>
        `;
      }).join('');
    }

    let hayCambiosPendientes = false;

    function mostrarBotonGuardar() {
      const btnGuardar = document.getElementById('btn-guardar-cambios');
      if (btnGuardar) {
        btnGuardar.style.display = 'inline-block';
        hayCambiosPendientes = true;
      }
    }

    function ocultarBotonGuardar() {
      const btnGuardar = document.getElementById('btn-guardar-cambios');
      if (btnGuardar) {
        btnGuardar.style.display = 'none';
        hayCambiosPendientes = false;
      }
    }

    function agregarEquipo() {
      const nombre = document.getElementById('equipo-nombre').value.trim();
      const tag = document.getElementById('equipo-tag').value.trim();
      const logoInput = document.getElementById('equipo-logo');
      
      if (!nombre) {
        alert('Debes ingresar un nombre para el equipo');
        return;
      }

      const formData = new FormData();
      formData.append('action', equipoEditando ? 'editar' : 'agregar');
      formData.append('torneo_id', TORNEO_ID);
      formData.append('nombre_equipo', nombre);
      if (tag) formData.append('tag', tag);
      if (logoInput.files && logoInput.files[0]) {
        formData.append('logo', logoInput.files[0]);
      }
      if (equipoEditando) {
        formData.append('equipo_id', equipoEditando);
      }

      const btn = document.getElementById('btn-agregar-equipo');
      btn.disabled = true;
      btn.textContent = 'Guardando...';

      fetch('ajax_equipos.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = '+ Agregar Equipo';
        
        if (data.success) {
          alert(data.message);
          resetForm();
          cargarEquipos();
          // Los cambios ya se guardaron, pero mostrar botón para refrescar
          mostrarBotonGuardar();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = '+ Agregar Equipo';
        alert('Error al guardar el equipo: ' + error);
      });
    }

    function editarEquipo(equipoId) {
      const equipo = equipos.find(e => e.id == equipoId);
      if (!equipo) return;
      
      document.getElementById('equipo-nombre').value = equipo.nombre_equipo;
      document.getElementById('equipo-tag').value = equipo.tag || '';
      
      const preview = document.getElementById('logo-preview');
      if (equipo.logo) {
        preview.src = equipo.logo;
        preview.classList.add('visible');
      } else {
        preview.classList.remove('visible');
      }

      document.getElementById('form-title').textContent = 'Editar Equipo';
      document.getElementById('btn-agregar-equipo').textContent = 'Guardar Cambios';
      document.getElementById('btn-cancelar-edicion').style.display = 'inline-block';
      document.getElementById('equipo-form-container').classList.add('editing-mode');
      
      equipoEditando = equipoId;
      mostrarBotonGuardar(); // Mostrar botón cuando se inicia la edición
      
      document.getElementById('equipo-form-container').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function eliminarEquipo(equipoId) {
      if (!confirm('¿Estás seguro de que deseas eliminar este equipo?')) {
        return;
      }

      const formData = new FormData();
      formData.append('action', 'eliminar');
      formData.append('torneo_id', TORNEO_ID);
      formData.append('equipo_id', equipoId);

      fetch('ajax_equipos.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          if (equipoEditando == equipoId) {
            resetForm();
            ocultarBotonGuardar();
          }
          cargarEquipos();
          // Mostrar botón para refrescar la vista
          mostrarBotonGuardar();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        alert('Error al eliminar el equipo: ' + error);
      });
    }

    function resetForm() {
      document.getElementById('form-agregar-equipo').reset();
      document.getElementById('logo-preview').classList.remove('visible');
      document.getElementById('form-title').textContent = 'Agregar Nuevo Equipo';
      document.getElementById('btn-agregar-equipo').textContent = '+ Agregar Equipo';
      document.getElementById('btn-cancelar-edicion').style.display = 'none';
      document.getElementById('equipo-form-container').classList.remove('editing-mode');
      equipoEditando = null;
      // No ocultar el botón aquí, solo cuando se guarde exitosamente
    }

    // Event listener para el botón Actualizar Vista
    document.getElementById('btn-guardar-cambios')?.addEventListener('click', function() {
      // Recargar la página para ver los cambios actualizados
      window.location.reload();
    });

    function cargarEquipos() {
      // Recargar la página para obtener los equipos actualizados desde la BD
      window.location.reload();
    }

    // Event listeners
    document.getElementById('form-agregar-equipo').addEventListener('submit', function(e) {
      e.preventDefault();
      agregarEquipo();
    });

    document.getElementById('btn-cancelar-edicion').addEventListener('click', resetForm);

    // Inicializar
    renderEquipos();
    <?php else: ?>
    // Paso 1: Crear torneo
    function previewTorneoLogo(input) {
      const preview = document.getElementById('logo-torneo-preview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.add('visible');
          preview.style.maxWidth = '200px';
          preview.style.maxHeight = '200px';
          preview.style.borderRadius = '8px';
          preview.style.border = '2px solid rgba(212, 175, 55, 0.3)';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    document.getElementById('form-crear-torneo').addEventListener('submit', function(e) {
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

      const btn = document.getElementById('btn-crear-torneo-inicial');
      btn.disabled = true;
      btn.textContent = 'Creando...';

      const formData = new FormData();
      formData.append('nombre_torneo', nombre);
      formData.append('modalidad', modalidad);
      
      const logoInput = document.getElementById('logo_torneo');
      if (logoInput.files && logoInput.files[0]) {
        formData.append('logo', logoInput.files[0]);
      }

      fetch('ajax_crear_torneo.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = '🏆 Crear Torneo';
        
        if (data.success) {
          window.location.href = `crear_torneo.php?torneo_id=${data.torneo_id}`;
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = '🏆 Crear Torneo';
        alert('Error al crear el torneo: ' + error);
      });
    });
    <?php endif; ?>
  </script>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
</body>
</html>
