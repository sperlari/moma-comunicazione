import { initSmoothScroll } from './components/smooth-scroll.js';
import { initMomaPageTransition } from './components/moma-page-transition.js';
import { initMomaCursor } from './cursor';
import { initMomaNav } from './components/moma-nav';
import { initMomaLogo } from './components/moma-logo';

document.addEventListener('DOMContentLoaded', () => {
  initMomaNav();
  initMomaLogo();
  initMomaCursor();
});

document.addEventListener('DOMContentLoaded', async () => {
  initSmoothScroll();
  initMomaPageTransition();

  const nav = document.getElementById('primary-navigation');
  const toggle = document.getElementById('primary-menu-toggle');
  if (nav && toggle) {
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      nav.classList.toggle('hidden');
    });
  }

  if (document.querySelector('.moma-hero')) {
    const mod = await import('./pages/home-hero.js');
    mod.initHomeHero();
  }

  if (document.querySelector('[data-moma-case-studies]')) {
    const mod = await import('./components/moma-case-studies-stack.js');
    mod.initMomaCaseStudies();
  }

  if (document.querySelector('[data-moma-servizi-slider]')) {
    const mod = await import('./components/moma-servizi-slider.js');
    mod.initMomaServiziSlider();
  }

  if (document.querySelector('[data-moma-typewriter], .moma-typewriter')) {
    const mod = await import('./components/moma-typewriter.js');
    mod.initMomaTypewriter();
  }

  if (document.querySelector('[data-moma-reveal]')) {
    const mod = await import('./components/moma-reveal.js');
    mod.initMomaReveal();
  }

  if (document.querySelector('[data-moma-servizi-page]')) {
    const mod = await import('./pages/servizi-archive.js');
    mod.initServiziArchive();
  }


  if (document.querySelector('[data-moma-case-studies-archive]')) {
    const mod = await import('./pages/case-studies-archive.js');
    mod.initCaseStudiesArchive();
  }

  if (document.querySelector('.moma-project-card')) {
    const mod = await import('./components/moma-servizi-projects.js');
    mod.initMomaServiziProjects();
  }

  if (document.querySelector('.moma-glossary-trigger')) {
    const mod = await import('./components/moma-glossary.js');
    mod.initMomaGlossary();
  }
});
