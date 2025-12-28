<?php

declare(strict_types=1);

namespace App\Services\NotionData\Models;

class Logo
{
    public function __construct(
        public string $url,
    ) {}
}
