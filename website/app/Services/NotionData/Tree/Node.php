<?php

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\Enums\NodeType;

class Node
{
    /** @var Node[] $children */
    public array $children = [];

    public function __construct(
        public string $id,
        public string $parentId,
        public array $trail = []
    )
    {
    }
}
