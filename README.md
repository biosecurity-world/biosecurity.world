# biosecurity.world

An interactive, filterable map of organizations working on biosecurity, grouped by what they do (Prevention, Detection, Response, Transversal) and where they operate.

**Live site:** [biosecurity.world](https://biosecurity.world)

[![Screenshot of the biosecurity.world map](docs/landscape.jpg)](https://biosecurity.world)

## Data

The dataset is curated in Notion and refreshed on every deploy (plus a weekly Monday cron). It is openly available as:

- **CSV download** — [`biosecurity.world/data/entries.csv`](https://biosecurity.world/data/entries.csv), also versioned in this repo at [`public/data/entries.csv`](public/data/entries.csv). One row per organization, multi-valued fields joined with `;`.
- **Notion source of truth** — viewable via the "Notion" link in the site footer.

**Suggest a new organization:** [biosecurityworld.notion.site/…](https://biosecurityworld.notion.site/33a4061a75b7806fad1dee0fcd2e921a)

## How it works

There's no application database — Notion is queried at build time, hydrated into domain models, rendered by Laravel + Blade, exported to static HTML by [`bare`](https://github.com/felixdorn/bare), and deployed to Cloudflare Pages via `wrangler`.

## Development

Requires PHP 8.3+, Node, and Nix (the repo ships a `flake.nix` and works with `direnv`).

```bash
direnv allow              # or: nix develop
composer install && pnpm install
cp .env.example .env      # fill NOTION_DATABASE, NOTION_TOKEN, LOGO_DEV_TOKEN

pnpm dev                  # Vite, in one terminal
php artisan serve         # Laravel, in another

composer test             # Pint + PHPStan level 9 + PHPUnit
pnpm test                 # Prettier + TypeScript + Vitest
```

Pushes to `master` (and the Monday cron) run [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml), which builds, exports, deploys, and commits the refreshed CSV back to the repo.

## Credits

Built and maintained by Alix Pham, Sofya Lebedeva, Johan Täng, and Jérémy Andréoletti, with support from Lin Bowker-Lonnecker, Will Saunter, and Félix Dorn. Logos via [logo.dev](https://logo.dev).
