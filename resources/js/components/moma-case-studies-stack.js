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

export function initMomaCaseStudies() {
  const root = document.querySelector("[data-moma-case-studies]");
  if (!root) return;

  const reduce = prefersReduced();
  const items = Array.from(root.querySelectorAll(".case-studies_item"));

  runAfterPageEnter(() => {
    linkLenisToScrollTrigger();

    // kill SOLO i trigger di questa sezione (back/forward / hot reload)
    ScrollTrigger.getAll().forEach((st) => {
      const id = st?.vars?.id;
      if (typeof id === "string" && id.startsWith("moma-cs-")) st.kill(true);
    });

    items.forEach((item, idx) => {
      const trigger = item.querySelector(".case-card_sticky-trigger");
      const sticky  = item.querySelector("[data-cs-sticky]");
      if (!trigger || !sticky) return;

      gsap.set(sticky, {
        opacity: 1,
        scale: 1,
        transformOrigin: "50% 50%",
        pointerEvents: "auto",
      });

      if (reduce) return;

      // Identico comportamento “Clou”:
      // - quando arriva al punto di stop: resta stabile
      // - poi, continuando lo scroll: rimpicciolisce gradualmente
      // - nel finale: svanisce, mentre la successiva prende il posto
      const isLast = idx === (items.length - 1);

      const tl = gsap.timeline({
        scrollTrigger: {
          id: `moma-cs-${idx}`,
          trigger,
          start: "top top",
          end: "bottom top",
          scrub: true,
          invalidateOnRefresh: true,
          onUpdate: (self) => {
            sticky.style.pointerEvents = self.progress > 0.97 ? "none" : "auto";

            // SOLO ultimo: quando sta uscendo, deve finire "dietro" al contenuto successivo
            if (isLast) {
              if (self.progress > 0.82) {
                item.style.zIndex = "0";
                sticky.style.zIndex = "0";
              } else {
                item.style.zIndex = "";
                sticky.style.zIndex = "";
              }
            }
          },
        },
      });

      tl.to(sticky, { scale: 1, opacity: 1, duration: 0.06, ease: "none" });
      tl.to(sticky, { scale: 0.82, opacity: 0.55, duration: 0.74, ease: "none" });
      tl.to(sticky, { scale: 0.80, opacity: 0, duration: 0.20, ease: "none" });
    });

    // quando immagini/font finiscono di stabilizzarsi
    window.addEventListener("load", () => ScrollTrigger.refresh(), { once: true });
    ScrollTrigger.refresh();
  });
}