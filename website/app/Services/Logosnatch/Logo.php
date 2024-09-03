<?php

namespace App\Services\Logosnatch;

class Logo
{
    public function __construct(
        public string $format,
        public string $path,
        public bool $filled,
    ) {}
}
