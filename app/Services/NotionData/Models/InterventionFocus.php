<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Enums\FocusCategory;
use App\Services\NotionData\Models\Concerns\BelongsToMultiselect;
use App\Support\IdMap;

class InterventionFocus
{
    use BelongsToMultiselect;

    public function category(): ?FocusCategory
    {
        return match ($id = IdMap::find($this->id)) {
            // Upstream Interventions
            'b0429371-b6ef-4f35-9a93-2f2de3b8f9db',
            '0f63a586-4183-4caa-b36d-9426f95f55ab',
            '439e9965-ea05-43b8-859e-6b72d8a124d8',
            '3e706d09-46f7-489d-9ea3-afaa14251e4c',
            '6f98ce49-31e6-4455-9206-474858d53966' => FocusCategory::UpstreamInterventions,

            // Detection and Early Warning
            '37d0706c-e716-444e-a245-392933eb192d',
            '5b0337ba-508b-4516-83e9-c53c87a48074',
            '63b0606f-66d1-4937-a8be-af75d22651cd',
            '6fee6294-67d7-4cb8-aa9f-d8c40d1fe146' => FocusCategory::DetectionAndEarlyWarning,

            // Response and Consequence Management
            '4b97bdcc-415e-40bd-a6c1-5ddf42e26a50',
            'd38e7662-1b90-4f14-a18d-d2434176dcfc',
            'e80e3b20-7d13-4097-bef6-b1ee5287d456',
            'eba69807-d0ce-4635-a120-5dfea7bed703',
            'ea850109-25e5-480b-8c16-fef8834b5f45',
            '7978ca82-618e-473f-a622-8c55c73701ca' => FocusCategory::ResponseAndConsequenceManagement,

            // Cross-cutting Topics
            '09c5b80c-a4ce-4de2-94f2-22eb6c5cf3c',
            'c8ecfb42-b364-4dc5-8f17-636140d5f45e',
            '426f1779-35d6-4baa-8cae-3946ba8c6e52',
            '4eea4955-bfa9-433a-8c20-3055493bd83f' => FocusCategory::CrossCuttingTopics,

            default => FocusCategory::Uncategorized,
        };
    }

    public function globalSortOrder(): int
    {
        return self::all()->search(fn (int $id) => $id === $this->id);
    }
}
