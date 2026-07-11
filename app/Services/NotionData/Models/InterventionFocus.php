<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Enums\FocusCategory;
use App\Services\NotionData\Models\Concerns\BelongsToMultiselect;
use App\Support\IdMap;

class InterventionFocus
{
    use BelongsToMultiselect;

    public function category(): FocusCategory
    {
        // First check by name for specific overrides
        return match ($this->label) {
            'Epidemiology' => FocusCategory::DetectionAndEarlyWarning,
            'Indoor air quality' => FocusCategory::ResponseAndConsequenceManagement,
            default => $this->categoryById(),
        };
    }

    private function categoryById(): FocusCategory
    {
        return match (IdMap::find($this->id)) {
            '|_z{', 'KnnZ', 'f815b152-f213-4e99-bcfa-830e1143e59c', 'K~O{', 'hiAX', 'Yjy:', '>SYG', 'h_T{' => FocusCategory::UpstreamInterventions,
            'APQ~', 'SV{{', 'e:NA' => FocusCategory::DetectionAndEarlyWarning,
            'k\\W', '{[Rj', 'ZXKA', 'hop{', 'CNRk', '=lC^', '8fa4d53b-aa93-49ef-af76-32940fb918e7' => FocusCategory::ResponseAndConsequenceManagement,
            default => FocusCategory::Uncategorized,
        };
    }

    public function globalSortOrder(): int
    {
        $offset = self::all()->search(fn (int $id) => $id === $this->id);

        if ($offset === false) {
            throw new \LogicException('Intervention focus is missing from its own registry.');
        }

        return $offset;
    }
}
