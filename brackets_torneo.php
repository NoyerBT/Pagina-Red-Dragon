<?php
session_start();
require_once 'cnt/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar que el usuario tenga plan activo
$plan_activo = false;
$usuario_id = null;
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

// Obtener el ID del torneo
$torneo_id = isset($_GET['torneo_id']) ? intval($_GET['torneo_id']) : 0;

if ($torneo_id <= 0) {
    header("Location: torneo.php");
    exit();
}

// Verificar si existe la columna modalidad, si no, agregarla
$check_modalidad = $conn->query("SHOW COLUMNS FROM torneos LIKE 'modalidad'");
if ($check_modalidad->num_rows === 0) {
    $conn->query("ALTER TABLE torneos ADD COLUMN modalidad VARCHAR(50) NOT NULL DEFAULT 'Single Elimination' AFTER nombre_torneo");
}

// Verificar si existe la columna torneo_id en matches, si no, agregarla
$check_torneo_id = $conn->query("SHOW COLUMNS FROM matches LIKE 'torneo_id'");
if ($check_torneo_id->num_rows === 0) {
    $conn->query("ALTER TABLE matches ADD COLUMN torneo_id INT DEFAULT NULL AFTER id");
    $conn->query("ALTER TABLE matches ADD INDEX idx_torneo (torneo_id)");
}

// Obtener información del torneo con nombre del creador
$stmt = $conn->prepare("SELECT t.id, t.nombre_torneo, t.modalidad, t.logo, t.fecha_creacion, u.usuario as creador_nombre 
                        FROM torneos t 
                        LEFT JOIN usuarios u ON t.usuario_id = u.id 
                        WHERE t.id = ? AND t.usuario_id = ?");
if ($stmt === false) {
    $stmt = $conn->prepare("SELECT t.id, t.nombre_torneo, t.logo, t.fecha_creacion, u.usuario as creador_nombre 
                            FROM torneos t 
                            LEFT JOIN usuarios u ON t.usuario_id = u.id 
                            WHERE t.id = ? AND t.usuario_id = ?");
    if ($stmt === false) {
        error_log("Error preparando consulta: " . $conn->error);
        header("Location: torneo.php");
        exit();
    }
$stmt->bind_param("ii", $torneo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$torneo = $result->fetch_assoc();
    if ($torneo) {
        $torneo['modalidad'] = 'Single Elimination';
    }
} else {
    $stmt->bind_param("ii", $torneo_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $torneo = $result->fetch_assoc();
    if ($torneo && !isset($torneo['modalidad'])) {
        $torneo['modalidad'] = 'Single Elimination';
    }
}
$stmt->close();

if (!$torneo) {
    header("Location: torneo.php");
    exit();
}

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Crear match
    if ($accion === 'crear_match') {
        $bracket_tipo = $conn->real_escape_string($_POST['bracket_tipo'] ?? 'winners');
        $ronda = intval($_POST['ronda'] ?? 1);
        $equipo1_id = !empty($_POST['equipo1_id']) ? intval($_POST['equipo1_id']) : null;
        $equipo2_id = !empty($_POST['equipo2_id']) ? intval($_POST['equipo2_id']) : null;
        
        // Verificar que los equipos pertenezcan al torneo
        if ($equipo1_id) {
            $check1 = $conn->query("SELECT id FROM equipos_torneo WHERE id = $equipo1_id AND torneo_id = $torneo_id");
            if ($check1->num_rows == 0) $equipo1_id = null;
        }
        if ($equipo2_id) {
            $check2 = $conn->query("SELECT id FROM equipos_torneo WHERE id = $equipo2_id AND torneo_id = $torneo_id");
            if ($check2->num_rows == 0) $equipo2_id = null;
        }
        
        // Obtener el siguiente número de match
        $result = $conn->query("SELECT MAX(numero_match) as max_num FROM matches WHERE torneo_id = $torneo_id AND bracket_tipo='$bracket_tipo' AND ronda=$ronda");
        $row = $result->fetch_assoc();
        $numero_match = ($row['max_num'] ?? 0) + 1;
        
        $equipo1_sql = $equipo1_id ? $equipo1_id : 'NULL';
        $equipo2_sql = $equipo2_id ? $equipo2_id : 'NULL';
        
        $sql = "INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                VALUES ($torneo_id, '$bracket_tipo', $ronda, $numero_match, $equipo1_sql, $equipo2_sql)";
        $conn->query($sql);
    }
    
    // Actualizar resultado del match (equipos y/o puntajes)
    if ($accion === 'actualizar_resultado') {
        $match_id = intval($_POST['match_id']);
        $puntos1 = intval($_POST['puntos_equipo1'] ?? 0);
        $puntos2 = intval($_POST['puntos_equipo2'] ?? 0);
        $equipo1_id = !empty($_POST['equipo1_id']) ? intval($_POST['equipo1_id']) : null;
        $equipo2_id = !empty($_POST['equipo2_id']) ? intval($_POST['equipo2_id']) : null;
        
        // Verificar que el match pertenezca al torneo
        $check_match = $conn->query("SELECT * FROM matches WHERE id = $match_id AND torneo_id = $torneo_id");
        if ($check_match->num_rows > 0) {
            $match_info = $check_match->fetch_assoc();
            $ronda_actual = $match_info['ronda'];
            $bracket_tipo = $match_info['bracket_tipo'];
            $numero_match = $match_info['numero_match'];
            
            // Verificar que los equipos pertenezcan al torneo
            if ($equipo1_id) {
                $check1 = $conn->query("SELECT id FROM equipos_torneo WHERE id = $equipo1_id AND torneo_id = $torneo_id");
                if ($check1->num_rows == 0) $equipo1_id = null;
            }
            if ($equipo2_id) {
                $check2 = $conn->query("SELECT id FROM equipos_torneo WHERE id = $equipo2_id AND torneo_id = $torneo_id");
                if ($check2->num_rows == 0) $equipo2_id = null;
            }
            
            // Determinar ganador
            $ganador_id = null;
            if ($equipo1_id && $equipo2_id && $puntos1 > 0 && $puntos2 > 0) {
                if ($puntos1 > $puntos2) {
                    $ganador_id = $equipo1_id;
                } elseif ($puntos2 > $puntos1) {
                    $ganador_id = $equipo2_id;
                }
            }
            
            // Actualizar match (equipos y puntajes)
            $equipo1_sql = $equipo1_id ? $equipo1_id : 'NULL';
            $equipo2_sql = $equipo2_id ? $equipo2_id : 'NULL';
            $ganador_sql = $ganador_id ? $ganador_id : 'NULL';
            $sql = "UPDATE matches SET 
                    equipo1_id=$equipo1_sql,
                    equipo2_id=$equipo2_sql,
                    puntos_equipo1=$puntos1, 
                    puntos_equipo2=$puntos2, 
                    ganador_id=$ganador_sql,
                    completado=" . ($ganador_id ? 1 : 0) . "
                    WHERE id=$match_id AND torneo_id=$torneo_id";
            $conn->query($sql);
    
            // Determinar perdedor
            $perdedor_id = null;
            if ($equipo1_id && $equipo2_id && $ganador_id) {
                $perdedor_id = ($ganador_id == $equipo1_id) ? $equipo2_id : $equipo1_id;
            }
            
            // Avanzar ganador al siguiente match
            if ($ganador_id) {
                if ($bracket_tipo === 'winners') {
                    // En Winners Bracket, el ganador avanza a la siguiente ronda de Winners
                    $siguiente_ronda = $ronda_actual + 1;
                    $siguiente_match_num = ceil($numero_match / 2);
            
                    // Determinar si va a equipo1 o equipo2 del siguiente match
                    $es_equipo1 = ($numero_match % 2 == 1);
                    
                    $check_siguiente = $conn->query("SELECT id, equipo1_id, equipo2_id FROM matches 
                                                     WHERE torneo_id = $torneo_id 
                                                     AND bracket_tipo = 'winners' 
                                                     AND ronda = $siguiente_ronda 
                                                     AND numero_match = $siguiente_match_num");
                    
                    if ($check_siguiente->num_rows > 0) {
                        $siguiente_match = $check_siguiente->fetch_assoc();
                        if ($es_equipo1) {
                            $conn->query("UPDATE matches SET equipo1_id = $ganador_id WHERE id = " . $siguiente_match['id']);
                        } else {
                            $conn->query("UPDATE matches SET equipo2_id = $ganador_id WHERE id = " . $siguiente_match['id']);
                        }
                    } else {
                        // Crear el siguiente match si no existe
                        $equipo1_sql = $es_equipo1 ? $ganador_id : 'NULL';
                        $equipo2_sql = $es_equipo1 ? 'NULL' : $ganador_id;
                        $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                     VALUES ($torneo_id, 'winners', $siguiente_ronda, $siguiente_match_num, $equipo1_sql, $equipo2_sql)");
                    }
                    
                    // Si es Double Elimination, enviar perdedor al Losers Bracket
                    if ($modalidad_actual === 'Double Elimination' && $perdedor_id) {
                        enviarPerdedorALosers($conn, $torneo_id, $perdedor_id, $ronda_actual, $numero_match);
                    }
                } elseif ($bracket_tipo === 'losers') {
                    // En Losers Bracket, el ganador avanza según la estructura:
                    // Rondas impares: ganadores avanzan a la siguiente ronda impar
                    // Rondas pares: ganadores avanzan a la siguiente ronda impar
                    
                    if ($ronda_actual % 2 == 1) {
                        // Ronda impar: ganadores avanzan a la siguiente ronda impar
                        $siguiente_ronda = $ronda_actual + 2;
                        $siguiente_match_num = ceil($numero_match / 2);
                        
                        $check_siguiente = $conn->query("SELECT id, equipo1_id, equipo2_id FROM matches 
                                                         WHERE torneo_id = $torneo_id 
                                                         AND bracket_tipo = 'losers' 
                                                         AND ronda = $siguiente_ronda 
                                                         AND numero_match = $siguiente_match_num");
                        
                        if ($check_siguiente->num_rows > 0) {
                            $siguiente_match = $check_siguiente->fetch_assoc();
                            $es_equipo1 = ($numero_match % 2 == 1);
                            if ($es_equipo1) {
                                $conn->query("UPDATE matches SET equipo1_id = $ganador_id WHERE id = " . $siguiente_match['id']);
                            } else {
                                $conn->query("UPDATE matches SET equipo2_id = $ganador_id WHERE id = " . $siguiente_match['id']);
                            }
                        }
                    } else {
                        // Ronda par: ganadores avanzan a la siguiente ronda impar (que enfrenta perdedores de Winners)
                        $siguiente_ronda = $ronda_actual + 1;
                        $siguiente_match_num = ceil($numero_match / 2);
                        
                        $check_siguiente = $conn->query("SELECT id, equipo1_id, equipo2_id FROM matches 
                                                         WHERE torneo_id = $torneo_id 
                                                         AND bracket_tipo = 'losers' 
                                                         AND ronda = $siguiente_ronda 
                                                         AND numero_match = $siguiente_match_num");
                        
                        if ($check_siguiente->num_rows > 0) {
                            $siguiente_match = $check_siguiente->fetch_assoc();
                            // En rondas pares, el ganador va al equipo1 (el equipo2 será el perdedor de Winners)
                            if (!$siguiente_match['equipo1_id']) {
                                $conn->query("UPDATE matches SET equipo1_id = $ganador_id WHERE id = " . $siguiente_match['id']);
                            }
                        }
                    }
                    
                    // Si es la última ronda del Losers, verificar si va a la Gran Final
                    $check_ultima_ronda_losers = $conn->query("SELECT COUNT(*) as total FROM matches 
                                                                WHERE torneo_id = $torneo_id 
                                                                AND bracket_tipo = 'losers' 
                                                                AND ronda > $ronda_actual");
                    $ultima_ronda_losers = $check_ultima_ronda_losers->fetch_assoc();
                    if ($ultima_ronda_losers['total'] == 0) {
                        // Es la última ronda del Losers, el ganador va a la Gran Final
                        $check_gran_final = $conn->query("SELECT id, equipo1_id, equipo2_id FROM matches 
                                                          WHERE torneo_id = $torneo_id 
                                                          AND bracket_tipo = 'grand_final' 
                                                          AND ronda = 1 
                                                          LIMIT 1");
                        if ($check_gran_final->num_rows > 0) {
                            $gf_match = $check_gran_final->fetch_assoc();
                            // El ganador del Losers va al equipo2 de la Gran Final
                            $conn->query("UPDATE matches SET equipo2_id = $ganador_id WHERE id = " . $gf_match['id']);
                        } else {
                            // Crear Gran Final con ganador del Losers
                            $ganador_winners = obtenerGanadorWinners($conn, $torneo_id);
                            $equipo1_sql = $ganador_winners ? $ganador_winners : 'NULL';
                            $equipo2_sql = $ganador_id;
                            $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                         VALUES ($torneo_id, 'grand_final', 1, 1, $equipo1_sql, $equipo2_sql)");
                        }
                    }
                } elseif ($bracket_tipo === 'grand_final') {
                    // La Gran Final ya es el final, no hay más rondas
                }
            }
            
            // Si el ganador del Winners completa su bracket, crear/actualizar Gran Final
            if ($bracket_tipo === 'winners' && $ganador_id && $ronda_actual > 1) {
                $check_ultima_ronda = $conn->query("SELECT COUNT(*) as total FROM matches 
                                                    WHERE torneo_id = $torneo_id 
                                                    AND bracket_tipo = 'winners' 
                                                    AND ronda > $ronda_actual");
                $ultima_ronda = $check_ultima_ronda->fetch_assoc();
                if ($ultima_ronda['total'] == 0 && $modalidad_actual === 'Double Elimination') {
                    // Es la última ronda del Winners, crear/actualizar Gran Final
                    $check_gf = $conn->query("SELECT id, equipo1_id FROM matches 
                                              WHERE torneo_id = $torneo_id 
                                              AND bracket_tipo = 'grand_final' 
                                              AND ronda = 1 
                                              LIMIT 1");
                    if ($check_gf->num_rows > 0) {
                        $gf = $check_gf->fetch_assoc();
                        $conn->query("UPDATE matches SET equipo1_id = $ganador_id WHERE id = " . $gf['id']);
                    } else {
                        $ganador_losers = obtenerGanadorLosers($conn, $torneo_id);
                        $equipo1_sql = $ganador_id;
                        $equipo2_sql = $ganador_losers ? $ganador_losers : 'NULL';
                        $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                     VALUES ($torneo_id, 'grand_final', 1, 1, $equipo1_sql, $equipo2_sql)");
                    }
                }
            }
        }
    }
    
    header("Location: brackets_torneo.php?torneo_id=$torneo_id");
    exit();
}

// Función para enviar perdedor al Losers Bracket
function enviarPerdedorALosers($conn, $torneo_id, $perdedor_id, $ronda_winners, $numero_match_winners) {
    // Estructura del Losers Bracket en doble eliminación:
    // Ronda 1 Winners → Ronda 1 Losers (perdedores se enfrentan entre sí)
    // Ronda 2 Winners → Ronda 2 Losers (perdedores vs ganadores de Ronda 1 Losers)
    // Ronda 3 Winners → Ronda 4 Losers (perdedores vs ganadores de Ronda 3 Losers)
    // Ronda 4 Winners → Ronda 6 Losers (perdedores vs ganadores de Ronda 5 Losers)
    // Patrón: Ronda N Winners → Ronda (si N=1: 1, si N>1: (N-1)*2) Losers
    
    if ($ronda_winners == 1) {
        // Perdedores de Ronda 1 Winners van a Ronda 1 Losers
        // Los perdedores se enfrentan entre sí: match 1 vs match 2, match 3 vs match 4, etc.
        $losers_ronda = 1;
        // Calcular qué match en Losers: match 1 y 2 de Winners → match 1 de Losers
        $losers_match_num = ceil($numero_match_winners / 2);
        
        // Buscar match en Losers Ronda 1
        $check_losers = $conn->query("SELECT id, equipo1_id, equipo2_id FROM matches 
                                      WHERE torneo_id = $torneo_id 
                                      AND bracket_tipo = 'losers' 
                                      AND ronda = $losers_ronda 
                                      AND numero_match = $losers_match_num");
        
        if ($check_losers->num_rows > 0) {
            $losers_match = $check_losers->fetch_assoc();
            // Agregar al slot vacío
            // Si es match impar (1, 3, 5...) va a equipo1, si es par (2, 4, 6...) va a equipo2
            if ($numero_match_winners % 2 == 1) {
                // Match impar → equipo1
                if (!$losers_match['equipo1_id']) {
                    $conn->query("UPDATE matches SET equipo1_id = $perdedor_id WHERE id = " . $losers_match['id']);
                }
            } else {
                // Match par → equipo2
                if (!$losers_match['equipo2_id']) {
                    $conn->query("UPDATE matches SET equipo2_id = $perdedor_id WHERE id = " . $losers_match['id']);
                }
            }
        } else {
            // Crear nuevo match en Losers Ronda 1
            $equipo1_sql = ($numero_match_winners % 2 == 1) ? $perdedor_id : 'NULL';
            $equipo2_sql = ($numero_match_winners % 2 == 1) ? 'NULL' : $perdedor_id;
            $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                         VALUES ($torneo_id, 'losers', 1, $losers_match_num, $equipo1_sql, $equipo2_sql)");
        }
    } else {
        // Perdedores de rondas siguientes van a rondas específicas del Losers
        // Ronda 2 Winners → Ronda 2 Losers
        // Ronda 3 Winners → Ronda 4 Losers
        // Ronda 4 Winners → Ronda 6 Losers
        $losers_ronda = ($ronda_winners == 2) ? 2 : (($ronda_winners - 1) * 2);
        $losers_match_num = ceil($numero_match_winners / 2);
        
        // Buscar match en Losers con slot vacío
        $check_losers = $conn->query("SELECT id, equipo1_id, equipo2_id FROM matches 
                                      WHERE torneo_id = $torneo_id 
                                      AND bracket_tipo = 'losers' 
                                      AND ronda = $losers_ronda 
                                      AND numero_match = $losers_match_num");
        
        if ($check_losers->num_rows > 0) {
            $losers_match = $check_losers->fetch_assoc();
            // Agregar al slot vacío (el otro slot debería tener el ganador de la ronda anterior del Losers)
            if (!$losers_match['equipo1_id']) {
                $conn->query("UPDATE matches SET equipo1_id = $perdedor_id WHERE id = " . $losers_match['id']);
            } elseif (!$losers_match['equipo2_id']) {
                $conn->query("UPDATE matches SET equipo2_id = $perdedor_id WHERE id = " . $losers_match['id']);
            }
        } else {
            // Crear nuevo match en Losers (el otro equipo vendrá del ganador de la ronda anterior)
            $equipo1_sql = 'NULL';
            $equipo2_sql = $perdedor_id;
            $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                         VALUES ($torneo_id, 'losers', $losers_ronda, $losers_match_num, $equipo1_sql, $equipo2_sql)");
        }
    }
}

// Función para obtener ganador del Winners Bracket
function obtenerGanadorWinners($conn, $torneo_id) {
    $result = $conn->query("SELECT ganador_id FROM matches 
                            WHERE torneo_id = $torneo_id 
                            AND bracket_tipo = 'winners' 
                            AND completado = 1 
                            AND ganador_id IS NOT NULL
                            ORDER BY ronda DESC, numero_match DESC 
                            LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['ganador_id'];
    }
    return null;
}

// Función para obtener ganador del Losers Bracket
function obtenerGanadorLosers($conn, $torneo_id) {
    $result = $conn->query("SELECT ganador_id FROM matches 
                            WHERE torneo_id = $torneo_id 
                            AND bracket_tipo = 'losers' 
                            AND completado = 1 
                            AND ganador_id IS NOT NULL
                            ORDER BY ronda DESC, numero_match DESC 
                            LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['ganador_id'];
    }
    return null;
}

// Obtener equipos del torneo
$stmt = $conn->prepare("SELECT id, nombre_equipo, tag, logo, orden FROM equipos_torneo WHERE torneo_id = ? ORDER BY orden ASC");
$stmt->bind_param("i", $torneo_id);
$stmt->execute();
$result = $stmt->get_result();
$equipos_torneo = [];
while ($equipo = $result->fetch_assoc()) {
    $equipos_torneo[] = $equipo;
}
$stmt->close();

// Obtener matches del torneo
$matches_query = $conn->query("SELECT m.*, 
               e1.nombre_equipo as equipo1_nombre, 
               e2.nombre_equipo as equipo2_nombre
        FROM matches m
        LEFT JOIN equipos_torneo e1 ON m.equipo1_id = e1.id
        LEFT JOIN equipos_torneo e2 ON m.equipo2_id = e2.id
                               WHERE m.torneo_id = $torneo_id
                               ORDER BY m.bracket_tipo, m.ronda, m.numero_match");
    $matches = [];
if ($matches_query) {
    while ($row = $matches_query->fetch_assoc()) {
            $matches[] = $row;
        }
    }

// Validar mínimo de equipos (12 equipos mínimo)
$total_equipos = count($equipos_torneo);
$minimo_equipos = 12;
$error_equipos_insuficientes = false;
$error_equipos_impares = false;

// Generar matches automáticamente si no existen y hay equipos suficientes
if ($total_equipos > 0 && count($matches) == 0) {
    // Verificar que haya mínimo 12 equipos
    if ($total_equipos < $minimo_equipos) {
        // No generar matches si no hay suficientes equipos
        $error_equipos_insuficientes = true;
    } elseif ($total_equipos % 2 != 0) {
        // Verificar que el número de equipos sea par
        $error_equipos_insuficientes = true;
        $error_equipos_impares = true;
    } else {
    // Calcular número de matches para ronda 1 (debe ser par, así que división exacta)
    $matches_ronda1 = $total_equipos / 2;
    
    // Crear matches para ronda 1 Winners (todos vacíos, sin equipos asignados)
    for ($i = 1; $i <= $matches_ronda1; $i++) {
        $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                     VALUES ($torneo_id, 'winners', 1, $i, NULL, NULL)");
    }
    
    // Generar rondas siguientes de Winners vacías
    $matches_actuales = $matches_ronda1;
    $ronda_actual = 2;
    while ($matches_actuales > 1) {
        $matches_siguiente = ceil($matches_actuales / 2);
        for ($i = 1; $i <= $matches_siguiente; $i++) {
            $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                         VALUES ($torneo_id, 'winners', $ronda_actual, $i, NULL, NULL)");
        }
        $matches_actuales = $matches_siguiente;
        $ronda_actual++;
    }
    
    // Si es Double Elimination, generar Losers Bracket
    if ($modalidad_actual === 'Double Elimination') {
        // Ronda 1 Losers: Perdedores de Ronda 1 Winners (se enfrentan entre sí)
        // Si hay 8 equipos en Ronda 1 Winners (4 matches), habrá 4 perdedores → 2 matches en Losers Ronda 1
        $matches_losers_r1 = ceil($matches_ronda1 / 2);
        for ($i = 1; $i <= $matches_losers_r1; $i++) {
            $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                         VALUES ($torneo_id, 'losers', 1, $i, NULL, NULL)");
        }
        
        // Generar rondas siguientes del Losers Bracket
        // Patrón: Rondas impares = ganadores anteriores, Rondas pares = perdedores Winners + ganadores Losers
        $ronda_losers = 2;
        $matches_losers_actual = $matches_losers_r1;
        $ronda_winners = 2;
        
        while ($ronda_winners <= $ronda_actual - 1) {
            // Ronda par de Losers: Perdedores de Ronda N Winners vs Ganadores de Ronda (N-1) Losers
            $matches_winners_perdedores = ceil($matches_ronda1 / pow(2, $ronda_winners - 1));
            $matches_losers_ganadores = ceil($matches_losers_actual / 2);
            
            // El número de matches es el máximo entre perdedores de Winners y ganadores de Losers
            $matches_losers_ronda = max($matches_winners_perdedores, $matches_losers_ganadores);
            
            if ($matches_losers_ronda > 0) {
                for ($i = 1; $i <= $matches_losers_ronda; $i++) {
                    $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                 VALUES ($torneo_id, 'losers', $ronda_losers, $i, NULL, NULL)");
                }
                $matches_losers_actual = $matches_losers_ronda;
            }
            
            $ronda_losers++;
            $ronda_winners++;
            
            // Ronda impar de Losers: Solo ganadores de la ronda anterior (si no es la última)
            if ($ronda_losers <= ($rondas_necesarias - 1) * 2) {
                $matches_losers_ronda_impar = ceil($matches_losers_actual / 2);
                if ($matches_losers_ronda_impar > 0) {
                    for ($i = 1; $i <= $matches_losers_ronda_impar; $i++) {
                        $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                                     VALUES ($torneo_id, 'losers', $ronda_losers, $i, NULL, NULL)");
                    }
                    $matches_losers_actual = $matches_losers_ronda_impar;
                    $ronda_losers++;
                }
            }
        }
        
        // Crear Gran Final vacía
        $conn->query("INSERT INTO matches (torneo_id, bracket_tipo, ronda, numero_match, equipo1_id, equipo2_id) 
                     VALUES ($torneo_id, 'grand_final', 1, 1, NULL, NULL)");
    }
    }
    
    // Recargar matches
    $matches_query = $conn->query("SELECT m.*, 
                                   e1.nombre_equipo as equipo1_nombre, 
                                   e2.nombre_equipo as equipo2_nombre
                                   FROM matches m
                                   LEFT JOIN equipos_torneo e1 ON m.equipo1_id = e1.id
                                   LEFT JOIN equipos_torneo e2 ON m.equipo2_id = e2.id
                                   WHERE m.torneo_id = $torneo_id
                                   ORDER BY m.bracket_tipo, m.ronda, m.numero_match");
    $matches = [];
    if ($matches_query) {
        while ($row = $matches_query->fetch_assoc()) {
            $matches[] = $row;
        }
    }
}

// Organizar matches por ronda
$matches_por_ronda = [];
foreach ($matches as $match) {
    $bracket = $match['bracket_tipo'];
    $ronda = $match['ronda'];
    if (!isset($matches_por_ronda[$bracket])) {
        $matches_por_ronda[$bracket] = [];
    }
    if (!isset($matches_por_ronda[$bracket][$ronda])) {
        $matches_por_ronda[$bracket][$ronda] = [];
    }
    $matches_por_ronda[$bracket][$ronda][] = $match;
}

// Función para obtener equipos asignados (excluyendo un match específico)
function obtenerEquiposAsignados($matches, $excluir_match_id = null) {
    $asignados = [];
    foreach ($matches as $match) {
        if ($excluir_match_id && $match['id'] == $excluir_match_id) {
            continue; // Excluir equipos del match que se está editando
        }
        if ($match['equipo1_id']) {
            $asignados[] = $match['equipo1_id'];
        }
        if ($match['equipo2_id']) {
            $asignados[] = $match['equipo2_id'];
        }
    }
    return $asignados;
}

// Obtener equipos ya asignados (para mostrar en el modal)
$equipos_asignados = obtenerEquiposAsignados($matches);

// Determinar formato
$modalidad_actual = isset($torneo['modalidad']) ? $torneo['modalidad'] : 'Single Elimination';
$formato_texto = ($modalidad_actual === 'Single Elimination') ? 'Single Elimination' : 'Double Elimination';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Brackets - <?php echo htmlspecialchars($torneo['nombre_torneo']); ?> - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="brackets.css" />
  <link rel="stylesheet" href="animations.css" />
  <style>
    .tournament-info {
      background: rgba(30, 30, 30, 0.8);
      border-radius: 12px;
      padding: 1.5rem;
      margin: 2rem auto;
      max-width: 1200px;
      border: 1px solid rgba(212, 175, 55, 0.3);
    }
    
    .tournament-info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    
    .tournament-info-item {
      color: #d0d0d0;
      font-size: 0.95rem;
    }

    .tournament-info-item strong {
      color: #d4af37;
    }

    .organizer-badge {
      background: rgba(40, 40, 40, 0.9);
      padding: 0.5rem 1rem;
      border-radius: 20px;
      color: #ffd277;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .bracket-container {
      max-width: 100%;
      margin: 2rem auto;
      padding: 0 1rem;
      overflow-x: hidden;
      cursor: grab;
      user-select: none;
    }
    
    .bracket-container:active {
      cursor: grabbing;
    }

    /* ESTILOS GENERALES DEL CONTENEDOR */
    .tournament-bracket {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 20px;
      background-color: transparent;
      color: #fff;
      overflow-x: auto;
      overflow-y: hidden;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      cursor: grab;
    }

    .tournament-bracket:active {
      cursor: grabbing;
    }

    .tournament-bracket::-webkit-scrollbar {
      height: 8px;
    }

    .tournament-bracket::-webkit-scrollbar-track {
      background: rgba(20, 20, 20, 0.5);
      border-radius: 4px;
    }
    
    .tournament-bracket::-webkit-scrollbar-thumb {
      background: rgba(212, 175, 55, 0.5);
      border-radius: 4px;
    }

    .tournament-bracket::-webkit-scrollbar-thumb:hover {
      background: rgba(212, 175, 55, 0.7);
    }

    /* ESTILOS DE LA RONDA */
    .round {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      margin: 0 30px;
      position: relative;
      padding-top: 2.5rem;
      min-height: 300px;
    }

    .round-header {
      background: rgba(40, 40, 40, 0.9);
      padding: 0.75rem;
      border-radius: 8px;
      text-align: center;
      color: #d4af37;
      font-weight: bold;
      border: 1px solid rgba(212, 175, 55, 0.3);
      margin-bottom: 1rem;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      box-sizing: border-box;
    }

    /* ESTILOS DEL ENFRENTAMIENTO (Caja donde va cada equipo) */
    .matchup {
      display: flex;
      flex-direction: column;
      margin-bottom: 40px;
      position: relative;
      cursor: pointer;
      transition: all 0.3s ease;
      align-self: flex-start;
    }
    
    .matchup:last-child {
      margin-bottom: 0;
    }

    .matchup:hover {
      transform: translateY(-2px);
    }

    /* Asegurar que los matchups estén alineados correctamente */
    .round .matchup {
      margin-left: 0;
      margin-right: 0;
    }

    /* Centrar verticalmente los matchups en la ronda */
    .round:first-child .matchup:first-child {
      margin-top: 0;
    }

    /* ESTILOS DE CADA LUGAR DE EQUIPO (Slot) */
    .team-slot {
      width: 200px;
      min-height: 35px;
      line-height: 35px;
      padding: 0 10px;
      margin: 1px 0;
      background-color: rgba(20, 20, 20, 0.8);
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 4px;
      font-size: 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .team-slot.empty {
      color: rgba(150, 150, 150, 0.4);
      border-color: rgba(212, 175, 55, 0.15);
    }

    .team-slot.empty .team-name {
      color: rgba(150, 150, 150, 0.4);
    }

    .team-slot.empty .team-score {
      color: rgba(150, 150, 150, 0.3);
    }

    /* CONECTOR HORIZONTAL DERECHO (Sale de cada enfrentamiento) */
    .matchup::after {
      content: '';
      display: block;
      position: absolute;
      right: -30px;
      top: calc(50% - 1px);
      width: 30px;
      height: 0;
      border-top: 2px solid rgba(212, 175, 55, 0.6);
      z-index: 1;
    }

    /* ESPACIADOR: Dibuja la línea vertical que une los partidos */
    .spacer {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: 30px;
      min-width: 30px;
      margin: 0;
      flex-shrink: 0;
      align-self: center;
    }

    .spacer::before {
      content: '';
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 100%;
      border-left: 2px solid rgba(212, 175, 55, 0.6);
      top: 0;
    }
    
    /* CONECTOR DE ENTRADA A LA SIGUIENTE RONDA */
    .matchup-second-round::before,
    .round:not(:first-child) .matchup::before {
      content: '';
      display: block;
      position: absolute;
      left: -30px;
      top: calc(50% - 1px);
      width: 30px;
      height: 0;
      border-top: 2px solid rgba(212, 175, 55, 0.6);
      z-index: 1;
    }

    /* Centrar verticalmente los matchups en la ronda */
    .round:first-child .matchup:first-child {
      margin-top: 0;
    }

    .team-name {
      color: #fff;
      font-size: 0.85rem;
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .team-seed {
      color: #999;
      font-size: 0.75rem;
      margin-right: 0.5rem;
    }

    .team-score {
      color: #d0d0d0;
      font-weight: bold;
      font-size: 0.9rem;
      min-width: 30px;
      text-align: right;
    }

    .team-score.winner {
      color: #ffd277;
    }

    .team-score.loser {
      color: #999;
    }



    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: rgba(30, 30, 30, 0.98);
      padding: 2rem;
      border-radius: 10px;
      border: 2px solid #d4af37;
      max-width: 500px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
    }
    
    .modal-content h2 {
      color: #d4af37;
      margin-top: 0;
      margin-bottom: 1.5rem;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-group label {
      display: block;
      color: #d0d0d0;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    
    .form-group input {
      width: 100%;
      padding: 0.8rem;
      background: rgba(40, 40, 40, 0.9);
      border: 2px solid #555;
      border-radius: 5px;
      color: #fff;
      font-size: 1rem;
      box-sizing: border-box;
    }
    
    .form-group input:focus {
      outline: none;
      border-color: #d4af37;
    }
    
    .btn-modal {
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      margin-right: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .btn-modal-primary {
      background: linear-gradient(135deg, #d4af37, #c09b2d);
      color: #000;
    }
    
    .btn-modal-danger {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      color: #fff;
    }
    
    .btn-modal:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    /* From Uiverse.io by vinodjangid07 */
    .Btn {
      width: 200px;
      height: 40px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(to right,#77530a,#ffd277,#77530a,#77530a,#ffd277,#77530a);
      background-size: 250%;
      background-position: left;
      color: #ffd277;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition-duration: 1s;
      overflow: hidden;
      margin: 1rem auto;
    }

    .Btn::before {
      position: absolute;
      content: "CREAR NUEVO MATCH";
      color: #ffd277;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 97%;
      height: 90%;
      border-radius: 8px;
      transition-duration: 1s;
      background-color: rgba(0, 0, 0, 0.842);
      background-size: 200%;
      font-weight: bold;
      font-size: 0.9rem;
      }

    .Btn:hover {
      background-position: right;
      transition-duration: 1s;
    }

    .Btn:hover::before {
      background-position: right;
      transition-duration: 1s;
    }
    
    .Btn:active {
      transform: scale(0.95);
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
      <a href="salon_fama.php">SALÓN DE LA FAMA</a>
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
      <h1><?php echo htmlspecialchars($torneo['nombre_torneo']); ?></h1>
    </section>

    <div class="tournament-info">
      <div class="tournament-info-row">
        <div class="tournament-info-item">
          <strong>Equipos:</strong> <?php echo count($equipos_torneo); ?>
      </div>
        <div class="tournament-info-item">
          <strong>Formato:</strong> <?php echo htmlspecialchars($formato_texto); ?>
          </div>
        <div class="organizer-badge">
          Organizado por <?php echo htmlspecialchars($torneo['creador_nombre'] ?? 'Usuario'); ?>
        </div>
        </div>
      </div>
      
    <?php if (isset($error_equipos_insuficientes) && $error_equipos_insuficientes): ?>
    <div style="max-width: 800px; margin: 2rem auto; padding: 2rem; background: rgba(231, 76, 60, 0.2); border: 2px solid #e74c3c; border-radius: 12px; text-align: center;">
      <h2 style="color: #e74c3c; margin-bottom: 1rem;">⚠️ No se pueden generar los brackets</h2>
      <?php if (isset($error_equipos_impares) && $error_equipos_impares): ?>
      <p style="color: #fff; font-size: 1.1rem; margin-bottom: 1rem;">
        El número de equipos debe ser <strong style="color: #ffd277;">par</strong> para crear enfrentamientos 1 vs 1.
      </p>
      <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1.5rem;">
        Actualmente tienes <strong style="color: #ffd277;"><?php echo $total_equipos; ?> equipos</strong> (número impar). 
        Necesitas <strong style="color: #ffd277;">agregar 1 equipo más</strong> o eliminar 1 equipo para tener un número par.
      </p>
      <?php else: ?>
      <p style="color: #fff; font-size: 1.1rem; margin-bottom: 1rem;">
        Se requieren <strong style="color: #ffd277;">mínimo 12 equipos</strong> para generar los brackets del torneo.
      </p>
      <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1.5rem;">
        Actualmente tienes <strong style="color: #ffd277;"><?php echo $total_equipos; ?> equipos</strong>. 
        Necesitas agregar <strong style="color: #ffd277;"><?php echo $minimo_equipos - $total_equipos; ?> equipos más</strong>.
      </p>
      <?php endif; ?>
      <a href="crear_torneo.php?torneo_id=<?php echo $torneo_id; ?>" style="display: inline-block; padding: 1rem 2rem; background: linear-gradient(135deg, #d4af37, #c09b2d); color: #000; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.3s ease;">
        ➕ Agregar Más Equipos
      </a>
    </div>
    <?php else: ?>
    <div style="text-align: center; margin-bottom: 2rem;">
      <button onclick="abrirModalCrearMatch()" class="Btn"></button>
    </div>
    
    <?php if ($modalidad_actual === 'Double Elimination'): ?>
    <h2 style="text-align: center; color: #d4af37; margin-bottom: 2rem; font-size: 2rem;">Winners Bracket</h2>
    <?php endif; ?>
    
    <div class="bracket-container">
      <div class="tournament-bracket" id="tournament-bracket">
        <?php
        // Calcular número de rondas necesarias según cantidad de equipos
        $total_equipos = count($equipos_torneo);
        $matches_ronda1 = 0;
        if (isset($matches_por_ronda['winners'][1])) {
            $matches_ronda1 = count($matches_por_ronda['winners'][1]);
        } elseif ($total_equipos > 0) {
            $matches_ronda1 = ceil($total_equipos / 2);
        }
        
        // Renderizar brackets según modalidad
        $max_rondas = 0;
        if (isset($matches_por_ronda['winners'])) {
            $max_rondas = max(array_keys($matches_por_ronda['winners']));
        }
        
        // Calcular rondas necesarias basado en matches de ronda 1
        $rondas_necesarias = 0;
        if ($matches_ronda1 > 0) {
            $rondas_necesarias = ceil(log($matches_ronda1, 2)) + 1;
        }
        
        // Usar el mayor entre rondas existentes y rondas necesarias
        $rondas_a_mostrar = max($max_rondas, $rondas_necesarias, 3);
        
        for ($ronda = 1; $ronda <= $rondas_a_mostrar; $ronda++):
            $ronda_matches = $matches_por_ronda['winners'][$ronda] ?? [];
            $is_first_round = ($ronda == 1);
            $is_last_round = ($ronda == $rondas_a_mostrar);
        ?>
        <?php if (!$is_first_round): ?>
        <div class="spacer"></div>
        <?php endif; ?>
        
        <div class="round" data-ronda="<?php echo $ronda; ?>">
          <div class="round-header">Ronda <?php echo $ronda; ?></div>
          <?php if (count($ronda_matches) > 0): ?>
            <?php foreach ($ronda_matches as $match): ?>
              <div class="matchup <?php echo ($ronda > 1) ? 'matchup-second-round' : ''; ?>" 
                   data-match-id="<?php echo $match['id']; ?>" 
                   onclick="abrirModalEditar(<?php echo $match['id']; ?>, '<?php echo htmlspecialchars($match['equipo1_nombre'] ?? 'TBD'); ?>', '<?php echo htmlspecialchars($match['equipo2_nombre'] ?? 'TBD'); ?>', <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>, <?php echo $match['puntos_equipo1'] ?? 0; ?>, <?php echo $match['puntos_equipo2'] ?? 0; ?>)" 
                   title="Haz clic para editar resultado">
                <div class="team-slot <?php echo (!$match['equipo1_id']) ? 'empty' : ''; ?>">
                  <span class="team-name">
                    <span class="team-seed"><?php echo $match['numero_match'] * 2 - 1; ?></span>
                    <?php echo $match['equipo1_id'] ? htmlspecialchars($match['equipo1_nombre']) : ''; ?>
                  </span>
                  <span class="team-score <?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] > $match['puntos_equipo2']) ? 'winner' : (($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] < $match['puntos_equipo2']) ? 'loser' : ''); ?>">
                    <?php echo $match['puntos_equipo1'] !== null ? $match['puntos_equipo1'] : '-'; ?>
                  </span>
          </div>
                <div class="team-slot <?php echo (!$match['equipo2_id']) ? 'empty' : ''; ?>">
                  <span class="team-name">
                    <span class="team-seed"><?php echo $match['numero_match'] * 2; ?></span>
                    <?php echo $match['equipo2_id'] ? htmlspecialchars($match['equipo2_nombre']) : ''; ?>
                  </span>
                  <span class="team-score <?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] > $match['puntos_equipo1']) ? 'winner' : (($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] < $match['puntos_equipo1']) ? 'loser' : ''); ?>">
                    <?php echo $match['puntos_equipo2'] !== null ? $match['puntos_equipo2'] : '-'; ?>
                  </span>
          </div>
          </div>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>
        <?php endfor; ?>
      </div>
    </div>

    <?php if ($modalidad_actual === 'Double Elimination'): ?>
    <!-- Losers Bracket (Bracket B) -->
    <div style="margin-top: 4rem; padding-top: 2rem; border-top: 3px solid rgba(212, 175, 55, 0.5);">
      <h2 style="text-align: center; color: #d4af37; margin-bottom: 2rem; font-size: 2rem;">Losers Bracket</h2>
      <div class="bracket-container">
        <div class="tournament-bracket" id="tournament-bracket-losers">
          <?php
          // Calcular rondas del Losers Bracket
          $max_rondas_losers = 0;
          if (isset($matches_por_ronda['losers'])) {
              $max_rondas_losers = max(array_keys($matches_por_ronda['losers']));
          }
          
          // Calcular rondas necesarias para Losers
          $rondas_losers_necesarias = 0;
          if ($matches_ronda1 > 0) {
              // Losers tiene aproximadamente el doble de rondas que Winners
              $rondas_losers_necesarias = ($rondas_necesarias - 1) * 2;
          }
          
          $rondas_losers_a_mostrar = max($max_rondas_losers, $rondas_losers_necesarias, 2);
          
          for ($ronda = 1; $ronda <= $rondas_losers_a_mostrar; $ronda++):
              $ronda_matches = $matches_por_ronda['losers'][$ronda] ?? [];
              $is_first_round_losers = ($ronda == 1);
          ?>
          <?php if (!$is_first_round_losers): ?>
          <div class="spacer"></div>
          <?php endif; ?>
          
          <div class="round" data-ronda="<?php echo $ronda; ?>" data-bracket="losers">
            <div class="round-header">LB Ronda <?php echo $ronda; ?></div>
            <?php if (count($ronda_matches) > 0): ?>
              <?php foreach ($ronda_matches as $match): ?>
                <div class="matchup <?php echo ($ronda > 1) ? 'matchup-second-round' : ''; ?>" 
                     data-match-id="<?php echo $match['id']; ?>" 
                     onclick="abrirModalEditar(<?php echo $match['id']; ?>, '<?php echo htmlspecialchars($match['equipo1_nombre'] ?? ''); ?>', '<?php echo htmlspecialchars($match['equipo2_nombre'] ?? ''); ?>', <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>, <?php echo $match['puntos_equipo1'] ?? 0; ?>, <?php echo $match['puntos_equipo2'] ?? 0; ?>)" 
                     title="Haz clic para editar resultado">
                  <div class="team-slot <?php echo (!$match['equipo1_id']) ? 'empty' : ''; ?>">
                    <span class="team-name">
                      <span class="team-seed"><?php echo $match['numero_match'] * 2 - 1; ?></span>
                      <?php echo $match['equipo1_id'] ? htmlspecialchars($match['equipo1_nombre']) : ''; ?>
                    </span>
                    <span class="team-score <?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] > $match['puntos_equipo2']) ? 'winner' : (($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] < $match['puntos_equipo2']) ? 'loser' : ''); ?>">
                      <?php echo $match['puntos_equipo1'] !== null ? $match['puntos_equipo1'] : '-'; ?>
                    </span>
                  </div>
                  <div class="team-slot <?php echo (!$match['equipo2_id']) ? 'empty' : ''; ?>">
                    <span class="team-name">
                      <span class="team-seed"><?php echo $match['numero_match'] * 2; ?></span>
                      <?php echo $match['equipo2_id'] ? htmlspecialchars($match['equipo2_nombre']) : ''; ?>
                    </span>
                    <span class="team-score <?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] > $match['puntos_equipo1']) ? 'winner' : (($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] < $match['puntos_equipo1']) ? 'loser' : ''); ?>">
                      <?php echo $match['puntos_equipo2'] !== null ? $match['puntos_equipo2'] : '-'; ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <?php endfor; ?>
        </div>
      </div>
    </div>

    <!-- Gran Final -->
    <?php if (isset($matches_por_ronda['grand_final'][1])): 
        $gf_matches = $matches_por_ronda['grand_final'][1];
    ?>
    <div style="margin-top: 4rem; padding-top: 2rem; border-top: 3px solid rgba(212, 175, 55, 0.5);">
      <h2 style="text-align: center; color: #d4af37; margin-bottom: 2rem; font-size: 2rem;">🏆 Gran Final</h2>
      <div class="bracket-container">
        <div class="tournament-bracket" style="justify-content: center;">
          <div class="round">
            <div class="round-header">Gran Final</div>
            <?php foreach ($gf_matches as $match): ?>
              <div class="matchup" 
                   data-match-id="<?php echo $match['id']; ?>" 
                   onclick="abrirModalEditar(<?php echo $match['id']; ?>, '<?php echo htmlspecialchars($match['equipo1_nombre'] ?? ''); ?>', '<?php echo htmlspecialchars($match['equipo2_nombre'] ?? ''); ?>', <?php echo $match['equipo1_id'] ?? 'null'; ?>, <?php echo $match['equipo2_id'] ?? 'null'; ?>, <?php echo $match['puntos_equipo1'] ?? 0; ?>, <?php echo $match['puntos_equipo2'] ?? 0; ?>)" 
                   title="Haz clic para editar resultado">
                <div class="team-slot <?php echo (!$match['equipo1_id']) ? 'empty' : ''; ?>">
                  <span class="team-name">
                    Ganador Winners
                    <?php echo $match['equipo1_id'] ? ' - ' . htmlspecialchars($match['equipo1_nombre']) : ''; ?>
                  </span>
                  <span class="team-score <?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] > $match['puntos_equipo2']) ? 'winner' : (($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo1'] < $match['puntos_equipo2']) ? 'loser' : ''); ?>">
                    <?php echo $match['puntos_equipo1'] !== null ? $match['puntos_equipo1'] : '-'; ?>
                  </span>
                </div>
                <div class="team-slot <?php echo (!$match['equipo2_id']) ? 'empty' : ''; ?>">
                  <span class="team-name">
                    Ganador Losers
                    <?php echo $match['equipo2_id'] ? ' - ' . htmlspecialchars($match['equipo2_nombre']) : ''; ?>
                  </span>
                  <span class="team-score <?php echo ($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] > $match['puntos_equipo1']) ? 'winner' : (($match['puntos_equipo1'] !== null && $match['puntos_equipo2'] !== null && $match['puntos_equipo2'] < $match['puntos_equipo1']) ? 'loser' : ''); ?>">
                    <?php echo $match['puntos_equipo2'] !== null ? $match['puntos_equipo2'] : '-'; ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <?php endif; // Cierre del else de error_equipos_insuficientes ?>
  </main>

  <!-- Modal Crear Match -->
  <div id="modalCrearMatch" class="modal">
    <div class="modal-content">
      <h2>➕ Crear Nuevo Match</h2>
      <form method="POST">
        <input type="hidden" name="accion" value="crear_match">
        <div class="form-group">
          <label>Tipo de Bracket</label>
          <select name="bracket_tipo" required style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
            <option value="winners">Winners Bracket</option>
            <?php if ($modalidad_actual === 'Double Elimination'): ?>
            <option value="losers">Losers Bracket</option>
            <option value="grand_final">Gran Final</option>
            <?php endif; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Ronda</label>
          <input type="number" name="ronda" required min="1" max="10" value="1">
        </div>
        <div class="form-group">
          <label>Equipo 1 (opcional)</label>
          <select name="equipo1_id" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
            <option value="">-- TBD --</option>
            <?php foreach ($equipos_torneo as $equipo): ?>
              <option value="<?php echo $equipo['id']; ?>"><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Equipo 2 (opcional)</label>
          <select name="equipo2_id" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff;">
            <option value="">-- TBD --</option>
            <?php foreach ($equipos_torneo as $equipo): ?>
              <option value="<?php echo $equipo['id']; ?>"><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
          <button type="submit" class="btn-modal btn-modal-primary">💾 Crear Match</button>
          <button type="button" onclick="cerrarModal('modalCrearMatch')" class="btn-modal btn-modal-danger">❌ Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar Resultado -->
  <div id="modalEditarResultado" class="modal">
    <div class="modal-content">
      <h2>📊 Editar Match</h2>
      <form method="POST">
        <input type="hidden" name="accion" value="actualizar_resultado">
        <input type="hidden" name="match_id" id="edit_match_id">
        <input type="hidden" id="edit_equipo1_id_actual" value="">
        <input type="hidden" id="edit_equipo2_id_actual" value="">
        
        <div class="form-group">
          <label>Equipo 1</label>
          <select name="equipo1_id" id="edit_equipo1_id" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff; box-sizing: border-box;">
            <option value="">-- Sin equipo --</option>
            <?php 
            // Obtener equipos disponibles para este match específico
            $match_id_js = '0';
            foreach ($equipos_torneo as $equipo): 
              // Verificar si el equipo ya está asignado en otro match (excepto el actual)
              $ya_asignado = in_array($equipo['id'], $equipos_asignados);
            ?>
              <option value="<?php echo $equipo['id']; ?>" <?php echo $ya_asignado ? 'disabled' : ''; ?>>
                <?php echo htmlspecialchars($equipo['nombre_equipo']); ?>
                <?php if ($ya_asignado): ?> (Ya asignado)<?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label>Equipo 2</label>
          <select name="equipo2_id" id="edit_equipo2_id" style="width: 100%; padding: 0.8rem; background: rgba(40, 40, 40, 0.9); border: 2px solid #555; border-radius: 5px; color: #fff; box-sizing: border-box;">
            <option value="">-- Sin equipo --</option>
            <?php foreach ($equipos_torneo as $equipo): 
              $ya_asignado = in_array($equipo['id'], $equipos_asignados);
            ?>
              <option value="<?php echo $equipo['id']; ?>" <?php echo $ya_asignado ? 'disabled' : ''; ?>>
                <?php echo htmlspecialchars($equipo['nombre_equipo']); ?>
                <?php if ($ya_asignado): ?> (Ya asignado)<?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label>Puntos Equipo 1</label>
          <input type="number" name="puntos_equipo1" id="edit_puntos1" min="0" value="0">
        </div>
        
        <div class="form-group">
          <label>Puntos Equipo 2</label>
          <input type="number" name="puntos_equipo2" id="edit_puntos2" min="0" value="0">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
          <button type="submit" class="btn-modal btn-modal-primary" id="btn_guardar_match">💾 Guardar Cambios</button>
          <button type="button" onclick="cerrarModal('modalEditarResultado')" class="btn-modal btn-modal-danger">❌ Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function abrirModalCrearMatch() {
      document.getElementById('modalCrearMatch').classList.add('active');
    }

    const equiposDisponibles = <?php echo json_encode($equipos_torneo); ?>;
    const equiposAsignados = <?php echo json_encode($equipos_asignados); ?>;
    const todosLosMatches = <?php echo json_encode($matches); ?>;
    let matchActualEditando = null;

    function abrirModalEditar(matchId, equipo1Nombre, equipo2Nombre, equipo1Id, equipo2Id, puntos1, puntos2) {
      matchActualEditando = matchId;
      document.getElementById('edit_match_id').value = matchId;
      document.getElementById('edit_equipo1_id_actual').value = equipo1Id || '';
      document.getElementById('edit_equipo2_id_actual').value = equipo2Id || '';
      
      // Establecer equipos seleccionados
      document.getElementById('edit_equipo1_id').value = equipo1Id || '';
      document.getElementById('edit_equipo2_id').value = equipo2Id || '';
      
      // Establecer puntajes
      document.getElementById('edit_puntos1').value = puntos1 || 0;
      document.getElementById('edit_puntos2').value = puntos2 || 0;
      
      // Actualizar opciones disponibles (habilitar equipos del match actual)
      actualizarOpcionesEquipos(matchId, equipo1Id, equipo2Id);
      
      // Cambiar texto del botón según si hay equipos asignados
      const btnGuardar = document.getElementById('btn_guardar_match');
      if (equipo1Id || equipo2Id) {
        btnGuardar.textContent = '💾 Guardar Cambios';
      } else {
        btnGuardar.textContent = '💾 Guardar Cambios';
      }
      
      document.getElementById('modalEditarResultado').classList.add('active');
    }

    function actualizarOpcionesEquipos(matchId, equipo1Actual, equipo2Actual) {
      // Obtener equipos asignados excluyendo el match actual
      const equiposAsignadosFiltrados = [];
      todosLosMatches.forEach(match => {
        if (match.id != matchId) {
          if (match.equipo1_id) equiposAsignadosFiltrados.push(match.equipo1_id);
          if (match.equipo2_id) equiposAsignadosFiltrados.push(match.equipo2_id);
        }
      });
      
      // Actualizar select equipo 1
      const select1 = document.getElementById('edit_equipo1_id');
      Array.from(select1.options).forEach(option => {
        if (option.value) {
          const equipoId = parseInt(option.value);
          // Habilitar si es el equipo actual o si no está asignado
          if (equipoId == equipo1Actual || equipoId == equipo2Actual) {
            option.disabled = false;
            option.textContent = option.textContent.replace(' (Ya asignado)', '');
          } else {
            option.disabled = equiposAsignadosFiltrados.includes(equipoId);
            if (option.disabled && !option.textContent.includes('(Ya asignado)')) {
              option.textContent += ' (Ya asignado)';
            } else if (!option.disabled) {
              option.textContent = option.textContent.replace(' (Ya asignado)', '');
            }
          }
        }
      });
      
      // Actualizar select equipo 2
      const select2 = document.getElementById('edit_equipo2_id');
      Array.from(select2.options).forEach(option => {
        if (option.value) {
          const equipoId = parseInt(option.value);
          // Habilitar si es el equipo actual o si no está asignado
          if (equipoId == equipo1Actual || equipoId == equipo2Actual) {
            option.disabled = false;
            option.textContent = option.textContent.replace(' (Ya asignado)', '');
          } else {
            option.disabled = equiposAsignadosFiltrados.includes(equipoId);
            if (option.disabled && !option.textContent.includes('(Ya asignado)')) {
              option.textContent += ' (Ya asignado)';
            } else if (!option.disabled) {
              option.textContent = option.textContent.replace(' (Ya asignado)', '');
            }
          }
        }
      });
    }

    function cerrarModal(modalId) {
      document.getElementById(modalId).classList.remove('active');
    }

    // Cerrar modal al hacer clic fuera
    document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('click', function(e) {
        if (e.target === this) {
          cerrarModal(this.id);
        }
      });
    });

    // Funcionalidad de arrastre horizontal
    let isDown = false;
    let startX;
    let scrollLeft;
    let hasMoved = false;
    const bracketContainer = document.querySelector('.bracket-container');
    const tournamentBracket = document.getElementById('tournament-bracket');

    if (bracketContainer && tournamentBracket) {
      bracketContainer.addEventListener('mousedown', (e) => {
        // No activar drag si se hace clic en un matchup
        if (e.target.closest('.matchup')) {
          return;
        }
        isDown = true;
        hasMoved = false;
        bracketContainer.style.cursor = 'grabbing';
        startX = e.pageX - bracketContainer.offsetLeft;
        scrollLeft = tournamentBracket.scrollLeft;
      });

      bracketContainer.addEventListener('mouseleave', () => {
        isDown = false;
        bracketContainer.style.cursor = 'grab';
      });

      bracketContainer.addEventListener('mouseup', () => {
        isDown = false;
        bracketContainer.style.cursor = 'grab';
      });

      bracketContainer.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        hasMoved = true;
        e.preventDefault();
        const x = e.pageX - bracketContainer.offsetLeft;
        const walk = (x - startX) * 2; // Velocidad de desplazamiento
        tournamentBracket.scrollLeft = scrollLeft - walk;
      });

      // Prevenir que el click en matchup se active si hubo movimiento
      document.querySelectorAll('.matchup').forEach(matchup => {
        matchup.addEventListener('click', function(e) {
          if (hasMoved) {
            e.preventDefault();
            e.stopPropagation();
            hasMoved = false;
            return false;
          }
        });
      });
    }

    // Ajustar altura de spacers y centrar las llaves
    function ajustarSpacers() {
      // Ajustar spacers para Winners Bracket
      const bracketWinners = document.getElementById('tournament-bracket');
      if (bracketWinners) {
        ajustarSpacersEnBracket(bracketWinners);
      }
      
      // Ajustar spacers para Losers Bracket
      const bracketLosers = document.getElementById('tournament-bracket-losers');
      if (bracketLosers) {
        ajustarSpacersEnBracket(bracketLosers);
      }
    }
    
    function ajustarSpacersEnBracket(bracketElement) {
      const rounds = bracketElement.querySelectorAll('.round');
      rounds.forEach((round, roundIndex) => {
        if (roundIndex < rounds.length - 1) {
          const nextRound = rounds[roundIndex + 1];
          const spacer = round.nextElementSibling;
          
          if (spacer && spacer.classList.contains('spacer')) {
            // Calcular altura total de matchups en cada ronda (sin el header)
            const matchupsRound = round.querySelectorAll('.matchup');
            const matchupsNextRound = nextRound.querySelectorAll('.matchup');
            
            let heightRound = 0;
            let heightNextRound = 0;
            
            matchupsRound.forEach((m, idx) => {
              heightRound += m.offsetHeight;
              if (idx < matchupsRound.length - 1) {
                heightRound += 40; // margin-bottom excepto el último
              }
            });
            
            matchupsNextRound.forEach((m, idx) => {
              heightNextRound += m.offsetHeight;
              if (idx < matchupsNextRound.length - 1) {
                heightNextRound += 40;
              }
            });
            
            // Usar la altura máxima
            const maxHeight = Math.max(heightRound, heightNextRound, 200);
            spacer.style.height = maxHeight + 'px';
            spacer.style.alignSelf = 'center';
          }
        }
      });
    }

    // Ejecutar al cargar y después de cambios
    window.addEventListener('load', () => {
      setTimeout(ajustarSpacers, 100);
    });
    window.addEventListener('resize', ajustarSpacers);
    
    // Observar cambios en el DOM para ajustar spacers
    if (tournamentBracket) {
      const observer = new MutationObserver(() => {
        setTimeout(ajustarSpacers, 50);
      });
      observer.observe(tournamentBracket, { childList: true, subtree: true, attributes: true });
    }
  </script>
  <script src="scripts.js"></script>
</body>
</html>
<?php
$conn->close();
?>
