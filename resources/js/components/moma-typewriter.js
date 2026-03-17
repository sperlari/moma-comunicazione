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

function buildWordReveal(el) {
  const raw = (el.textContent || "").trim();
  if (!raw) return null;

  el.setAttribute("aria-label", raw);

  const hasDot = raw.endsWith(".");
  const existingDot = el.querySelector('[class*="__dot"]');
  const existingDotClass = existingDot ? existingDot.className : "";
  const base = hasDot ? raw.slice(0, -1) : raw;

  // Preserva la classe del punto finale se nel markup originale esisteva un dot (es. moma-case-studies__dot)
  const originalDot = el.querySelector("[class*='__dot']");
  const originalDotClass = (originalDot?.getAttribute("class") || "").trim();
  const originalDotClassAttr = originalDotClass ? ` ${originalDotClass}` : "";

  const words = base.split(/\s+/).filter(Boolean);
  if (!words.length) return null;

  // Ricostruisci markup (inline, wrappa bene)
  const htmlWords = words
    .map((w) => `<span class="moma-tw__word" aria-hidden="true">${w}</span>`)
    .join('<span class="moma-tw__space" aria-hidden="true"> </span>');

  el.innerHTML = `
  <span class="moma-tw" aria-hidden="true">${htmlWords}</span>
  ${hasDot ? `<span class="moma-tw__dot ${existingDotClass}" aria-hidden="true">.</span>` : ""}
`;

  const wordEls = Array.from(el.querySelectorAll(".moma-tw__word"));
  const dotEl = el.querySelector(".moma-tw__dot");

  gsap.set(wordEls, { opacity: 0, y: 10, filter: "blur(2px)" });
  if (dotEl) gsap.set(dotEl, { opacity: 0, y: 6 });

  const tl = gsap.timeline({ paused: true });
  tl.to(wordEls, {
    opacity: 1,
    y: 0,
    filter: "blur(0px)",
    duration: 0.35,
    ease: "power2.out",
    stagger: 0.06,
  });

  if (dotEl) {
    tl.to(dotEl, { opacity: 1, y: 0, duration: 0.18, ease: "power2.out" }, "-=0.10");
  }

  return tl;
}

export function initMomaTypewriter() {
  const nodes = Array.from(
    document.querySelectorAll("[data-moma-typewriter], .moma-typewriter")
  ).filter((el) => !el.classList.contains("moma-hero__wordmark"));

  if (!nodes.length) return;

  runAfterPageEnter(() => {
    linkLenisToScrollTrigger();

    ScrollTrigger.getAll().forEach((st) => {
      const id = st?.vars?.id;
      if (typeof id === "string" && id.startsWith("moma-tw-")) st.kill(true);
    });

    const reduce = prefersReduced();
    if (reduce) return;

    nodes.forEach((el, idx) => {
      if (el.dataset.momaTypewriterInit === "1") return;
      el.dataset.momaTypewriterInit = "1";

      const tl = buildWordReveal(el);
      if (!tl) return;

      ScrollTrigger.create({
        id: `moma-tw-${idx}`,
        trigger: el,
        start: "top 85%",
        once: true,
        onEnter: () => tl.play(0),
      });
    });
  });
}