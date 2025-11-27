<?php
session_start();
require_once 'cnt/conexion.php';

// Función para obtener matches por bracket y ronda
function getMatches($conn, $bracket_tipo, $ronda) {
    $bracket_tipo = $conn->real_escape_string($bracket_tipo);
    $ronda = intval($ronda);
    
    $query = "
        SELECT m.*, 
               e1.nombre as equipo1_nombre, 
               e2.nombre as equipo2_nombre
        FROM matches m
        LEFT JOIN equipos e1 ON m.equipo1_id = e1.id
        LEFT JOIN equipos e2 ON m.equipo2_id = e2.id
        WHERE m.bracket_tipo = '$bracket_tipo' AND m.ronda = $ronda
        ORDER BY m.numero_match ASC
    ";
    
    $result = $conn->query($query);
    $matches = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
    }
    return $matches;
}

// Función para renderizar un match
function renderMatch($match) {
    $equipo1 = $match['equipo1_nombre'] ?? 'TBD';
    $equipo2 = $match['equipo2_nombre'] ?? 'TBD';
    $puntos1 = $match['puntos_equipo1'];
    $puntos2 = $match['puntos_equipo2'];
    
    $class1 = '';
    $class2 = '';
    
    if ($puntos1 !== null && $puntos2 !== null) {
        if ($puntos1 > $puntos2) {
            $class1 = 'score-winner';
            $class2 = 'score-loser';
        } elseif ($puntos2 > $puntos1) {
            $class1 = 'score-loser';
            $class2 = 'score-winner';
        }
    }
    
    $display1 = $puntos1 !== null ? $puntos1 : '-';
    $display2 = $puntos2 !== null ? $puntos2 : '-';
    
    return "
    <div class='match'>
      <div class='team-slot'>
        <span class='team-name'>" . htmlspecialchars($equipo1) . "</span>
        <span class='score $class1'>$display1</span>
      </div>
      <div class='team-slot'>
        <span class='team-name'>" . htmlspecialchars($equipo2) . "</span>
        <span class='score $class2'>$display2</span>
      </div>
    </div>
    ";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Brackets - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="brackets.css" />
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
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="torneo.php">TORNEO</a>
      <?php endif; ?>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="brackets-main">
    <section class="brackets-header">
      <h1>🏆 Doble Eliminación</h1>
      <p class="brackets-subtitle">Sistema de llaves con Winners Bracket y Losers Bracket</p>
    </section>

    <div class="tournament-container">
      
      <!-- WINNER'S BRACKET -->
      <div class="bracket-wrapper">
        <h2 class="bracket-section-title">WINNER'S BRACKET</h2>
        
        <div class="bracket">
          <?php
          // Estructura de rondas del Winners Bracket
          $wb_rondas = [
              1 => 'Ronda 1',
              2 => 'Ronda 2',
              3 => 'Ronda 3',
              4 => 'Ronda 4',
              5 => 'Semifinales',
              6 => 'Final Winners'
          ];
          
          foreach ($wb_rondas as $ronda_num => $ronda_nombre):
              $matches = getMatches($conn, 'winners', $ronda_num);
              if (count($matches) > 0):
          ?>
          <div class="round">
            <div class="round-label"><?php echo $ronda_nombre; ?> (<?php echo count($matches); ?> Matches)</div>
            <?php foreach ($matches as $match): ?>
              <?php echo renderMatch($match); ?>
            <?php endforeach; ?>
          </div>
          <?php 
              endif;
          endforeach; 
          ?>
        </div>
      </div>
      
      <!-- LOSER'S BRACKET -->
      <div class="bracket-wrapper bracket-losers-wrapper">
        <h2 class="bracket-section-title losers-title">LOSER'S BRACKET</h2>
        <p class="bracket-description">Los perdedores de cada ronda del Winners Bracket caen aquí para una segunda oportunidad</p>
        
        <div class="bracket">
          <?php
          // Estructura de rondas del Losers Bracket
          $lb_rondas = [
              1 => 'LB R1',
              2 => 'LB R2',
              3 => 'LB R3',
              4 => 'LB R4',
              5 => 'LB R5',
              6 => 'LB R6',
              7 => 'LB Semifinal',
              8 => 'LB Final'
          ];
          
          foreach ($lb_rondas as $ronda_num => $ronda_nombre):
              $matches = getMatches($conn, 'losers', $ronda_num);
              if (count($matches) > 0):
          ?>
          <div class="round">
            <div class="round-label"><?php echo $ronda_nombre; ?> (<?php echo count($matches); ?> Matches)</div>
            <?php foreach ($matches as $match): ?>
              <?php echo renderMatch($match); ?>
            <?php endforeach; ?>
          </div>
          <?php 
              endif;
          endforeach; 
          ?>
        </div>
      </div>
      
      <!-- GRAN FINAL -->
      <div class="grand-final-wrapper">
        <h2 class="bracket-section-title grand-final-title">🏆 GRAN FINAL</h2>
        <?php
        $gf_matches = getMatches($conn, 'grand_final', 1);
        if (count($gf_matches) > 0):
            foreach ($gf_matches as $match):
                echo str_replace('match', 'match match-grand-final', renderMatch($match));
            endforeach;
        else:
        ?>
        <div class="match match-grand-final">
          <div class="team-slot">
            <span class="team-name">Ganador Winners</span>
            <span class="score">-</span>
          </div>
          <div class="team-slot">
            <span class="team-name">Ganador Losers</span>
            <span class="score">-</span>
          </div>
        </div>
        <?php endif; ?>
        <p class="grand-final-info">Best of 5 (Bo5)</p>
      </div>
    </div>

    <div class="back-button-container">
      <a href="torneo.php" class="btn-back">← Volver al Torneo</a>
    </div>
  </main>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
</body>
</html>
