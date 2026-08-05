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

  const tocLinks = [...document.querySelectorAll('[data-toc-link]')];
  const sectionIds = [...new Set(tocLinks.map((link) => decodeURIComponent(link.hash.slice(1))).filter(Boolean))];
  const sections = sectionIds.map((id) => document.getElementById(id)).filter(Boolean);
  const setActiveSection = (id) => {
    tocLinks.forEach((link) => {
      const active = decodeURIComponent(link.hash.slice(1)) === id;
      if (active) link.setAttribute('aria-current', 'location');
      else link.removeAttribute('aria-current');
    });
  };

  if (sections.length) {
    const hashId = decodeURIComponent(window.location.hash.slice(1));
    setActiveSection(sectionIds.includes(hashId) ? hashId : sections[0].id);
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        const visible = entries
          .filter((entry) => entry.isIntersecting)
          .sort((a, b) => Math.abs(a.boundingClientRect.top) - Math.abs(b.boundingClientRect.top));
        if (visible[0]) setActiveSection(visible[0].target.id);
      }, { rootMargin: '-10% 0px -72% 0px', threshold: 0 });
      sections.forEach((section) => observer.observe(section));
    }
    window.addEventListener('hashchange', () => {
      const id = decodeURIComponent(window.location.hash.slice(1));
      if (sectionIds.includes(id)) setActiveSection(id);
    });
  }
})();
