(() => {
  'use strict';

  const focusable = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  document.querySelectorAll('.tk-product-form input:not([type="hidden"]), .tk-product-form select, .tk-product-form textarea').forEach((field) => {
    const firstOption = field.tagName === 'SELECT' ? field.options?.[0]?.text : '';
    if (!field.getAttribute('aria-label')) field.setAttribute('aria-label', field.getAttribute('placeholder') || firstOption || field.value || field.name || 'Form field');
    if (field.closest('.wpcf7-validates-as-required')) field.setAttribute('aria-required', 'true');
  });

  document.querySelectorAll('.tk-product-form .wpcf7-form').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const output = form.querySelector('.wpcf7-response-output');
      const showNetworkError = () => {
        if (!output) return;
        output.textContent = document.documentElement.lang.startsWith('en')
          ? 'No network connection. Please reconnect and try again.'
          : 'Không có kết nối mạng. Vui lòng kết nối lại và thử lần nữa.';
        output.setAttribute('role', 'status');
        form.classList.remove('submitting');
        form.classList.add('failed');
      };
      if (!navigator.onLine) {
        event.stopImmediatePropagation();
        showNetworkError();
        return;
      }
      window.setTimeout(() => {
        if (form.classList.contains('submitting')) showNetworkError();
      }, 4000);
    }, true);
  });

  document.querySelectorAll('[data-category-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const expanded = button.getAttribute('aria-expanded') === 'true';
      const panel = document.getElementById(button.getAttribute('aria-controls'));
      button.setAttribute('aria-expanded', String(!expanded));
      if (panel) panel.hidden = expanded;
    });
  });

  const filterOpen = document.querySelector('[data-product-filter-open]');
  const filterClose = document.querySelector('[data-product-filter-close]');
  const filterDrawer = document.querySelector('[data-product-filter-drawer]');
  const filterOverlay = document.querySelector('[data-product-filter-overlay]');
  let filterLastFocus = null;

  const closeFilter = () => {
    document.body.classList.remove('tk-product-filter-open');
    filterOpen?.setAttribute('aria-expanded', 'false');
    filterDrawer?.setAttribute('aria-hidden', 'true');
    filterDrawer?.setAttribute('inert', '');
    filterLastFocus?.focus();
  };
  const openFilter = () => {
    filterLastFocus = document.activeElement;
    document.body.classList.add('tk-product-filter-open');
    filterOpen?.setAttribute('aria-expanded', 'true');
    filterDrawer?.setAttribute('aria-hidden', 'false');
    filterDrawer?.removeAttribute('inert');
    filterDrawer?.querySelector(focusable)?.focus();
  };
  filterOpen?.addEventListener('click', openFilter);
  filterClose?.addEventListener('click', closeFilter);
  filterOverlay?.addEventListener('click', closeFilter);
  filterDrawer?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') return closeFilter();
    if (event.key !== 'Tab') return;
    const nodes = [...filterDrawer.querySelectorAll(focusable)];
    if (!nodes.length) return;
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });

  const modal = document.querySelector('[data-product-modal]');
  const modalPanel = modal?.querySelector('[data-product-modal-panel]');
  const modalOpeners = [...document.querySelectorAll('[data-product-modal-open]')];
  const modalClosers = [...document.querySelectorAll('[data-product-modal-close]')];
  let modalLastFocus = null;

  const closeModal = () => {
    if (!modal) return;
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('tk-product-modal-open');
    modalLastFocus?.focus();
  };
  const openModal = () => {
    if (!modal) return;
    modalLastFocus = document.activeElement;
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('tk-product-modal-open');
    modalPanel?.querySelector(focusable)?.focus();
  };
  modalOpeners.forEach((button) => button.addEventListener('click', openModal));
  modalClosers.forEach((button) => button.addEventListener('click', closeModal));
  modalPanel?.addEventListener('click', (event) => event.stopPropagation());
  modal?.addEventListener('click', closeModal);
  modal?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') return closeModal();
    if (event.key !== 'Tab') return;
    const nodes = [...modal.querySelectorAll(focusable)];
    if (!nodes.length) return;
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });
  document.addEventListener('wpcf7mailsent', closeModal);
})();
