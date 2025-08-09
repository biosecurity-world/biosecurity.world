@props(["border" => false, "invert" => false])
<div class="mx-auto w-full max-w-7xl px-6 xl:px-0">
    <nav
        class="@if($border) md:border md:border-gray-200 @endif {{ $attributes->get("class") }} md:rounded-full md:px-8 md:py-3 xl:-mx-8"
    >
        <ul class="items-center md:flex md:space-x-4">
            <li
                class="@if ($invert) text-white lg:hover:bg-white/20 lg:focus:bg-white/20 @else lg:hover:bg-primary-50 lg:focus:bg-primary-50 @endif font-display mr-4 mb-1.5 inline-block rounded-xl underline focus:outline-hidden md:mb-0 lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
            >
                <a href="{{ route("welcome", absolute: false) }}">biosecurity.world</a>
            </li>
            <li class="block flex-1" aria-hidden="true"></li>
            @foreach ([
                    [route("inclusion-criteria", absolute: false), "Inclusion criteria"],
                    [route("welcome", absolute: false) . "#faq", "FAQ"],
                    [route("give-feedback", absolute: false), "Give feedback"]
                ]
                as [$url, $label])
                <li class="inline">
                    <a
                        href="{{ $url }}"
                        class="@if ($invert) text-white lg:hover:bg-white/20 lg:focus:bg-white/20 @else lg:hover:bg-primary-50 lg:focus:bg-primary-50 @endif mr-4 rounded-xl underline focus:outline-hidden lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
                    >
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
