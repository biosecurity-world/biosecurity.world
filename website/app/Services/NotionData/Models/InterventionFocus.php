<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Enums\FocusCategory;
use App\Services\NotionData\Models\Concerns\BelongsToMultiselect;
use App\Support\IdMap;

class InterventionFocus
{
    use BelongsToMultiselect;

    public function category(): ?FocusCategory {
        return match ($id = IdMap::find($this->id)) {
            ">SYG", "h_T{", "hiAX", "|_z{" => FocusCategory::SafeEthicalResearch,
            "APQ~", "e:NA", "SV{{", "83c6fe3d-7d6f-4f25-a203-53f4bde05575" => FocusCategory::Surveillance,
            "CNRk", "hop{", "ZXKA" => FocusCategory::Therapies,
            "k\\\\W", "{[Rj", "8fa4d53b-aa93-49ef-af76-32940fb918e7" => FocusCategory::Preparedness,
            "Yjy:", "K~O{", "f815b152-f213-4e99-bcfa-830e1143e59c" => FocusCategory::EmergingBiotechnologies,
            default => throw new \Exception("Unknown focus category: {$id}"),
        };
    }

    public function globalSortOrder(): int {
        return self::all()->search(fn (int $id) => $id === $this->id);
    }
}
