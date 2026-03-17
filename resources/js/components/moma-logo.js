import { gsap } from "gsap";

export function initMomaLogo() {
  const logo = document.querySelector(".moma-logo");
  if (!logo) return;

  const word = logo.querySelector(".moma-logo__word");
  const mark = logo.querySelector(".moma-logo__mark");
  const aDot = logo.querySelector(".moma-logo__aDot");
  const aEl  = logo.querySelector(".moma-logo__a");

  if (!word || !mark || !aDot || !aEl) return;

  gsap.set(mark, { autoAlpha: 0, scale: 0.7, yPercent: -50, transformOrigin: "50% 50%" });
  gsap.set(aEl,  { clipPath: "inset(0 0 100% 0)", willChange: "clip-path" }); // “scrittura” dall’alto
  gsap.set(aDot, { autoAlpha: 0, scale: 0.5, transformOrigin: "50% 50%" });
  gsap.set(word, { x: 0, autoAlpha: 1 });

  const tl = gsap.timeline({ paused: true });

  // word scorre e si spegne, ma resta clippata dal wrapper
  tl.to(word, { x: -44, autoAlpha: 0, duration: 0.18, ease: "power2.inOut" }, 0);

  // cerchio zoom-in
  tl.to(mark, { autoAlpha: 1, scale: 1, duration: 0.16, ease: "power3.out" }, 0.10);

  // “disegno” della a: reveal con clipPath (effetto pulito)
  tl.to(aEl, { clipPath: "inset(0 0 0% 0)", duration: 0.32, ease: "power2.out" }, 0.20);

  // punto
  tl.to(aDot, { autoAlpha: 1, scale: 1, duration: 0.12, ease: "power2.out" }, 0.48);

  logo.addEventListener("mouseenter", () => tl.play());
  logo.addEventListener("mouseleave", () => tl.reverse());
}