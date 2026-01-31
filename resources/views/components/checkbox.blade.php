@props([
    "name",
])
<input
    type="checkbox"
    name="{{ $name }}"
    id="{{ $name }}"
    {{ $attributes->class("text-primary-600 focus:ring-primary-600 checked:border-primary-600 size-4 rounded-sm border-gray-300") }}
/>
