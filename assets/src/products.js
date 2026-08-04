(() => {
  'use strict';

  const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
  const productPage = document.querySelector('[data-product-detail]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const catalogPage = document.querySelector('[data-product-catalog]');
  const productContext = productPage ? {
    product_id: Number(productPage.dataset.productId || 0),
    product_slug: productPage.dataset.productSlug || '',
    product_taxonomy: productPage.dataset.productTaxonomy || '',
  } : {};
  if (productPage && !reducedMotion.matches) productPage.classList.add('has-product-reveal');
  if (catalogPage && !reducedMotion.matches) catalogPage.classList.add('has-catalog-reveal');
  let dialogStylesPromise = null;
  const ensureDialogStyles = () => {
    const source = productPage?.dataset.productDialogStyle;
    if (dialogStylesPromise) return dialogStylesPromise;
    if (!source || document.querySelector('link[data-product-dialog-styles]')) return Promise.resolve();
    dialogStylesPromise = new Promise((resolve) => {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = source;
      link.dataset.productDialogStyles = '';
      link.addEventListener('load', resolve, { once: true });
      link.addEventListener('error', resolve, { once: true });
      document.head.append(link);
    });
    return dialogStylesPromise;
  };

  const productHero = document.querySelector('[data-product-hero]');
  const mobileBar = document.querySelector('[data-product-mobile-bar]');
  if (productHero) {
    new IntersectionObserver(([entry]) => {
      mobileBar.classList.toggle('is-visible', !entry.isIntersecting);
    }).observe(productHero);
  }

  const getAttribution = () => {
    const names = ['utm_source', 'utm_medium', 'utm_campaign'];
    const query = new URLSearchParams(window.location.search);
    const attribution = {};
    names.forEach((name) => {
      let value = query.get(name) || '';
      try {
        if (value) sessionStorage.setItem(`tk_${name}`, value);
        else value = sessionStorage.getItem(`tk_${name}`) || '';
      } catch (_) {}
      attribution[name] = value.slice(0, 150);
    });
    return attribution;
  };
  const attribution = getAttribution();

  const track = (eventName, detail) => {
    if (!eventName || !productPage) return;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: eventName,
      ...productContext,
      ...attribution,
      ...detail,
    });
  };

  document.querySelectorAll('[data-tk-event]').forEach((control) => {
    control.addEventListener('click', () => track(control.dataset.tkEvent, {
      action: control.dataset.tkAction || '',
      placement: control.dataset.tkPlacement || '',
    }));
  });

  const trapFocus = (container, event) => {
    if (event.key !== 'Tab') return;
    const nodes = [...container.querySelectorAll(focusableSelector)].filter((node) => !node.hidden && node.offsetParent !== null);
    if (!nodes.length) return;
    const first = nodes[0];
    const last = nodes[nodes.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  };

  const enhanceProductForms = () => {
    document.querySelectorAll('.tk-product-form .wpcf7-form').forEach((form) => {
      const hiddenValues = {
        tk_product_id: String(productContext.product_id || ''),
        tk_product_slug: productContext.product_slug || '',
        tk_page_url: window.location.href.split('#')[0],
        tk_cta_source: form.dataset.ctaSource || '',
        tk_utm_source: attribution.utm_source,
        tk_utm_medium: attribution.utm_medium,
        tk_utm_campaign: attribution.utm_campaign,
      };
      Object.entries(hiddenValues).forEach(([name, value]) => {
        let field = form.querySelector(`input[name="${name}"]`);
        if (!field) {
          field = document.createElement('input');
          field.type = 'hidden';
          field.name = name;
          form.append(field);
        }
        field.value = value;
      });

      form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach((field) => {
        const firstOption = field.tagName === 'SELECT' ? field.options?.[0]?.text : '';
        if (!field.getAttribute('aria-label') && !field.labels?.length) {
          field.setAttribute('aria-label', field.getAttribute('placeholder') || firstOption || field.name || 'Form field');
        }
        if (field.closest('.wpcf7-validates-as-required')) field.setAttribute('aria-required', 'true');
        if (field.name === 'fullname') field.autocomplete = 'name';
        if (field.name === 'phone') {
          field.autocomplete = 'tel';
          field.inputMode = 'tel';
        }
        if (field.name === 'message') field.autocomplete = 'off';
      });

      const output = form.querySelector('.wpcf7-response-output');
      if (output) {
        output.setAttribute('role', 'status');
        output.setAttribute('aria-live', 'polite');
      }

      form.addEventListener('submit', (event) => {
        if (!navigator.onLine) {
          event.preventDefault();
          event.stopImmediatePropagation();
          if (output) {
            output.textContent = document.documentElement.lang.startsWith('en')
              ? 'No network connection. Reconnect and try again.'
              : 'Không có kết nối mạng. Vui lòng kết nối lại và thử lần nữa.';
          }
          form.classList.remove('submitting');
          form.classList.add('failed');
          track('tk_product_form_error', { error_type: 'offline' });
          return;
        }
        form.setAttribute('aria-busy', 'true');
      }, true);
    });
  };
  enhanceProductForms();

  const ensureProductForm = () => {
    const host = document.querySelector('[data-product-form-host]');
    const template = document.querySelector('[data-product-form-template]');
    if (!host || host.querySelector('form') || !template) return;
    host.replaceChildren(template.content.cloneNode(true));
    const form = host.querySelector('.wpcf7 > form');
    if (form && window.wpcf7?.init) window.wpcf7.init(form);
    enhanceProductForms();
  };

  const updateFormSource = (source) => {
    document.querySelectorAll('.tk-product-form .wpcf7-form').forEach((form) => {
      form.dataset.ctaSource = source;
      const field = form.querySelector('input[name="tk_cta_source"]');
      if (field) field.value = source;
    });
  };

  document.addEventListener('wpcf7beforesubmit', (event) => {
    if (Number(event.detail?.contactFormId) !== 14) return;
    track('tk_product_form_submit', {
      placement: event.target?.querySelector('input[name="tk_cta_source"]')?.value || '',
    });
  });
  document.addEventListener('wpcf7mailsent', (event) => {
    if (Number(event.detail?.contactFormId) !== 14) return;
    event.target?.removeAttribute('aria-busy');
    track('tk_product_form_success', {
      placement: event.target?.querySelector('input[name="tk_cta_source"]')?.value || '',
    });
  });
  ['wpcf7invalid', 'wpcf7mailfailed', 'wpcf7spam'].forEach((eventName) => {
    document.addEventListener(eventName, (event) => {
      if (Number(event.detail?.contactFormId) !== 14) return;
      event.target?.removeAttribute('aria-busy');
      const errorType = eventName.replace('wpcf7', '');
      track('tk_product_form_error', { error_type: errorType });
      window.setTimeout(() => {
        const invalid = event.target?.querySelector('.wpcf7-not-valid');
        if (invalid) {
          invalid.setAttribute('aria-invalid', 'true');
          const tip = invalid.parentElement?.querySelector('.wpcf7-not-valid-tip');
          if (tip) {
            if (!tip.id) tip.id = `tk-form-error-${Date.now()}`;
            invalid.setAttribute('aria-describedby', tip.id);
          }
          invalid.focus();
        }
      }, 30);
    });
  });

  document.querySelectorAll('[data-category-tree]').forEach((tree) => {
    const buttons = [...tree.querySelectorAll(':scope > li > div [data-category-toggle]')];
    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const willExpand = button.getAttribute('aria-expanded') !== 'true';
        buttons.forEach((candidate) => {
          const panel = document.getElementById(candidate.getAttribute('aria-controls'));
          const expanded = candidate === button && willExpand;
          candidate.setAttribute('aria-expanded', String(expanded));
          if (panel) panel.hidden = !expanded;
        });
      });
    });
  });

  document.querySelectorAll('[data-product-sort]').forEach((select) => {
    select.addEventListener('change', () => select.form?.requestSubmit());
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
    filterDrawer?.classList.remove('is-open');
    filterOverlay?.classList.remove('is-visible');
    window.setTimeout(() => {
      if (!document.body.classList.contains('tk-product-filter-open') && filterOverlay) filterOverlay.hidden = true;
    }, reducedMotion.matches ? 0 : 260);
    filterLastFocus?.focus();
  };
  const openFilter = () => {
    filterLastFocus = document.activeElement;
    document.body.classList.add('tk-product-filter-open');
    filterOpen?.setAttribute('aria-expanded', 'true');
    filterDrawer?.setAttribute('aria-hidden', 'false');
    filterDrawer?.removeAttribute('inert');
    if (filterOverlay) filterOverlay.hidden = false;
    requestAnimationFrame(() => {
      filterDrawer?.classList.add('is-open');
      filterOverlay?.classList.add('is-visible');
      filterDrawer?.querySelector(focusableSelector)?.focus();
    });
  };
  filterOpen?.addEventListener('click', openFilter);
  filterClose?.addEventListener('click', closeFilter);
  filterOverlay?.addEventListener('click', closeFilter);
  filterDrawer?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeFilter();
    else trapFocus(filterDrawer, event);
  });
  window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
    if (event.matches && document.body.classList.contains('tk-product-filter-open')) closeFilter();
  });

  const modal = document.querySelector('[data-product-modal]');
  const modalOpeners = [...document.querySelectorAll('[data-product-modal-open]')];
  const modalClosers = [...document.querySelectorAll('[data-product-modal-close]')];
  let modalLastFocus = null;
  const closeModal = () => {
    if (!modal?.open) return;
    modal.close();
  };
  const openModal = async (opener) => {
    if (!modal || modal.open) return;
    await ensureDialogStyles();
    ensureProductForm();
    modalLastFocus = opener || document.activeElement;
    const source = opener?.dataset.ctaSource || opener?.dataset.tkPlacement || 'unknown';
    updateFormSource(source);
    modal.showModal();
    document.body.classList.add('tk-product-dialog-open');
    modal.querySelector(focusableSelector)?.focus();
    track('tk_product_form_open', { placement: source });
  };
  modalOpeners.forEach((button) => button.addEventListener('click', () => openModal(button)));
  modalClosers.forEach((button) => button.addEventListener('click', closeModal));
  modal?.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
  });
  modal?.addEventListener('keydown', (event) => trapFocus(modal, event));
  modal?.addEventListener('close', () => {
    document.body.classList.remove('tk-product-dialog-open');
    modalLastFocus?.focus();
  });

  const gallery = document.querySelector('[data-product-gallery]');
  const gallerySlides = gallery ? [...gallery.querySelectorAll('[data-product-gallery-slide]')] : [];
  const galleryThumbs = gallery ? [...gallery.querySelectorAll('[data-product-gallery-thumb]')] : [];
  let activeGalleryIndex = 0;
  const selectGalleryImage = (index) => {
    if (!gallerySlides[index]) return;
    activeGalleryIndex = index;
    gallerySlides.forEach((slide, slideIndex) => {
      const active = slideIndex === index;
      slide.hidden = !active;
      slide.setAttribute('aria-hidden', String(!active));
    });
    galleryThumbs.forEach((thumb, thumbIndex) => thumb.setAttribute('aria-pressed', String(thumbIndex === index)));
  };
  galleryThumbs.forEach((thumb) => {
    thumb.addEventListener('click', () => selectGalleryImage(Number(thumb.dataset.productGalleryThumb || 0)));
  });

  const imageDialog = document.querySelector('[data-product-image-dialog]');
  const imageDialogStage = imageDialog?.querySelector('[data-product-image-dialog-stage]');
  const imageDialogClose = imageDialog?.querySelector('[data-product-image-dialog-close]');
  const galleryZoom = gallery?.querySelector('[data-product-gallery-zoom]');
  let imageDialogLastFocus = null;
  const closeImageDialog = () => {
    if (imageDialog?.open) imageDialog.close();
  };
  galleryZoom?.addEventListener('click', async () => {
    const activeSlide = gallerySlides[activeGalleryIndex];
    if (!imageDialog || !imageDialogStage || !activeSlide) return;
    await ensureDialogStyles();
    imageDialogLastFocus = galleryZoom;
    imageDialogStage.replaceChildren(activeSlide.querySelector('picture')?.cloneNode(true) || activeSlide.cloneNode(true));
    const image = imageDialogStage.querySelector('img');
    if (image) {
      image.loading = 'eager';
      image.fetchPriority = 'high';
    }
    imageDialog.showModal();
    document.body.classList.add('tk-product-dialog-open');
    imageDialogClose?.focus();
  });
  imageDialogClose?.addEventListener('click', closeImageDialog);
  imageDialog?.addEventListener('click', (event) => {
    if (event.target === imageDialog) closeImageDialog();
  });
  imageDialog?.addEventListener('keydown', (event) => trapFocus(imageDialog, event));
  imageDialog?.addEventListener('close', () => {
    document.body.classList.remove('tk-product-dialog-open');
    if (imageDialogStage) imageDialogStage.replaceChildren();
    imageDialogLastFocus?.focus();
  });

  const nav = document.querySelector('[data-product-section-nav]');
  const sectionLinks = nav ? [...nav.querySelectorAll('[data-product-section-link]')] : [];
  const sections = sectionLinks.map((link) => document.getElementById(link.dataset.productSectionLink)).filter(Boolean);
  const setActiveSection = (id) => {
    sectionLinks.forEach((link) => {
      const active = link.dataset.productSectionLink === id;
      link.classList.toggle('is-active', active);
      if (active) {
        link.setAttribute('aria-current', 'location');
        link.scrollIntoView({ behavior: reducedMotion.matches ? 'auto' : 'smooth', block: 'nearest', inline: 'center' });
      } else {
        link.removeAttribute('aria-current');
      }
    });
  };
  sectionLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      const section = document.getElementById(link.dataset.productSectionLink);
      if (!section) return;
      event.preventDefault();
      section.scrollIntoView({ behavior: reducedMotion.matches ? 'auto' : 'smooth', block: 'start' });
      history.replaceState(null, '', `#${section.id}`);
      setActiveSection(section.id);
    });
  });
  if ('IntersectionObserver' in window && sections.length) {
    const sectionObserver = new IntersectionObserver((entries) => {
      const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (visible) setActiveSection(visible.target.id);
    }, { rootMargin: '-30% 0px -55% 0px', threshold: [0, .2, .6] });
    sections.forEach((section) => sectionObserver.observe(section));
  }

  const revealItems = [...document.querySelectorAll('[data-reveal]')];
  if (reducedMotion.matches || !('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
  } else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: .08 });
    revealItems.forEach((item) => revealObserver.observe(item));
  }

  document.querySelectorAll('[data-product-video-play]').forEach((button) => {
    button.addEventListener('click', () => {
      const source = button.dataset.videoSrc;
      if (!source) return;
      const iframe = document.createElement('iframe');
      iframe.src = source;
      iframe.title = document.documentElement.lang.startsWith('en') ? 'Product video' : 'Video giới thiệu sản phẩm';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      button.parentElement?.replaceChildren(iframe);
    }, { once: true });
  });
})();
