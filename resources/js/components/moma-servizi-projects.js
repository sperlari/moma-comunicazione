import { gsap } from 'gsap';

export function initMomaServiziProjects() {
  const cards = Array.from(document.querySelectorAll('.moma-project-card'));
  if (!cards.length) return;

  cards.forEach((card) => {
    const media = card.querySelector('.moma-project-card__media');
    const img = card.querySelector('.moma-project-card__img');
    const cta = card.querySelector('.moma-project-card__cta');
    if (!media || !img) return;

    const tl = gsap.timeline({ paused: true, defaults: { duration: 0.24, ease: 'power2.out', overwrite: 'auto' } });

    tl.to(card, { backgroundColor: '#f0ece8', borderRadius: 24, boxShadow: '0 12px 30px rgba(24,8,90,.08)' }, 0)
      .to(media, { borderTopLeftRadius: 24, borderTopRightRadius: 24, borderBottomLeftRadius: 0, borderBottomRightRadius: 0 }, 0)
      .to(img, { borderTopLeftRadius: 24, borderTopRightRadius: 24, borderBottomLeftRadius: 0, borderBottomRightRadius: 0, scale: 1.02 }, 0)
      .to(cta, { y: -2, boxShadow: '0 14px 26px rgba(43,30,114,0.26)' }, 0);

    const enter = () => { card.classList.add('is-hover'); tl.play(); };
    const leave = () => { card.classList.remove('is-hover'); tl.reverse(); };

    card.addEventListener('mouseenter', enter);
    card.addEventListener('mouseleave', leave);
    card.addEventListener('focusin', enter);
    card.addEventListener('focusout', (e) => {
      if (!card.contains(e.relatedTarget)) leave();
    });
  });
}
