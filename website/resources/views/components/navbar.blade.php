@props(['border' => false, 'invert' => false])
<div class="w-full max-w-7xl mx-auto px-6 xl:px-0">
    <nav class="md:py-3 @if($border) md:border @endif md:rounded-full md:px-8 xl:-mx-8 {{ $attributes->get('class') }}">
        <ul class="md:flex md:space-x-4 items-center">
            <li class="font-display inline-block mb-1.5 md:mb-0 underline lg:no-underline mr-4 lg:mr-0 lg:px-2  lg:hover:underline rounded-xl focus:outline-none @if ($invert) text-white lg:hover:bg-white/20 lg:focus:bg-white/20 @else lg:hover:bg-primary-50 lg:focus:bg-primary-50 @endif">
                <a href="{{ route('welcome', absolute: false) }}">biosecurity.world</a>
            </li>
            <li class="block flex-1" aria-hidden="true"></li>
            @foreach([
[route('how-to-contribute', absolute: false), 'Contribute'],
[route('give-feedback', absolute: false), 'Give feedback'],
[route('about', absolute: false), 'About us'],
] as [$url, $label])
                <li class="inline">
                    <a href="{{ $url }}" class="underline lg:no-underline mr-4 lg:mr-0 lg:px-2  lg:hover:underline rounded-xl focus:outline-none @if ($invert) text-white lg:hover:bg-white/20 lg:focus:bg-white/20 @else lg:hover:bg-primary-50 lg:focus:bg-primary-50 @endif">
                        {{$label}}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
