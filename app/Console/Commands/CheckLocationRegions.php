<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\LocationHint;
use App\Services\NotionData\NotionClient;
use Illuminate\Console\Command;

class CheckLocationRegions extends Command
{
    protected $signature = 'app:check-location-regions';

    protected $description = 'Report location hints that are not mapped to a location region';

    public function handle(NotionClient $notion): int
    {
        $unknownLocations = [];

        $notion->tree()->entries()->each(function (Entry $entry) use (&$unknownLocations) {
            $entry->locationHints->each(function (LocationHint $location) use ($entry, &$unknownLocations) {
                if ($location->isTopLevel() || $location->region() !== null) {
                    return;
                }

                $unknownLocations[$location->label] ??= [];
                $unknownLocations[$location->label][] = $entry->label;
            });
        });

        if ($unknownLocations === []) {
            $this->info('All location hints are mapped to a location region.');

            return self::SUCCESS;
        }

        ksort($unknownLocations);

        foreach ($unknownLocations as $location => $entryLabels) {
            $entryLabels = array_values(array_unique($entryLabels));
            sort($entryLabels);

            $message = sprintf(
                'Unmapped location hint "%s" used by: %s',
                $location,
                implode('; ', $entryLabels)
            );

            if (getenv('GITHUB_ACTIONS') === 'true') {
                $this->line('::warning title=Unmapped location hint::'.$message);
            }

            $this->warn($message);
        }

        $this->error('Add each new location to App\Services\NotionData\Enums\LocationRegion before deploying.');

        return self::FAILURE;
    }
}
