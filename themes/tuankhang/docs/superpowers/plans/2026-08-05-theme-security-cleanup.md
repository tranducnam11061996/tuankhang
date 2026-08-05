# Theme Security Audit and Cleanup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Audit the complete `tuankhang` WordPress theme, remove verified obsolete files, and report exploitable PHP/JavaScript security issues without overwriting existing work.

**Architecture:** Treat the nested theme Git repository as the source of truth, separate first-party runtime code from generated assets and development dependencies, and require reference checks before deletion. Scan source and built artifacts for obfuscation and dangerous execution primitives, then validate PHP, JavaScript builds, and the final Git diff.

**Tech Stack:** WordPress/PHP, browser JavaScript, Tailwind CSS, npm/esbuild, Git, ripgrep.

## Global Constraints

- Preserve all pre-existing modified, deleted, and untracked user files.
- Delete only files that are explicitly requested or proven to be obsolete, duplicated backups with no references, ignored logs, or reproducible dependencies.
- Do not edit first-party runtime code as part of this review-only security scan; report findings for a separate remediation decision.
- Report every deletion and every unresolved risk with file and line evidence.

---

### Task 1: Establish the audit baseline

**Files:**
- Create: `docs/superpowers/plans/2026-08-05-theme-security-cleanup.md`
- Inspect: `.gitignore`, `package.json`, `package-lock.json`, all tracked theme files

**Interfaces:**
- Consumes: Nested Git repository state and theme filesystem inventory.
- Produces: A protected baseline of existing changes and a classified file inventory.

- [x] **Step 1: Capture repository state**

Run: `git status -sb && git diff --stat && git ls-files`

Expected: branch and tracked files are known; existing user changes are listed before cleanup.

- [x] **Step 2: Classify runtime, generated, backup, dependency, and ignored files**

Run: `git ls-files | rg "(^|/)(backup|old|copy|tmp)|duytv|node_modules|error_log" -i`

Expected: potential removal candidates are a small, reviewable set.

- [x] **Step 3: Prove candidates are unused**

Run: `rg -n "index-duytv|function-backup23122024|style-backup23122024|style-duytv|screenshot - backup|logo - backup" . -g '!node_modules/**' -g '!.git/**'`

Expected: only self/path metadata matches; any active reference defers deletion.

### Task 2: Scan PHP and JavaScript security

**Files:**
- Inspect: `*.php`, `inc/**/*.php`, `template-parts/**/*.php`, `scripts/**/*.php`
- Inspect: `assets/src/**/*.js`, `assets/dist/**/*.js`, `js/**/*.js`, `scripts/**/*.mjs`
- Inspect: non-code files for embedded PHP/script signatures

**Interfaces:**
- Consumes: Classified first-party code and build outputs from Task 1.
- Produces: Evidence-ranked P0-P3 findings and safe fix candidates.

- [x] **Step 1: Scan for obfuscation and execution primitives**

Run: `rg -n -i "eval\\s*\\(|assert\\s*\\(|base64_decode|gzinflate|str_rot13|create_function|shell_exec|passthru|proc_open|popen|system\\s*\\(|Function\\s*\\(|document\\.write|atob\\s*\\(|fromCharCode" --glob '*.php' --glob '*.js' --glob '*.mjs'`

Expected: each match is manually classified as legitimate library/build code or suspicious behavior.

- [x] **Step 2: Audit WordPress request boundaries**

Run: `rg -n "\\$_(GET|POST|REQUEST|COOKIE|FILES)|wp_ajax|register_rest_route|admin_post|\\$wpdb|wp_remote_|file_get_contents|include|require|echo" --glob '*.php'`

Expected: nonce, capability, sanitization, prepared-query, URL/path, and escaping coverage is checked at every externally controlled boundary.

- [x] **Step 3: Scan secrets, remote hosts, hidden payloads, and misleading extensions**

Run: `rg -n -i "api[_-]?key|secret|token|password|https?://|data:(text|application)|[A-Za-z0-9+/]{200,}={0,2}" . -g '!node_modules/**' -g '!.git/**'`

Expected: hard-coded secrets and unexplained payloads are absent or reported with impact.

- [x] **Step 4: Check dependencies**

Run: `npm audit --omit=dev`

Expected: the runtime dependency audit is clean; development-only findings are separately documented.

### Task 3: Remove verified artifacts

**Files:**
- Delete: `index-duytv.php`, `css/`, `js/`, six tracked FontAwesome files, four unreachable `inc` partials, `icon.jpg`, `image/logo - backup.png`, `screenshot - backup.png`, and `error_log`
- Delete as reproducible development artifacts: `node_modules/`
- Preserve: existing modified/untracked files, `fonts/Inter-Variable-Latin.woff2`, and `fonts/Inter-Variable-Vietnamese.woff2`

**Interfaces:**
- Consumes: Reference and security evidence from Tasks 1-2.
- Produces: A smaller runtime tree with legacy executable assets removed and security findings preserved for review.

- [x] **Step 1: Delete approved and proven-unused files**

Use `apply_patch` for tracked text files and verified native PowerShell deletion for binary files, ignored logs, and the resolved `node_modules` directory.

Expected: every deletion is visible in `git status` except ignored/regenerable artifacts.

- [x] **Step 2: Preserve source-level findings for a separate remediation pass**

Record the missing CLI guards in the three PHP image builders, the unescaped login URL, the unsanitized admin post ID read, and the dynamic Facebook SDK load with exact locations and severity.

Expected: no runtime source code is changed without explicit remediation authorization.

- [x] **Step 3: Verify generated assets against source**

Run: rebuild each CSS/JS entry to a temporary audit output and compare SHA-256 with `assets/dist`.

Expected: all generated assets match source without hidden appended payloads.

### Task 4: Verify and report

**Files:**
- Verify: all remaining PHP/JS and the complete cleanup diff

**Interfaces:**
- Consumes: Cleaned tree from Task 3.
- Produces: Reproducible validation results and a final audit report.

- [x] **Step 1: Lint every first-party PHP file**

Run: `git ls-files '*.php' | % { php -l $_ }`

Expected: every remaining PHP file reports `No syntax errors detected`.

- [x] **Step 2: Run project tests and builds**

Run: `npm run test:unicode && npm run validate:unicode && npm run build:css && npm run build:js`

Expected: tests, validation, and builds exit successfully.

- [x] **Step 3: Repeat high-risk scans and inspect the diff**

Run: `git diff --check && git status -sb && git diff --stat`

Expected: no unexplained dangerous patterns, whitespace errors, or accidental modifications remain.

- [x] **Step 4: Report findings and residual risks**

Document the reviewed scope, exact deletions, P0-P3 findings, completed cleanup, commands run, and anything requiring production/runtime verification.
