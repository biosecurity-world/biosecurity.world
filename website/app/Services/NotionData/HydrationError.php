<?php

namespace App\Services\NotionData;

use Illuminate\Validation\ValidationException;
use Notion\Pages\Page;

class HydrationError
{
    public function __construct(public string $id, public string $message) {}

    public static function fromValidationException(ValidationException $e, Page $page): self
    {
        $errors = $e->errors();
        if (count($errors) > 1) {
            throw new \InvalidArgumentException('Expected ValidationException to have exactly one error.');
        }

        return new self($page->id, $e->getMessage());
    }

    public static function fromString(string $message, Page|string $page): self
    {
        return new self(is_string($page) ? $page : $page->id, $message);
    }
}
