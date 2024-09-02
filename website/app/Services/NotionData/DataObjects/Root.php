<?php

namespace App\Services\NotionData\DataObjects;

#[\AllowDynamicProperties]
class Root
{
    public function __construct(
        public int $id
    ) {}
}
