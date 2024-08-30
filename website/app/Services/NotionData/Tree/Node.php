<?php

namespace App\Services\NotionData\Tree;

class Node
{
    public function __construct(
        public string $id,
        public string $parentId,
        /** @var string[] $trail */
        public array $trail = [],
        /** @var Node[] $children */
        public array $children = []
    ) {}
}
