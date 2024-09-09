<?php

declare(strict_types=1);

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Root;

class Node
{
    public int $depth;

    /** @var array<int> */
    public array $trail = [];

    public int $od;

    public function __construct(
        public int $id,
        public int $parentId
    ) {}

    /** @return array<string> */
    public function breadcrumbs(Tree $tree): array
    {
        $breadcrumbs = [];

        foreach ($this->trail as $id) {
            $page = $tree->lookup[$id];
            if ($page instanceof Root) {
                continue;
            }

            if (! $page instanceof Category) {
                throw new \RuntimeException('Should not happen: a node in the trail is not a category');
            }

            $breadcrumbs[] = $page->label;
        }

        return $breadcrumbs;
    }
}
