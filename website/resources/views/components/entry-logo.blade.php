@props(['logo'])
    <span {{ $attributes->class([
        "inline-block border rounded-md entry-logo overflow-hidden size-6 flex items-center justify-center",
        'p-[0.125rem]' => !$logo->filled,
        'border-transparent' => $logo->filled,
]) }} >
    <img loading="lazy" decoding="async" src="{{ $logo->path }}" alt=""
         @if ($logo->filled)
             class="size-6"
         @elseif($logo->size === 64)
             class="size-5"
         @else
             style="width: {{ 50 + 100 *( ($logo->size - 16) / 48)  }}%;"
         @endif

         width="{{ $logo->size }}" height="{{ $logo->size }}"
        {{ $attributes->get('alt') }} />
</span>
