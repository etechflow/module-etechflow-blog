# Etechflow_Blog

A fast, SEO‑friendly, fully responsive, **theme‑agnostic** blog for Magento 2 / Adobe Commerce.
Runs identically on **Hyvä**, **Luma**, and **Adobe Commerce** themes. Standalone — owns all of its own
data and logic, no third‑party blog engine required.

> Built as an in‑house replacement that covers the full feature set of leading blog extensions (Magefan was
> used only as a feature reference). Nothing in this module depends on Magefan.

---

## Why it works on every theme (and stays fast)

| Concern | Approach |
|---|---|
| **All themes** | Content is **server‑rendered PHTML** (visible without JS). Presentation uses **self‑contained, scoped CSS** (`etf-` prefix) — no Tailwind or theme LESS dependency — so it looks the same on Hyvä and Luma. |
| **No framework lock‑in** | Interactivity (comments, search, lightbox, table of contents, reading progress) uses **tiny vanilla JS** — no jQuery, no RequireJS, no Alpine. |
| **Speed** | Cacheable blocks (FPC/`block_html` friendly), lazy‑loaded images with width/height (zero CLS), responsive `srcset`, deferred JS, optional inline‑CSS minification. |
| **SEO** | Per‑entity meta + canonical + clean permalinks + OpenGraph + Twitter cards + **JSON‑LD** (BlogPosting, BreadcrumbList, Person, Organization) + XML sitemap + RSS autodiscovery. |
| **Responsive** | Mobile‑first scoped CSS, fluid grids, touch‑friendly controls. |

---

## Feature set

**Entities:** Posts · Categories (nested) · Tags · Authors · Comments
**Storefront pages:** Blog home · Post · Category · Tag · Author · Archive (by date) · Search · RSS feed
**Post features:** related posts · related products · author box · reading time · reading progress bar ·
views counter · table of contents · social share · image gallery + lightbox · next/prev · breadcrumbs ·
nested comments (with optional reCAPTCHA) · responsive per‑post banners (desktop/tablet/mobile) · Cloudinary quality option
**Sidebar / widgets:** search · categories · recent · popular · featured · archive · tag cloud · RSS ·
related products · "recent posts" CMS widget · "related posts" block on product pages · blog link in top menu
**Admin:** full CRUD grids + forms for every entity, comment moderation, ACL, and a System Configuration
screen with **a short “how to use” hint under every field**.

---

## Requirements
- Magento 2.4.x / Adobe Commerce (PHP 7.4+ / 8.1+)

## Install
```bash
# 1. Copy this folder to app/code/Etechflow/Blog  (or require via composer)
# 2. Generate the declarative-schema whitelist for this module:
bin/magento setup:db-declaration:generate-whitelist --module-name=Etechflow_Blog
# 3. Enable + upgrade:
bin/magento module:enable Etechflow_Blog
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy        # add locales as needed
bin/magento cache:flush
```
Configure under **Stores → Configuration → Etechflow Blog**.

---

## Directory structure (target)
```
Etechflow_Blog/
├── registration.php
├── composer.json
├── README.md
├── etc/
│   ├── module.xml              ✅
│   ├── db_schema.xml           ✅  (post, category, tag, author, comment + link/store tables)
│   ├── acl.xml                 ✅
│   ├── di.xml                  ✅
│   ├── config.xml              ✅  (all default settings)
│   ├── csp_whitelist.xml       ⬜  (allow share/embed domains)
│   ├── frontend/routes.xml     ✅
│   ├── adminhtml/routes.xml    ✅
│   ├── adminhtml/menu.xml      ✅
│   └── adminhtml/system.xml    ⬜  (config fields + per-field hints)
├── Api/                        ⬜  (service-contract interfaces + Data interfaces)
├── Model/                      ⬜  (models, resource models, repositories, sources, URL resolver)
├── Controller/                 ⬜  (frontend page actions + admin CRUD)
├── Block/ + ViewModel/         ⬜  (rendering + view models)
├── Setup/Patch/Data/           ⬜  (sample author/category seed)
├── view/frontend/              ⬜  (layouts, templates, scoped css, vanilla js)
└── view/adminhtml/             ⬜  (ui_components: grids + forms with field hints)
```

---

## Build progress
- [x] **Phase 1 — Foundation:** module skeleton, full DB schema, routing, ACL, admin menu, DI, default config
- [x] **Phase 2 — Backend:** Api interfaces (Data + Repository + SearchResults) for Post/Category/Tag/Author/Comment, models + resource models + collections + repositories, option sources (status/author/category), permalink builder (`Model/Url`), clean-URL router (`Controller/Router` + `frontend/di.xml`)
- [x] **Phase 3 — Admin:** `system.xml` Configuration screen with a hint under **every** field · grid DI for all entities · full CRUD + grids + forms (per-field hints) for **Post, Category, Tag, Author** (shared generic buttons + configurable actions column) · **Comment moderation** grid (approve / not-approve / delete mass actions + edit/reply form)
- [x] **Phase 4 — Frontend core:** 7 controllers (home/post/category/tag/author/archive/search) + PageContext, PostList/Post/Sidebar blocks, Config view model, layouts (universal 1column + scoped CSS for the 2-col look), `post/list.phtml` + `post/view.phtml` + `sidebar.phtml`, self-contained `blog.css`, vanilla `blog.js`
- [x] **Phase 5 — Post features:** related posts/products, auto table of contents, reading time, reading progress bar, view counter, social share, gallery lightbox, **nested comments + submission controller** (moderation/guest aware) — all config-gated
- [x] **Phase 6 — SEO:** head block with canonical + Open Graph + Twitter cards + JSON‑LD (BlogPosting/Person/Organization) · `csp_whitelist.xml` · store-aware visibility for Category/Tag/Author (shared `StoreAwareTrait`) · **XML sitemap** provider (posts + categories) · **RSS feed** at `/blog/rss`
- [x] **Phase 7 — Widgets & integrations:** sidebar widgets · **CMS "Recent Posts" widget** · **product-page related-posts** block · **top-menu** link (observer)
- [x] **Phase 8 — Polish:** responsive scoped CSS, lazy images, deferred JS, cacheable blocks, install steps documented. _Optional future: GraphQL resolvers, WordPress importer, fileUploader image widgets._

**Status: feature-complete.** Install with the steps above (run the whitelist generator, `setup:upgrade`, `setup:di:compile`, `setup:static-content:deploy`, `cache:flush`).

_Developed locally only — no server/production changes._
