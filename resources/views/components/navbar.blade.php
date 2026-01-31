<div class="mx-auto w-full max-w-7xl px-6 xl:px-0">
    <nav
        {{ $attributes->merge(["class" => "md:border md:border-gray-200 md:rounded-full md:px-8 md:py-3 xl:-mx-8"]) }}
    >
        <ul class="items-center md:flex md:space-x-4 lg:-ml-2">
            <li
                class="font-display lg:hover:bg-primary-50 lg:focus:bg-primary-50 mr-4 mb-1.5 inline-block rounded-xl underline focus:outline-hidden md:mb-0 lg:mr-0 lg:px-2 lg:no-underline lg:hover:underline"
            >
                <a href="{{ route("welcome", absolute: false) }}">biosecurity.world</a>
            </li>
            <li class="block flex-1" aria-hidden="true"></li>
            <li class="inline">
                <a
                    href="https://notion.so/{{ config("services.notion.database") }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="bg-primary-700 hover:bg-primary-800 mr-4 inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-medium text-white transition focus:outline-hidden lg:mr-0"
                >
                    Database view
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3.5">
                        <path
                            d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                        />
                        <path
                            d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                        />
                    </svg>
                </a>
            </li>
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
