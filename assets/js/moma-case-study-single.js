(function () {
  const root = document.querySelector('[data-moma-case-study-single], [data-moma-multimedia-page]');
  if (!root) return;

  const parseValue = (raw) => {
    const original = String(raw || '').trim();
    const normalized = original.replace(',', '.');
    const number = Number.parseFloat(normalized);
    const decimals = normalized.includes('.') ? normalized.split('.').pop().length : 0;
    return {
      raw: original,
      number: Number.isFinite(number) ? number : 0,
      decimals,
    };
  };

  const formatValue = (number, decimals) => {
    return Number(number).toFixed(decimals).replace(/\.0+$/, '').replace(/(\.[0-9]*?)0+$/, '$1');
  };

  const countups = Array.from(root.querySelectorAll('[data-countup-target]'));
  if (countups.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        const el = entry.target;
        if (el.dataset.countupDone === '1') {
          observer.unobserve(el);
          return;
        }

        const parsed = parseValue(el.getAttribute('data-countup-value'));
        const duration = 1400;
        const start = performance.now();

        const tick = (now) => {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const current = parsed.number * eased;
          el.textContent = formatValue(current, parsed.decimals);

          if (progress < 1) {
            requestAnimationFrame(tick);
          } else {
            el.textContent = parsed.raw;
            el.dataset.countupDone = '1';
          }
        };

        requestAnimationFrame(tick);
        observer.unobserve(el);
      });
    }, {
      threshold: 0.45,
      rootMargin: '0px 0px -40px 0px',
    });

    countups.forEach((el) => observer.observe(el));
  }

  const openers = Array.from(document.querySelectorAll('[data-moma-lightbox="1"]'));
  if (!openers.length) return;

  let activeLightbox = null;
  let activeTrigger = null;

  const buildUploadedVideo = (dataset) => {
    const video = document.createElement('video');
    video.controls = true;
    video.autoplay = true;
    video.playsInline = true;
    video.preload = 'metadata';

    if (dataset.thumb) {
      video.poster = dataset.thumb;
    }

    if (dataset.webm) {
      const source = document.createElement('source');
      source.src = dataset.webm;
      source.type = 'video/webm';
      video.appendChild(source);
    }

    if (dataset.mp4) {
      const source = document.createElement('source');
      source.src = dataset.mp4;
      source.type = 'video/mp4';
      video.appendChild(source);
    }

    return video;
  };

  const buildIframe = (src) => {
    const iframe = document.createElement('iframe');
    iframe.src = src;
    iframe.allow = 'autoplay; fullscreen; picture-in-picture';
    iframe.allowFullscreen = true;
    iframe.loading = 'eager';
    iframe.referrerPolicy = 'strict-origin-when-cross-origin';
    iframe.title = 'Video';
    return iframe;
  };

  const closeLightbox = () => {
    if (!activeLightbox) return;
    document.body.style.removeProperty('overflow');
    activeLightbox.remove();
    activeLightbox = null;
    if (activeTrigger) {
      activeTrigger.focus();
      activeTrigger = null;
    }
  };

  const openLightbox = (trigger) => {
    const dataset = trigger.dataset;
    const type = dataset.type || 'image';

    if (activeLightbox) closeLightbox();

    const lightbox = document.createElement('div');
    lightbox.className = 'moma-case-single__lightbox';
    lightbox.innerHTML = `
      <div class="moma-case-single__lightbox-backdrop"></div>
      <div class="moma-case-single__lightbox-dialog">
        <button type="button" class="moma-case-single__lightbox-close" aria-label="Chiudi">×</button>
        <div class="moma-case-single__lightbox-content"></div>
      </div>
    `;

    const content = lightbox.querySelector('.moma-case-single__lightbox-content');
    if (!content) return;

    if (type === 'image' && dataset.src) {
      const image = document.createElement('img');
      image.src = dataset.src;
      image.alt = '';
      content.appendChild(image);
    }

    if (type === 'video') {
      if (dataset.sourceType === 'upload') {
        content.appendChild(buildUploadedVideo(dataset));
      } else if (dataset.embed) {
        content.appendChild(buildIframe(dataset.embed));
      }
    }

    document.body.appendChild(lightbox);
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => lightbox.classList.add('is-open'));

    const closeButton = lightbox.querySelector('.moma-case-single__lightbox-close');
    const backdrop = lightbox.querySelector('.moma-case-single__lightbox-backdrop');
    closeButton?.addEventListener('click', closeLightbox);
    backdrop?.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (event) => {
      if (event.target === lightbox) closeLightbox();
    });

    activeLightbox = lightbox;
    activeTrigger = trigger;
  };

  openers.forEach((trigger) => {
    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      openLightbox(trigger);
    });
  });


  const lightboxCards = Array.from(document.querySelectorAll('[data-moma-lightbox-card="1"]'));
  lightboxCards.forEach((card) => {
    const triggerOpener = () => {
      const opener = card.querySelector('[data-moma-lightbox="1"]');
      if (opener) opener.click();
    };

    card.addEventListener('click', (event) => {
      if (event.defaultPrevented) return;
      if (event.target.closest('a, button, input, textarea, select, label')) return;
      event.preventDefault();
      triggerOpener();
    });

    card.addEventListener('keydown', (event) => {
      if (event.key !== 'Enter' && event.key !== ' ') return;
      if (event.target.closest('a, button, input, textarea, select, label') && event.target !== card) return;
      event.preventDefault();
      triggerOpener();
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && activeLightbox) {
      closeLightbox();
    }
  });
})();
