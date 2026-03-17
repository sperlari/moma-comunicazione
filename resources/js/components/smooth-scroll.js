import Lenis from "@studio-freight/lenis";

let lenisInstance = null;

export function initSmoothScroll() {
  // evita doppie init
  if (lenisInstance) return lenisInstance;

  const reduceMotion =
    window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches;

  // Se reduce motion: niente smooth scroll
  if (reduceMotion) return null;

  // Config identica allo snippet CLOU
  const lenis = new Lenis({
    lerp: 0.1,
    wheelMultiplier: 0.5,
    gestureOrientation: "vertical",
    normalizeWheel: false,
    smoothTouch: false,
  });

  function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  }
  requestAnimationFrame(raf);

  // Hook opzionali "come CLOU" ma vanilla (no jQuery)
  document.querySelectorAll("[data-lenis-start]").forEach((el) => {
    el.addEventListener("click", () => lenis.start());
  });

  document.querySelectorAll("[data-lenis-stop]").forEach((el) => {
    el.addEventListener("click", () => lenis.stop());
  });

  document.querySelectorAll("[data-lenis-toggle]").forEach((el) => {
    el.addEventListener("click", () => {
      el.classList.toggle("stop-scroll");
      if (el.classList.contains("stop-scroll")) lenis.stop();
      else lenis.start();
    });
  });

  // Espongo per debug (se serve)
  window.__momaLenis = lenis;

  lenisInstance = lenis;
  return lenisInstance;
}
