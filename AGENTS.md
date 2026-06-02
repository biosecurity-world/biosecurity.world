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
composer test        # Run all checks (lint + types)
composer test:types  # PHPStan level 9
composer test:lint   # Check PHP formatting

npm run test:lint    # Check Prettier formatting
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
- **Location region layout**: Uses a 4-column CSS grid with explicit column assignments defined in `ShowWelcomeController` (`$locationColumns`). USA and Europe get dedicated columns (3 and 4) due to their size (25 and 31 locations respectively).
- **Entry detail panel**: Left-side overlay loaded via AJAX from `/partials/entries/{entrygroup}/{entry}`. Closes on cross button or clicking outside
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

The workflow triggers on push but **pushing to `master` alone does not update the production site**. To deploy to `biosecurity-world.pages.dev`, you must manually trigger the workflow:
```bash
gh workflow run deploy.yml --ref master
```

The workflow path filter excludes `.github/**`, so workflow-only changes also need `workflow_dispatch` to trigger.

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
