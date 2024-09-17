@props([
    "logo",
])
<span
    {{
        $attributes->class([
            "entry-logo inline-block flex size-6 items-center justify-center overflow-hidden rounded-md border",
            "p-[0.125rem]" => ! $logo->filled,
            "border-transparent" => $logo->filled,
        ])
    }}
>
    @if ($logo->format === "svg")
        <img
            loading="lazy"
            decoding="async"
            src="{{ $logo->path }}"
            class="{{ $logo->filled ? "size-6" : "size-5" }}"
            width="64"
            height="64"
            {{ $attributes->get("alt") }}
        />
    @else
        <img
            loading="lazy"
            decoding="async"
            src="{{ $logo->path }}"
            @if ($logo->filled)
                class="size-6"
            @elseif ($logo->size === 64)
                class="size-5"
            @else
                style="width: {{ 50 + 100 * (($logo->size - 16) / 48) }}%;"
            @endif
            width="{{ $logo->size }}"
            height="{{ $logo->size }}"
            {{ $attributes->get("alt") }}
        />
    @endif
</span>
