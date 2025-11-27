// ================================================
// ANIMACIONES DE ENTRADA - RED DRAGONS
// ================================================

document.addEventListener('DOMContentLoaded', function() {
  
  // Verificar si estamos en la página principal
  const isHomePage = window.location.pathname.endsWith('index.php') || 
                     window.location.pathname === '/' || 
                     window.location.pathname.endsWith('/');
  
  // Solo crear pantalla de carga en la página principal
  if (isHomePage) {
    createLoadingScreen();
  }
  
  // Crear partículas de fondo
  createParticles();
  
  // Iniciar animaciones cuando la página esté cargada
  window.addEventListener('load', function() {
    if (isHomePage) {
      setTimeout(function() {
        hideLoadingScreen();
        animatePageElements();
      }, 1500); // Espera 1.5 segundos antes de ocultar la pantalla de carga
    } else {
      // En páginas secundarias, animar elementos inmediatamente sin pantalla de carga
      animatePageElements();
    }
  });
  
});

// Crear pantalla de carga
function createLoadingScreen() {
  const loadingScreen = document.createElement('div');
  loadingScreen.className = 'loading-screen';
  
  // CONFIGURACIÓN: Cambia esta variable para usar video o spinner
  const useVideo = true; // true = usar video, false = usar spinner tradicional
  const videoPath = 'Img/loading-video.mp4'; // Ruta de tu video
  
  if (useVideo) {
    // Pantalla de carga con VIDEO
    loadingScreen.innerHTML = `
      <div class="loading-video">
        <video autoplay muted loop playsinline>
          <source src="${videoPath}" type="video/mp4">
          Tu navegador no soporta el elemento de video.
        </video>
      </div>
      <div class="loading-text">Cargando...</div>
    `;
  } else {
    // Pantalla de carga con SPINNER tradicional
    loadingScreen.innerHTML = `
      <div class="loading-spinner"></div>
      <div class="loading-logo">
        <img src="Img/imagen de carga.png" alt="Loading">
      </div>
      <div class="loading-text">Cargando...</div>
    `;
  }
  
  document.body.prepend(loadingScreen);
}

// Ocultar pantalla de carga
function hideLoadingScreen() {
  const loadingScreen = document.querySelector('.loading-screen');
  if (loadingScreen) {
    loadingScreen.classList.add('fade-out');
    setTimeout(function() {
      loadingScreen.remove();
    }, 800);
  }
}

// Animar elementos de la página
function animatePageElements() {
  // Animar navbar completo (incluye logo integrado)
  const navbar = document.querySelector('.top-bar');
  if (navbar) {
    navbar.classList.add('navbar-animate');
  }
  
  // Animar título hero
  const heroTitle = document.querySelector('.hero-content h1');
  if (heroTitle) {
    heroTitle.classList.add('hero-title-animate');
  }
  
  // Animar logo hero
  const heroLogo = document.querySelector('.hero-logo');
  if (heroLogo) {
    heroLogo.classList.add('hero-logo-animate');
  }
  
  // Animar subtítulo
  const subtitle = document.querySelector('.subtitle');
  if (subtitle) {
    subtitle.classList.add('animate-fade-in-delay-2');
  }
  
  // Animar botones
  const buttons = document.querySelectorAll('.hero-buttons .btn');
  buttons.forEach(function(btn, index) {
    btn.classList.add('btn-animate');
    btn.style.animationDelay = (1.2 + index * 0.1) + 's';
  });
  
  // Animar info tags
  const infoTags = document.querySelector('.info-tags');
  if (infoTags) {
    infoTags.classList.add('animate-fade-in-delay-3');
  }
  
  // Animar secciones con scroll reveal
  animateSectionsOnScroll();
}

// Animar secciones cuando aparecen en viewport
function animateSectionsOnScroll() {
  const sections = document.querySelectorAll('.section');
  
  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1
  });
  
  sections.forEach(function(section) {
    section.classList.add('animate-on-load');
    observer.observe(section);
  });
  
  // Animar cards dentro de las secciones
  const cards = document.querySelectorAll('.torneo-card, .feature-card, .contact-card, .payment-card');
  cards.forEach(function(card) {
    card.classList.add('animate-on-load');
    observer.observe(card);
  });
}

// Crear partículas flotantes
function createParticles() {
  const particleCount = 20;
  const bgOverlay = document.querySelector('.bg-overlay');
  
  if (!bgOverlay) return;
  
  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    
    // Posición aleatoria
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = Math.random() * 100 + '%';
    
    // Retraso aleatorio
    particle.style.animationDelay = Math.random() * 8 + 's';
    
    // Deriva horizontal aleatoria
    particle.style.setProperty('--drift', (Math.random() - 0.5) * 200 + 'px');
    
    // Tamaño aleatorio
    const size = Math.random() * 3 + 2;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    
    bgOverlay.appendChild(particle);
  }
}

// Añadir efecto de brillo a elementos específicos
function addGlowEffects() {
  const glowElements = document.querySelectorAll('.btn, .nav-links a');
  glowElements.forEach(function(element) {
    element.classList.add('glow-on-hover');
  });
}

// Iniciar efectos adicionales
setTimeout(addGlowEffects, 2000);

// Efecto de parallax suave en el hero
document.addEventListener('mousemove', function(e) {
  const heroLogo = document.querySelector('.hero-logo');
  if (!heroLogo) return;
  
  const mouseX = e.clientX / window.innerWidth;
  const mouseY = e.clientY / window.innerHeight;
  
  const moveX = (mouseX - 0.5) * 20;
  const moveY = (mouseY - 0.5) * 20;
  
  heroLogo.style.transform = `translate(${moveX}px, ${moveY}px)`;
  heroLogo.style.transition = 'transform 0.3s ease-out';
});
