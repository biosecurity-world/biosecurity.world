@props(['name'])
<input
    type="checkbox"
    name="{{ $name }}"
    id="{{ $name }}"
    {{ $attributes->class("size-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600") }}
/>
