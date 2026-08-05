# Footer Color Logo Halo Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the inverse footer logo with the existing full-color brand asset and keep it legible on the navy footer through a restrained white silhouette contour and soft halo.

**Architecture:** Keep the existing deferred image loader and `tk_site_logo()` API. Scope all presentation to dedicated footer-brand classes, reserve the logo's native `200 / 139` aspect ratio to avoid layout shift, and use CSS-only effects so no asset request or JavaScript is added.

**Tech Stack:** WordPress PHP templates, Tailwind CSS v4 source bundle, CSS pseudo-elements and `drop-shadow()` filters.

## Global Constraints

- Keep the public footer structure, content, navigation and data unchanged.
- Do not edit or generate raster assets; reuse `image/logo.png` through `tk_site_logo(false)`.
- Do not add packages, plugins, JavaScript or network requests.
- Keep the displayed logo width at `172px` and reserve the native `200 / 139` ratio.
- Respect `prefers-reduced-motion` and retain an obvious keyboard focus state.

---

### Task 1: Footer brand markup

**Files:**
- Modify: `footer.php`

**Interfaces:**
- Consumes: `tk_site_logo(bool $white): string`
- Produces: `.tk-footer-brand` wrapper and `.tk-footer-brand-logo` image hooks.

- [ ] Replace both deferred and `<noscript>` calls from `tk_site_logo(true)` to `tk_site_logo(false)`.
- [ ] Add `.tk-footer-brand` to the homepage link and `.tk-footer-brand-logo` to both image variants.
- [ ] Preserve `width="200"`, `height="139"`, alt text and deferred loading attributes.
- [ ] Run `php -l footer.php`; expected result: `No syntax errors detected`.

### Task 2: Color emblem treatment

**Files:**
- Modify: `assets/src/site.css`
- Modify (generated): `assets/dist/site.min.css`

**Interfaces:**
- Consumes: `.tk-footer-brand` and `.tk-footer-brand-logo` from Task 1.
- Produces: a stable `172px × 119.54px` brand area with a CSS-only halo and contour.

- [ ] Make `.tk-footer-brand` a positioned, isolated `172px` wrapper with `aspect-ratio: 200 / 139`.
- [ ] Add a non-interactive radial white/cyan halo through `::before`, without a rectangular plate.
- [ ] Make the image fill the wrapper with `object-fit: contain`.
- [ ] Add one centered white `drop-shadow()` contour and use the radial halo for depth; avoid stacking directional filters because the PNG contains faint full-canvas alpha that would produce a rectangular shadow artifact.
- [ ] Add a restrained 180ms hover/focus transform and a cyan `focus-visible` outline.
- [ ] Disable transforms inside the existing `prefers-reduced-motion` media query.
- [ ] Build only the shared CSS bundle with `npx tailwindcss -i ./assets/src/site.css -o ./assets/dist/site.min.css --minify`.

### Task 3: Verification

**Files:**
- Test: rendered shared footer on homepage, product listing and content pages.

**Interfaces:**
- Consumes: generated `assets/dist/site.min.css` and rendered `footer.php`.
- Produces: verified shared footer behavior with no regression.

- [ ] Check 375px, 768px, 1024px and 1440px: the colored logo is visible, the halo follows the silhouette, and no horizontal overflow is introduced.
- [ ] Confirm both deferred and noscript sources point to `logo.png` and the reserved aspect ratio prevents placeholder layout shift.
- [ ] Verify hover, keyboard focus and reduced-motion behavior.
- [ ] Run `npm run test:unicode`; expected result: 6 passing tests.
- [ ] Run `git diff --check -- footer.php assets/src/site.css assets/dist/site.min.css`; expected result: no whitespace errors.
