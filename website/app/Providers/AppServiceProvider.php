<?php

namespace App\Providers;

use App\Services\NotionData\NotionWrapper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NotionWrapper::class, function() {
            $database = config('services.notion.database');
            $token = config('services.notion.token');

            if (empty($database)) {
                throw new \Exception("No notion database specified, please set the environment variable NOTION_DATABASE.");
            }

            if (empty($token)) {
                throw new \Exception("No notion token specified, please set the environment variable NOTION_TOKEN.");
            }

            return new NotionWrapper($database, $token);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
