// Script compartido para todas las páginas
const yearEl = document.getElementById('year');
if (yearEl) {
  yearEl.textContent = new Date().getFullYear();
}

const rulesModal = document.getElementById('rules-modal');
const openRulesBtn = document.getElementById('open-rules');

if (rulesModal && openRulesBtn) {
  const toggleModal = (show) => {
    rulesModal.classList.toggle('is-visible', show);
    rulesModal.setAttribute('aria-hidden', show ? 'false' : 'true');
  };

  openRulesBtn.addEventListener('click', () => toggleModal(true));

  rulesModal.addEventListener('click', (event) => {
    const target = event.target;
    if (target.matches('[data-close-modal]') || target === rulesModal) {
      toggleModal(false);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && rulesModal.classList.contains('is-visible')) {
      toggleModal(false);
    }
  });
}

// Ocultar/mostrar barra de navegación y logo al hacer scroll
let lastScrollTop = 0;
const navbar = document.querySelector('.top-bar');
const logo = document.querySelector('.top-logo-section');

if (navbar && logo) {
  window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop && scrollTop > 120) {
      navbar.classList.add('hidden');
      logo.classList.add('hidden');
    } else {
      navbar.classList.remove('hidden');
      logo.classList.remove('hidden');
    }

    lastScrollTop = scrollTop;
  });
}
