<?php

declare(strict_types=1);

return [
    /*
     * Canonical origin of the public site, used to build absolute canonical and
     * og:url values. This is deliberately NOT app.url: the static export is
     * crawled over http://localhost:8000, so app.url points at localhost during
     * the build and would leak into the exported metadata.
     */
    'url' => rtrim((string) env('SEO_URL', 'https://biosecurity.world'), '/'),

    /*
     * og:site_name, and the suffix already used in <title>.
     */
    'site_name' => 'biosecurity.world',

    /*
     * Fallback meta description, used for any page that does not pass its own.
     */
    'description' => 'A crowd-sourced, open-data map of the organizations working on biosecurity worldwide. Filter them by activity, intervention focus and location.',
];
