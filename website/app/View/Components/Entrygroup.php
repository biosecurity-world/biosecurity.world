<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Entrygroup extends Component
{
    /** @var Collection<string, array> */
    public Collection $groups;

    public function __construct(array $entries, public string $id)
    {
        $this->groups = collect($entries)
            ->groupBy('organizationType')
            ->sortKeysDesc();
    }

    public function render(): View
    {
        return view('components.entrygroup');
    }
}
