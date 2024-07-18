<?php

namespace App\Services\NotionData\GraphBuilder;

use App\Services\NotionData\Enums\NodeType;

class Node
{
    public array $children = [];

    public function __construct(
        public NodeType $type,
        public string      $id,
        public string   $label = "",
    )
    {
    }
}
