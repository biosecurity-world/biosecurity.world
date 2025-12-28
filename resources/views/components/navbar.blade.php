<div class="mx-auto w-full max-w-7xl px-6 xl:px-0">
    <nav
        {{ $attributes->merge(["class" => "md:border md:border-gray-200 md:rounded-full md:px-8 md:py-3 xl:-mx-8"]) }}
    >
        <ul class="items-center md:flex md:space-x-4 lg:-ml-2">
            <li class="font-display lg:hover:bg-primary-50 lg:focus:bg-primary-50 mr-4 mb-1.5 inline-block rounded-xl underline focus:outline-hidden md:mb-0 lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline">
                <a href="{{ route("welcome", absolute: false) }}">biosecurity.world</a>
            </li>
            <li class="block flex-1" aria-hidden="true"></li>
            <li class="inline">
                <a
                    href="{{ route("inclusion-criteria", absolute: false) }}"
                    class="lg:hover:bg-primary-50 lg:focus:bg-primary-50 mr-4 rounded-xl underline focus:outline-hidden lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
                >
                    Inclusion criteria
                </a>
            </li>
            <li class="inline">
                <a
                    href="{{ route("give-feedback", absolute: false) }}"
                    class="lg:hover:bg-primary-50 lg:focus:bg-primary-50 mr-4 rounded-xl underline focus:outline-hidden lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
                >
                    Give feedback
                </a>
            </li>
        </ul>
    </nav>
</div>
