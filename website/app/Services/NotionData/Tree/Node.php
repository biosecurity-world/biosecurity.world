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
}
