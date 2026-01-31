@use(App\Services\NotionData\Enums\OrganizationType)

@props([
    "logo",
    "organizationType" => null,
])
@php
    $orgType = OrganizationType::fromString($organizationType);
    $color = $orgType?->color() ?? OrganizationType::defaultColor();
    $darkColor = $orgType?->darkColor() ?? OrganizationType::defaultDarkColor();
@endphp

<span
    {{
        $attributes->class([
            "entry-logo flex inline-block size-6 items-center justify-center overflow-hidden rounded-md border",
        ])
    }}
    style="background-color: {{ $color->withAlpha(15) }}; border-color: {{ $darkColor }}"
>
    <img
        loading="lazy"
        decoding="async"
        src="{{ $logo->url }}"
        class="size-6 grayscale transition-[filter] duration-200 hover:grayscale-0"
        width="128"
        height="128"
        {{ $attributes->get("alt") }}
    />
</span>
