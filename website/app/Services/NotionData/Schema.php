<?php

namespace App\Services\NotionData;

class Schema
{
    public const array SCHEMA = [
        "0f5d415db7b4410e9e9bab814c37af8e" => [ // Félix's Test database
            "organizationType" => "%3EfkD",
            "link" => "BEe%7D",
            "description" => "C%3Fc%3A",
            "interventionFocuses" => "L%3FRx",
            "parent" => "QTQ%5D",
            "locationHints" => "VQ%5B%7D",
            "activityTypes" => "Wmi~",
            "gcbrFocus" => "kC%5Cr",
            "name" => "title",
            "isCategory" => "uR%3DA",
        ]
    ];

    public static function schemaForDatabase(string $databaseId): array
    {
        $databaseId = str_replace('-', '', $databaseId);
        if (!array_key_exists($databaseId, self::SCHEMA)) {
            throw new \Exception("Could not find a schema for the database `" . $databaseId . "`");
        }

        return self::SCHEMA[$databaseId];
    }
}
