<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
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
            $name .= '-'. Str::slug($extra);
        }

        (new Differ(
            name: $name,
            url: route('tree-rendering', ['caseId' => $id]),
            browserWidth: 1920,
            browserHeight: 1080,
            nodeBinary: config('services.differ.node_path'),
            chromePath: config('services.differ.chrome_path'),
            antialias: false,
            threshold: 0.1,
            errorPercentage: 0
        ))->runTest();
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
