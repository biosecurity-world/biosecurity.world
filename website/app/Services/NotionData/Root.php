<?php

namespace App\Services\NotionData;

#[\AllowDynamicProperties]
class Root
{
    public function __construct(
        public string $id
    ) {}
}
