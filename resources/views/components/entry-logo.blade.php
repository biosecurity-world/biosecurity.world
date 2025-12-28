@props([
    "logo",
])
<span
    {{
        $attributes->class([
            "entry-logo flex inline-block size-6 items-center justify-center overflow-hidden rounded-md",
        ])
    }}
>
    <img
        loading="lazy"
        decoding="async"
        src="{{ $logo->url }}"
        class="size-6"
        width="128"
        height="128"
        {{ $attributes->get("alt") }}
    />
</span>
