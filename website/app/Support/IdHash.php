<?php

declare(strict_types=1);

namespace App\Support;

/**
 * @class Takes all the external IDs used during a request and reduces them to a number.
 * This is useful because Notion uses UUIDs for IDs, and we reference them _a lot_.
 */
class IdHash
{
    /** @var array<string|int, int> */
    protected static array $idMap = [];

    protected static int $counter = 0;

    public static function next(): int
    {
        return self::$counter++;
    }

    public static function last(): int
    {
        return self::$counter;
    }

    public static function hash(string|int $id): int
    {
        // PHP's type casting is a foot gun, this is foot armor.
        if (is_int($id)) {
            throw new \RuntimeException('You are trying to hash an integer. Was it already hashed?');
        }

        if (! isset(self::$idMap[$id])) {
            self::$idMap[$id] = self::next();
        }

        return self::$idMap[$id];
    }
}