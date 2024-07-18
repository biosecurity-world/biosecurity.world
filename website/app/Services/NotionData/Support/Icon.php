<?php

namespace App\Services\NotionData\Support;

class Icon
{
    protected function __construct(
        public string $url,
        public bool $filled,
        public string $version,
    )
    {
    }

    public static function fromResponse(object $data): Icon
    {
        return new self(
            url: $data->value,
            filled: $data->meta->filled === "yes",
            version: $data->meta->version,
        );
    }
}
