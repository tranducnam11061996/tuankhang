# Editorial Title Rail Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the article signal metadata with a premium one-line title rail that matches the Clinical Editorial Briefing design.

**Architecture:** Keep the component inside the shared `content-detail.php` renderer so Page and post detail templates remain consistent. Style it only inside the existing content bundle and preserve the full title in the native tooltip when visual truncation occurs.

**Tech Stack:** WordPress PHP templates, scoped CSS, Tailwind CSS v4 build pipeline.

## Global Constraints

- Display only the current Page/post title in the rail; remove `TK / PAGE`, `TK / POST`, and `MIN` text.
- Keep the title on one line with an ellipsis at every viewport.
- Keep the full title available through the `title` attribute.
- Preserve the hidden scrollbar behavior of the Reading Rail.
- Do not change WordPress data, JavaScript, URLs, header, footer, archive, search, or 404 templates.

---

### Task 1: Shared title rail markup

**Files:**
- Modify: `template-parts/content-detail.php`

**Interfaces:**
- Consumes: `$context['title']` from `tk_content_detail_context()`.
- Produces: `.tk-article-signal-title` and a flexible decorative datum line.

- [ ] **Step 1: Replace metadata spans with the current content title**

  Render the escaped title as the first span, retain the complete escaped value in `title`, and keep the second span as an `aria-hidden` decorative line.

- [ ] **Step 2: Lint the template**

  Run `php.exe -l template-parts/content-detail.php` and expect no syntax errors.

### Task 2: Editorial typography and responsive truncation

**Files:**
- Modify: `assets/src/content.css`
- Modify: `assets/dist/content.min.css`

**Interfaces:**
- Consumes: `.tk-article-signal` markup from Task 1.
- Produces: a single-line title with `overflow: hidden`, `text-overflow: ellipsis`, and `white-space: nowrap`.

- [ ] **Step 1: Restyle the rail**

  Use restrained navy-blue typography, a flexible pale-blue datum line, compact mobile spacing, and a slightly larger desktop title without competing with the page H1.

- [ ] **Step 2: Build only the content stylesheet**

  Run `npx.cmd tailwindcss -i ./assets/src/content.css -o ./assets/dist/content.min.css --minify`.

- [ ] **Step 3: Verify rendered output**

  Confirm a short title displays fully, a long title truncates on one line, no `TK / PAGE`, `TK / POST`, or `MIN` remains, and the page has no horizontal overflow.

- [ ] **Step 4: Run final checks**

  Run PHP lint and `git diff --check` for the modified files.
