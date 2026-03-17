import { gsap } from "gsap";

const STORAGE_PENDING = "moma_transition_pending";
const STORAGE_PHRASE  = "moma_transition_phrase";

/**
 * TIMING (accorcia qui se vuoi ancora più “snappy”)
 */
const DUR_COVER_IN     = 0.40; // chiusura arancione (sx -> dx)
const DUR_REVEAL_OUT   = 0.45; // apertura (dx -> sx)
const DUR_COVER_CLICK  = 0.36; // chiusura prima del redirect

const WORD_DUR         = 0.18; // durata animazione singola parola
const WORD_STAGGER     = 0.040; // ritardo tra parole

const HOLD_MIN         = 0.45; // minimo tempo di lettura frase
const HOLD_PER_WORD    = 0.028; // aggiunta per parola
const HOLD_MAX         = 1.10; // cap massimo lettura

const FETCH_TIMEOUT_MS = 900;  // non bloccare transizione se endpoint lento

function prefersReduced() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches;
}

function wait(seconds) {
  return new Promise((resolve) => gsap.delayedCall(seconds, resolve));
}

function clamp(n, min, max) {
  return Math.max(min, Math.min(max, n));
}

function isSameOrigin(url) {
  try {
    const u = new URL(url, window.location.href);
    return u.origin === window.location.origin;
  } catch {
    return false;
  }
}

function shouldIgnoreLink(a) {
  if (!a) return true;
  const href = a.getAttribute("href");
  if (!href) return true;

  if (href.startsWith("#")) return true;
  if (href.startsWith("mailto:") || href.startsWith("tel:")) return true;
  if (a.target && a.target !== "" && a.target !== "_self") return true;
  if (a.hasAttribute("download")) return true;
  if (a.dataset.noTransition === "1") return true;
  if (a.closest("#wpadminbar")) return true;
  if (!isSameOrigin(href)) return true;

  const u = new URL(href, window.location.href);
  if (u.href === window.location.href) return true;

  return false;
}

async function fetchPhrase() {
  const endpoint = window.MomaTransition?.endpoint;
  if (!endpoint) return "";

  try {
    const res = await fetch(endpoint, { credentials: "same-origin" });
    const json = await res.json();
    return (json?.text || "").trim();
  } catch {
    return "";
  }
}

async function fetchPhraseSafe(timeoutMs = FETCH_TIMEOUT_MS) {
  const t = new Promise((resolve) => setTimeout(() => resolve(""), timeoutMs));
  const text = await Promise.race([fetchPhrase(), t]);
  return (text || "").trim();
}

function emitPageEnter() {
  // flag utile se qualche script arriva “tardi”
  window.__momaPageEnterFired = true;
  document.dispatchEvent(new CustomEvent("moma:page-enter"));
}

function buildWordsHTML(text) {
  const t = (text || "").trim();
  if (!t) return "";
  const words = t.split(/\s+/).filter(Boolean);
  // IMPORTANT: nessuno spazio tra span, ci pensa il gap CSS
  return words.map((w) => `<span class="moma-transition__w">${w}</span>`).join("");
}

function calcHoldSeconds(text) {
  const words = (text || "").trim().split(/\s+/).filter(Boolean);
  const sec = HOLD_MIN + words.length * HOLD_PER_WORD;
  return clamp(sec, HOLD_MIN, HOLD_MAX);
}

async function animateWords(phraseEl, text, reduceMotion) {
  phraseEl.innerHTML = buildWordsHTML(text);

  const words = Array.from(phraseEl.querySelectorAll(".moma-transition__w"));
  if (words.length === 0) return;

  gsap.set(words, { opacity: 0, y: 10 });

  if (reduceMotion) {
    gsap.set(words, { opacity: 1, y: 0 });
    return;
  }

  // fade-up rapido per parola
  await gsap.to(words, {
    opacity: 1,
    y: 0,
    duration: WORD_DUR,
    ease: "power2.out",
    stagger: WORD_STAGGER,
  });
}

export function initMomaPageTransition() {
  const wrap     = document.querySelector(".moma-transition");
  const panel    = document.querySelector(".moma-transition__panel");
  const phraseEl = document.querySelector('[data-role="phrase"]');
  const caret    = document.querySelector(".moma-transition__caret");

  if (!wrap || !panel || !phraseEl) {
    document.documentElement.classList.remove("moma-preload");
    emitPageEnter();
    return;
  }

  const reduceMotion = prefersReduced();

  // stato iniziale
  gsap.set(panel, { scaleX: 0, transformOrigin: "left" });
  gsap.set(phraseEl, { opacity: 0, y: 10 });
  if (caret) gsap.set(caret, { opacity: 0 }); // non usato

  const hidePhrase = () => {
    gsap.set(phraseEl, { opacity: 0, y: 10 });
    phraseEl.innerHTML = "";
    if (caret) gsap.set(caret, { opacity: 0 });
  };

  const revealOut = () => {
  // testo fuori dal pannello: lo sfumo mentre apro
  gsap.to(phraseEl, {
    opacity: 0,
    duration: reduceMotion ? 0.01 : 0.16,
    ease: "power2.out",
    overwrite: "auto",
  });

  gsap.set(panel, { transformOrigin: "right" });

  gsap.to(panel, {
      scaleX: 0,
      duration: reduceMotion ? 0.01 : DUR_REVEAL_OUT,
      ease: "power3.inOut",
      onComplete: () => {
        wrap.classList.remove("is-active");
        hidePhrase();
        sessionStorage.removeItem(STORAGE_PENDING);
        sessionStorage.removeItem(STORAGE_PHRASE);

        emitPageEnter();
      },
    });
  };

  const runReveal = async () => {
    wrap.classList.add("is-active");

    const stored = (sessionStorage.getItem(STORAGE_PHRASE) || "").trim();

    // frase centrata mentre il pannello copre tutto
    gsap.set(phraseEl, { opacity: 1, y: 0 });
    await animateWords(phraseEl, stored, reduceMotion);

    // tempo lettura (più breve e cap)
    if (!reduceMotion && stored) {
      await wait(calcHoldSeconds(stored));
    }

    // rendo visibile la pagina sotto (ancora coperta)
    document.documentElement.classList.remove("moma-preload");

    revealOut();
  };

  const runInitial = async () => {
    wrap.classList.add("is-active");

    // fetch in parallelo alla chiusura (accorcia tempo percepito)
    const phrasePromise = fetchPhraseSafe();

    // cover in: da sinistra a destra
    gsap.set(panel, { transformOrigin: "left" });

    gsap.to(panel, {
      scaleX: 1,
      duration: reduceMotion ? 0.01 : DUR_COVER_IN,
      ease: "power3.inOut",
      onComplete: async () => {
        const text = (await phrasePromise).trim();
        sessionStorage.setItem(STORAGE_PHRASE, text);

        gsap.set(phraseEl, { opacity: 1, y: 0 });
        await animateWords(phraseEl, text, reduceMotion);

        if (!reduceMotion && text) {
          await wait(calcHoldSeconds(text));
        }

        document.documentElement.classList.remove("moma-preload");

        revealOut();
      },
    });
  };

  // Arrivo da click interno?
  const pending = sessionStorage.getItem(STORAGE_PENDING) === "1";

  if (pending) {
    gsap.set(panel, { scaleX: 1, transformOrigin: "left" });
    runReveal();
  } else {
    runInitial();
  }

  // Intercetto click link interni
  document.addEventListener(
    "click",
    (ev) => {
      const a = ev.target.closest("a");
      if (!a || shouldIgnoreLink(a)) return;

      ev.preventDefault();
      const href = a.href;

      sessionStorage.setItem(STORAGE_PENDING, "1");

      // fetch in parallelo alla chiusura
      const phrasePromise = fetchPhraseSafe();

      wrap.classList.add("is-active");
      gsap.set(panel, { transformOrigin: "left" });

      gsap.to(panel, {
        scaleX: 1,
        duration: reduceMotion ? 0.01 : DUR_COVER_CLICK,
        ease: "power3.inOut",
        onComplete: async () => {
          const phrase = (await phrasePromise).trim();
          sessionStorage.setItem(STORAGE_PHRASE, phrase);

          window.location.href = href;
        },
      });
    },
    true
  );

  // BFCache: se torno indietro senza pending, niente preload
  window.addEventListener("pageshow", () => {
    if (sessionStorage.getItem(STORAGE_PENDING) !== "1") {
      document.documentElement.classList.remove("moma-preload");
    }
  });
}