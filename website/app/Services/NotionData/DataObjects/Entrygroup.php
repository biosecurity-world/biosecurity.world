<?php

namespace App\Services\NotionData\DataObjects;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Entrygroup
{
    public function __construct(
        public int $id,
        /** @var int[] */
        public array $entries,
    ) {}
}
