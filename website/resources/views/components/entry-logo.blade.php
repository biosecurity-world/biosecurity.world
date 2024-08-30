@props(['logo', 'size'])
@if ($logo && $logo->filled)
    <img
        decoding="async" loading="lazy"
        src="{{ $logo->url ?? '/images/missing-icon.svg' }}"
        alt=""
        style="width: {{ $size + 2 }}px; height: {{ $size + 2 }}px"
        {{ $attributes->class('rounded-md inline') }}
    />
@else
    <span {{ $attributes->class('inline-flex size-6 items-center justify-center rounded-md border border-solid border-gray-200 bg-gray-50') }}>
        <img
            decoding="async" loading="lazy"
            src="{{$logo ? $logo->url : '/images/missing-icon.svg'}}"
            alt=""
            style="width: {{ $size }}px; height: {{ $size }}px"
            class="inline"
        />
    </span>
@endif
