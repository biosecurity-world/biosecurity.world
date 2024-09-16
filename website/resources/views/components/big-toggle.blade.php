@props(['name', 'kind'])
<input type="checkbox" class="sr-only peer big-toggle {{ $kind ?? '' }}" aria-hidden="true" id="{{ $name }}" name="{{ $name }}">
<label for="{{ $name }}"
       class="{{ isset($kind) ? ($kind . '-label') : '' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-600 peer-focus:ring-offset-2">
        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 translate-x-0 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
</label>
