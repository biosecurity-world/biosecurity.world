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
            return new NotionWrapper(
              config('services.notion.database'),
              config('services.notion.token'),
            );
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
