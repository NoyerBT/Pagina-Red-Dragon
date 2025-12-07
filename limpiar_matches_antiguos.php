<?php
/**
 * Script para limpiar matches antiguos de la base de datos
 * 
 * Este script elimina:
 * 1. Matches que no tienen torneo_id asignado (NULL)
 * 2. Matches que tienen torneo_id pero el torneo ya no existe
 * 3. Matches que usan equipos de la tabla antigua 'equipos' en lugar de 'equipos_torneo'
 * 
 * IMPORTANTE: Ejecutar este script solo una vez después de migrar a la nueva estructura
 */

session_start();
require_once 'cnt/conexion.php';

// Verificar que el usuario esté logueado y sea admin o tenga plan activo
if (!isset($_SESSION['usuario'])) {
    die("Error: Debes estar logueado para ejecutar este script.");
}

// Verificar que el usuario tenga plan activo o sea admin
$es_admin = false;
$plan_activo = false;
$stmt = $conn->prepare("SELECT id, estado, fecha_expiracion, rol FROM usuarios WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if ($user['rol'] === 'admin') {
        $es_admin = true;
    }
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

if (!$es_admin && !$plan_activo) {
    die("Error: No tienes permisos para ejecutar este script. Debes ser administrador o tener plan activo.");
}

// Verificar si existe el campo torneo_id
$check_column = $conn->query("SHOW COLUMNS FROM matches LIKE 'torneo_id'");
if ($check_column->num_rows == 0) {
    die("Error: El campo 'torneo_id' no existe en la tabla 'matches'. Ejecuta primero brackets_torneo.php para agregarlo.");
}

$resultados = [
    'matches_sin_torneo' => 0,
    'matches_torneo_inexistente' => 0,
    'matches_equipos_antiguos' => 0,
    'total_eliminados' => 0,
    'errores' => []
];

echo "<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Limpieza de Matches Antiguos</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #1a1a1a;
      color: #d0d0d0;
      padding: 2rem;
      max-width: 900px;
      margin: 0 auto;
    }
    h1 {
      color: #d4af37;
      text-align: center;
    }
    .resultado {
      background: rgba(30, 30, 30, 0.9);
      border: 2px solid #d4af37;
      border-radius: 10px;
      padding: 1.5rem;
      margin: 1rem 0;
    }
    .success {
      color: #2ecc71;
      font-weight: bold;
    }
    .error {
      color: #e74c3c;
      font-weight: bold;
    }
    .info {
      color: #3498db;
    }
    .btn {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background: linear-gradient(135deg, #d4af37, #c09b2d);
      color: #000;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      margin-top: 1rem;
      cursor: pointer;
      border: none;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    }
    .btn-danger {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      color: #fff;
    }
  </style>
</head>
<body>
  <h1>🧹 Limpieza de Matches Antiguos</h1>";

// 1. Eliminar matches sin torneo_id (NULL)
echo "<div class='resultado'>";
echo "<h2>1. Eliminando matches sin torneo_id...</h2>";
$query1 = "SELECT COUNT(*) as total FROM matches WHERE torneo_id IS NULL";
$result1 = $conn->query($query1);
if ($result1) {
    $row1 = $result1->fetch_assoc();
    $resultados['matches_sin_torneo'] = $row1['total'];
    echo "<p class='info'>Encontrados: <strong>{$row1['total']}</strong> matches sin torneo_id</p>";
    
    if ($row1['total'] > 0) {
        $delete1 = "DELETE FROM matches WHERE torneo_id IS NULL";
        if ($conn->query($delete1)) {
            echo "<p class='success'>✓ Eliminados {$row1['total']} matches sin torneo_id</p>";
            $resultados['total_eliminados'] += $row1['total'];
        } else {
            $error = "Error eliminando matches sin torneo_id: " . $conn->error;
            echo "<p class='error'>✗ $error</p>";
            $resultados['errores'][] = $error;
        }
    } else {
        echo "<p class='success'>✓ No hay matches sin torneo_id</p>";
    }
} else {
    $error = "Error consultando matches sin torneo_id: " . $conn->error;
    echo "<p class='error'>✗ $error</p>";
    $resultados['errores'][] = $error;
}
echo "</div>";

// 2. Eliminar matches con torneo_id de torneos que ya no existen
echo "<div class='resultado'>";
echo "<h2>2. Eliminando matches de torneos inexistentes...</h2>";
$query2 = "SELECT m.id, m.torneo_id 
           FROM matches m 
           LEFT JOIN torneos t ON m.torneo_id = t.id 
           WHERE m.torneo_id IS NOT NULL AND t.id IS NULL";
$result2 = $conn->query($query2);
if ($result2) {
    $matches_torneo_inexistente = [];
    while ($row = $result2->fetch_assoc()) {
        $matches_torneo_inexistente[] = $row['id'];
    }
    $resultados['matches_torneo_inexistente'] = count($matches_torneo_inexistente);
    echo "<p class='info'>Encontrados: <strong>" . count($matches_torneo_inexistente) . "</strong> matches de torneos inexistentes</p>";
    
    if (count($matches_torneo_inexistente) > 0) {
        $ids = implode(',', array_map('intval', $matches_torneo_inexistente));
        $delete2 = "DELETE FROM matches WHERE id IN ($ids)";
        if ($conn->query($delete2)) {
            echo "<p class='success'>✓ Eliminados " . count($matches_torneo_inexistente) . " matches de torneos inexistentes</p>";
            $resultados['total_eliminados'] += count($matches_torneo_inexistente);
        } else {
            $error = "Error eliminando matches de torneos inexistentes: " . $conn->error;
            echo "<p class='error'>✗ $error</p>";
            $resultados['errores'][] = $error;
        }
    } else {
        echo "<p class='success'>✓ No hay matches de torneos inexistentes</p>";
    }
} else {
    $error = "Error consultando matches de torneos inexistentes: " . $conn->error;
    echo "<p class='error'>✗ $error</p>";
    $resultados['errores'][] = $error;
}
echo "</div>";

// 3. Identificar matches que usan equipos de la tabla antigua 'equipos' en lugar de 'equipos_torneo'
// (Estos matches tienen equipo1_id o equipo2_id que no están en equipos_torneo)
echo "<div class='resultado'>";
echo "<h2>3. Identificando matches con equipos de tabla antigua...</h2>";

// Obtener todos los IDs de equipos_torneo
$equipos_torneo_ids = [];
$query_equipos = "SELECT DISTINCT id FROM equipos_torneo";
$result_equipos = $conn->query($query_equipos);
if ($result_equipos) {
    while ($row = $result_equipos->fetch_assoc()) {
        $equipos_torneo_ids[] = $row['id'];
    }
}

// Buscar matches que tienen equipos que NO están en equipos_torneo
$query3 = "SELECT DISTINCT m.id, m.torneo_id, m.equipo1_id, m.equipo2_id 
           FROM matches m 
           WHERE m.torneo_id IS NOT NULL 
           AND (
               (m.equipo1_id IS NOT NULL AND m.equipo1_id NOT IN (" . (empty($equipos_torneo_ids) ? '0' : implode(',', array_map('intval', $equipos_torneo_ids))) . "))
               OR 
               (m.equipo2_id IS NOT NULL AND m.equipo2_id NOT IN (" . (empty($equipos_torneo_ids) ? '0' : implode(',', array_map('intval', $equipos_torneo_ids))) . "))
           )";
$result3 = $conn->query($query3);
if ($result3) {
    $matches_equipos_antiguos = [];
    while ($row = $result3->fetch_assoc()) {
        $matches_equipos_antiguos[] = $row['id'];
    }
    $resultados['matches_equipos_antiguos'] = count($matches_equipos_antiguos);
    echo "<p class='info'>Encontrados: <strong>" . count($matches_equipos_antiguos) . "</strong> matches usando equipos de tabla antigua</p>";
    
    if (count($matches_equipos_antiguos) > 0) {
        $ids3 = implode(',', array_map('intval', $matches_equipos_antiguos));
        $delete3 = "DELETE FROM matches WHERE id IN ($ids3)";
        if ($conn->query($delete3)) {
            echo "<p class='success'>✓ Eliminados " . count($matches_equipos_antiguos) . " matches con equipos de tabla antigua</p>";
            $resultados['total_eliminados'] += count($matches_equipos_antiguos);
        } else {
            $error = "Error eliminando matches con equipos antiguos: " . $conn->error;
            echo "<p class='error'>✗ $error</p>";
            $resultados['errores'][] = $error;
        }
    } else {
        echo "<p class='success'>✓ No hay matches usando equipos de tabla antigua</p>";
    }
} else {
    $error = "Error consultando matches con equipos antiguos: " . $conn->error;
    echo "<p class='error'>✗ $error</p>";
    $resultados['errores'][] = $error;
}
echo "</div>";

// Resumen final
echo "<div class='resultado' style='border-color: #2ecc71;'>";
echo "<h2>📊 Resumen de Limpieza</h2>";
echo "<p class='info'>Matches sin torneo_id: <strong>{$resultados['matches_sin_torneo']}</strong></p>";
echo "<p class='info'>Matches de torneos inexistentes: <strong>{$resultados['matches_torneo_inexistente']}</strong></p>";
echo "<p class='info'>Matches con equipos antiguos: <strong>{$resultados['matches_equipos_antiguos']}</strong></p>";
echo "<p class='success' style='font-size: 1.2rem; margin-top: 1rem;'>Total eliminado: <strong>{$resultados['total_eliminados']}</strong> matches</p>";

if (count($resultados['errores']) > 0) {
    echo "<h3 class='error'>Errores encontrados:</h3>";
    echo "<ul>";
    foreach ($resultados['errores'] as $error) {
        echo "<li class='error'>$error</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='success'>✓ Limpieza completada sin errores</p>";
}
echo "</div>";

echo "<div style='text-align: center; margin-top: 2rem;'>";
echo "<a href='torneo.php' class='btn'>← Volver a Torneos</a>";
echo "</div>";

echo "</body></html>";

$conn->close();
?>
