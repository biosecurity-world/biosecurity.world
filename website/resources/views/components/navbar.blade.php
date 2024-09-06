@props(['border' => false, 'invert' => false])
<div class="w-full max-w-7xl mx-auto px-6 xl:px-0">
    <nav class="lg:py-3 @if($border) lg:border @endif lg:rounded-full lg:px-8 xl:-mx-8 {{ $attributes->get('class') }}">
        <ul class="lg:flex lg:items-center">
            <li class="font-display  flex-1">
                <a href="{{ route('welcome') }}" class="hover:underline lg:text-lg xl:text-xl @if ($invert) text-white @else text-primary-900 @endif">
                    biosecurity.world
                </a>
            </li>
            <li>
                <span class="sr-only">Menu items</span>

                <ul class="flex space-x-4 lg:space-x-8 lg:mt-0 items-center overflow-x-scroll">
                    @foreach([
    [route('how-to-contribute', absolute: false), 'Contribute'],
    [route('give-feedback', absolute: false), 'Give feedback'],
    [route('about', absolute: false), 'About'],
] as [$url, $label])
                        <li class="">
                            <a href="{{ $url }}" class="whitespace-nowrap underline @if ($invert) text-white @else text-black @endif lg:no-underline text-sm lg:text-base font-medium hover:underline xl:text-lg">
                                {{$label}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </nav>
</div>
