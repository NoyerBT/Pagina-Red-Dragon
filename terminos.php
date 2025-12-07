<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Términos y Condiciones - Red Dragons Cup</title>
  <link rel="stylesheet" href="styles.css" />
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

  <main class="section legal-section" style="max-width: 900px; margin: 0 auto; padding: 8rem 2rem 3rem 2rem;">
    <h1 style="color: #d4af37; text-align: center; margin-bottom: 1rem; font-size: 2.5rem; margin-top: 0;">Términos y Condiciones de Uso</h1>
    <p style="text-align: center; color: rgba(255, 255, 255, 0.6); margin-bottom: 3rem; font-size: 0.9rem;">Última actualización: <?php echo date('d/m/Y'); ?></p>

    <div style="background: rgba(0, 0, 0, 0.4); padding: 2rem; border-radius: 12px; border: 1px solid rgba(212, 175, 55, 0.2);">
      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">1. Definiciones y Aceptación de los Términos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          Al acceder, registrarte o utilizar los servicios de Red Dragons Cup ("la Plataforma", "nosotros", "nuestro"), aceptas estar legalmente vinculado por estos Términos y Condiciones de Uso. Si no estás de acuerdo con alguna parte de estos términos, no debes utilizar nuestros servicios.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Estos términos constituyen un acuerdo legalmente vinculante entre tú ("Usuario", "Participante") y Red Dragons Cup. El uso continuado de nuestros servicios después de cualquier modificación a estos términos constituye tu aceptación de los mismos.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">2. Requisitos de Elegibilidad y Registro</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>2.1.</strong> Para utilizar nuestros servicios, debes ser mayor de edad según la legislación de tu país de residencia, o contar con el consentimiento de tus padres o tutores legales si eres menor de edad.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>2.2.</strong> Debes proporcionar información veraz, precisa, actualizada y completa durante el proceso de registro. Es tu responsabilidad mantener esta información actualizada.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>2.3.</strong> Eres responsable de mantener la confidencialidad de tus credenciales de acceso. Todas las actividades realizadas bajo tu cuenta serán tu responsabilidad.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>2.4.</strong> Nos reservamos el derecho de rechazar, suspender o cancelar cualquier registro que consideremos inapropiado, fraudulento o que viole estos términos.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">3. Servicios Ofrecidos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>3.1.</strong> Red Dragons Cup ofrece servicios de organización de torneos, sistema anticheat, servidores privados y herramientas de gestión de brackets y equipos.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>3.2.</strong> Nos reservamos el derecho de modificar, suspender o discontinuar cualquier aspecto de nuestros servicios en cualquier momento, con o sin previo aviso.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>3.3.</strong> No garantizamos que nuestros servicios estarán disponibles de forma ininterrumpida o libre de errores. Podemos realizar mantenimientos programados o de emergencia.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">4. Pagos, Suscripciones y Reembolsos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>4.1.</strong> Los pagos del Plan Anticheat Premium se realizan de forma mensual y recurrente. El precio puede variar y será comunicado antes de cada renovación.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>4.2.</strong> Todos los pagos son finales y no reembolsables, excepto en casos de error técnico de nuestra parte que impida el uso del servicio durante más del 50% del período pagado.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>4.3.</strong> El incumplimiento del pago puede resultar en la suspensión inmediata de tu cuenta y pérdida de acceso a todos los servicios premium, incluyendo servidores privados y herramientas exclusivas.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>4.4.</strong> Puedes cancelar tu suscripción en cualquier momento, pero no recibirás reembolso por el período restante del mes actual. El acceso continuará hasta el final del período pagado.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">5. Uso del Sistema Anticheat</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>5.1.</strong> El sistema anticheat es una herramienta de seguridad diseñada para detectar y prevenir el uso de software no autorizado, hacks, modificaciones o cualquier forma de trampa.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>5.2.</strong> Al utilizar nuestro sistema anticheat, aceptas que el software puede recopilar información técnica de tu dispositivo necesaria para su funcionamiento.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>5.3.</strong> Cualquier intento de modificar, desactivar o eludir el sistema anticheat resultará en la suspensión permanente de tu cuenta y puede tener consecuencias legales.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">6. Servidores Privados</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>6.1.</strong> Los servidores privados son un beneficio exclusivo para usuarios VIP con membresía activa. El acceso a estos servidores es personal e intransferible.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>6.2.</strong> Está estrictamente prohibido compartir las credenciales de acceso a tu servidor privado con terceros. El incumplimiento resultará en la revocación inmediata del acceso.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>6.3.</strong> No garantizamos disponibilidad del 100% de los servidores privados. Podemos realizar mantenimientos sin previo aviso.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">7. Conducta del Usuario y Prohibiciones</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>7.1.</strong> Debes mantener un comportamiento respetuoso hacia otros usuarios, administradores y el personal de Red Dragons Cup. Está prohibido:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li>Utilizar lenguaje ofensivo, discriminatorio, acosador o amenazante</li>
          <li>Utilizar hacks, cheats, modificaciones no autorizadas o cualquier forma de trampa</li>
          <li>Intentar acceder a cuentas de otros usuarios o sistemas no autorizados</li>
          <li>Realizar actividades que puedan dañar, sobrecargar o comprometer nuestros servicios</li>
          <li>Compartir información falsa o engañosa</li>
          <li>Realizar actividades comerciales no autorizadas</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>7.2.</strong> El incumplimiento de estas normas puede resultar en advertencias, suspensión temporal o permanente de tu cuenta, sin derecho a reembolso.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">8. Propiedad Intelectual</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>8.1.</strong> Todos los contenidos, logotipos, marcas, diseños, textos, gráficos, software y materiales de Red Dragons Cup son propiedad exclusiva de Red Dragons Cup o sus licenciantes.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>8.2.</strong> Queda estrictamente prohibida la reproducción, distribución, modificación, creación de obras derivadas, o cualquier uso comercial de nuestros materiales sin autorización previa y por escrito.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>8.3.</strong> Los contenidos que publiques en nuestra plataforma siguen siendo de tu propiedad, pero nos otorgas una licencia mundial, no exclusiva, para usar, reproducir y distribuir dicho contenido en relación con nuestros servicios.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">9. Limitación de Responsabilidad</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>9.1.</strong> Red Dragons Cup no será responsable por daños indirectos, incidentales, especiales, consecuentes o punitivos, incluyendo pero no limitado a pérdida de datos, pérdida de beneficios, o interrupción del negocio.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>9.2.</strong> No garantizamos que nuestros servicios sean ininterrumpidos, seguros, libres de errores o que cumplan con tus expectativas específicas.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>9.3.</strong> En ningún caso nuestra responsabilidad total excederá el monto que hayas pagado por nuestros servicios en los últimos 12 meses.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">10. Indemnización</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Aceptas indemnizar, defender y eximir de responsabilidad a Red Dragons Cup, sus afiliados, directores, empleados y agentes de cualquier reclamo, demanda, pérdida, responsabilidad y gasto (incluyendo honorarios legales) que surjan de tu uso de nuestros servicios, violación de estos términos, o violación de cualquier derecho de terceros.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">11. Terminación</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>11.1.</strong> Puedes terminar tu cuenta en cualquier momento contactando a nuestro equipo de soporte.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>11.2.</strong> Nos reservamos el derecho de suspender o terminar tu acceso a nuestros servicios inmediatamente, sin previo aviso, por cualquier violación de estos términos o por cualquier otra razón que consideremos apropiada.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>11.3.</strong> Tras la terminación, tu derecho a utilizar nuestros servicios cesará inmediatamente. Todas las disposiciones que por su naturaleza deban sobrevivir a la terminación permanecerán en vigor.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">12. Modificaciones de los Términos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>12.1.</strong> Nos reservamos el derecho de modificar estos términos en cualquier momento. Las modificaciones entrarán en vigor inmediatamente después de su publicación en esta página.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>12.2.</strong> Es tu responsabilidad revisar periódicamente estos términos. El uso continuado de nuestros servicios después de cualquier modificación constituye tu aceptación de los términos modificados.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">13. Ley Aplicable y Jurisdicción</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>13.1.</strong> Estos términos se regirán e interpretarán de acuerdo con las leyes de la República del Perú, sin dar efecto a cualquier principio de conflictos de leyes.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>13.2.</strong> Cualquier disputa que surja de o esté relacionada con estos términos será sometida a la jurisdicción exclusiva de los tribunales competentes de Lima, Perú.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">14. Disposiciones Generales</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>14.1.</strong> Si alguna disposición de estos términos se considera inválida o inaplicable, las disposiciones restantes permanecerán en pleno vigor y efecto.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>14.2.</strong> Estos términos constituyen el acuerdo completo entre tú y Red Dragons Cup respecto al uso de nuestros servicios y reemplazan todos los acuerdos anteriores.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>14.3.</strong> Nuestra falta de ejercer o hacer valer cualquier derecho o disposición de estos términos no constituirá una renuncia a tal derecho o disposición.
        </p>
      </article>

      <article>
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">15. Contacto</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Si tienes preguntas sobre estos Términos y Condiciones, puedes contactarnos a través de nuestra página de <a href="contacto.php" style="color: #d4af37;">contacto</a> o mediante nuestro canal de soporte oficial.
        </p>
      </article>
    </div>
  </main>

  <script src="scripts.js"></script>
</body>
</html>
