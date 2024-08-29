<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Enums\NodeType;

class Page
{
    public function __construct(public NodeType $type, public string      $id, public ?string $parent, public array    $data)
    {
    }
}
