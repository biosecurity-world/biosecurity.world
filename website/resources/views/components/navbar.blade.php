@props(['border' => false, 'invert' => false])
<div class="w-full max-w-7xl mx-auto px-6 xl:px-0">
    <nav class="md:py-3 @if($border) md:border @endif md:rounded-full md:px-8 xl:-mx-8 {{ $attributes->get('class') }}">
        <ul class="md:flex md:space-x-4 items-center">
            <li class="font-display inline-block mb-1.5 md:mb-0 navlink @if ($invert) inverted @endif">
                <a href="{{ route('welcome', absolute: false) }}">biosecurity.world</a>
            </li>

            <li class="block flex-1" aria-hidden="true"></li>

            @foreach([
[route('how-to-contribute', absolute: false), 'Contribute'],
[route('give-feedback', absolute: false), 'Give feedback'],
[route('about', absolute: false), 'About us'],
] as [$url, $label])
                <li class="inline">
                    <a href="{{ $url }}" class="navlink @if ($invert) inverted @endif">
                        {{$label}}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
