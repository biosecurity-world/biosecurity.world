<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\NotionClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * Write a sitemap for the static site.
 *
 * This matters more here than on a typical site: the only HTML links to the
 * 168 entry pages live in the map partial, whose URL is built in JavaScript,
 * so a crawler that does not execute JS finds no path to them at all. The
 * sitemap makes discovery independent of that.
 *
 * It writes straight into the export directory rather than public/, because
 * `bare` only emits the pages it crawls and does not copy arbitrary public/
 * files — the same reason deploy.yml copies the CSV and the fonts by hand.
 * Run it after `bare export`.
 *
 * URLs carry a trailing slash so they match the canonical tags and the form
 * Cloudflare Pages serves without a 308.
 *
 * No <lastmod>: Notion gives us a creation date, not a content-modification
 * date, and stamping every URL with the deploy time would claim a change that
 * did not happen. <changefreq> and <priority> are omitted because Google
 * ignores them.
 */
class GenerateSitemap extends Command
{
    protected $signature = 'app:generate-sitemap {path?}';

    protected $description = 'Write sitemap.xml for the exported static site';

    public function handle(NotionClient $notion): int
    {
        /** @var string $path */
        $path = $this->argument('path') ?? base_path('dist/sitemap.xml');

        $dir = dirname($path);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            $this->error("Could not create directory: $dir");

            return self::FAILURE;
        }

        $base = rtrim(Config::string('seo.url'), '/');

        $locations = ['/'];
        foreach ($notion->tree()->entries()->sortBy(fn (Entry $e) => $e->id) as $entry) {
            $locations[] = sprintf('/entry/%d/%s/', $entry->id, $entry->slug());
        }

        $body = '';
        foreach ($locations as $location) {
            $body .= '    <url><loc>'.htmlspecialchars($base.$location, ENT_XML1).'</loc></url>'."\n";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$body
            .'</urlset>'."\n";

        if (file_put_contents($path, $xml) === false) {
            $this->error("Could not write $path");

            return self::FAILURE;
        }

        $this->info(sprintf('Wrote %d URLs to %s', count($locations), $path));

        return self::SUCCESS;
    }
}
