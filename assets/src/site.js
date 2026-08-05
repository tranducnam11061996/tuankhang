(() => {
  'use strict';

  const focusable = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
  const menuButton = document.querySelector('[data-menu-open]');
  const menuClose = document.querySelector('[data-menu-close]');
  const drawer = document.querySelector('[data-menu-drawer]');
  const overlay = document.querySelector('[data-menu-overlay]');
  let lastFocus = null;

  const siteHeader = document.querySelector('[data-site-header]');
  const headerSentinel = document.querySelector('[data-header-sentinel]');
  if (siteHeader && headerSentinel && 'IntersectionObserver' in window) {
    const headerObserver = new IntersectionObserver(([entry]) => {
      siteHeader.dataset.scrolled = String(!entry.isIntersecting);
    });
    headerObserver.observe(headerSentinel);
  }

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
      button.parentElement?.nextElementSibling?.classList.toggle('hidden', expanded);
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
    item.addEventListener('click', (event) => event.stopPropagation());
  });
  document.addEventListener('click', () => closeDropdowns());

  const mapFrame = document.querySelector('[data-map-src]');
  if (mapFrame) {
    const loadMap = () => { if (!mapFrame.src) mapFrame.src = mapFrame.dataset.mapSrc; };
    if ('IntersectionObserver' in window) {
      const mapObserver = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) { loadMap(); mapObserver.disconnect(); }
      }, { rootMargin: '500px' });
      mapObserver.observe(mapFrame);
    } else loadMap();
  }

  const deferredImages = [...document.querySelectorAll('img[data-deferred-src]')];
  const loadDeferredImage = (img) => {
    if (!img.dataset.deferredSrc) return;
    img.src = img.dataset.deferredSrc;
    img.removeAttribute('data-deferred-src');
  };
  if (deferredImages.length) {
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          loadDeferredImage(entry.target);
          imageObserver.unobserve(entry.target);
        });
      }, { rootMargin: '500px' });
      deferredImages.forEach((img) => imageObserver.observe(img));
    } else deferredImages.forEach(loadDeferredImage);
  }

})();
