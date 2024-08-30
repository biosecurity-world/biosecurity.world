<?php

namespace App\Services\Iconsnatch;

class Logo
{
    protected function __construct(
        public string $url,
        public bool $filled,
        public string $version,
    )
    {
    }

    /**
     * @param \stdClass $data
     * @return Logo|null
     */
    public static function fromResponse(\stdClass $data): ?Logo
    {
        if (!$data->success || $data->value === "") {
            return null;
        }

        return new self(
            url: $data->value,
            filled: $data->meta->filled === "yes",
            version: $data->meta->version,
        );
    }
}
