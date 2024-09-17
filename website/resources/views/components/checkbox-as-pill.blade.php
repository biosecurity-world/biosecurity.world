@props([
    "name",
    "value",
    "kind",
])
<input
    type="checkbox"
    checked
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ $value }}"
    class="checkbox-as-pill {{ $kind }} peer sr-only"
    {{ $attributes->except(["class", "style"]) }}
/>
<label
    for="{{ $name }}"
    class="{{ $kind }}-label {{ $attributes->get("class") }} group flex items-center whitespace-nowrap rounded-full border px-2 py-1 text-sm font-semibold shadow-sm transition peer-focus:ring-2 peer-focus:ring-primary-600 peer-focus:ring-opacity-50 peer-focus:ring-offset-2 peer-disabled:pointer-events-none peer-disabled:opacity-25"
    style="{{ $attributes->get("style") }}"
>
    {{ $slot }}
</label>
