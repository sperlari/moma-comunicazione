import { gsap } from "gsap";

function prefersReduced() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches;
}

function setSliderHeight(sliderEl, slides) {
  if (!sliderEl || !slides?.length) return;

  slides.forEach((s) => {
    s.style.position = "static";
    s.style.opacity = "1";
    s.style.pointerEvents = "auto";
    s.style.transform = "none";
  });

  const h = Math.max(...slides.map((s) => s.offsetHeight || 0));
  sliderEl.style.minHeight = `${Math.max(h, 1)}px`;

  slides.forEach((s) => {
    s.style.position = "";
    s.style.opacity = "";
    s.style.pointerEvents = "";
    s.style.transform = "";
  });
}

export function initMomaServiziPageSlider() {
  const section = document.querySelector("[data-moma-servizi-page]");
  if (!section) return;

  const slider = section.querySelector("[data-srv-page-slider]");
  const track = section.querySelector("[data-srv-track]");
  const slides = Array.from(section.querySelectorAll("[data-srv-slide]"));
  const dots = section.querySelector("[data-srv-dots]");
  if (!slider || !track || slides.length === 0) return;

  const reduce = prefersReduced();

  const recalc = () => setSliderHeight(slider, slides);
  recalc();
  window.addEventListener("resize", recalc);
  window.addEventListener("load", recalc, { once: true });

  // init state
  gsap.set(slides, { autoAlpha: 0, xPercent: 110, pointerEvents: "none" });
  gsap.set(slides[0], { autoAlpha: 1, xPercent: 0, pointerEvents: "auto" });

  let current = 0;
  let isTransitioning = false;

  const dotBtns = [];

  const updateDots = () => {
    dotBtns.forEach((b, i) => {
      b.setAttribute("aria-selected", i === current ? "true" : "false");
      b.tabIndex = i === current ? 0 : -1;
    });
  };

  const buildDots = () => {
    if (!dots) return;
    dots.innerHTML = "";
    slides.forEach((_, i) => {
      const b = document.createElement("button");
      b.type = "button";
      b.className = "moma-servizi-slider__dot"; // riusa stile dot esistente
      b.setAttribute("aria-label", `Vai alla slide ${i + 1}`);
      b.addEventListener("click", () => goTo(i));
      dots.appendChild(b);
      dotBtns.push(b);
    });
  };

  const wrapIndex = (i) => {
    const n = slides.length;
    return (i % n + n) % n;
  };

  const go = (dir) => {
    if (slides.length < 2) return;
    if (isTransitioning) return;

    const next = wrapIndex(current + dir);
    if (next === current) return;

    const curSlide = slides[current];
    const nextSlide = slides[next];

    const inFrom = dir === 1 ? 110 : -110;
    const outTo = dir === 1 ? -110 : 110;

    gsap.set(nextSlide, { autoAlpha: 1, xPercent: inFrom, pointerEvents: "none" });
    gsap.set(curSlide, { pointerEvents: "none" });

    isTransitioning = true;

    gsap.timeline({
      defaults: { ease: "power1.inOut" },
      onComplete: () => {
        gsap.set(curSlide, { autoAlpha: 0, xPercent: inFrom });
        gsap.set(nextSlide, { pointerEvents: "auto" });
        current = next;
        updateDots();
        isTransitioning = false;
      },
    })
    .to(curSlide, { xPercent: outTo, duration: reduce ? 0 : 0.85 }, 0)
    .to(nextSlide, { xPercent: 0, duration: reduce ? 0 : 0.85 }, 0);
  };

  const goTo = (target) => {
    const n = slides.length;
    const t = ((target % n) + n) % n;
    if (t === current) return;
    const diffFwd = (t - current + n) % n;
    const diffBwd = (current - t + n) % n;
    go(diffFwd <= diffBwd ? 1 : -1);
    // se distanza > 1, richiama finché arrivi
    const steps = Math.min(diffFwd, diffBwd);
    let left = steps - 1;
    const dir = diffFwd <= diffBwd ? 1 : -1;

    const tick = () => {
      if (left <= 0) return;
      left -= 1;
      go(dir);
      requestAnimationFrame(() => setTimeout(tick, reduce ? 0 : 120));
    };
    tick();
  };

  // espongo API per i pulsanti sopra
  slider.__momaGoTo = goTo;

  buildDots();
  updateDots();
}