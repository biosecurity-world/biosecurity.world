<?php

namespace App\Services\NotionData\Utils;

use App\Services\NotionData\Utils;

class IdPool
{
    /** @var array<string|int, int> */
    private static array $idMap = [];
    private static int $count = 0;

    public static function getNextId(): int
    {
        self::$count += 1;
        return self::$count;
    }

    public static function getIdFor(string|int $id): int
    {
        if (array_key_exists($id, static::$idMap)) {
            return static::$idMap[$id];
        }

        self::$count += 1;
        static::$idMap[$id] = self::$count;
        return static::$idMap[$id];
    }
}
