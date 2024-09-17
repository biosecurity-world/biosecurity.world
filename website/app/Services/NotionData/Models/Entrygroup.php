<?php

namespace App\Services\NotionData\Models;

class Entrygroup
{
    public function __construct(
        public int $id,
        /** @var int[] */
        public array $entries,
    ) {}
}
