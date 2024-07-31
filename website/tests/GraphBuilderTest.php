<?php

use App\Services\NotionData\Page;
use App\Services\NotionData\Enums\PageType;
use App\Services\NotionData\Graph\GraphBuilder;

function vertex(PageType $category, string $id, array $parents = []): Page
{
    return new Page($category, $id, $parents, []);
}

it('can build a simple graph', function () {
    $pages = [
        vertex(PageType::Category, '1', []),

        vertex(PageType::Category, '2', ['1']),
        vertex(PageType::Category, '3', ['1']),
        vertex(PageType::Category, '4', ['1']),

        vertex(PageType::Category, '5', ['2']),
        vertex(PageType::Entry, '6', ['2']),

        vertex(PageType::Entry, '7', ['5']),
        vertex(PageType::Entry, '8', ['5'])
    ];

    $graph = (new GraphBuilder($pages))->build();

    expect(array_map(fn($n) => $n->id, $graph))->toBe(['1', '2', '5', sha1('7' . '8'), sha1('6'), '3', '4']);
});
