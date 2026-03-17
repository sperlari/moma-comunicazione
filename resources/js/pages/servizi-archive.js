export function initServiziArchive() {
  const links = Array.from(document.querySelectorAll('[data-srv-nav]'));
  if (!links.length) return;

  links.forEach((a) => {
    a.addEventListener('click', (e) => {
      const href = a.getAttribute('href') || '';
      if (!href.startsWith('#')) return;

      const target = document.querySelector(href);
      if (!target) return;

      e.preventDefault();

      if (window.history?.replaceState) {
        window.history.replaceState(null, '', href);
      }

      const lenis = window.__momaLenis;
      if (lenis && typeof lenis.scrollTo === 'function') {
        lenis.scrollTo(target, { offset: 0, duration: 1.1, immediate: false });
      } else {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}
