<?php

namespace BeyondCode\VisualDiff;

use Illuminate\Testing\TestResponse;
use Laravel\Dusk\Browser;
use Illuminate\Support\ServiceProvider;
use Psy\Util\Str;

class VisualDiffServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('visualdiff.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'visualdiff');

        TestResponse::macro('visualDiff', function (?string $extra = null, ?string $url = null) {
            $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)[4]['function'];
            if (!is_null($extra)) {
                $name .= '-' . $extra;
            }

            $tester = new VisualDiffTester($this->content(), $name, config('visualdiff.resolutions'));
            $tester->setScreenshotOutputPath(config('visualdiff.screenshot_path'));
            $tester->setDiffOutputPath(config('visualdiff.diff_path'));

            $tester->createDiffs($url);

            return $this;
        });
    }
}
