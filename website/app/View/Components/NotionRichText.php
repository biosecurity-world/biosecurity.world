<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Notion\Pages\Properties\RichTextProperty;

class NotionRichText extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public RichTextProperty $text)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.notion-rich-text');
    }
}
