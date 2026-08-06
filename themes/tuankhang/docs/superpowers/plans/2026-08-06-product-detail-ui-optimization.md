# Product Detail UI Optimization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Improve long product-title balance, render a sharp click-to-play video poster, and make standalone rich-content images fill their 850px content column without resizing icons or inline media.

**Architecture:** Keep the existing WordPress product-detail module and its lazy iframe behavior. Replace the URL-only video helper with structured video data, expose an optional ACF poster override, classify standalone product media during content normalization, and scope all visual changes to existing product-detail classes.

**Tech Stack:** WordPress/PHP 8.3, ACF PRO, Tailwind CSS v4 build pipeline, vanilla JavaScript, esbuild.

## Global Constraints

- Apply changes to every singular `san-pham` page.
- Preserve existing URLs, product copy, navy clinical visual language, and click-to-play behavior.
- Use `clamp(2.25rem, 4vw, 3.75rem)` for the product H1.
- Use `object-fit: cover` for video posters and natural aspect ratio for rich-content images.
- Do not load the iframe or autoplay before user activation.
- Do not use a global `img` selector for standalone-media sizing.

---

### Task 1: Structured video data and editor poster override

**Files:**
- Modify: `wp-content/plugins/tuankhang-core/includes/acf-product-premium.php`
- Modify: `wp-content/themes/tuankhang/inc/product-tailwind.php`

**Interfaces:**
- Produces: `tk_product_video_data(int $post_id): array{provider:string,video_id:string,embed_url:string,poster_id:int,poster_url:string,poster_fallbacks:array}` or an empty array.
- Preserves: `tk_product_video_embed_url(int $post_id): string` as a compatibility wrapper.

- [ ] Add optional ACF image field `tk_product_video_poster`, returning an attachment ID.
- [ ] Parse supported YouTube and Vimeo URL forms into provider and sanitized video ID.
- [ ] Prefer the custom poster. For YouTube, provide `maxresdefault`, `maxres1`, and `sddefault`; for Vimeo, retrieve oEmbed thumbnail data through a bounded cached request.
- [ ] Return an empty array for missing or unsupported URLs and retain the existing privacy-enhanced embed URLs.
- [ ] Run PHP lint on both files.

### Task 2: Semantic standalone rich-content media

**Files:**
- Modify: `wp-content/themes/tuankhang/inc/product-tailwind.php`

**Interfaces:**
- Produces: `tk-product-content-media--single` only on a paragraph or figure whose meaningful content is exactly one generated product `<picture>`.

- [ ] Give generated product pictures a stable marker class.
- [ ] Classify only single-picture paragraph/figure wrappers after attachment normalization.
- [ ] Leave emoji images, SVGs, inline images, linked text, and multi-image wrappers unclassified.
- [ ] Verify the ABCcolla content contains classified clinical images while its contact icon text remains unchanged.

### Task 3: Product template and presentation behavior

**Files:**
- Modify: `wp-content/themes/tuankhang/single-san-pham.php`
- Modify: `wp-content/themes/tuankhang/assets/src/product-detail.css`
- Modify: `wp-content/themes/tuankhang/assets/src/products.js`

**Interfaces:**
- Consumes: structured video data from Task 1 and standalone-media classes from Task 2.
- Produces: poster markup with `data-product-video-poster` and a JSON fallback list in `data-video-poster-fallbacks`.

- [ ] Render a lazy, decorative 16:9 poster under a scoped overlay and centered play UI.
- [ ] Keep section heading outside the media frame and place the caption in a padded pill.
- [ ] Cycle poster fallbacks on image error without showing a broken-image icon, then reveal the poster only after a successful load.
- [ ] Replace the button contents with the existing autoplay iframe on activation and preserve title, allow-list, keyboard semantics, focus style, and fullscreen support.
- [ ] Apply the approved H1 scale, width, spacing, and line-height; remove the oversized desktop override.
- [ ] Size only classified standalone product media to 100% of the content column.

### Task 4: Build and verification

**Files:**
- Rebuild: `wp-content/themes/tuankhang/assets/dist/product-detail.min.css`
- Rebuild: `wp-content/themes/tuankhang/assets/dist/products.min.js`

- [ ] Run the targeted Tailwind and esbuild commands for the two changed bundles.
- [ ] Run PHP lint, project tests, and the Unicode validator.
- [ ] Smoke-test the ABCcolla page and representative products with no video.
- [ ] Check 320, 640, 768, 1024, and 1440px layouts for title overflow, 16:9 video layout, full-width standalone media, icon isolation, keyboard playback, and absence of initial iframe requests.
