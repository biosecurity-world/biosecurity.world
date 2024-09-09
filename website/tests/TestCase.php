<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Tests\Browser\Utils\Differ;

abstract class TestCase extends BaseTestCase
{
    public function assertTreeRenderingMatchesSnapshot(array $boxes, ?string $extra = null): void
    {
        $id = Str::uuid();
        // We use the cache as an ad-hoc IPC mechanism
        // This way we can have boxes be as large as we want without
        // being limited by the query string length or using a non-GET method.
        cache()->forever('tree-'.$id, $boxes);

        $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        if ($extra) {
            $name .= '-'.Str::slug($extra);
        }

        (new Differ(
            name: $name,
            url: route('tree-rendering', ['caseId' => $id]),
            nodeBinary: config('services.differ.node_path'),
            chromePath: config('services.differ.chrome_path'),
        ))->configureBrowsershot(function (Browsershot $browsershot) {
            $browsershot->waitForFunction('window.visualDiffReady === true');
        })->runTest();
    }

    public function assertDesktopPageMatchesSnapshot(string $url): void
    {
        $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        (new Differ(
            name: $name,
            url: url($url),
            nodeBinary: config('services.differ.node_path'),
            chromePath: config('services.differ.chrome_path'),
        ))->configureBrowsershot(function (Browsershot $browsershot) {
            $browsershot->waitUntilNetworkIdle()->fullPage();
        })->runTest();
    }

    public function assertMobilePageMatchesSnapshot(string $url): void
    {
        $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        (new Differ(
            name: $name,
            url: url($url),
            nodeBinary: config('services.differ.node_path'),
            chromePath: config('services.differ.chrome_path'),
            windowHeight: 812,
            windowWidth: 375,
        ))->configureBrowsershot(function (Browsershot $browsershot) {
            $browsershot
                ->waitUntilNetworkIdle()
                ->userAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1')
                ->deviceScaleFactor(3)
                ->mobile()
                ->touch()
                ->landscape(false)
                ->fullPage();
        })->runTest();
    }

    public function node(float $delta, float $theta, $width = 25, $length = 100): array
    {
        return [
            'sector' => [$delta, $theta],
            'width' => $width,
            'length' => $length,
        ];
    }
}
