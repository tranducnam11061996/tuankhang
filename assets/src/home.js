(() => {
  'use strict';

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const focusable = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  const menuButton = document.querySelector('[data-menu-open]');
  const menuClose = document.querySelector('[data-menu-close]');
  const drawer = document.querySelector('[data-menu-drawer]');
  const overlay = document.querySelector('[data-menu-overlay]');
  let lastFocus = null;

  const closeMenu = () => {
    document.body.classList.remove('tk-menu-open');
    menuButton?.setAttribute('aria-expanded', 'false');
    drawer?.setAttribute('aria-hidden', 'true');
    drawer?.setAttribute('inert', '');
    lastFocus?.focus();
  };
  const openMenu = () => {
    lastFocus = document.activeElement;
    document.body.classList.add('tk-menu-open');
    menuButton?.setAttribute('aria-expanded', 'true');
    drawer?.setAttribute('aria-hidden', 'false');
    drawer?.removeAttribute('inert');
    drawer?.querySelector(focusable)?.focus();
  };
  menuButton?.addEventListener('click', openMenu);
  menuClose?.addEventListener('click', closeMenu);
  overlay?.addEventListener('click', closeMenu);
  drawer?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') return closeMenu();
    if (event.key !== 'Tab') return;
    const nodes = [...drawer.querySelectorAll(focusable)];
    if (!nodes.length) return;
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });

  document.querySelectorAll('[data-submenu-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const expanded = button.getAttribute('aria-expanded') === 'true';
      button.setAttribute('aria-expanded', String(!expanded));
      button.nextElementSibling?.classList.toggle('hidden', expanded);
    });
  });

  const dropdowns = [...document.querySelectorAll('[data-dropdown]')];
  const closeDropdowns = (except) => dropdowns.forEach((item) => {
    if (item === except) return;
    item.dataset.open = 'false';
    item.querySelector('[aria-expanded]')?.setAttribute('aria-expanded', 'false');
  });
  dropdowns.forEach((item) => {
    const trigger = item.querySelector('[data-dropdown-trigger]');
    trigger?.addEventListener('click', (event) => {
      event.stopPropagation();
      const open = item.dataset.open === 'true';
      closeDropdowns(item);
      item.dataset.open = String(!open);
      trigger.setAttribute('aria-expanded', String(!open));
      if (!open) item.querySelector('input, a')?.focus({ preventScroll: true });
    });
    item.addEventListener('keydown', (event) => { if (event.key === 'Escape') { closeDropdowns(); trigger?.focus(); } });
  });
  document.addEventListener('click', () => closeDropdowns());
  dropdowns.forEach((item) => item.addEventListener('click', (event) => event.stopPropagation()));

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
    show(0); start();
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

  const mapFrame = document.querySelector('[data-map-src]');
  if (mapFrame) {
    const loadMap = () => {
      if (!mapFrame.src) mapFrame.src = mapFrame.dataset.mapSrc;
    };
    if ('IntersectionObserver' in window) {
      const mapObserver = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) { loadMap(); mapObserver.disconnect(); }
      }, { rootMargin: '500px' });
      mapObserver.observe(mapFrame);
    } else loadMap();
  }

  let facebookLoaded = false;
  const loadFacebook = () => {
    if (facebookLoaded || document.getElementById('facebook-jssdk')) return;
    facebookLoaded = true;
    window.fbAsyncInit = () => window.FB?.init({ xfbml: true, version: 'v11.0' });
    const script = document.createElement('script');
    script.id = 'facebook-jssdk';
    script.async = true;
    script.defer = true;
    script.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
    document.body.appendChild(script);
  };
  ['pointerdown', 'keydown', 'touchstart'].forEach((eventName) => window.addEventListener(eventName, loadFacebook, { once: true, passive: true }));
  window.setTimeout(() => {
    if ('requestIdleCallback' in window) window.requestIdleCallback(loadFacebook, { timeout: 5000 });
    else loadFacebook();
  }, 5000);
})();
