(() => {
  'use strict';

  const styledUnicode = /[\u{1D400}-\u{1D7FF}]/u;
  const settings = window.tuankhangContentUnicodeValidation || {};
  let notice = null;
  let successTimer = 0;

  const decode = (value) => {
    try {
      return decodeURIComponent(value);
    } catch (_) {
      return value;
    }
  };

  const normalize = (value) => {
    const decoded = decode(String(value || ''));
    return typeof decoded.normalize === 'function' ? decoded.normalize('NFKC') : decoded;
  };

  const defaultLanguageTitle = (value) => {
    const vietnamese = String(value || '').match(/\[:vi\]([\s\S]*?)\[:\]/i);
    if (vietnamese) return vietnamese[1];
    return String(value || '').replace(/\[:[a-z_-]+\]|\[:\]/gi, ' ');
  };

  const slugify = (value) => defaultLanguageTitle(normalize(value))
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[đĐ]/g, 'd')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');

  const ensureNotice = () => {
    if (notice) return notice;
    const titleBox = document.getElementById('titlediv');
    if (!titleBox) return null;

    notice = document.createElement('div');
    notice.className = 'notice notice-warning inline';
    notice.id = 'tk-content-unicode-validation';
    notice.hidden = true;
    notice.setAttribute('role', 'status');
    notice.setAttribute('aria-live', 'polite');
    titleBox.insertAdjacentElement('afterend', notice);
    return notice;
  };

  const fieldValues = () => {
    const title = document.getElementById('title');
    const slug = document.getElementById('post_name');
    return {
      title,
      slug,
      titleValue: title ? decode(title.value) : '',
      slugValue: slug ? decode(slug.value) : '',
    };
  };

  const render = () => {
    const values = fieldValues();
    const titleDirty = styledUnicode.test(values.titleValue);
    const slugDirty = styledUnicode.test(values.slugValue);
    const panel = ensureNotice();
    if (!panel) return;

    if (!titleDirty && !slugDirty) {
      if (!panel.classList.contains('notice-success')) {
        panel.hidden = true;
        panel.replaceChildren();
      }
      return;
    }

    window.clearTimeout(successTimer);
    panel.hidden = false;
    panel.className = 'notice notice-warning inline';

    const message = document.createElement('p');
    message.textContent = settings.warning || 'Ký tự Unicode tạo kiểu sẽ được chuyển về chữ thông thường trước khi lưu.';

    const titlePreview = document.createElement('p');
    const titleLabel = document.createElement('strong');
    const normalizedTitle = document.createElement('span');
    titleLabel.textContent = `${settings.titlePreview || 'Tiêu đề sau khi chuẩn hóa:'} `;
    normalizedTitle.textContent = normalize(values.titleValue);
    titlePreview.append(titleLabel, normalizedTitle);

    const slugPreview = document.createElement('p');
    const slugLabel = document.createElement('strong');
    const slugCode = document.createElement('code');
    slugLabel.textContent = `${settings.slugPreview || 'Đường dẫn sạch dự kiến:'} `;
    slugCode.textContent = slugify(values.titleValue || values.slugValue);
    slugPreview.append(slugLabel, slugCode);

    panel.replaceChildren(message, titlePreview, slugPreview);
  };

  const renderSuccess = () => {
    const panel = ensureNotice();
    if (!panel) return;

    panel.hidden = false;
    panel.className = 'notice notice-success inline';
    const message = document.createElement('p');
    message.textContent = settings.normalized || 'Đã chuyển ký tự tạo kiểu về chữ thông thường.';
    panel.replaceChildren(message);

    window.clearTimeout(successTimer);
    successTimer = window.setTimeout(() => {
      panel.hidden = true;
      panel.replaceChildren();
      panel.className = 'notice notice-warning inline';
    }, 4000);
  };

  const normalizeVisibleFields = () => {
    const values = fieldValues();
    const titleDirty = styledUnicode.test(values.titleValue);
    const slugDirty = styledUnicode.test(values.slugValue);
    let changed = false;

    if (titleDirty && values.title) {
      values.title.value = normalize(values.titleValue);
      values.title.dispatchEvent(new Event('change', { bubbles: true }));
      changed = true;
    }

    if ((slugDirty || (titleDirty && !values.slugValue.trim())) && values.slug) {
      values.slug.value = slugify(values.titleValue || values.slugValue);
      values.slug.dispatchEvent(new Event('change', { bubbles: true }));
      changed = true;
    }

    if (changed) renderSuccess();
    return changed;
  };

  document.addEventListener('input', (event) => {
    if (event.target && (event.target.id === 'title' || event.target.id === 'post_name')) render();
  });

  document.addEventListener('focusout', (event) => {
    if (event.target && (event.target.id === 'title' || event.target.id === 'post_name')) {
      if (!normalizeVisibleFields()) render();
    }
  });

  document.getElementById('post')?.addEventListener('submit', normalizeVisibleFields);

  const permalinkBox = document.getElementById('edit-slug-box');
  if (permalinkBox && 'MutationObserver' in window) {
    new MutationObserver(render).observe(permalinkBox, { childList: true, subtree: true });
  }

  render();
})();
