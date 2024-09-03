@props(['logo'])
<span {{ $attributes->class([
        "inline-block size-6 border rounded-md entry-logo overflow-hidden",
        'p-[0.188rem]' => !$logo->filled
]) }} >
    <img src="{{ $logo->path }}" alt="" />
</span>
