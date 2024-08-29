<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Psy\Util\Str;

abstract class TestCase extends BaseTestCase
{
    public function assertTreeRenderingMatchesSnapshot(array $boxes, string $name = null): void
    {
        $id = \Illuminate\Support\Str::uuid();
        // We use the cache as an ad-hoc IPC mechanism
        // This way we can have boxes be as large as we want without
        // being limited by the query string length or using a non-GET method.
        \Cache::rememberForever('tree-' . $id, fn () => $boxes);

        $url = route('tree-rendering', ['caseId' => $id]);
        $response = $this->get($url);
        $response->assertStatus(200);

        $response->visualDiff($name !== null ? \Illuminate\Support\Str::slug($name) : null, $url);
    }

    function vertex(float $delta, float $theta, $width = 25, $length = 100,): array
    {
        return [
            'sector' => [$delta, $theta],
            'width' => $width,
            'length' => $length,
        ];
    }
}
