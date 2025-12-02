// Script compartido para todas las páginas
const yearEl = document.getElementById('year');
if (yearEl) {
  yearEl.textContent = new Date().getFullYear();
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
