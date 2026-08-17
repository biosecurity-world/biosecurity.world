# AGENTS.md

This file provides guidance to Codex (Codex.ai/code) when working with code in this repository.

## Commands

### Development
```bash
# Start development (run both in separate terminals)
npm run dev          # Vite dev server for frontend assets
php artisan serve    # Laravel dev server
```

### Testing & Linting
```bash
composer test        # Pint --test + PHPStan + PHPUnit
composer test:types  # PHPStan level 9
composer test:lint   # Check PHP formatting
composer test:unit   # PHPUnit

npm run test         # Prettier --check + tsc --noEmit + Vitest
npm run test:lint    # Check Prettier formatting
npm run test:types   # TypeScript, strict mode
npm run test:unit    # Vitest
```

### Formatting
```bash
npm run lint         # Format JS/TS/Blade with Prettier
composer lint        # Format PHP with Pint
```

## Architecture

This is a Laravel 12 application that displays biosecurity organizations/initiatives. **There is no database** - all data comes from Notion via API.

### Data Flow
```
Notion Database → NotionClient → Hydrator → Domain Models → Tree → Controllers → Blade Views
```

### Key Services (`app/Services/NotionData/`)

- **NotionClient**: Fetches and caches Notion pages indefinitely
- **Hydrator**: Transforms Notion pages into domain models (Entry, Category, etc.)
- **Tree/Tree**: Builds hierarchical structure for display

### Logos

Logos are fetched from `logo.dev` API using the domain from entry URLs. Requires `LOGO_DEV_TOKEN` in `.env`.

### Domain Models (`app/Services/NotionData/Models/`)

- `Entry` - Individual biosecurity organization/initiative
- `Entrygroup` - Container grouping related entries
- `Category` - Hierarchical grouping
- `Activity`, `InterventionFocus`, `LocationHint` - Entry metadata
- `Logo` - Simple URL wrapper for logo.dev images

### Design system (`resources/css/main.css`)

The site uses a warm "paper" palette rather than Tailwind's cold grays. Custom `@theme` tokens: `paper` (page background), `card`, `sand-100…400` (borders and muted fills), `ink` / `ink-muted` (text), `band` / `band-light` (dark green filter and FAQ bands), `hero` (h1 green), `mint-border`. Prefer these over `gray-*` for surfaces, borders and body text; `gray-400` is still used for small decorative icons.

`--font-display` is **Space Grotesk**, self-hosted from `public/fonts/` as a variable woff2 (one file per unicode subset, covering weights 300–700) with `@font-face` declared in `main.css`. There is no Google Fonts request. `bare` does not copy arbitrary `public/` files into the static export, so `deploy.yml` has an explicit step copying `public/fonts/*.woff2` into `dist/fonts/` — mirroring the CSV step. Adding a font file means updating that step too.

Map colors live in `layout.ts` (`HIGH_LEVEL_CATEGORIES`), not in CSS: each zone renders as a white surface with a 3px colored top rule and a colored dot, while subcategories share one neutral treatment (`SUBCATEGORY_BG` / `SUBCATEGORY_BORDER`).

### Frontend (`resources/js/`)

- `map.ts` - Main entry point: loads the map partial, initializes Panzoom, sets up filter store, handles entry open/close
- `filters.ts` - `FiltersState` class managing bitmask-based filters with URL query param sync. `shouldFilterEntry()` uses bitwise AND to check activity/focus/domain matches
- `layout.ts` - Nested box layout renderer. Builds HTML-in-SVG (via foreignObject) with high-level category boxes (Prevention, Detection, Response, Transversal) containing subcategory boxes and entry pills

### Filtering

Filters use bitmasks for fast matching. Each activity and intervention focus is assigned a bit position. The filter state is a set of bitmasks compared via bitwise AND with each entry's bitmask. Entries without intervention focuses (e.g. newsletters) should pass through focus filters.

**Location filtering** uses BigInt bitmasks (82+ locations exceed JS 32-bit limit). PHP uses GMP to compute location bitmasks as strings; JS wraps them with `BigInt()`. Location bitmask is at index `[4]` in `filterData`.

### Enums (`app/Services/NotionData/Enums/`)

- `LocationRegion` - Geographic regions mapping location hints to their parent region (USA, UK, Europe, East Asia & Pacific, South Asia, Central Asia, Middle East, North Africa, Sub-Saharan Africa, North America excl. US, South America). Defines `TOP_LEVEL_LABELS` (Global, Remote) shown as standalone pills, `childLabels()` for locations within each region, and `headerLocationLabel()` returning the location hint that acts as the region header.
- `FocusCategory` - Groups intervention focuses into categories (Prevention, Detection, Response).

### UI Patterns

- **Collapsible bands**: Filter panel and FAQ section use a reusable `setupToggle(toggleId, contentId, chevronId, hintId)` pattern in `welcome.blade.php` inline JS
- **Filter pill click behavior**: Three-state logic for focus and location pills: (1) all checked → click one = exclusive select; (2) not all checked → click = additive toggle; (3) only this one checked → click = re-enable all. Activity pills use a simpler exclusive-only pattern.
- **Region/category label click**: Clicking a region label (Locations) or category label (Intervention focuses) keeps only that group active; clicking again re-enables all. This is distinct from the master checkbox which toggles the group on/off.
- **Top-level location pills** (Global, Remote): Simple independent toggles, not affected by exclusive click behavior on region locations. Marked with `data-top-level="true"`.
- **Country vs city display**: Within each region, locations are sorted country-first (each country followed by its cities). Countries are styled with `uppercase` text and `rounded-md` shape; cities use the default pill style. The `LocationHint` model has `isCountry()`, `parentCountryLabel()`, and `CITY_TO_COUNTRY` mapping.
- **Location region layout**: Regions flow through a CSS multi-column container (`columns-1 sm:columns-2 md:columns-3`), each wrapper using `break-inside-avoid` so a region is never split across columns.
- **Collapsed location pills**: A region with more than 8 locations renders only its first 6; the rest get `.location-overflow hidden` and a `.location-more-toggle` button labelled `+ N…` reveals them (toggling to `− less`). The handler lives in the `welcome.blade.php` inline JS. Hidden pills stay checked and keep their `data-global-offset`, so collapsing never changes the filter bitmask.
- **Proportional focus columns**: The intervention-focus grid does not use equal thirds. `ShowWelcomeController` computes `$focusColumns` — one `fr` value per category, weighted by the total label length of its pills plus `PILL_BASE_WIDTH` per pill — and the view feeds it through `style="--focus-columns: …"` + `md:grid-cols-(--focus-columns)`. This keeps a short category (Detection, 4 pills) from reserving a full third. `fr` keeps min-content as its floor, so headings never get crushed, and the ratio adapts on its own when focuses are added in Notion.
- **Entry detail panel**: Left-side overlay loaded via AJAX from `/partials/entries/{entrygroup}/{entry}`. Closes on cross button or clicking outside
- **Result counter**: The filter band shows `X / total organizations`. `map.ts` recomputes `X` on every filter change by counting distinct `data-entry` values carrying `.matches-filters` — an entry listed under several categories counts once, so this number is lower than the number of pills drawn on the map.
- **Prettier compatibility**: Inline links in `<p>` tags use `<!-- prettier-ignore -->` to prevent Prettier from adding whitespace around `<a>` tags

### Routes (`routes/web.php`)

- `/` - Main page with filterable entries
- `/entry/{id}/{slug}` - Entry detail page
- `/partials/*` - AJAX partials for entries and map

### Deployment

GitHub Actions workflow (`.github/workflows/deploy.yml`) deploys to Cloudflare Pages:
1. Builds frontend assets with Vite (`pnpm build`)
2. Installs PHP dependencies with Composer
3. Starts Laravel dev server, then exports static site with `bare`
4. Deploys to Cloudflare Pages via `wrangler`

**Pushing code to `master` does deploy to production.** The push trigger matches every path except `.github/**` and `public/data/**`, so any change under `app/`, `resources/`, `public/` (outside `data/`) etc. ships as soon as it lands on master. There is also a weekly run (Mondays 06:00 UTC) that refreshes the data.

Trigger the workflow by hand only when the push trigger will not fire — a workflow-only change, or re-running a deploy without a new commit:
```bash
gh workflow run deploy.yml --ref master
```
Beware that pushing and dispatching together starts two concurrent runs, both of which deploy.

**Verifying a deploy — use the right URL.** The live site is `https://biosecurity.world`, and the Pages project's own subdomain is `https://biosecurity-world-966.pages.dev` (note the suffix; wrangler prints it at the end of the deploy step). `biosecurity-world.pages.dev` is a *different* project serving a stale copy — and it answers `200` with fallback HTML even for paths that do not exist, so checking it will silently mislead you. When verifying an asset, assert on the content type and size, not just the status code:
```bash
curl -s -o /dev/null -w '%{http_code} %{content_type} %{size_download}\n' https://biosecurity.world/fonts/space-grotesk-latin.woff2
```

## Environment Setup

Uses Nix for reproducible dev environment:
```bash
direnv allow  # If using nix-direnv
# or
nix develop
```

Requires: PHP 8.3+, Node

## Code Style

- PHPStan level 9 (strict static analysis)
- TypeScript strict mode
- Prettier for JS/TS/Blade formatting
- Pint for PHP formatting

## Technical Gotchas

- **Blade `@php` blocks after components**: Placing `@php...@endphp` blocks with complex expressions (e.g. enum references like `LocationRegion::USA`) after `<x-checkbox-as-pill>` or other Blade components can cause Blade compiler failures ("unexpected token 'endif'"). The compiled PHP mixes uncompiled directives. **Fix**: move complex PHP logic to the controller and pass data to the view.
- **CSS `columns` vs `flex-wrap` for entry pills**: CSS `columns` forces children to stretch to fill column width, making pills unnaturally wide. Use `flex-wrap` with `display: inline-flex` on children for compact fit-content pills.
- **layout.ts renders HTML inside SVG**: The map uses `foreignObject` elements to embed HTML within SVG. All DOM manipulation in `layout.ts` creates HTML elements that get wrapped in a foreignObject for panzoom support on desktop, or rendered as plain HTML above the SVG on mobile.
- **Composer resolves against the PHP running it**: CI builds with `pkgs.php83` (`flake.nix`), so a `composer update` run on a newer local PHP can lock packages that CI cannot install — `composer install` then fails the deploy with *"Your lock file does not contain a compatible set of packages"*, before any deploy step runs. This bit once when Symfony 8.1 components (requiring PHP >= 8.4.1) got locked from a local PHP 8.5. `composer.json` now pins `config.platform.php` to `8.3.11` so resolution is reproducible regardless of local PHP. **Keep that pin in sync with `flake.nix` when the CI PHP version moves**, and don't remove it to "get newer packages" — that just moves the failure to CI.
- **Pint upgrades can reformat untouched files**: new Pint releases enable new fixers (e.g. 1.30 added `fully_qualified_strict_types`, which rewrote 5 files). After bumping `laravel/pint`, expect `composer test` to fail on formatting; run `composer lint` and commit the reformatting separately from the dependency bump.
