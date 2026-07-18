<?php

declare(strict_types=1);

return [
    /*
     * Explicit deployment timestamp (ISO-8601). Optional: when unset, the app
     * falls back to now(), which for the static export equals the moment the
     * site was rebuilt & deployed. Can be provided by the deploy workflow.
     */
    'deployed_at' => env('DEPLOYED_AT'),
];
