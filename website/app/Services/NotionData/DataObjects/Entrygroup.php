<?php

namespace App\Services\NotionData\DataObjects;

#[\AllowDynamicProperties]
class Entrygroup
{
    public function __construct(
        public string $id,
        /** @var int[] */
        public array $entries,
    ) {}
}
