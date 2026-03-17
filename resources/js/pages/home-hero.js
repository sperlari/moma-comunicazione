import { gsap } from "gsap";

function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }
function prefersReduced() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches;
}
function isCoarse() {
  return window.matchMedia?.("(pointer: coarse)")?.matches;
}
function isMobileHero() {
  return window.matchMedia?.("(max-width: 768px)")?.matches || isCoarse();
}
function canHoverFine() {
  return window.matchMedia?.("(hover: hover) and (pointer: fine)")?.matches;
}
function isMobileNow() {
  const mq1 = window.matchMedia?.("(max-width: 768px)")?.matches;
  const mq2 = window.matchMedia?.("(pointer: coarse)")?.matches;
  return !!(mq1 || mq2);
}

/* wordmark “scrittura” (parte DOPO la transizione) */
function animateWordmark(el) {
  if (!el || prefersReduced()) return;

  const mode = el.dataset.momaWordmarkMode || "text";
  if (mode === "svg") {
    const svg = el.querySelector("svg");
    if (!svg) return;

    const animationMode = el.dataset.momaWordmarkAnimation || "whole";
    const parts = Array.from(svg.querySelectorAll("path, circle, ellipse, rect, polygon, polyline"));

    gsap.set(el, { autoAlpha: 1 });
    gsap.set(svg, { autoAlpha: 1 });

    if (animationMode === "parts" && parts.length > 1) {
      gsap.set(parts, { autoAlpha: 0, y: 10, transformOrigin: "50% 50%" });

      gsap.timeline()
        .to(parts, {
          autoAlpha: 1,
          y: 0,
          duration: 0.20,
          ease: "power2.out",
          stagger: 0.035,
        });

      return;
    }

    gsap.fromTo(
      svg,
      { autoAlpha: 0, y: 12, scale: 0.96, transformOrigin: "50% 50%" },
      { autoAlpha: 1, y: 0, scale: 1, duration: 0.52, ease: "power2.out" }
    );
    return;
  }

  gsap.set(el, { autoAlpha: 0 });

  const raw = el.textContent.trim();
  const hasDot = raw.endsWith(".");
  const base = hasDot ? raw.slice(0, -1) : raw;

  el.innerHTML = `
    <span class="moma-type" aria-hidden="true"></span>
    ${hasDot ? '<span class="moma-dot" aria-hidden="true">.</span>' : ""}
  `;

  const type = el.querySelector(".moma-type");
  const dotEl = el.querySelector(".moma-dot");

  type.textContent = base;

  gsap.set(type, {
    display: "inline-block",
    overflow: "hidden",
    whiteSpace: "nowrap",
    width: 0,
  });

  if (dotEl) gsap.set(dotEl, { opacity: 0, y: 2 });

  gsap.set(el, { autoAlpha: 1 });

  gsap.timeline()
    .to(type, { width: "auto", duration: 0.68, ease: "power2.out" })
    .to(dotEl, { opacity: 1, y: 0, duration: 0.18, ease: "power2.out" }, "-=0.10");
}

/* Hover CLOU-like più evidente (solo desktop hover fine) */
function attachCardHover(card) {
  if (!canHoverFine() || prefersReduced()) return;

  const img = card.querySelector(".moma-hero__img");
  if (!img) return;

  const quickTo = gsap.quickTo;

  const setRX = quickTo(card, "rotationX", { duration: 0.35, ease: "power3.out" });
  const setRY = quickTo(card, "rotationY", { duration: 0.35, ease: "power3.out" });
  const setZ  = quickTo(card, "z",         { duration: 0.35, ease: "power3.out" });

  const setImgX  = quickTo(img, "x",      { duration: 0.35, ease: "power3.out" });
  const setImgY  = quickTo(img, "y",      { duration: 0.35, ease: "power3.out" });
  const setImgSX = quickTo(img, "scaleX", { duration: 0.35, ease: "power3.out" });
  const setImgSY = quickTo(img, "scaleY", { duration: 0.35, ease: "power3.out" });

  function onMove(e) {
    const r = card.getBoundingClientRect();
    const nx = (e.clientX - r.left) / r.width - 0.5;
    const ny = (e.clientY - r.top) / r.height - 0.5;

    setRX(-ny * 9);
    setRY(nx * 9);
    setZ(30);

    setImgX(nx * 18);
    setImgY(ny * 18);
    setImgSX(1.06);
    setImgSY(1.06);
  }

  function onEnter() {
    gsap.set(card, { transformPerspective: 900, transformStyle: "preserve-3d" });
    gsap.to(card, { scale: 1.02, duration: 0.20, ease: "power2.out" });
  }

  function onLeave() {
    setRX(0); setRY(0); setZ(0);
    setImgX(0); setImgY(0); setImgSX(1); setImgSY(1);
    gsap.to(card, { scale: 1, duration: 0.20, ease: "power2.out" });
  }

  card.addEventListener("pointerenter", onEnter, { passive: true });
  card.addEventListener("pointermove", onMove, { passive: true });
  card.addEventListener("pointerleave", onLeave, { passive: true });
}

export function initHomeHero() {
  const root = document.querySelector(".moma-hero");
  if (!root) return;

  const inner = root.querySelector(".moma-hero__canvas-inner");
  const wordmark = root.querySelector(".moma-hero__wordmark");
  const cards = Array.from(root.querySelectorAll(".moma-hero__card"));
  if (!inner || cards.length === 0) return;

  const reduceMotion = prefersReduced();

  gsap.set(inner, { xPercent: -50, yPercent: -50, x: 0, y: 0 });

  if (wordmark) gsap.set(wordmark, { autoAlpha: 0 });
  gsap.set(cards, { autoAlpha: 0 });

function sizeToPx(size, base, isMobile) {
  if (isMobile) {
    if (size === "lg") return clamp(base * 1.00, 120, 240);
    if (size === "sm") return clamp(base * 0.78, 95, 180);
    return clamp(base * 0.88, 110, 210);
  }
  if (size === "lg") return clamp(base * 1.22, 340, 760);
  if (size === "sm") return clamp(base * 0.86, 220, 460);
  return clamp(base * 1.02, 280, 620);
}

  function layout() {
    const isMobile = isMobileNow();

    gsap.set(inner, { x: 0, y: 0 });
    cards.forEach((c) => gsap.set(c, { x: 0, y: 0 }));

    const heroRect = root.getBoundingClientRect();
    const innerRect = inner.getBoundingClientRect();
    const innerW = innerRect.width;
    const innerH = innerRect.height;

    const visibleX = innerW / 2 - heroRect.width / 2;
    const visibleY = innerH / 2 - heroRect.height / 2;

    // Mobile: niente overscan (tutto deve stare nel viewport)
    const overscanX = heroRect.width  * (isMobile ? 0.12 : 0.16);
    const overscanY = heroRect.height * (isMobile ? 0.12 : 0.16);

    const region = {
      x: clamp(visibleX - overscanX, 0, innerW),
      y: clamp(visibleY - overscanY, 0, innerH),
      w: clamp(heroRect.width + overscanX * 2, 0, innerW),
      h: clamp(heroRect.height + overscanY * 2, 0, innerH),
    };

    let safe = null;
    if (wordmark) {
      const wm = wordmark.getBoundingClientRect();
      const padX = isMobile ? 18 : Math.max(34, heroRect.width * 0.04);
      const padY = isMobile ? 14 : Math.max(22, heroRect.height * 0.03);
      safe = {
        x: (visibleX + (wm.left - heroRect.left)) - padX,
        y: (visibleY + (wm.top - heroRect.top)) - padY,
        w: wm.width + padX * 2,
        h: wm.height + padY * 2,
      };
    }

    // Safe area attiva anche su mobile (evita card sotto al wordmark)

    const n = cards.length;

    let cols;
    if (isMobile) cols = (n <= 6 ? 3 : 4);
    else cols = (n <= 6 ? 4 : 5);

    let rows;
    if (isMobile) rows = Math.ceil(n / cols);
    else rows = 4;

    rows = clamp(rows, isMobile ? 3 : 4, isMobile ? 6 : 4);

    const cellW = region.w / cols;
    const cellH = region.h / rows;

    const base = Math.min(cellW, cellH) * (isMobile ? 0.78 : 1.02);
    const gap = isMobile ? Math.max(10, base * 0.08) : Math.max(30, base * 0.12);

    let cells = [];
    for (let r = 0; r < rows; r++) {
      for (let c = 0; c < cols; c++) {
        const cx = region.x + (c + 0.5) * cellW;
        const cy = region.y + (r + 0.5) * cellH;

        if (safe) {
          const inside = cx >= safe.x && cx <= safe.x + safe.w && cy >= safe.y && cy <= safe.y + safe.h;
          if (inside) continue;
        }

        const jx = (Math.random() - 0.5) * Math.min(14, cellW * 0.10);
        const jy = (Math.random() - 0.5) * Math.min(14, cellH * 0.10);
        cells.push({ cx: cx + jx, cy: cy + jy });
      }
    }

    const center = { x: region.x + region.w / 2, y: region.y + region.h / 2 };

    // FIX: db deve usare b.cy
    cells.sort((a, b) => {
      const da = Math.hypot(a.cx - center.x, a.cy - center.y);
      const db = Math.hypot(b.cx - center.x, b.cy - center.y);
      return da - db;
    });

    const interleaved = [];
    let i = 0, j = cells.length - 1;
    while (i <= j) {
      if (cells[i]) interleaved.push(cells[i++]);
      if (cells[j] && j >= i) interleaved.push(cells[j--]);
    }
    cells = interleaved;

    const placed = [];
    cards.forEach((card, idx) => {
      const sizeKey = card.dataset.size || "md";
      const w = sizeToPx(sizeKey, base, isMobile);
      const h = w;

      card.style.width = `${w}px`;
      card.style.height = `${h}px`;

      let chosen = null;

      for (let k = 0; k < cells.length; k++) {
        const { cx, cy } = cells[k];
        const x = clamp(cx - w / 2, region.x + gap, region.x + region.w - w - gap);
        const y = clamp(cy - h / 2, region.y + gap, region.y + region.h - h - gap);

        const rectPad = { x: x - gap, y: y - gap, w: w + gap * 2, h: h + gap * 2 };

        let ok = true;
        for (const p of placed) {
          const collide = !(
            rectPad.x + rectPad.w <= p.x ||
            rectPad.x >= p.x + p.w ||
            rectPad.y + rectPad.h <= p.y ||
            rectPad.y >= p.y + p.h
          );
          if (collide) { ok = false; break; }
        }
        if (!ok) continue;

        chosen = { x, y };
        placed.push(rectPad);
        break;
      }

      if (!chosen) {
        chosen = {
          x: clamp(region.x + (region.w - w) / 2, region.x + gap, region.x + region.w - w - gap),
          y: clamp(region.y + (region.h - h) / 2, region.y + gap, region.y + region.h - h - gap),
        };
      }

      card.style.left = `${chosen.x}px`;
      card.style.top  = `${chosen.y}px`;

      card.dataset.depth = card.dataset.depth || (0.10 + (idx % 5) * 0.03).toFixed(2);
    });

    root.__pan = { x: overscanX, y: overscanY };
  }

  layout();
  window.addEventListener("resize", () => {
    clearTimeout(window.__momaHeroResizeT);
    window.__momaHeroResizeT = setTimeout(layout, 140);
  });

  cards.forEach(attachCardHover);

  function startAnimations() {
    const isMobile = isMobileNow();

    if (reduceMotion) {
      if (wordmark) gsap.set(wordmark, { autoAlpha: 1 });
      gsap.set(cards, { autoAlpha: 1, scale: 1 });
    } else {
      if (wordmark) animateWordmark(wordmark);

      gsap.fromTo(
        cards,
        { autoAlpha: 0, scale: 0.92 },
        {
          autoAlpha: 1,
          scale: 1,
          duration: 0.72,
          ease: "power3.out",
          delay: 0.05,
          stagger: { from: "random", amount: 0.55 },
          overwrite: "auto",
          immediateRender: false,
        }
      );
    }

    // Desktop: panning; Mobile: NO (devono essere tutte raggiungibili senza “muovere”)
    if (reduceMotion) return;

    const setInnerX = gsap.quickTo(inner, "x", { duration: 0.85, ease: "power3.out" });
    const setInnerY = gsap.quickTo(inner, "y", { duration: 0.85, ease: "power3.out" });

    const cardSetters = cards.map((el) => {
      const d = parseFloat(el.dataset.depth || "0.14");
      return {
        d,
        setX: gsap.quickTo(el, "x", { duration: 0.85, ease: "power3.out" }),
        setY: gsap.quickTo(el, "y", { duration: 0.85, ease: "power3.out" }),
      };
    });

    function resetMove() {
      setInnerX(0); setInnerY(0);
      cardSetters.forEach((s) => { s.setX(0); s.setY(0); });
    }

    function onMove(e) {
      const r = root.getBoundingClientRect();
      const nx = (e.clientX - r.left) / r.width - 0.5;
      const ny = (e.clientY - r.top) / r.height - 0.5;

      const pan = root.__pan || { x: r.width * 0.16, y: r.height * 0.16 };

      setInnerX(-nx * (pan.x * 2));
      setInnerY(-ny * (pan.y * 2));

      const micro = 38;
      cardSetters.forEach((s) => {
        s.setX(nx * micro * s.d);
        s.setY(ny * micro * s.d);
      });
    }

// Desktop (mouse): move libero
root.addEventListener("pointerenter", (e) => {
  if (e.pointerType !== "mouse") return;
  resetMove();
}, { passive: true });

root.addEventListener("pointermove", (e) => {
  if (e.pointerType === "touch" || e.pointerType === "pen") return; // touch gestito sotto
  onMove(e);
}, { passive: true });

root.addEventListener("pointerleave", (e) => {
  if (e.pointerType !== "mouse") return;
  resetMove();
}, { passive: true });

// Mobile (touch/pen): drag while pressed (direzione naturale)
let dragging = false;
let dragCandidate = false;
let lockedDir = null; // "h" | "v"
let startX = 0;
let startY = 0;
let baseInnerX = 0;
let baseInnerY = 0;

const LOCK_THRESHOLD = 10;

root.addEventListener("pointerdown", (e) => {
  if (e.pointerType !== "touch" && e.pointerType !== "pen") return;
  dragCandidate = true;
  dragging = false;
  lockedDir = null;
  startX = e.clientX;
  startY = e.clientY;
  baseInnerX = gsap.getProperty(inner, "x") || 0;
  baseInnerY = gsap.getProperty(inner, "y") || 0;
  try { root.setPointerCapture(e.pointerId); } catch {}
}, { passive: true });

root.addEventListener("pointermove", (e) => {
  if (!dragCandidate) return;

  const dx = e.clientX - startX;
  const dy = e.clientY - startY;

  if (!lockedDir && (Math.abs(dx) > LOCK_THRESHOLD || Math.abs(dy) > LOCK_THRESHOLD)) {
    lockedDir = Math.abs(dx) > Math.abs(dy) ? "h" : "v";
    if (lockedDir === "h") dragging = true;
  }

  if (lockedDir === "v") {
    // lascia scorrere la pagina
    dragCandidate = false;
    try { root.releasePointerCapture(e.pointerId); } catch {}
    return;
  }

  if (!dragging) return;

  e.preventDefault?.();

  const r = root.getBoundingClientRect();
  const pan = root.__pan || { x: r.width * 0.16, y: r.height * 0.16 };

  // movimento naturale: dx positivo => canvas verso destra
  const maxX = pan.x * 2;
  const maxY = pan.y * 2;
  const nx = clamp(baseInnerX + dx, -maxX, maxX);
  const ny = clamp(baseInnerY + dy * 0.6, -maxY, maxY);

  setInnerX(nx);
  setInnerY(ny);

  const micro = 34;
  cardSetters.forEach((s) => {
    s.setX((dx * 0.12) * s.d);
    s.setY((dy * 0.08) * s.d);
  });
}, { passive: false });

const endDrag = (e) => {
  if (!dragCandidate) return;
  dragCandidate = false;
  dragging = false;
  lockedDir = null;
  try { root.releasePointerCapture(e.pointerId); } catch {}

  // Mobile UX: dopo il drag NON deve tornare alla posizione iniziale,
  // altrimenti diventa impossibile tappare/cliccare le card.
  // Manteniamo il pan del canvas e azzeriamo solo il micro-parallax delle card.
  cardSetters.forEach((s) => { s.setX(0); s.setY(0); });
};

root.addEventListener("pointerup", endDrag, { passive: true });
root.addEventListener("pointercancel", endDrag, { passive: true });
  }

  let started = false;
  const handler = () => {
    if (started) return;
    started = true;
    startAnimations();
  };

  if (window.__momaPageEnterFired) handler();
  else document.addEventListener("moma:page-enter", handler, { once: true });
}
