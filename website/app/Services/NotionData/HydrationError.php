<?php

namespace App\Services\NotionData;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class HydrationError
{
    public function __construct(public $page, public array $messages) {}

    public static function fromValidationException(ValidationException $e, $page): self
    {
        return new self($page, Arr::flatten($e->errors(), 1));
    }

    public static function fromString(string $message, $page): self
    {
        return new self($page, [$message]);
    }
}
