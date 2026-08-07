(() => {
  'use strict';

  const consoleRoot = document.querySelector('[data-options-console]');
  const form = consoleRoot?.querySelector('[data-theme-options-form]');
  if (!consoleRoot || !form) return;

  const optionName = (scope) => scope === 'home' ? 'tuankhang_home_options' : 'tuankhang_site_options';
  const status = consoleRoot.querySelector('[data-save-status]');
  const statusText = consoleRoot.querySelector('[data-save-status-text]');
  const live = consoleRoot.querySelector('[data-options-live]');
  const saveButtons = [...consoleRoot.querySelectorAll('[data-save-button], [data-bottom-submit]')];
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let submitting = false;
  let dirty = false;
  let baseline = '';
  let dragState = null;

  const icon = (name) => {
    const paths = {
      image: '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m3 16 5-5 4 4 3-3 6 6"/>',
      grip: '<circle cx="9" cy="7" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="7" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="17" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="17" r="1" fill="currentColor" stroke="none"/>',
      more: '<circle cx="5" cy="12" r="1.3" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.3" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="1.3" fill="currentColor" stroke="none"/>',
      up: '<path d="m6 14 6-6 6 6"/>',
      down: '<path d="m6 10 6 6 6-6"/>',
      trash: '<path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/>',
    };
    return `<svg class="tk-admin-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">${paths[name] || paths.image}</svg>`;
  };

  const announce = (message) => {
    if (!live) return;
    live.textContent = '';
    window.requestAnimationFrame(() => { live.textContent = message; });
  };

  const setStatus = (state) => {
    if (!status || !statusText) return;
    status.classList.remove('is-saved', 'is-dirty', 'is-saving');
    status.classList.add(`is-${state}`);
    statusText.textContent = state === 'saving' ? 'Đang lưu' : state === 'dirty' ? 'Có thay đổi chưa lưu' : 'Đã đồng bộ';
  };

  const serializeForm = () => new URLSearchParams(new FormData(form)).toString();
  const refreshDirtyState = () => {
    dirty = serializeForm() !== baseline;
    setStatus(dirty ? 'dirty' : 'saved');
  };

  const closeRowMenus = (except = null) => {
    consoleRoot.querySelectorAll('[data-row-menu][open]').forEach((menu) => {
      if (menu !== except) menu.removeAttribute('open');
    });
  };

  const rowMenu = (removeAttribute, removeLabel) => `<details class="tk-row-menu" data-row-menu>
    <summary aria-label="Mở menu hành động">${icon('more')}</summary>
    <div class="tk-row-menu-popover">
      <button type="button" data-row-move="up">${icon('up')}<span>Di chuyển lên</span></button>
      <button type="button" data-row-move="down">${icon('down')}<span>Di chuyển xuống</span></button>
      <button type="button" class="is-destructive" ${removeAttribute}>${icon('trash')}<span>${removeLabel}</span></button>
    </div>
  </details>`;

  const emptyMedia = () => `<span class="tk-media-empty">${icon('image')}<small>Chưa chọn ảnh</small></span>`;

  const updateSectionCount = (sectionId, count) => {
    consoleRoot.querySelectorAll(`[data-section-count="${sectionId}"]`).forEach((badge) => { badge.textContent = String(count); });
    const sectionBadge = consoleRoot.querySelector(`[data-options-section="${sectionId}"] .tk-section-count`);
    if (sectionBadge) sectionBadge.textContent = String(count);
  };

  const updateRowControls = (container) => {
    const rows = [...container.querySelectorAll(':scope > [data-repeater-row], :scope > [data-partner-row]')];
    rows.forEach((row, index) => {
      const position = row.querySelector('.tk-row-index');
      if (position) position.textContent = String(index + 1).padStart(2, '0');
      const up = row.querySelector('[data-row-move="up"]');
      const down = row.querySelector('[data-row-move="down"]');
      if (up) up.disabled = index === 0;
      if (down) down.disabled = index === rows.length - 1;
    });
  };

  const reindexRepeater = (repeater) => {
    const scope = repeater.dataset.repeaterScope;
    const path = repeater.dataset.repeaterPath;
    const container = repeater.querySelector('[data-repeater-rows]');
    const rows = [...container.querySelectorAll(':scope > [data-repeater-row]')];
    rows.forEach((row, index) => {
      row.querySelectorAll('[data-repeater-field]').forEach((input) => {
        input.name = `${optionName(scope)}[${path}][${index}][${input.dataset.repeaterField}]`;
      });
    });
    updateRowControls(container);
    updateSectionCount('branches-map', rows.length);
  };

  const reindexPartners = (repeater) => {
    const container = repeater.querySelector('[data-partner-rows]');
    const rows = [...container.querySelectorAll(':scope > [data-partner-row]')];
    rows.forEach((row, index) => {
      row.querySelectorAll('[data-partner-field]').forEach((input) => {
        input.name = `tuankhang_home_options[partners][${index}][${input.dataset.partnerField}]`;
      });
    });
    updateRowControls(container);
    updateSectionCount('partners', rows.length);
  };

  const reindexForRow = (row) => {
    const repeater = row.closest('[data-repeater], [data-partner-repeater]');
    if (!repeater) return;
    if (repeater.matches('[data-partner-repeater]')) reindexPartners(repeater);
    else reindexRepeater(repeater);
  };

  const moveRow = (row, direction) => {
    const sibling = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;
    if (!sibling) return false;
    if (direction === 'up') row.parentElement.insertBefore(row, sibling);
    else row.parentElement.insertBefore(sibling, row);
    reindexForRow(row);
    refreshDirtyState();
    const index = [...row.parentElement.children].indexOf(row) + 1;
    announce(`${row.dataset.rowLabel || 'Mục'} đã chuyển tới vị trí ${index}.`);
    row.querySelector('[data-sort-handle]')?.focus();
    return true;
  };

  const setNormalMediaPreview = (field, url) => {
    const preview = field.querySelector('[data-media-preview]');
    if (!preview) return;
    preview.replaceChildren();
    if (url) {
      const image = document.createElement('img');
      image.src = url;
      image.alt = '';
      preview.append(image);
    } else {
      preview.innerHTML = emptyMedia();
    }
    const select = field.querySelector('[data-media-select]');
    if (select) select.textContent = url ? select.dataset.replaceLabel || 'Thay ảnh' : select.dataset.selectLabel || 'Chọn ảnh';
  };

  const setPartnerMediaPreview = (field, url) => {
    const preview = field.querySelector('[data-media-preview]');
    if (!preview) return;
    preview.replaceChildren();
    if (url) {
      const image = document.createElement('img');
      image.src = url;
      image.alt = '';
      preview.append(image);
    } else {
      preview.innerHTML = icon('image');
    }
    const label = field.querySelector('[data-media-select] small');
    if (label) label.textContent = url ? 'Thay ảnh' : 'Chọn ảnh';
  };

  const openMedia = (field) => {
    if (!window.wp?.media || !field) return;
    const frame = window.wp.media({
      title: 'Chọn hình ảnh',
      button: { text: 'Sử dụng hình ảnh' },
      library: { type: 'image' },
      multiple: false,
    });
    frame.on('select', () => {
      const attachment = frame.state().get('selection').first().toJSON();
      const input = field.querySelector('[data-media-id]');
      const remove = field.querySelector('[data-media-remove]');
      const url = attachment.sizes?.medium?.url || attachment.sizes?.thumbnail?.url || attachment.url;
      input.value = attachment.id;
      if (field.matches('.tk-partner-media')) setPartnerMediaPreview(field, url);
      else setNormalMediaPreview(field, url);
      if (remove) remove.hidden = false;
      refreshDirtyState();
      announce('Đã chọn hình ảnh mới.');
    });
    frame.open();
  };

  const createBranchRow = (scope, path, index) => {
    const row = document.createElement('div');
    row.className = 'tk-repeater-row tk-branch-row';
    row.dataset.repeaterRow = '';
    row.dataset.sortableRow = '';
    row.dataset.rowLabel = `Chi nhánh ${index + 1}`;
    row.innerHTML = `<button type="button" class="tk-sort-handle" data-sort-handle aria-label="Kéo để sắp xếp chi nhánh ${index + 1}">${icon('grip')}</button>
      <span class="tk-row-index">${String(index + 1).padStart(2, '0')}</span>
      <div class="tk-repeater-row-fields">
        <label><strong>Tên chi nhánh</strong><textarea rows="2" name="${optionName(scope)}[${path}][${index}][label]" data-repeater-field="label" data-option-input></textarea></label>
        <label><strong>Địa chỉ</strong><textarea rows="2" name="${optionName(scope)}[${path}][${index}][address]" data-repeater-field="address" data-option-input></textarea></label>
      </div>${rowMenu('data-repeater-remove', 'Xóa chi nhánh')}`;
    return row;
  };

  const createPartnerRow = (index) => {
    const row = document.createElement('div');
    row.className = 'tk-repeater-row tk-partner-row';
    row.dataset.partnerRow = '';
    row.dataset.sortableRow = '';
    row.dataset.rowLabel = `Đối tác ${index + 1}`;
    row.innerHTML = `<button type="button" class="tk-sort-handle" data-sort-handle aria-label="Kéo để sắp xếp đối tác ${index + 1}">${icon('grip')}</button>
      <span class="tk-row-index">${String(index + 1).padStart(2, '0')}</span>
      <div class="tk-media-field tk-partner-media" data-media-field>
        <input type="hidden" name="tuankhang_home_options[partners][${index}][image_id]" value="" data-media-id data-partner-field="image_id">
        <button type="button" class="tk-partner-preview" data-media-select aria-label="Chọn ảnh cho đối tác ${index + 1}"><span data-media-preview>${icon('image')}</span><small>Chọn ảnh</small></button>
        <button type="button" class="button-link-delete tk-partner-remove-image" data-media-remove hidden>Xóa ảnh</button>
      </div>
      <label class="tk-partner-name"><strong>Tên đối tác / alt</strong><input type="text" name="tuankhang_home_options[partners][${index}][name]" data-partner-field="name" data-option-input></label>
      ${rowMenu('data-partner-remove', 'Xóa đối tác')}`;
    return row;
  };

  const validationMessage = (input) => {
    const value = input.value.trim();
    if (!value) return '';
    const type = input.dataset.validation;
    if (type === 'email' && !input.validity.valid) return 'Vui lòng nhập đúng định dạng email.';
    if (type === 'cf7' && (!/^\d+$/.test(value) || Number(value) < 0)) return 'Contact Form 7 ID phải là số nguyên không âm.';
    if (type === 'url' || type === 'map') {
      let parsed;
      try { parsed = new URL(value); } catch { return 'URL phải đầy đủ và bắt đầu bằng http:// hoặc https://.'; }
      if (!['http:', 'https:'].includes(parsed.protocol)) return 'URL chỉ được sử dụng giao thức http hoặc https.';
      if (type === 'map' && (!/(^|\.)google\.[a-z.]+$/i.test(parsed.hostname) || !parsed.pathname.startsWith('/maps/embed'))) {
        return 'Google Maps URL phải thuộc domain Google và có đường dẫn /maps/embed.';
      }
    }
    return '';
  };

  const validateInput = (input) => {
    if (!input.matches('[data-validation]')) return true;
    const field = input.closest('.tk-option-field');
    const error = field?.querySelector('[data-field-error]');
    const message = validationMessage(input);
    field?.classList.toggle('is-invalid', Boolean(message));
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (error) {
      error.textContent = message;
      error.hidden = !message;
    }
    return !message;
  };

  const validateForm = () => {
    const invalid = [...form.querySelectorAll('[data-validation]')].filter((input) => !validateInput(input));
    if (invalid.length) {
      invalid[0].focus();
      announce(`Có ${invalid.length} trường cần kiểm tra trước khi lưu.`);
      return false;
    }
    return true;
  };

  const startDrag = (event, handle) => {
    if (event.button !== 0 && event.pointerType === 'mouse') return;
    const row = handle.closest('[data-sortable-row]');
    if (!row) return;
    event.preventDefault();
    dragState = { row, handle, container: row.parentElement, pointerId: event.pointerId, changed: false };
    handle.setPointerCapture?.(event.pointerId);
    row.classList.add('is-dragging');
    document.body.classList.add('tk-options-sorting');
  };

  const updateDrag = (event) => {
    if (!dragState || event.pointerId !== dragState.pointerId) return;
    const { row, container } = dragState;
    const siblings = [...container.children].filter((item) => item !== row && item.matches('[data-sortable-row]'));
    const before = siblings.find((item) => event.clientY < item.getBoundingClientRect().top + item.getBoundingClientRect().height / 2);
    const previousIndex = [...container.children].indexOf(row);
    container.insertBefore(row, before || null);
    if ([...container.children].indexOf(row) !== previousIndex) dragState.changed = true;
  };

  const finishDrag = (event) => {
    if (!dragState || (event && event.pointerId !== dragState.pointerId)) return;
    const { row, handle, changed, pointerId } = dragState;
    if (handle.hasPointerCapture?.(pointerId)) handle.releasePointerCapture(pointerId);
    row.classList.remove('is-dragging');
    document.body.classList.remove('tk-options-sorting');
    dragState = null;
    if (changed) {
      reindexForRow(row);
      refreshDirtyState();
      const index = [...row.parentElement.children].indexOf(row) + 1;
      announce(`${row.dataset.rowLabel || 'Mục'} đã chuyển tới vị trí ${index}.`);
    }
    handle.focus();
  };

  document.addEventListener('pointerdown', (event) => {
    const handle = event.target.closest('[data-sort-handle]');
    if (handle) startDrag(event, handle);
  });
  document.addEventListener('pointermove', updateDrag);
  document.addEventListener('pointerup', finishDrag);
  document.addEventListener('pointercancel', finishDrag);

  document.addEventListener('click', (event) => {
    const select = event.target.closest('[data-media-select]');
    if (select) {
      event.preventDefault();
      openMedia(select.closest('[data-media-field]'));
      return;
    }

    const removeMedia = event.target.closest('[data-media-remove]');
    if (removeMedia) {
      event.preventDefault();
      const field = removeMedia.closest('[data-media-field]');
      field.querySelector('[data-media-id]').value = '';
      if (field.matches('.tk-partner-media')) setPartnerMediaPreview(field, '');
      else setNormalMediaPreview(field, '');
      removeMedia.hidden = true;
      refreshDirtyState();
      announce('Đã xóa hình ảnh khỏi mục này.');
      return;
    }

    const add = event.target.closest('[data-repeater-add]');
    if (add) {
      event.preventDefault();
      const repeater = add.closest('[data-repeater]');
      const rows = repeater.querySelector('[data-repeater-rows]');
      if (rows.children.length >= Number(repeater.dataset.repeaterMax || 20)) return;
      const row = createBranchRow(repeater.dataset.repeaterScope, repeater.dataset.repeaterPath, rows.children.length);
      rows.append(row);
      reindexRepeater(repeater);
      refreshDirtyState();
      row.querySelector('textarea').focus();
      announce('Đã thêm một chi nhánh mới.');
      return;
    }

    const addPartner = event.target.closest('[data-partner-add]');
    if (addPartner) {
      event.preventDefault();
      const repeater = addPartner.closest('[data-partner-repeater]');
      const rows = repeater.querySelector('[data-partner-rows]');
      if (rows.children.length >= Number(repeater.dataset.repeaterMax || 20)) return;
      const row = createPartnerRow(rows.children.length);
      rows.append(row);
      reindexPartners(repeater);
      refreshDirtyState();
      row.querySelector('[data-media-select]').focus();
      announce('Đã thêm một đối tác mới.');
      return;
    }

    const remove = event.target.closest('[data-repeater-remove], [data-partner-remove]');
    if (remove) {
      event.preventDefault();
      const row = remove.closest('[data-repeater-row], [data-partner-row]');
      const label = row.dataset.rowLabel || 'Mục';
      const repeater = row.closest('[data-repeater], [data-partner-repeater]');
      row.remove();
      if (repeater.matches('[data-partner-repeater]')) reindexPartners(repeater);
      else reindexRepeater(repeater);
      refreshDirtyState();
      announce(`Đã xóa ${label}. Thay đổi chỉ có hiệu lực sau khi lưu.`);
      return;
    }

    const move = event.target.closest('[data-row-move]');
    if (move) {
      event.preventDefault();
      const row = move.closest('[data-repeater-row], [data-partner-row]');
      moveRow(row, move.dataset.rowMove);
      closeRowMenus();
      return;
    }

    const summary = event.target.closest('[data-row-menu] > summary');
    if (summary) {
      closeRowMenus(summary.parentElement);
      return;
    }
    if (!event.target.closest('[data-row-menu]')) closeRowMenus();
  });

  document.addEventListener('keydown', (event) => {
    const handle = event.target.closest('[data-sort-handle]');
    if (handle && event.altKey && ['ArrowUp', 'ArrowDown'].includes(event.key)) {
      event.preventDefault();
      moveRow(handle.closest('[data-sortable-row]'), event.key === 'ArrowUp' ? 'up' : 'down');
      return;
    }
    if (event.key === 'Escape') {
      const menu = event.target.closest('[data-row-menu][open]') || consoleRoot.querySelector('[data-row-menu][open]');
      if (menu) {
        event.preventDefault();
        menu.removeAttribute('open');
        menu.querySelector('summary')?.focus();
      }
    }
  });

  form.addEventListener('input', (event) => {
    if (event.target.matches('[data-validation]') && event.target.getAttribute('aria-invalid') === 'true') validateInput(event.target);
    refreshDirtyState();
  });
  form.addEventListener('change', refreshDirtyState);
  form.addEventListener('focusout', (event) => {
    if (event.target.matches('[data-validation]')) validateInput(event.target);
  });

  form.addEventListener('submit', (event) => {
    if (!validateForm()) {
      event.preventDefault();
      setStatus(dirty ? 'dirty' : 'saved');
      return;
    }
    submitting = true;
    setStatus('saving');
    saveButtons.forEach((button) => {
      button.disabled = true;
      button.setAttribute('aria-busy', 'true');
    });
  });

  window.addEventListener('beforeunload', (event) => {
    if (!dirty || submitting) return;
    event.preventDefault();
    event.returnValue = '';
  });

  const sectionLinks = [...consoleRoot.querySelectorAll('[data-section-link]')];
  const sections = [...consoleRoot.querySelectorAll('[data-options-section]')];
  const setActiveSection = (id) => {
    sectionLinks.forEach((link) => {
      if (link.dataset.sectionLink === id) link.setAttribute('aria-current', 'location');
      else link.removeAttribute('aria-current');
    });
  };

  sectionLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      const section = consoleRoot.querySelector(`[data-options-section="${link.dataset.sectionLink}"]`);
      if (!section) return;
      event.preventDefault();
      section.scrollIntoView({ behavior: reducedMotion.matches ? 'auto' : 'smooth', block: 'start' });
      window.history.replaceState(null, '', `#${section.id}`);
      setActiveSection(link.dataset.sectionLink);
    });
  });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (visible) setActiveSection(visible.target.dataset.optionsSection);
    }, { rootMargin: '-22% 0px -62% 0px', threshold: [0, .1, .35] });
    sections.forEach((section) => observer.observe(section));
  }

  consoleRoot.querySelectorAll('[data-repeater-rows], [data-partner-rows]').forEach(updateRowControls);
  baseline = serializeForm();
  setStatus('saved');
  if (sections[0]) setActiveSection(sections[0].dataset.optionsSection);
})();
