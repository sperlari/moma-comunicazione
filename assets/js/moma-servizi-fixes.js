(function(){
  function clamp(v, min, max){ return Math.max(min, Math.min(max, v)); }

  function bindServiceNav(){
    document.querySelectorAll('[data-srv-nav]').forEach((a) => {
      if (a.dataset.momaSrvBound === '1') return;
      a.dataset.momaSrvBound = '1';
      a.addEventListener('click', (e) => {
        const href = a.getAttribute('href') || '';
        if (!href.startsWith('#')) return;
        const target = document.querySelector(href);
        if (!target) return;
        e.preventDefault();
        if (window.history && window.history.replaceState) window.history.replaceState(null, '', href);
        const lenis = window.__momaLenis;
        if (lenis && typeof lenis.scrollTo === 'function') lenis.scrollTo(target, { offset: 0, duration: 1.1, immediate: false });
        else target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  function enablePopoverDrag(panel, handle){
    if (!panel || !handle || handle.dataset.momaDragBound === '1') return;
    handle.dataset.momaDragBound = '1';
    let startX = 0, startY = 0, originLeft = 0, originTop = 0, dragging = false;

    const onMove = (ev) => {
      if (!dragging) return;
      const rect = panel.getBoundingClientRect();
      panel.style.left = clamp(originLeft + (ev.clientX - startX), 16, window.innerWidth - rect.width - 16) + 'px';
      panel.style.top = clamp(originTop + (ev.clientY - startY), 16, window.innerHeight - rect.height - 16) + 'px';
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

  function patchPopover(panel){
    if (!panel || panel.dataset.momaPatched === '1') return;
    panel.dataset.momaPatched = '1';
    const close = panel.querySelector('.moma-glossary-popover__close');
    if (!close) return;
    if (panel.querySelector('.moma-glossary-popover__drag')) return;

    const controls = document.createElement('div');
    controls.className = 'moma-glossary-popover__controls';
    Object.assign(controls.style, { position:'absolute', top:'10px', right:'10px', display:'flex', gap:'8px' });

    const drag = document.createElement('button');
    drag.type = 'button';
    drag.className = 'moma-glossary-popover__drag';
    drag.setAttribute('aria-label', 'Sposta card');
    drag.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12 3l2.8 2.8-1.4 1.4L13 6.8V10h3.2l-.4-.4 1.4-1.4L20 11l-2.8 2.8-1.4-1.4.4-.4H13v3.2l.4-.4 1.4 1.4L12 21l-2.8-2.8 1.4-1.4.4.4V13H7.8l.4.4-1.4 1.4L4 12l2.8-2.8 1.4 1.4-.4.4H11V6.8l-.4.4-1.4-1.4L12 3z" fill="currentColor"/></svg>';
    Object.assign(drag.style, { width:'36px', height:'36px', borderRadius:'999px', border:'1px solid rgba(24,8,90,.15)', background:'rgba(255,255,255,.75)', color:'#18085a', cursor:'grab', display:'inline-flex', alignItems:'center', justifyContent:'center', transition:'transform .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease' });
    ['mouseenter','mouseleave'].forEach(function(evt){
      drag.addEventListener(evt, function(){
        if (!window.gsap) return;
        if (evt==='mouseenter') window.gsap.to(drag, { scale:1.08, backgroundColor:'rgba(73,28,170,.12)', borderColor:'rgba(73,28,170,.28)', boxShadow:'0 8px 18px rgba(73,28,170,.14)', duration:0.18, overwrite:'auto' });
        else window.gsap.to(drag, { scale:1, backgroundColor:'rgba(255,255,255,.75)', borderColor:'rgba(24,8,90,.15)', boxShadow:'0 0 0 rgba(73,28,170,0)', duration:0.18, overwrite:'auto' });
      });
    });

    close.parentNode.insertBefore(controls, close);
    controls.appendChild(drag);
    controls.appendChild(close);
    Object.assign(close.style, { position:'static', margin:0 });
    const title = panel.querySelector('.moma-glossary-popover__title');
    if (title) title.style.paddingRight = '86px';
    enablePopoverDrag(panel, drag);
  }

  function observePopovers(){
    const scan = () => document.querySelectorAll('.moma-glossary-popover__panel').forEach(patchPopover);
    scan();
    const mo = new MutationObserver(scan);
    mo.observe(document.body, { childList:true, subtree:true });
  }

  function initProjectCards(){
    if (!window.gsap) return;
    document.querySelectorAll('.moma-project-card').forEach(function(card){
      if (card.dataset.momaProjectsBound === '1') return;
      card.dataset.momaProjectsBound = '1';
      const media = card.querySelector('.moma-project-card__media');
      const img = card.querySelector('.moma-project-card__img');
      const cta = card.querySelector('.moma-project-card__cta');
      if (!media || !img) return;
      const tl = window.gsap.timeline({ paused:true, defaults:{ duration:0.24, ease:'power2.out', overwrite:'auto' } });
      tl.to(card, { backgroundColor:'#f0ece8', borderRadius:24, boxShadow:'0 12px 30px rgba(24,8,90,.08)' }, 0)
        .to(media, { borderTopLeftRadius:24, borderTopRightRadius:24, borderBottomLeftRadius:0, borderBottomRightRadius:0 }, 0)
        .to(img, { borderTopLeftRadius:24, borderTopRightRadius:24, borderBottomLeftRadius:0, borderBottomRightRadius:0, scale:1.02 }, 0)
        .to(cta, { y:-2, boxShadow:'0 14px 26px rgba(43,30,114,0.26)' }, 0);
      function enter(){ card.classList.add('is-hover'); tl.play(); }
      function leave(){ card.classList.remove('is-hover'); tl.reverse(); }
      card.addEventListener('mouseenter', enter);
      card.addEventListener('mouseleave', leave);
      card.addEventListener('focusin', enter);
      card.addEventListener('focusout', function(e){ if (!card.contains(e.relatedTarget)) leave(); });
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    bindServiceNav();
    observePopovers();
    initProjectCards();
  });
})();
