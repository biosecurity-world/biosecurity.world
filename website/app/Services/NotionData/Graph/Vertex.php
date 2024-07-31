<?php

namespace App\Services\NotionData\Graph;

class Vertex
{
    public int $siblingsCount;
    public int $index;

    public function __construct(
        public string $id,
        public string $label,
        public string $parentId,
        public array $children = []
    )
    {
    }
}
