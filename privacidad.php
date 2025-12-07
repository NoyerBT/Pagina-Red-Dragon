<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Política de Privacidad - Red Dragons Cup</title>
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
    <h1 style="color: #d4af37; text-align: center; margin-bottom: 1rem; font-size: 2.5rem; margin-top: 0;">Política de Privacidad</h1>
    <p style="text-align: center; color: rgba(255, 255, 255, 0.6); margin-bottom: 3rem; font-size: 0.9rem;">Última actualización: <?php echo date('d/m/Y'); ?></p>

    <div style="background: rgba(0, 0, 0, 0.4); padding: 2rem; border-radius: 12px; border: 1px solid rgba(212, 175, 55, 0.2);">
      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">1. Introducción y Responsable del Tratamiento</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          Red Dragons Cup ("nosotros", "nuestro", "la Plataforma") se compromete a proteger tu privacidad y a manejar tus datos personales de manera responsable y conforme a la legislación aplicable, incluyendo la Ley de Protección de Datos Personales del Perú (Ley N° 29733) y el Reglamento General de Protección de Datos (RGPD) cuando aplique.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Esta Política de Privacidad describe cómo recopilamos, utilizamos, almacenamos y protegemos tu información personal cuando utilizas nuestros servicios.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">2. Información que Recopilamos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>2.1. Información Personal Identificable:</strong>
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li>Nombre completo y apellidos</li>
          <li>Dirección de correo electrónico</li>
          <li>Nombre de usuario</li>
          <li>Número de teléfono (cuando se proporciona)</li>
          <li>País de residencia</li>
          <li>Información de pago (procesada de forma segura a través de terceros)</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>2.2. Información Técnica:</strong>
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li>Dirección IP</li>
          <li>Tipo de navegador y sistema operativo</li>
          <li>Información del dispositivo (para el sistema anticheat)</li>
          <li>Registros de actividad y uso de la plataforma</li>
          <li>Cookies y tecnologías de seguimiento similares</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>2.3. Información de Uso:</strong> Recopilamos información sobre cómo interactúas con nuestros servicios, incluyendo páginas visitadas, tiempo de permanencia, y acciones realizadas.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">3. Base Legal y Finalidades del Tratamiento</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          Procesamos tus datos personales basándonos en las siguientes bases legales y para las siguientes finalidades:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li><strong>Consentimiento:</strong> Para el registro y uso de nuestros servicios</li>
          <li><strong>Ejecución de contrato:</strong> Para gestionar tu cuenta, suscripciones y proporcionar los servicios solicitados</li>
          <li><strong>Interés legítimo:</strong> Para mejorar nuestros servicios, prevenir fraudes y garantizar la seguridad</li>
          <li><strong>Cumplimiento legal:</strong> Para cumplir con obligaciones legales y regulatorias</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Utilizamos tu información para: gestionar tu cuenta y participación en torneos, procesar pagos y suscripciones, proporcionar soporte técnico, enviar comunicaciones importantes sobre nuestros servicios, mejorar y personalizar tu experiencia, detectar y prevenir fraudes o actividades ilegales, y cumplir con obligaciones legales.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">4. Compartición de Información con Terceros</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>4.1.</strong> No vendemos, alquilamos ni comercializamos tu información personal a terceros con fines comerciales.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>4.2.</strong> Podemos compartir tu información con:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li><strong>Proveedores de servicios:</strong> Empresas que nos ayudan a operar nuestra plataforma (hosting, procesamiento de pagos, análisis)</li>
          <li><strong>Autoridades legales:</strong> Cuando sea requerido por ley, orden judicial o proceso legal</li>
          <li><strong>Protección de derechos:</strong> Para proteger nuestros derechos, propiedad o seguridad, o la de nuestros usuarios</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>4.3.</strong> Todos los terceros con los que compartimos información están contractualmente obligados a mantener la confidencialidad y seguridad de tus datos.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">5. Seguridad de los Datos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>5.1.</strong> Implementamos medidas de seguridad técnicas, administrativas y físicas apropiadas para proteger tu información personal contra acceso no autorizado, alteración, divulgación o destrucción.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>5.2.</strong> Estas medidas incluyen:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li>Cifrado de datos en tránsito y en reposo</li>
          <li>Controles de acceso estrictos y autenticación</li>
          <li>Monitoreo regular de sistemas para detectar vulnerabilidades</li>
          <li>Capacitación del personal sobre privacidad y seguridad</li>
          <li>Copias de seguridad regulares</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>5.3.</strong> Sin embargo, ningún método de transmisión por Internet o almacenamiento electrónico es 100% seguro. Aunque nos esforzamos por proteger tu información, no podemos garantizar su seguridad absoluta.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">6. Retención de Datos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>6.1.</strong> Conservamos tu información personal durante el tiempo necesario para cumplir con las finalidades descritas en esta política, a menos que la ley requiera o permita un período de retención más largo.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>6.2.</strong> Cuando elimines tu cuenta, eliminaremos o anonimizaremos tu información personal, excepto cuando tengamos una obligación legal de conservarla (por ejemplo, registros de transacciones financieras).
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>6.3.</strong> Los datos pueden conservarse por períodos adicionales para fines legítimos de negocio, resolución de disputas o cumplimiento de obligaciones legales.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">7. Transferencias Internacionales de Datos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>7.1.</strong> Tus datos pueden ser transferidos y procesados en países distintos al tuyo, donde las leyes de protección de datos pueden diferir.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>7.2.</strong> Al utilizar nuestros servicios, consientes estas transferencias. Nos aseguramos de que cualquier transferencia internacional se realice con las salvaguardas adecuadas para proteger tu información.
        </p>
      </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">8. Cookies y Tecnologías de Seguimiento</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>8.1.</strong> Utilizamos cookies y tecnologías similares para:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li>Mantener tu sesión activa</li>
          <li>Recordar tus preferencias</li>
          <li>Analizar el uso de la plataforma</li>
          <li>Mejorar la seguridad y detectar actividades sospechosas</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>8.2.</strong> Puedes controlar las cookies a través de la configuración de tu navegador. Sin embargo, deshabilitar ciertas cookies puede afectar la funcionalidad de nuestros servicios.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>8.3.</strong> Utilizamos cookies esenciales (necesarias para el funcionamiento), cookies de rendimiento (para análisis) y cookies de funcionalidad (para personalización).
        </p>
    </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">9. Derechos del Usuario (Derechos ARCO)</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          De acuerdo con la legislación de protección de datos, tienes los siguientes derechos:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li><strong>Acceso:</strong> Solicitar una copia de los datos personales que tenemos sobre ti</li>
          <li><strong>Rectificación:</strong> Corregir información inexacta o incompleta</li>
          <li><strong>Cancelación:</strong> Solicitar la eliminación de tus datos personales</li>
          <li><strong>Oposición:</strong> Oponerte al procesamiento de tus datos en ciertas circunstancias</li>
          <li><strong>Portabilidad:</strong> Recibir tus datos en un formato estructurado y comúnmente usado</li>
          <li><strong>Limitación:</strong> Solicitar la limitación del procesamiento de tus datos</li>
          <li><strong>Revocación del consentimiento:</strong> Retirar tu consentimiento en cualquier momento</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Para ejercer estos derechos, puedes contactarnos a través de nuestra página de <a href="contacto.php" style="color: #d4af37;">contacto</a>. Responderemos a tu solicitud dentro de los plazos legales establecidos.
        </p>
    </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">10. Menores de Edad</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>10.1.</strong> Nuestros servicios no están dirigidos a menores de 18 años. No recopilamos intencionalmente información personal de menores sin el consentimiento de sus padres o tutores legales.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>10.2.</strong> Si descubrimos que hemos recopilado información de un menor sin el consentimiento apropiado, tomaremos medidas para eliminar esa información de nuestros sistemas.
        </p>
    </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">11. Notificaciones de Violaciones de Datos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          En el caso poco probable de una violación de seguridad que pueda comprometer tu información personal, te notificaremos y a las autoridades competentes dentro de los plazos legales establecidos, proporcionando información sobre la naturaleza de la violación y las medidas que estamos tomando para abordarla.
        </p>
    </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">12. Enlaces a Sitios de Terceros</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          Nuestros servicios pueden contener enlaces a sitios web de terceros. No somos responsables de las prácticas de privacidad o el contenido de estos sitios externos. Te recomendamos revisar las políticas de privacidad de cualquier sitio que visites.
        </p>
    </article>

      <article style="margin-bottom: 2.5rem;">
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">13. Cambios a esta Política de Privacidad</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>13.1.</strong> Podemos actualizar esta Política de Privacidad periódicamente para reflejar cambios en nuestras prácticas, servicios o requisitos legales.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          <strong>13.2.</strong> Te notificaremos sobre cambios materiales mediante un aviso prominente en nuestra plataforma o por correo electrónico.
        </p>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          <strong>13.3.</strong> Te recomendamos revisar esta política periódicamente. El uso continuado de nuestros servicios después de los cambios constituye tu aceptación de la política revisada.
        </p>
    </article>

    <article>
        <h2 style="color: #d4af37; font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 2px solid rgba(212, 175, 55, 0.3); padding-bottom: 0.5rem;">14. Contacto y Ejercicio de Derechos</h2>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
          Si tienes preguntas, inquietudes o deseas ejercer tus derechos relacionados con esta Política de Privacidad, puedes contactarnos a través de:
        </p>
        <ul style="margin-left: 2rem; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9); line-height: 1.8;">
          <li>Nuestra página de <a href="contacto.php" style="color: #d4af37;">contacto</a></li>
          <li>Nuestro canal de soporte oficial</li>
        </ul>
        <p style="line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
          También tienes derecho a presentar una queja ante la autoridad de protección de datos competente si consideras que el tratamiento de tus datos personales viola la legislación aplicable.
        </p>
    </article>
    </div>
  </main>

  <script src="scripts.js"></script>
</body>
</html>
