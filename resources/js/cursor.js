import { gsap } from "gsap";

export function initMomaCursor() {
  const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const isTouch = window.matchMedia("(pointer: coarse)").matches;
  if (prefersReduced || isTouch) return;

  const cursorEl = document.getElementById("moma-cursor"); // immagine (solo in aree)
  const dotEl = document.getElementById("moma-dot");       // dot (sempre, vicino al cursore di sistema)

  if (!cursorEl && !dotEl) return;

  // ----------------------
  // DOT cursor (globale)
  // ----------------------
  let dotVisible = false;
  const showDot = dotEl
    ? () => {
        if (dotVisible) return;
        dotVisible = true;
        gsap.to(dotEl, {
          autoAlpha: 1,
          duration: 0.16,
          ease: "power2.out",
          overwrite: "auto",
        });
      }
    : () => {};

  const hideDot = dotEl
    ? () => {
        if (!dotVisible) return;
        dotVisible = false;
        gsap.to(dotEl, {
          autoAlpha: 0,
          duration: 0.12,
          ease: "power2.in",
          overwrite: "auto",
        });
      }
    : () => {};

  const dotXTo = dotEl ? gsap.quickTo(dotEl, "x", { duration: 0.18, ease: "power3" }) : null;
  const dotYTo = dotEl ? gsap.quickTo(dotEl, "y", { duration: 0.18, ease: "power3" }) : null;

  if (dotEl) {
    document.documentElement.classList.add("has-moma-dot");
    gsap.set(dotEl, { autoAlpha: 0, scale: 1 });
  }

  // ----------------------
  // IMAGE cursor (solo in aree)
  // ----------------------
  const imgEl = cursorEl ? cursorEl.querySelector("img") : null;

  if (cursorEl && imgEl) {
    // Stato iniziale
    gsap.set(cursorEl, { autoAlpha: 0, scale: 0 });
    gsap.set(imgEl, { rotation: 0, transformOrigin: "50% 50%" });
  }

  // Quick setters (immagine)
  const xTo = cursorEl ? gsap.quickTo(cursorEl, "x", { duration: 0.3, ease: "power3" }) : null;
  const yTo = cursorEl ? gsap.quickTo(cursorEl, "y", { duration: 0.3, ease: "power3" }) : null;
  const rTo = imgEl ? gsap.quickTo(imgEl, "rotation", { duration: 0.35, ease: "power3" }) : null;

  let activeScope = null;
  let lastX = 0;
  let lastY = 0;
  let rot = 0;

  // Per gestire page transition (DOM rimpiazzato)
  const bound = new Map(); // scope -> handlers

  function setFromScope(scope) {
    if (!cursorEl || !imgEl) return;

    const url = scope.getAttribute("data-cursor-img") || "";
    if (url) imgEl.src = url;
  }

  function showImageCursor() {
    if (!cursorEl) return;
    gsap.to(cursorEl, {
      autoAlpha: 1,
      scale: 1,
      duration: 0.22,
      ease: "power3.out",
      delay: 0.16,
      overwrite: "auto",
    });
  }

  function hideImageCursor() {
    if (!cursorEl) return;
    gsap.to(cursorEl, {
      autoAlpha: 0,
      scale: 0,
      duration: 0.12,
      ease: "power2.in",
      overwrite: "auto",
    });
  }

  let imgVisible = false;

  function isUiTarget(t, scope) {
    if (!t || !scope) return false;
    const el = t.closest?.('[data-cursor-ui="1"],button,input,select,textarea,[role="button"]');
    return !!(el && scope.contains(el) && el !== scope);
  }

  function ensureImageVisible(visible) {
    if (!cursorEl) return;
    if (visible && !imgVisible) {
      imgVisible = true;
      showImageCursor();
    } else if (!visible && imgVisible) {
      imgVisible = false;
      hideImageCursor();
    }
  }

  function onEnter(e) {
    if (!cursorEl || !imgEl) return;

    const scope = e.currentTarget;
    activeScope = scope;

    setFromScope(scope);

    // reset rotazione ogni volta che entri
    rot = 0;
    rTo && rTo(0);

    lastX = e.clientX;
    lastY = e.clientY;
    xTo && xTo(e.clientX);
    yTo && yTo(e.clientY);

    scope.classList.add("moma-cursor-scope--active");
    document.documentElement.classList.add("has-moma-cursor");

    // dot resta sempre visibile (cursore di sistema + dot). L'immagine appare solo se non siamo sopra UI.
    ensureImageVisible(!isUiTarget(e.target, scope));
  }

  function onMove(e) {
    // il DOT segue comunque in global pointermove (sotto)
    if (!activeScope || !cursorEl) return;

    // Se siamo su CTA/UI, nascondi l'immagine (così si vede il cursore "clic")
    ensureImageVisible(!isUiTarget(e.target, activeScope));

    const x = e.clientX;
    const y = e.clientY;

    xTo && xTo(x);
    yTo && yTo(y);

    // rotazione basata sulla velocità del mouse
    const dx = x - lastX;
    const dy = y - lastY;
    const speed = Math.sqrt(dx * dx + dy * dy);

    const dir = dx >= 0 ? 1 : -1;
    rot += speed * 0.55 * dir;
    rTo && rTo(rot);

    lastX = x;
    lastY = y;
  }

  function onLeave(e) {
    const scope = e.currentTarget;
    if (activeScope !== scope) return;

    scope.classList.remove("moma-cursor-scope--active");
    activeScope = null;

    ensureImageVisible(false);
    document.documentElement.classList.remove("has-moma-cursor");
  }

  function unbindAll() {
    bound.forEach((handlers, scope) => {
      scope.removeEventListener("pointerenter", handlers.enter);
      scope.removeEventListener("pointermove", handlers.move);
      scope.removeEventListener("pointerleave", handlers.leave);
    });
    bound.clear();
  }

  function bindScopes() {
    unbindAll();

    // reset safe
    activeScope = null;
    hideImageCursor();
    imgVisible = false;
    document.documentElement.classList.remove("has-moma-cursor");

    const scopes = gsap.utils.toArray("[data-cursor-scope='1']");
    scopes.forEach((scope) => {
      const handlers = { enter: onEnter, move: onMove, leave: onLeave };
      scope.addEventListener("pointerenter", handlers.enter);
      scope.addEventListener("pointermove", handlers.move);
      scope.addEventListener("pointerleave", handlers.leave);
      bound.set(scope, handlers);
    });
  }

  // Global move: DOT segue sempre (tranne quando siamo in area con immagine)
  if (dotEl && dotXTo && dotYTo) {
    window.addEventListener("pointermove", (e) => {
      // leggero offset: vicino alla punta del cursore di sistema
      dotXTo(e.clientX + 10);
      dotYTo(e.clientY + 10);
      showDot();
    });

    window.addEventListener("pointerleave", () => {
      hideDot();
    });

    // prima comparsa non immediata (evita flash a 0,0)
    // il dot apparirà al primo movimento
  }

  bindScopes();
  document.addEventListener("moma:page-enter", bindScopes);
}
