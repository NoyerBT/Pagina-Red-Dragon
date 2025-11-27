<?php
session_start();
require_once 'cnt/conexion.php';

$userHasAccount = isset($_SESSION['usuario']);
$plan_activo = false;
$dias_restantes = null;

if ($userHasAccount) {
    $stmt = $conn->prepare("SELECT estado, fecha_expiracion FROM usuarios WHERE usuario = ? LIMIT 1");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($user['estado'] === 'activo' && !empty($user['fecha_expiracion'])) {
            try {
                $hoy = new DateTime('today');
                $expiracion = new DateTime($user['fecha_expiracion']);
                if ($expiracion >= $hoy) {
                    $plan_activo = true;
                    $dias_restantes = (int)$hoy->diff($expiracion)->format('%a');
                }
            } catch (Exception $e) {
                error_log('Error evaluando expiración de plan: ' . $e->getMessage());
            }
        }
    }

    $stmt->close();
}

if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Organizar mi torneo - Red Dragons Cup</title>
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
      <?php if ($userHasAccount): ?>
        <a href="torneo.php">TORNEO</a>
      <?php endif; ?>
      <a href="anticheats.php">ANTICHEATS RDC</a>
      <a href="contacto.php">CONTACTO</a>
      <?php if ($userHasAccount): ?>
        <a href="dashboard.php">MI CUENTA</a>
        <a href="logout.php">CERRAR SESIÓN</a>
      <?php else: ?>
        <a href="login.php">INICIAR SESIÓN</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="hero hero--organizador">
    <section class="hero-content">
      <p class="hero-eyebrow">Suite táctica RDC</p>
      <h1>Organiza tu torneo</h1>
      <img src="Img/logo hacia la izquierda.png" alt="Logo Red Dragons Cup" class="hero-logo" />
      <?php if ($userHasAccount && $plan_activo): ?>
        <p class="subtitle">Genera brackets, matches y lleva el control de tus llaves en minutos. <span class="text-success">Plan Anticheat activo<?php if ($dias_restantes !== null) { echo ' • ' . $dias_restantes . ' días restantes'; } ?></span></p>
      <?php elseif ($userHasAccount): ?>
        <p class="subtitle">Activa tu plan Anticheat para desbloquear el generador de llaves y gestionar tus encuentros privados.</p>
      <?php else: ?>
        <p class="subtitle">Esta herramienta está disponible únicamente para usuarios registrados con un plan Anticheat vigente.</p>
      <?php endif; ?>

      <div class="hero-buttons">
        <?php if (!$userHasAccount): ?>
          <a href="registro.php" class="btn primary">Crear cuenta</a>
          <a href="login.php" class="btn secondary">Iniciar sesión</a>
        <?php elseif (!$plan_activo): ?>
          <a href="anticheats.php" class="btn primary">Ver plan Anticheat</a>
          <a href="dashboard.php" class="btn secondary">Ir a mi cuenta</a>
        <?php else: ?>
          <a href="#organizador" class="btn primary">Ir al organizador</a>
          <a href="dashboard.php" class="btn secondary">Dashboard</a>
        <?php endif; ?>
      </div>

      <div class="info-tags">
        <span>Matches instantáneos</span>
        <span>Brackets personalizables</span>
        <span>Exportación rápida</span>
      </div>
    </section>
  </main>

  <section class="section organizer-steps">
    <h2>Capacidades del organizador</h2>
    <div class="organizer-steps-grid">
      <article class="organizer-card">
        <span class="organizer-card__icon">🧩</span>
        <h3>Diseña tus llaves</h3>
        <p>Agrega equipos, define seeds y crea llaves con un solo clic, ideal para scrims o copas relámpago.</p>
      </article>
      <article class="organizer-card">
        <span class="organizer-card__icon">🎮</span>
        <h3>Controla cada match</h3>
        <p>Registra puntajes, marca ganadores y reinicia partidas sin necesidad de hojas externas.</p>
      </article>
      <article class="organizer-card">
        <span class="organizer-card__icon">🚀</span>
        <h3>Exporta en segundos</h3>
        <p>Copia la planificación completa para compartirla con tu staff o integrarla en otras plataformas.</p>
      </article>
      <article class="organizer-card">
        <span class="organizer-card__icon">🛡️</span>
        <h3>Soporte del plan Anticheat</h3>
        <p>El organizador se habilita junto a tu suscripción activa, asegurando juegos limpios y herramientas exclusivas.</p>
      </article>
    </div>
  </section>

  <?php if ($userHasAccount): ?>
    <section class="section organizer-section" id="organizador">
      <div class="organizer-section__header">
        <div>
          <p class="hero-eyebrow">Panel privado</p>
          <h2>Organizar mi torneo</h2>
        </div>
        <div class="organizer-badges">
          <?php if ($plan_activo): ?>
            <span class="organizer-badge organizer-badge--success">Plan Anticheat activo<?php if ($dias_restantes !== null) { echo ' · ' . $dias_restantes . ' días'; } ?></span>
          <?php else: ?>
            <span class="organizer-badge organizer-badge--warning">Plan inactivo</span>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($plan_activo): ?>
        <p class="organizer-section__intro">Construye llaves personalizadas, empareja equipos y lleva el score en un espacio pensado para los anfitriones del plan Anticheat.</p>
        <div class="organizer-workspace" id="organizer-workspace">
          <div class="organizer-column">
            <div class="organizer-form-card">
              <h3>1. Añade tus equipos</h3>
              <p class="organizer-hint">Máximo 16 equipos por bracket.</p>
              <form id="team-form" class="organizer-form" autocomplete="off">
                <label for="team-name">Nombre del equipo</label>
                <input type="text" id="team-name" maxlength="40" placeholder="Ej. Red Dragons" required />
                <label for="team-tag">Tag / seed (opcional)</label>
                <input type="text" id="team-tag" maxlength="10" placeholder="#1 / LATAM" />
                <button type="submit" class="btn primary">Agregar equipo</button>
              </form>
              <div class="chip-list" id="teams-list" aria-live="polite"></div>
            </div>
          </div>

          <div class="organizer-column">
            <div class="organizer-form-card">
              <div class="organizer-form-card__head">
                <h3>2. Genera tus matches</h3>
                <button type="button" class="btn primary btn-compact" id="generate-matches">Generar llaves</button>
              </div>
              <p class="organizer-hint">Los equipos se emparejan en orden. Si queda uno libre, obtendrá un pase automático.</p>
              <div id="matches-container" class="matches-grid">
                <div class="organizer-empty">
                  <p>Agrega al menos 2 equipos para crear tus matches.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="organizer-actions">
          <button type="button" class="btn secondary" id="clear-data">Limpiar todo</button>
          <button type="button" class="btn primary" id="export-data">Copiar resumen</button>
        </div>

        <div id="organizer-toast" class="organizer-toast" role="status" aria-live="polite"></div>
      <?php else: ?>
        <div class="organizer-locked">
          <p>Necesitas un plan Anticheat activo para habilitar el módulo de organización.</p>
          <a href="anticheats.php" class="btn primary">Ver planes disponibles</a>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <footer class="footer">
    <p>&copy; <span id="year"></span> Red Dragons Championship. Todos los derechos reservados.</p>
  </footer>

  <script src="scripts.js"></script>
  <script src="page-animations.js"></script>
  <?php if ($userHasAccount && $plan_activo): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const workspace = document.getElementById('organizer-workspace');
        if (!workspace) return;

        const MAX_TEAMS = 16;
        const state = { teams: [], matches: [] };

        const teamForm = document.getElementById('team-form');
        const teamNameInput = document.getElementById('team-name');
        const teamTagInput = document.getElementById('team-tag');
        const teamsList = document.getElementById('teams-list');
        const generateBtn = document.getElementById('generate-matches');
        const matchesContainer = document.getElementById('matches-container');
        const clearBtn = document.getElementById('clear-data');
        const exportBtn = document.getElementById('export-data');
        const toast = document.getElementById('organizer-toast');

        const uuid = () => (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(16).slice(2);

        const showToast = (message, type = 'info') => {
          if (!toast) return;
          toast.textContent = message;
          toast.dataset.state = type;
          toast.classList.add('is-visible');
          if (toast.hideTimeout) clearTimeout(toast.hideTimeout);
          toast.hideTimeout = setTimeout(() => toast.classList.remove('is-visible'), 3200);
        };

        const resetMatches = () => {
          state.matches = [];
          renderMatches();
        };

        const renderTeams = () => {
          if (!teamsList) return;
          if (!state.teams.length) {
            teamsList.innerHTML = '<p class="organizer-empty organizer-empty--mini">Todavía no hay equipos registrados.</p>';
            return;
          }

          teamsList.innerHTML = state.teams.map((team, index) => `
            <span class="chip" data-team-id="${team.id}">
              <strong>#${String(index + 1).padStart(2, '0')}</strong>
              ${team.name}
              ${team.tag ? `<small class="chip-tag">${team.tag}</small>` : ''}
              <button type="button" aria-label="Eliminar ${team.name}" data-remove-team="${team.id}">&times;</button>
            </span>
          `).join('');
        };

        const renderTeamRow = (match, slot) => {
          const name = match[`team${slot}`];
          const tag = match[`tag${slot}`];
          const score = match[`score${slot}`];
          const disabled = name ? '' : 'disabled';
          const winnerClass = match.winner === slot ? 'match-team--winner' : '';

          return `
            <div class="match-team ${winnerClass}">
              <div>
                <span class="match-team__name">${name || 'TBD'}</span>
                ${tag ? `<small class="match-team__tag">${tag}</small>` : ''}
              </div>
              <div class="match-team__controls">
                <input type="number" min="0" ${disabled} value="${score ?? ''}" data-score="${slot}" aria-label="Puntaje ${name || 'TBD'}" />
                <button type="button" class="match-winner-btn" ${disabled} data-mark-winner="${slot}" aria-label="Marcar como ganador">${match.winner === slot ? '👑' : '🏅'}</button>
              </div>
            </div>
          `;
        };

        const renderMatches = () => {
          if (!matchesContainer) return;
          if (!state.matches.length) {
            matchesContainer.innerHTML = `
              <div class="organizer-empty">
                <p>Agrega al menos 2 equipos y presiona "Generar llaves".</p>
              </div>
            `;
            return;
          }

          matchesContainer.innerHTML = state.matches.map(match => `
            <article class="match-card" data-match-id="${match.id}">
              <header class="match-header">
                <h4>Match #${String(match.id).padStart(2, '0')}</h4>
                <button type="button" class="match-clear" data-reset-match="${match.id}">Reiniciar</button>
              </header>
              <div class="match-body">
                ${renderTeamRow(match, 1)}
                <div class="match-divider">VS</div>
                ${renderTeamRow(match, 2)}
              </div>
            </article>
          `).join('');
        };

        const generateMatches = () => {
          if (state.teams.length < 2) {
            showToast('Agrega al menos dos equipos para generar matches', 'warn');
            return;
          }

          state.matches = [];
          for (let i = 0; i < state.teams.length; i += 2) {
            const team1 = state.teams[i];
            const team2 = state.teams[i + 1];
            state.matches.push({
              id: (i / 2) + 1,
              team1: team1 ? team1.name : null,
              tag1: team1 ? (team1.tag || `#${i + 1}`) : null,
              team2: team2 ? team2.name : null,
              tag2: team2 ? (team2.tag || `#${i + 2}`) : null,
              score1: null,
              score2: null,
              winner: null
            });
          }

          renderMatches();
          showToast('Matches generados correctamente', 'success');
        };

        const exportData = () => {
          if (!state.matches.length) {
            showToast('Genera tus matches antes de exportar', 'warn');
            return;
          }

          const payload = {
            generatedAt: new Date().toISOString(),
            teams: state.teams.map((team, index) => ({
              seed: index + 1,
              name: team.name,
              tag: team.tag || null
            })),
            matches: state.matches
          };

          const serialized = JSON.stringify(payload, null, 2);
          const fallback = () => {
            window.prompt('Copia manualmente tu planificación:', serialized);
          };

          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(serialized)
              .then(() => showToast('Resumen copiado al portapapeles', 'success'))
              .catch(() => {
                showToast('No se pudo copiar automáticamente', 'warn');
                fallback();
              });
          } else {
            fallback();
          }
        };

        teamForm?.addEventListener('submit', (event) => {
          event.preventDefault();
          const name = teamNameInput.value.trim();
          const tag = teamTagInput.value.trim();

          if (!name) {
            showToast('Ingresa un nombre de equipo válido', 'warn');
            return;
          }

          if (state.teams.length >= MAX_TEAMS) {
            showToast(`Solo se permiten ${MAX_TEAMS} equipos por bracket`, 'warn');
            return;
          }

          if (state.teams.some(team => team.name.toLowerCase() === name.toLowerCase())) {
            showToast('Ese equipo ya existe en la lista', 'warn');
            return;
          }

          state.teams.push({ id: uuid(), name, tag });
          teamForm.reset();
          teamNameInput.focus();
          renderTeams();
          resetMatches();
          showToast('Equipo agregado', 'success');
        });

        teamsList?.addEventListener('click', (event) => {
          const button = event.target.closest('[data-remove-team]');
          if (!button) return;
          const teamId = button.getAttribute('data-remove-team');
          state.teams = state.teams.filter(team => team.id !== teamId);
          renderTeams();
          resetMatches();
          showToast('Equipo eliminado', 'info');
        });

        matchesContainer?.addEventListener('input', (event) => {
          const input = event.target;
          if (!input.matches('input[data-score]')) return;
          const card = input.closest('[data-match-id]');
          if (!card) return;

          const matchId = Number(card.dataset.matchId);
          const match = state.matches.find(item => item.id === matchId);
          if (!match) return;

          const slot = Number(input.dataset.score);
          const value = input.value === '' ? null : Math.max(0, Number(input.value));

          match[`score${slot}`] = Number.isFinite(value) ? value : null;

          if (match.score1 !== null && match.score2 !== null) {
            if (match.score1 === match.score2) {
              match.winner = null;
            } else {
              match.winner = match.score1 > match.score2 ? 1 : 2;
            }
          } else {
            match.winner = null;
          }

          renderMatches();
        });

        matchesContainer?.addEventListener('click', (event) => {
          const resetBtn = event.target.closest('[data-reset-match]');
          if (resetBtn) {
            const matchId = Number(resetBtn.dataset.resetMatch);
            const match = state.matches.find(item => item.id === matchId);
            if (match) {
              match.score1 = null;
              match.score2 = null;
              match.winner = null;
              renderMatches();
              showToast('Match reiniciado', 'info');
            }
            return;
          }

          const winnerBtn = event.target.closest('[data-mark-winner]');
          if (winnerBtn) {
            const slot = Number(winnerBtn.dataset.markWinner);
            const card = winnerBtn.closest('[data-match-id]');
            const matchId = Number(card?.dataset.matchId);
            const match = state.matches.find(item => item.id === matchId);
            if (match && match[`team${slot}`]) {
              match.winner = slot;
              renderMatches();
              showToast('Ganador actualizado', 'success');
            }
          }
        });

        generateBtn?.addEventListener('click', generateMatches);
        exportBtn?.addEventListener('click', exportData);
        clearBtn?.addEventListener('click', () => {
          if (!state.teams.length && !state.matches.length) {
            showToast('No hay datos que limpiar', 'info');
            return;
          }
          state.teams = [];
          state.matches = [];
          renderTeams();
          renderMatches();
          showToast('Organizador reiniciado', 'success');
        });

        renderTeams();
        renderMatches();
      });
    </script>
  <?php endif; ?>
</body>
</html>
