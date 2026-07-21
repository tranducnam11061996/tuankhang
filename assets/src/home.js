(() => {
  'use strict';

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hero = document.querySelector('[data-hero]');
  if (hero) {
    const slides = [...hero.querySelectorAll('[data-hero-slide]')];
    const dots = [...hero.querySelectorAll('[data-hero-dot]')];
    let index = 0;
    let timer = null;
    let touchX = 0;
    const show = (next) => {
      index = (next + slides.length) % slides.length;
      slides.forEach((slide, i) => slide.dataset.active = String(i === index));
      dots.forEach((dot, i) => {
        dot.setAttribute('aria-current', i === index ? 'true' : 'false');
        const marker = dot.firstElementChild;
        marker?.classList.toggle('bg-white', i === index);
        marker?.classList.toggle('bg-white/50', i !== index);
      });
    };
    const stop = () => { if (timer) window.clearInterval(timer); timer = null; };
    const start = () => { stop(); if (!reducedMotion && slides.length > 1) timer = window.setInterval(() => show(index + 1), 2500); };
    hero.querySelector('[data-hero-prev]')?.addEventListener('click', () => { show(index - 1); start(); });
    hero.querySelector('[data-hero-next]')?.addEventListener('click', () => { show(index + 1); start(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); start(); }));
    hero.addEventListener('mouseenter', stop);
    hero.addEventListener('mouseleave', start);
    hero.addEventListener('focusin', stop);
    hero.addEventListener('focusout', start);
    hero.addEventListener('touchstart', (event) => { touchX = event.changedTouches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', (event) => { const delta = event.changedTouches[0].clientX - touchX; if (Math.abs(delta) > 45) { show(index + (delta < 0 ? 1 : -1)); start(); } }, { passive: true });
    show(0);
    start();
  }

  const counters = document.querySelectorAll('[data-counter]');
  const animateCounter = (node) => {
    if (node.dataset.done) return;
    node.dataset.done = 'true';
    const target = Number(node.dataset.counter || 0);
    if (reducedMotion) { node.textContent = target.toLocaleString('vi-VN'); return; }
    const duration = 1200;
    const started = performance.now();
    const tick = (now) => {
      const progress = Math.min((now - started) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      node.textContent = Math.round(target * eased).toLocaleString('vi-VN');
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => entries.forEach((entry) => { if (entry.isIntersecting) { animateCounter(entry.target); observer.unobserve(entry.target); } }), { rootMargin: '100px' });
    counters.forEach((counter) => observer.observe(counter));
  } else counters.forEach(animateCounter);
})();
