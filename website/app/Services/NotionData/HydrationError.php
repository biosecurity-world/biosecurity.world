<?php

namespace App\Services\NotionData;

use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Entry;
use Illuminate\Validation\ValidationException;
use Notion\Pages\Page;

class HydrationError
{
    public function __construct(public $page, public string $message) {}

    public static function fromValidationException(ValidationException $e, $page): self
    {
        $errors = $e->errors();
        if (count($errors) > 1) {
            throw new \InvalidArgumentException('Expected ValidationException to have exactly one error.');
        }

        return new self($page, $e->getMessage());
    }

    public static function fromString(string $message, $page): self
    {
        return new self($page, $message);
    }
}
