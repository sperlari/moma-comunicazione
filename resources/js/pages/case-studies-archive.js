import { gsap } from 'gsap';

function updateActiveButton(buttons, activeValue) {
  buttons.forEach((button) => {
    const isActive = button.dataset.caseFilter === activeValue;
    button.classList.toggle('is-active', isActive);
    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
  });
}

function getMatchingCards(cards, filter) {
  if (filter === 'all') return cards;

  return cards.filter((card) => {
    const raw = card.dataset.caseTerms || '';
    const terms = raw.split(',').map((term) => term.trim()).filter(Boolean);
    return terms.includes(filter);
  });
}

function updateFeaturedCard(allCards, visibleCards) {
  allCards.forEach((card) => {
    card.classList.remove('moma-case-archive-card--featured', 'is-featured');
  });

  if (visibleCards[0]) {
    visibleCards[0].classList.add('moma-case-archive-card--featured', 'is-featured');
  }
}

function animateEmptyState(emptyNode, shouldShow) {
  if (!emptyNode) return;

  if (shouldShow) {
    emptyNode.classList.remove('hidden');
    gsap.fromTo(emptyNode, { autoAlpha: 0, y: 18 }, { autoAlpha: 1, y: 0, duration: 0.35, ease: 'power3.out', overwrite: 'auto' });
  } else {
    gsap.to(emptyNode, {
      autoAlpha: 0,
      y: 12,
      duration: 0.2,
      ease: 'power2.out',
      overwrite: 'auto',
      onComplete: () => emptyNode.classList.add('hidden'),
    });
  }
}

function bindArchive(container) {
  if (!container || container.dataset.caseArchiveInit === '1') return;

  const buttons = Array.from(document.querySelectorAll('[data-case-filter]'));
  const grid = document.querySelector('[data-case-grid]');
  const cards = Array.from(document.querySelectorAll('[data-case-card]'));
  const emptyNode = document.querySelector('[data-case-empty]');

  if (!buttons.length || !grid || !cards.length) {
    container.dataset.caseArchiveInit = '1';
    return;
  }

  container.dataset.caseArchiveInit = '1';

  let activeFilter = 'all';
  let isAnimating = false;

  updateActiveButton(buttons, activeFilter);
  updateFeaturedCard(cards, cards.filter((card) => !card.classList.contains('hidden')));
  animateEmptyState(emptyNode, false);

  const runFilter = (nextFilter) => {
    if (isAnimating || nextFilter === activeFilter) return;
    isAnimating = true;

    const visibleCards = getMatchingCards(cards, nextFilter);
    const hiddenCards = cards.filter((card) => !visibleCards.includes(card));
    const nextButton = buttons.find((button) => button.dataset.caseFilter === nextFilter);

    updateActiveButton(buttons, nextFilter);

    if (nextButton) {
      gsap.to(nextButton, {
        y: -2,
        duration: 0.12,
        ease: 'power2.out',
        yoyo: true,
        repeat: 1,
        overwrite: 'auto',
      });
    }

    gsap.to(cards, {
      autoAlpha: 0,
      y: 18,
      duration: 0.18,
      ease: 'power2.out',
      stagger: 0.02,
      overwrite: 'auto',
      onComplete: () => {
        hiddenCards.forEach((card) => card.classList.add('hidden'));
        visibleCards.forEach((card) => card.classList.remove('hidden'));

        updateFeaturedCard(cards, visibleCards);
        animateEmptyState(emptyNode, visibleCards.length === 0);

        if (!visibleCards.length) {
          activeFilter = nextFilter;
          isAnimating = false;
          return;
        }

        gsap.fromTo(
          visibleCards,
          { autoAlpha: 0, y: 24 },
          {
            autoAlpha: 1,
            y: 0,
            duration: 0.46,
            ease: 'power3.out',
            stagger: 0.06,
            clearProps: 'transform,opacity,visibility',
            onComplete: () => {
              activeFilter = nextFilter;
              isAnimating = false;
            },
          }
        );
      },
    });
  };

  buttons.forEach((button) => {
    button.addEventListener('click', () => runFilter(button.dataset.caseFilter || 'all'));
  });
}

export function initCaseStudiesArchive() {
  const run = () => bindArchive(document.querySelector('[data-moma-case-studies-archive]'));

  run();
  document.addEventListener('moma:page-enter', run);
}
