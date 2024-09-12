<?php

namespace App\Providers;

use App\Services\NotionData\Notion;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Notion::class, function () {
            $database = config('services.notion.database');
            $token = config('services.notion.token');

            if (empty($database) || ! is_string($database)) {
                throw new \Exception('No notion database specified, please set the environment variable NOTION_DATABASE.');
            }

            if (str_contains($database, '-')) {
                throw new \Exception('The database ID should not contain dashes.');
            }

            if (empty($token) || ! is_string($token)) {
                throw new \Exception('No notion token specified, please set the environment variable NOTION_TOKEN.');
            }

            return new Notion($database, $token);
        });
    }
}
