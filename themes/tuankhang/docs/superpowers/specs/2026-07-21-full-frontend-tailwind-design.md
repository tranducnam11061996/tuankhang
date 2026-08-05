# Full Frontend Tailwind Design

## Goal

Convert every public WordPress frontend template to the existing Tailwind design system while preserving content, URLs, IDs, taxonomy relationships, ACF keys, desktop proportions, and multilingual behavior.

## Architecture

- `header.php` and `footer.php` become the single Tailwind shell used by every active frontend template.
- Runtime assets are split into `site`, `home`, `products`, and `content` bundles. Every route loads `site` plus exactly one route bundle.
- No active frontend route loads jQuery, UIkit, Owl, Slick, Flexslider, Font Awesome, WP-PageNavi CSS, or other legacy theme assets.
- Shared PHP helpers own menus, language output, banner, breadcrumb, pagination, responsive images, contextual sidebars, post cards, and related-content queries.

## Content Templates

- Pages retain the 1/4 sidebar and 3/4 content desktop composition. Company, service, policy, and contact pages receive context-specific navigation; mobile renders it in an accessible drawer.
- News, event, promotion, notice, recruitment, and project archives retain image-left/content-right desktop cards and switch to one column on mobile.
- Post detail pages use the deepest assigned category for breadcrumbs and related content. Related posts use a post card rather than the product card.
- Product search remains the default site-search behavior. Post search, 404, settings-page, and index fallbacks use the content shell.
- Contact keeps its existing content and does not add a new form. The introduction page keeps its existing ACF story field behavior.

## Images and Performance

- Generate AVIF/WebP for the shared banner, published post thumbnails, and static-page attachment images.
- Historical post galleries use local attachment URLs, WordPress responsive sizes, dimensions, decoding hints, and lazy loading; no full-library derivative explosion.
- The banner is eager/preloaded/high-priority. Maps, footer images, and Facebook chat remain deferred.
- Content pages target at most 45 KB raw CSS for `site + content`, 8 KB raw theme JavaScript, about 20 initial requests before map/chat, LCP under 2.5 seconds, CLS under 0.1, TBT under 200 ms, Lighthouse Performance at least 90, and Accessibility at least 95.

## Acceptance

- Test 320, 390, 768, 1024, and 1440 pixel viewports, VI/EN, keyboard interactions, drawers, dropdowns, pagination, empty states, and rich media.
- Smoke-test all published pages, posts, categories, products, and product taxonomies for HTTP 200 without PHP warnings or fatals.
- Run PHP lint and the production build twice; the second image build must be idempotent.
- Confirm database records, IDs, slugs, URLs, taxonomies, and ACF data remain unchanged.
