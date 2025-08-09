@props([
    "category",
])
<div class="flex w-fit flex-col items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-0.5 shadow-sm">
    <span class="block font-semibold">
        {{ $category->label }}
    </span>

    @if ($category->dominantDomainDisplay())
        <span class="{{ $category->dominantDomain() === "technical" ? "text-technical" : "text-governance" }} block">
            {{ $category->dominantDomainDisplay() }}
        </span>
    @endif
</div>
