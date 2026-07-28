(() => {
  'use strict';

  const focusable = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  document.querySelectorAll('[data-context-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const expanded = button.getAttribute('aria-expanded') === 'true';
      const panel = document.getElementById(button.getAttribute('aria-controls'));
      button.setAttribute('aria-expanded', String(!expanded));
      if (panel) panel.hidden = expanded;
    });
  });

  const openButton = document.querySelector('[data-context-filter-open]');
  const closeButton = document.querySelector('[data-context-filter-close]');
  const drawer = document.querySelector('[data-context-filter-drawer]');
  const overlay = document.querySelector('[data-context-filter-overlay]');
  let lastFocus = null;

  const closeDrawer = () => {
    document.body.classList.remove('tk-context-filter-open');
    openButton?.setAttribute('aria-expanded', 'false');
    drawer?.setAttribute('aria-hidden', 'true');
    drawer?.setAttribute('inert', '');
    lastFocus?.focus();
  };
  const openDrawer = () => {
    lastFocus = document.activeElement;
    document.body.classList.add('tk-context-filter-open');
    openButton?.setAttribute('aria-expanded', 'true');
    drawer?.setAttribute('aria-hidden', 'false');
    drawer?.removeAttribute('inert');
    drawer?.querySelector(focusable)?.focus();
  };

  openButton?.addEventListener('click', openDrawer);
  closeButton?.addEventListener('click', closeDrawer);
  overlay?.addEventListener('click', closeDrawer);
  drawer?.addEventListener('click', (event) => {
    if (event.target.closest('a[href]')) closeDrawer();
  });
  drawer?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') return closeDrawer();
    if (event.key !== 'Tab') return;
    const nodes = [...drawer.querySelectorAll(focusable)];
    if (!nodes.length) return;
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });
})();
