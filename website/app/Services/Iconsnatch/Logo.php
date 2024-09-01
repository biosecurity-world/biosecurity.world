<?php

namespace App\Services\Iconsnatch;

class Logo
{
    protected function __construct(
        public string $url,
        public bool $filled,
        public string $version,
    ) {}

    public static function fromResponse(\stdClass $data): ?Logo
    {
        if (! $data->success || $data->value === '') {
            return null;
        }

        return new self(
            url: $data->value,
            filled: $data->meta->filled === 'yes',
            version: $data->meta->version,
        );
    }

    public static function zero(): Logo
    {
        return new self(
            url: '/images/missing-logo.svg',
            filled: false,
            version: '',
        );
    }
}
