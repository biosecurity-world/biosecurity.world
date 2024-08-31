<?php

namespace App\Services\NotionData;

#[\AllowDynamicProperties]
class Entrygroup
{
    public function __construct(
        public string $id,
        /** @var string[] */
        public array $entries,
    ) {}
}
