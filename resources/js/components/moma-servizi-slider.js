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

export function initMomaServiziSlider() {
  const section = document.querySelector("[data-moma-servizi-slider]");
  if (!section) return;

  const slider = section.querySelector("[data-srv-slider]");
  const track = section.querySelector("[data-srv-track]");
  const slides = Array.from(section.querySelectorAll("[data-srv-slide]"));
  const dots = section.querySelector("[data-srv-dots]");
  if (!slider || !track || slides.length === 0) return;

  const sliderBlock = slider;
  const reduce = prefersReduced();

  runAfterPageEnter(() => {
    linkLenisToScrollTrigger();

    ScrollTrigger.getAll().forEach((st) => {
      const id = st?.vars?.id;
      if (typeof id === "string" && id.startsWith("moma-srv-")) st.kill(true);
    });

    const recalc = () => setSliderHeight(sliderBlock, slides);
    recalc();
    window.addEventListener("resize", recalc);
    window.addEventListener(
      "load",
      () => {
        recalc();
        ScrollTrigger.refresh();
      },
      { once: true }
    );

    if (!reduce) {
      gsap.from(sliderBlock, {
        y: 70,
        opacity: 0,
        duration: 0.9,
        ease: "power3.out",
        scrollTrigger: {
          id: "moma-srv-slider-in",
          trigger: sliderBlock,
          start: "top 85%",
          once: true,
        },
      });
    }

    const HOLD = 3.6;
    const DUR = 0.85;

    // standard slider: le non-attive stanno a destra
    gsap.set(slides, { autoAlpha: 0, xPercent: 110, pointerEvents: "none" });
    gsap.set(slides[0], { autoAlpha: 1, xPercent: 0, pointerEvents: "auto" });

    let current = 0;
    let isHovered = false;
    let timer = null;
    let transitionTl = null;
    let brakeTween = null;

    let isTransitioning = false;
    let queued = { dir: 0, left: 0 };

    // Dots
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
        b.className = "moma-servizi-slider__dot";
        b.setAttribute("role", "tab");
        b.setAttribute("aria-label", `Vai alla slide ${i + 1}`);
        b.setAttribute("aria-selected", i === 0 ? "true" : "false");
        b.tabIndex = i === 0 ? 0 : -1;
        b.addEventListener("click", () => goTo(i));
        dots.appendChild(b);
        dotBtns.push(b);
      });
    };

    const goSteps = (steps, dir) => {
      if (steps <= 0) return;
      queued = { dir, left: Math.max(0, steps - 1) };
      go(dir);
    };

    const goTo = (target) => {
      if (slides.length < 2) return;
      const n = slides.length;
      const t = ((target % n) + n) % n;
      if (t === current) return;
      const diffFwd = (t - current + n) % n;
      const diffBwd = (current - t + n) % n;
      const dir = diffFwd <= diffBwd ? 1 : -1;
      const steps = Math.min(diffFwd, diffBwd);
      goSteps(steps, dir);
    };

    const scheduleNext = () => {
      if (reduce || isHovered || slides.length < 2) return;
      if (timer) timer.kill();
      timer = gsap.delayedCall(HOLD, () => go(1));
    };

    const wrapIndex = (i) => {
      const n = slides.length;
      return (i % n + n) % n;
    };

    const hardStopTimer = () => {
      if (timer) timer.kill();
      timer = null;
    };

    const go = (dir) => {
      if (slides.length < 2) return;
      hardStopTimer();

      if (isTransitioning) {
        // se arriva un input mentre transiziona, sovrascrive la direzione e conta
        queued.dir = dir;
        queued.left = Math.max(queued.left, 1);
        if (transitionTl) {
          gsap.to(transitionTl, {
            timeScale: 1.15,
            duration: 0.12,
            ease: "power2.out",
            overwrite: true,
          });
        }
        return;
      }

      const next = wrapIndex(current + dir);
      if (next === current) return;

      const curSlide = slides[current];
      const nextSlide = slides[next];

      if (brakeTween) brakeTween.kill();
      if (transitionTl) transitionTl.kill();

      // dir=-1 (PREV): il contenuto scorre verso destra
      const inFrom = dir === 1 ? 110 : -110;
      const outTo  = dir === 1 ? -110 : 110;

      gsap.set(nextSlide, { autoAlpha: 1, xPercent: inFrom, pointerEvents: "none" });
      gsap.set(curSlide, { pointerEvents: "none" });

      isTransitioning = true;

      transitionTl = gsap.timeline({
        defaults: { ease: "power1.inOut" },
        onComplete: () => {
          gsap.set(curSlide, { autoAlpha: 0, xPercent: inFrom });
          gsap.set(nextSlide, { pointerEvents: "auto" });
          current = next;

          updateDots();

          isTransitioning = false;

          if (queued.left > 0 && queued.dir) {
            queued.left -= 1;
            go(queued.dir);
            return;
          }

          queued = { dir: 0, left: 0 };

          scheduleNext();
        },
      });

      transitionTl
        .to(curSlide, { xPercent: outTo, duration: DUR }, 0)
        .to(nextSlide, { xPercent: 0, duration: DUR }, 0);
    };

    const smoothPause = () => {
      if (reduce) return;
      hardStopTimer();
      if (!transitionTl || !transitionTl.isActive()) return;
      if (brakeTween) brakeTween.kill();
      brakeTween = gsap.to(transitionTl, {
        timeScale: 0,
        duration: 0.28,
        ease: "power3.out",
        onComplete: () => transitionTl.pause(),
      });
    };

    const smoothPlay = () => {
      if (reduce) return;
      if (transitionTl && transitionTl.paused()) transitionTl.play();
      if (brakeTween) brakeTween.kill();
      if (transitionTl) {
        brakeTween = gsap.to(transitionTl, {
          timeScale: 1,
          duration: 0.28,
          ease: "power3.out",
        });
      }
      scheduleNext();
    };

    // Hover sullo slider: pausa autoplay
    slider.addEventListener(
      "pointerenter",
      () => {
        isHovered = true;
        smoothPause();
      },
      { passive: true }
    );

    slider.addEventListener(
      "pointerleave",
      () => {
        isHovered = false;
        smoothPlay();
      },
      { passive: true }
    );

    buildDots();
    updateDots();

    // =========================
// SWIPE (mouse + touch) – click-safe
// =========================
const SWIPE_THRESHOLD = 60;
const LOCK_THRESHOLD = 10;

let swipeCandidate = false;
let swipeActive = false;
let startX = 0;
let startY = 0;
let lastX = 0;
let lastY = 0;
let lockedDir = null; // "h" | "v"
let suppressClickUntil = 0;
let activePointerId = null;

sliderBlock.addEventListener(
  "click",
  (e) => {
    if (Date.now() < suppressClickUntil) {
      e.preventDefault();
      e.stopPropagation();
    }
  },
  true
);

const isInteractive = (t) =>
  !!t?.closest?.("a,button,input,textarea,select,label,[role='button']");

const onPointerDown = (e) => {
  if (reduce || slides.length < 2) return;
  if (!e.isPrimary) return;

  // ✅ se clicchi su link/CTA/qualsiasi elemento interattivo, non attivare swipe
  if (isInteractive(e.target)) return;

  swipeCandidate = true;
  swipeActive = false;
  lockedDir = null;

  activePointerId = e.pointerId;
  startX = lastX = e.clientX;
  startY = lastY = e.clientY;
};

const onPointerMove = (e) => {
  if (!swipeCandidate) return;

  lastX = e.clientX;
  lastY = e.clientY;

  const dx = lastX - startX;
  const dy = lastY - startY;

  if (!lockedDir && (Math.abs(dx) > LOCK_THRESHOLD || Math.abs(dy) > LOCK_THRESHOLD)) {
    lockedDir = Math.abs(dx) > Math.abs(dy) ? "h" : "v";

    if (lockedDir === "h") {
      swipeActive = true;

      // cattura e pausa solo quando lo swipe è davvero orizzontale
      slider.setPointerCapture?.(activePointerId);

      isHovered = true;
      slider.classList.add("is-hovered");
      smoothPause();
    }
  }

  if (lockedDir === "h") {
    e.preventDefault?.();
  }
};

const onPointerUp = () => {
  if (!swipeCandidate) return;
  swipeCandidate = false;

  if (!swipeActive || lockedDir !== "h") {
    lockedDir = null;
    return;
  }

  const dx = lastX - startX;
  const dy = lastY - startY;

  isHovered = false;
  slider.classList.remove("is-hovered");
  smoothPlay();

  if (Math.abs(dx) >= SWIPE_THRESHOLD && Math.abs(dx) > Math.abs(dy)) {
    suppressClickUntil = Date.now() + 450;
    if (dx < 0) go(1);
    else go(-1);
  }

  lockedDir = null;
  swipeActive = false;
};

sliderBlock.addEventListener("pointerdown", onPointerDown, { passive: true });
sliderBlock.addEventListener("pointermove", onPointerMove, { passive: false });
sliderBlock.addEventListener("pointerup", onPointerUp, { passive: true });
sliderBlock.addEventListener("pointercancel", onPointerUp, { passive: true });

    scheduleNext();
  });
}