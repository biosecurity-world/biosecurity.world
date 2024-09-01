<?php

declare(strict_types=1);

namespace App\Services\NotionData\Tree;

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

    public function breadcrumb(Tree $tree): array
    {
        return array_map(fn ($id) => $tree->lookup[$id]->label, $this->trail);
    }
}
