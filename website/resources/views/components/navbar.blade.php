@props(["border" => false, "invert" => false])
<div class="mx-auto w-full max-w-7xl px-6 xl:px-0">
    <nav
        class="@if($border) md:border @endif {{ $attributes->get("class") }} md:rounded-full md:px-8 md:py-3 xl:-mx-8"
    >
        <ul class="items-center md:flex md:space-x-4">
            <li
                class="@if ($invert) text-white lg:hover:bg-white/20 lg:focus:bg-white/20 @else lg:hover:bg-primary-50 lg:focus:bg-primary-50 @endif mb-1.5 mr-4 inline-block rounded-xl font-display underline focus:outline-none md:mb-0 lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
            >
                <a href="{{ route("welcome", absolute: false) }}">biosecurity.world</a>
            </li>
            <li class="block flex-1" aria-hidden="true"></li>
            @foreach ([
                    [route("how-to-contribute", absolute: false), "Contribute"],
                    [route("give-feedback", absolute: false), "Give feedback"],
                    [route("about", absolute: false), "About us"]
                ]
                as [$url, $label])
                <li class="inline">
                    <a
                        href="{{ $url }}"
                        class="@if ($invert) text-white lg:hover:bg-white/20 lg:focus:bg-white/20 @else lg:hover:bg-primary-50 lg:focus:bg-primary-50 @endif mr-4 rounded-xl underline focus:outline-none lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
                    >
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
