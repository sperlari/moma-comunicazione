import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

function prefersReduced() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches;
}

function linkLenisToScrollTrigger() {
  const lenis = window.__momaLenis;
  if (!lenis) return;
  if (!window.__momaLenisSTLinked && typeof lenis.on === "function") {
    lenis.on("scroll", ScrollTrigger.update);
    window.__momaLenisSTLinked = true;
  }
}

function runAfterPageEnter(cb) {
  if (window.__momaPageEnterFired) cb();
  else document.addEventListener("moma:page-enter", cb, { once: true });
}

export function initMomaReveal() {
  const items = Array.from(document.querySelectorAll("[data-moma-reveal]"));
  if (!items.length) return;

  runAfterPageEnter(() => {
    if (prefersReduced()) return;

    linkLenisToScrollTrigger();

    // cleanup vecchi trigger (page transition)
    ScrollTrigger.getAll().forEach((st) => {
      const id = st?.vars?.id;
      if (typeof id === "string" && id.startsWith("moma-reveal-")) st.kill(true);
    });

    items.forEach((el, i) => {
      const y = Number(el.dataset.revealY || 24);
      const duration = Number(el.dataset.revealDuration || 0.85);
      const delay = Number(el.dataset.revealDelay || 0);
      const start = el.dataset.revealStart || "top 85%";
      const ease = el.dataset.revealEase || "power3.out";
      const once = (el.dataset.revealOnce ?? "1") !== "0";

      gsap.fromTo(
        el,
        { y, autoAlpha: 0 },
        {
          y: 0,
          autoAlpha: 1,
          duration,
          delay,
          ease,
          clearProps: "transform,opacity,visibility",
          scrollTrigger: {
            id: `moma-reveal-${i}`,
            trigger: el,
            start,
            once,
          },
        }
      );
    });

    ScrollTrigger.refresh();
  });
}