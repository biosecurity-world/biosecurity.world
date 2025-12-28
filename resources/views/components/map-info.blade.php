@props([
    // \Carbon\CarbonInterface|null
    'lastEditedAt' => null,
    // Optional extra classes for the wrapper
    'class' => '',
])

<div {{ $attributes->merge(['class' => $class]) }}>
    <div class="flex items-center">
        <h3 class="font-display flex-1 text-2xl">Map of Biosecurity</h3>
    </div>

    @if ($lastEditedAt)
        <p class="mt-1 text-gray-700">
            Last updated on <time datetime="{{ $lastEditedAt->toIso8601String() }}" title="{{ $lastEditedAt->diffForHumans() }}">{{ $lastEditedAt->format('F j, Y') }}</time>.
        </p>
    @endif
</div>
