<?php

declare(strict_types=1);

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\DataObjects\Category;

class Node
{
    public int $depth;

    /** @var array<int> */
    public array $trail = [];

    public int $od;

    public int $order;

    public function __construct(
        public int $id,
        public int $parentId
    ) {}

    /** @return array<string> */
    public function breadcrumbs(Tree $tree): array
    {
        return array_map(function ($id) use ($tree) {
            $page = $tree->lookup[$id];
            if (! $page instanceof Category) {
                throw new \RuntimeException('Should not happen: a node in the trail is not a category');
            }

            return $page->label;
        }, $this->trail);
    }
}
