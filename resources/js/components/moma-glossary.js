import { gsap } from 'gsap';

const SELECTOR = '.moma-glossary-trigger';
const MOBILE_BREAKPOINT = 900;

function readGlossaryData() {
  const node = document.getElementById('moma-glossary-data');
  if (!node) return {};
  try {
    return JSON.parse(node.textContent || '{}') || {};
  } catch {
    return {};
  }
}

function isMobile() {
  return window.innerWidth <= MOBILE_BREAKPOINT;
}

function clamp(value, min, max) {
  return Math.max(min, Math.min(max, value));
}

function buildAction(label, url, target = '', extraClass = '') {
  if (!label || !url) return '';
  const targetAttr = target ? ` target="${target}"` : '';
  const relAttr = target === '_blank' ? ' rel="noopener noreferrer"' : '';
  return `<a href="${url}"${targetAttr}${relAttr} class="m-btn ${extraClass}"><span class="m-btn__label">${label}</span></a>`;
}

function moveIcon() {
  return `<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12 3l2.8 2.8-1.4 1.4L13 6.8V10h3.2l-.4-.4 1.4-1.4L20 11l-2.8 2.8-1.4-1.4.4-.4H13v3.2l.4-.4 1.4 1.4L12 21l-2.8-2.8 1.4-1.4.4.4V13H7.8l.4.4-1.4 1.4L4 12l2.8-2.8 1.4 1.4-.4.4H11V6.8l-.4.4-1.4-1.4L12 3z" fill="currentColor"/></svg>`;
}

function applyIconStyles(btn) {
  if (!btn) return;
  Object.assign(btn.style, {
    width: '36px',
    height: '36px',
    borderRadius: '999px',
    border: '1px solid rgba(24,8,90,.15)',
    background: 'rgba(255,255,255,.75)',
    color: '#18085a',
    cursor: 'pointer',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    transition: 'transform .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease'
  });

  btn.addEventListener('mouseenter', () => {
    gsap.to(btn, { scale: 1.08, backgroundColor: 'rgba(73,28,170,.12)', borderColor: 'rgba(73,28,170,.28)', boxShadow: '0 8px 18px rgba(73,28,170,.14)', duration: 0.18, overwrite: 'auto' });
  });
  btn.addEventListener('mouseleave', () => {
    gsap.to(btn, { scale: 1, backgroundColor: 'rgba(255,255,255,.75)', borderColor: 'rgba(24,8,90,.15)', boxShadow: '0 0 0 rgba(73,28,170,0)', duration: 0.18, overwrite: 'auto' });
  });
}

function buildDesktopPopover(term) {
  const el = document.createElement('div');
  el.className = 'moma-glossary-popover';
  el.innerHTML = `
    <button type="button" class="moma-glossary-popover__backdrop" aria-hidden="true"></button>
    <div class="moma-glossary-popover__panel" role="dialog" aria-modal="false" aria-label="${term.title || ''}">
      <div class="moma-glossary-popover__controls">
        <button type="button" class="moma-glossary-popover__drag" aria-label="Sposta card">${moveIcon()}</button>
        <button type="button" class="moma-glossary-popover__close" aria-label="Chiudi">×</button>
      </div>
      <div class="moma-glossary-popover__title">${term.title || ''}</div>
      <div class="moma-glossary-popover__body">${term.body || ''}</div>
      <div class="moma-glossary-popover__actions">
        ${buildAction(term.btn1_label, term.btn1_url, term.btn1_target, 'm-btn--secondary m-btn--dot')}
        ${buildAction(term.btn2_label, term.btn2_url, term.btn2_target, 'm-btn--tag m-btn--dot')}
      </div>
    </div>
  `;

  Object.assign(el.style, {
    position: 'fixed',
    inset: '0',
    width: '100%',
    height: '100%',
    zIndex: '9998',
    pointerEvents: 'none'
  });

  const backdrop = el.querySelector('.moma-glossary-popover__backdrop');
  Object.assign(backdrop.style, {
    position: 'fixed',
    inset: '0',
    background: 'transparent',
    border: '0',
    padding: '0',
    margin: '0',
    pointerEvents: 'auto'
  });

  const panel = el.querySelector('.moma-glossary-popover__panel');
  Object.assign(panel.style, {
    position: 'fixed',
    width: 'min(420px, calc(100vw - 32px))',
    background: '#f5efe8',
    border: '1px solid rgba(24,8,90,.12)',
    borderRadius: '22px',
    boxShadow: '0 26px 70px rgba(15,5,56,.18)',
    padding: '22px 22px 18px',
    color: '#18085a',
    pointerEvents: 'auto',
    opacity: '0'
  });

  const controls = el.querySelector('.moma-glossary-popover__controls');
  Object.assign(controls.style, { position: 'absolute', top: '10px', right: '10px', display: 'flex', gap: '8px' });
  applyIconStyles(el.querySelector('.moma-glossary-popover__drag'));
  applyIconStyles(el.querySelector('.moma-glossary-popover__close'));

  const title = el.querySelector('.moma-glossary-popover__title');
  Object.assign(title.style, { fontFamily: 'Fraunces, ui-serif, Georgia, serif', fontSize: '1.5rem', lineHeight: '1.05', color: '#18085a', marginBottom: '14px', paddingRight: '86px' });
  const body = el.querySelector('.moma-glossary-popover__body');
  Object.assign(body.style, { fontSize: '0.98rem', lineHeight: '1.6' });
  const actions = el.querySelector('.moma-glossary-popover__actions');
  Object.assign(actions.style, { display: 'flex', flexWrap: 'wrap', gap: '12px', marginTop: '18px' });
  return el;
}

function buildMobileModal(term) {
  const el = document.createElement('div');
  el.className = 'moma-glossary-modal';
  el.innerHTML = `
    <div class="moma-glossary-modal__backdrop"></div>
    <div class="moma-glossary-modal__panel" role="dialog" aria-modal="true" aria-label="${term.title || ''}">
      <button type="button" class="moma-glossary-modal__close" aria-label="Chiudi">×</button>
      <div class="moma-glossary-modal__title">${term.title || ''}</div>
      <div class="moma-glossary-modal__body">${term.body || ''}</div>
      <div class="moma-glossary-modal__actions">
        ${buildAction(term.btn1_label, term.btn1_url, term.btn1_target, 'm-btn--secondary m-btn--dot')}
        ${buildAction(term.btn2_label, term.btn2_url, term.btn2_target, 'm-btn--tag m-btn--dot')}
      </div>
    </div>
  `;
  Object.assign(el.style, { position: 'fixed', inset: '0', zIndex: '9999' });
  const backdrop = el.querySelector('.moma-glossary-modal__backdrop');
  Object.assign(backdrop.style, { position: 'absolute', inset: '0', background: 'rgba(24,8,90,.42)', backdropFilter: 'blur(8px)' });
  const panel = el.querySelector('.moma-glossary-modal__panel');
  Object.assign(panel.style, { position: 'relative', minHeight: '100dvh', background: '#f5efe8', color: '#18085a', padding: '72px 22px 28px', overflowY: 'auto' });
  applyIconStyles(el.querySelector('.moma-glossary-modal__close'));
  Object.assign(el.querySelector('.moma-glossary-modal__close').style, { position: 'absolute', top: '18px', right: '18px', width: '42px', height: '42px', fontSize: '1.6rem' });
  const title = el.querySelector('.moma-glossary-modal__title');
  Object.assign(title.style, { fontFamily: 'Fraunces, ui-serif, Georgia, serif', fontSize: '2rem', lineHeight: '1', color: '#18085a', marginBottom: '18px', paddingRight: '56px' });
  const body = el.querySelector('.moma-glossary-modal__body');
  Object.assign(body.style, { fontSize: '1rem', lineHeight: '1.7' });
  const actions = el.querySelector('.moma-glossary-modal__actions');
  Object.assign(actions.style, { display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: '12px', marginTop: '22px' });
  return el;
}

function positionPopover(root, trigger) {
  const panel = root.querySelector('.moma-glossary-popover__panel');
  if (!panel || !trigger) return;
  const rect = trigger.getBoundingClientRect();
  const panelRect = panel.getBoundingClientRect();
  const gap = 14;
  let left = rect.left;
  let top = rect.bottom + gap;

  if (top + panelRect.height > window.innerHeight - 16) {
    top = rect.top - panelRect.height - gap;
  }
  if (top < 16) top = 16;
  left = clamp(left, 16, window.innerWidth - panelRect.width - 16);

  panel.style.left = `${left}px`;
  panel.style.top = `${top}px`;
}

function enablePopoverDrag(root) {
  const panel = root.querySelector('.moma-glossary-popover__panel');
  const handle = root.querySelector('.moma-glossary-popover__drag');
  if (!panel || !handle) return;
  let startX = 0, startY = 0, originLeft = 0, originTop = 0, dragging = false;

  const onMove = (ev) => {
    if (!dragging) return;
    const dx = ev.clientX - startX;
    const dy = ev.clientY - startY;
    const rect = panel.getBoundingClientRect();
    panel.style.left = `${clamp(originLeft + dx, 16, window.innerWidth - rect.width - 16)}px`;
    panel.style.top = `${clamp(originTop + dy, 16, window.innerHeight - rect.height - 16)}px`;
  };
  const onUp = () => {
    dragging = false;
    handle.style.cursor = 'grab';
    document.removeEventListener('pointermove', onMove);
    document.removeEventListener('pointerup', onUp);
  };

  handle.addEventListener('pointerdown', (ev) => {
    ev.preventDefault();
    ev.stopPropagation();
    dragging = true;
    startX = ev.clientX;
    startY = ev.clientY;
    const rect = panel.getBoundingClientRect();
    originLeft = rect.left;
    originTop = rect.top;
    handle.style.cursor = 'grabbing';
    document.addEventListener('pointermove', onMove);
    document.addEventListener('pointerup', onUp, { once: true });
  });
}

function highlightTrigger(btn) {
  if (!btn) return;
  if (!btn.dataset.baseColor) {
    btn.dataset.baseColor = getComputedStyle(btn).color || '#18085a';
  }
  gsap.to(btn, {
    backgroundColor: '#491caa',
    color: '#ffffff',
    paddingLeft: '0.18em',
    paddingRight: '0.18em',
    borderRadius: '0.3em',
    duration: 0.2,
    ease: 'power2.out',
    overwrite: 'auto'
  });
}

function resetTriggerHighlight(btn) {
  if (!btn) return;
  if (!btn.dataset.baseColor) {
    btn.dataset.baseColor = getComputedStyle(btn).color || '#18085a';
  }
  gsap.to(btn, {
    backgroundColor: 'rgba(0,0,0,0)',
    color: btn.dataset.baseColor || '#18085a',
    paddingLeft: '0em',
    paddingRight: '0em',
    borderRadius: '0em',
    duration: 0.16,
    ease: 'power2.out',
    overwrite: 'auto'
  });
}

function bindTriggerHover(triggers) {
  triggers.forEach((btn) => {
    if (!btn.dataset.baseColor) {
      btn.dataset.baseColor = getComputedStyle(btn).color || '#18085a';
    }

    btn.addEventListener('mouseenter', () => {
      highlightTrigger(btn);
    });

    btn.addEventListener('mouseleave', () => {
      if (btn.classList.contains('is-active')) return;
      resetTriggerHighlight(btn);
    });
  });
}

export function initMomaGlossary() {
  const triggers = Array.from(document.querySelectorAll(SELECTOR));
  if (!triggers.length) return;
  const data = readGlossaryData();
  if (!data || !Object.keys(data).length) return;

  bindTriggerHover(triggers);

  let currentCard = null;
  let activeBtn = null;
  let currentMode = null;

  const setTriggerActive = (btn, active) => {
    if (!btn) return;
    btn.classList.toggle('is-active', !!active);
    btn.setAttribute('aria-expanded', active ? 'true' : 'false');

    if (active) {
      highlightTrigger(btn);
    } else {
      resetTriggerHighlight(btn);
    }
  };

  const close = () => {
    if (!currentCard) return;
    const el = currentCard;
    const mode = currentMode;
    const prevBtn = activeBtn;
    currentCard = null;
    activeBtn = null;
    currentMode = null;
    document.body.style.overflow = '';
    setTriggerActive(prevBtn, false);
    gsap.to(el.querySelector(mode === 'mobile' ? '.moma-glossary-modal__panel' : '.moma-glossary-popover__panel') || el, {
      autoAlpha: 0,
      y: 10,
      duration: 0.16,
      ease: 'power2.in',
      overwrite: 'auto',
      onComplete: () => el.remove()
    });
  };

  const onEsc = (e) => { if (e.key === 'Escape') close(); };

  const open = (btn) => {
    const key = btn.getAttribute('data-glossary-key') || '';
    const term = data[key];
    if (!term) return;
    if (activeBtn === btn && currentCard) { close(); return; }

    close();
    currentMode = isMobile() ? 'mobile' : 'desktop';
    currentCard = currentMode === 'mobile' ? buildMobileModal(term) : buildDesktopPopover(term);
    activeBtn = btn;
    setTriggerActive(btn, true);
    document.body.appendChild(currentCard);
    if (currentMode === 'mobile') document.body.style.overflow = 'hidden';

    if (currentMode === 'desktop') {
      positionPopover(currentCard, btn);
      requestAnimationFrame(() => positionPopover(currentCard, btn));
      enablePopoverDrag(currentCard);
      currentCard.querySelector('.moma-glossary-popover__backdrop')?.addEventListener('click', close);
      currentCard.querySelector('.moma-glossary-popover__close')?.addEventListener('click', close);
      const panel = currentCard.querySelector('.moma-glossary-popover__panel');
      gsap.fromTo(panel, { autoAlpha: 0, y: 10, scale: 0.98 }, { autoAlpha: 1, y: 0, scale: 1, duration: 0.22, ease: 'power2.out', overwrite: 'auto' });
    } else {
      currentCard.querySelector('.moma-glossary-modal__backdrop')?.addEventListener('click', close);
      currentCard.querySelector('.moma-glossary-modal__close')?.addEventListener('click', close);
      const panel = currentCard.querySelector('.moma-glossary-modal__panel');
      gsap.fromTo(panel, { autoAlpha: 0, y: 18 }, { autoAlpha: 1, y: 0, duration: 0.22, ease: 'power2.out', overwrite: 'auto' });
    }
  };

  triggers.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      open(btn);
    });
  });

  window.addEventListener('resize', () => {
    if (!currentCard || !activeBtn) return;
    const nowMode = isMobile() ? 'mobile' : 'desktop';
    if (nowMode !== currentMode) { open(activeBtn); return; }
    if (currentMode === 'desktop') {
      positionPopover(currentCard, activeBtn);
      requestAnimationFrame(() => positionPopover(currentCard, activeBtn));
    }
  });

  document.addEventListener('click', (e) => {
    if (!currentCard || !activeBtn) return;
    if (e.target.closest(SELECTOR)) return;
    if (currentCard.contains(e.target)) return;
    close();
  });

  document.addEventListener('keydown', onEsc);
}
