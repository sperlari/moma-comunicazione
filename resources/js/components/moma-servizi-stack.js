import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

/**
 * Servizi – Sticky stack
 * Replica la logica di animazione/scroll della Home Case Studies.
 */
export function initMomaServiziStack() {
  const root = document.querySelector("[data-moma-servizi-stack]");
  if (!root) return;
  if (root.dataset.momaServiziStackInit) return;
  root.dataset.momaServiziStackInit = "1";

  const items = Array.from(root.querySelectorAll(".case-studies_item"));
  if (!items.length) return;

  const ctx = gsap.context(() => {
    items.forEach((item, i) => {
      const sticky = item.querySelector("[data-cs-sticky]");
      const trigger = item.querySelector(".case-card_sticky-trigger");
      if (!sticky || !trigger) return;

      gsap.set(sticky, {
        zIndex: 1000 + i,
        transformOrigin: "50% 50%",
        willChange: "transform, opacity",
      });

      ScrollTrigger.create({
        trigger,
        start: "top top",
        end: "bottom top",
        scrub: true,
        onUpdate: (self) => {
          // progress 0..1 mentre “consumi” lo step corrente
          const p = self.progress;
          const s = gsap.utils.interpolate(1, 0.85, p);
          const y = gsap.utils.interpolate(0, -40, p);
          const o = gsap.utils.interpolate(1, 0.85, p);

          gsap.set(sticky, { scale: s, y, opacity: o });
        },
      });
    });

    // refresh dopo load immagini
    window.addEventListener("load", () => ScrollTrigger.refresh());
  }, root);

  // cleanup (se serve in futuro)
  root.__momaServiziStackCleanup = () => ctx.revert();
}
