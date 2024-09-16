@props(['name', 'value', 'kind'])
<input type="checkbox" checked name="{{ $name }}" id="{{ $name }}" value="{{ $value  }}" class="sr-only peer checkbox-as-pill {{ $kind }}" {{ $attributes->except(['class', 'style']) }}>
<label for="{{ $name }}" class="{{ $kind }}-label group flex items-center py-1 px-2 rounded-full font-semibold border whitespace-nowrap shadow-sm transition text-sm peer-focus:ring-2 peer-focus:ring-opacity-50 peer-focus:ring-offset-2 peer-focus:ring-primary-600 peer-disabled:pointer-events-none peer-disabled:opacity-25 {{ $attributes->get('class') }}" style="{{ $attributes->get('style') }}">
    {{ $slot }}
</label>
