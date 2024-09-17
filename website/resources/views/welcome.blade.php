@php
    use App\Services\NotionData\Enums\FocusCategory;
@endphp

<x-layouts.default class="w-full bg-white antialiased" title="Understand the biosecurity landscape.">
    <x-slot:head>
        <script>
            window.nodes = @json($nodes)
            window.filterData = @json($filterData)
        </script>

        @vite("resources/js/map.ts")
    </x-slot>
    <header class="w-full bg-gradient-to-tl from-primary-600 to-primary-950 pb-36 pt-4 lg:pt-8">
        <x-navbar class="md:bg-white/20 md:shadow-inner md:shadow-white/30" invert />

        <h1
            class="mx-auto mt-6 max-w-3xl px-6 font-display text-3xl font-bold tracking-tight text-white sm:mt-16 md:text-center lg:mt-24 lg:text-center lg:text-6xl"
        >
            Understand the biosecurity landscape.
        </h1>

        <ul
            class="mx-auto mt-6 max-w-7xl space-y-6 px-6 md:mt-16 md:grid md:grid-cols-3 md:gap-x-8 md:space-y-0 lg:mt-20 xl:gap-x-12 xl:px-0"
        >
            <li>
                <h2 class="font-display text-white lg:text-lg xl:text-2xl">Up-to-date</h2>
                <hr class="mt-1.5 hidden w-8 border-white/40 lg:block" />

                <p class="mt-1 max-w-[65ch] text-white/95 lg:mt-2 xl:text-lg">
                    Humans update regularly the map and our team monitors privileged channels for announcements about
                    new organizations.
                </p>
            </li>
            <li>
                <h2 class="font-display text-white lg:text-lg xl:text-2xl">Crowd-sourced</h2>
                <hr class="mt-1.5 hidden w-8 border-white/40 lg:block" />

                <p class="mt-1 max-w-[65ch] text-white/95 lg:mt-2 xl:text-lg">
                    We encourage submissions and corrections, which are individually reviewed by researchers from
                    <a href="https://www.ens.psl.eu" class="text-white underline hover:text-white/70">ENS</a>
                    and
                    <a href="https://ox.ac.uk" class="text-white underline hover:text-white/70">Oxford University</a>
                    .
                </p>
            </li>
            <li>
                <h2 class="font-display text-white lg:text-lg xl:text-2xl">Transparent</h2>
                <hr class="mt-1.5 hidden w-8 border-white/40 lg:block" />

                <p class="mt-1 max-w-[65ch] text-white/95 lg:mt-2 xl:text-lg">
                    As an
                    <a href="https://github.com/biosecurity-world/biosecurity.world" class="underline">open-source</a>
                    ,
                    <a class="underline" href="{{ $databaseUrl }}">open-data</a>
                    , scientific non-profit, we keep a record of our inclusion decisions for people to challenge.
                </p>
            </li>
        </ul>
    </header>
    <div
        class="fullscreen mx-auto flex h-screen w-full rounded-3xl shadow-lg duration-1000 motion-safe:transition-[transform,order]"
        id="map-wrapper"
    >
        <aside
            class="hidden h-full w-full max-w-md overflow-y-scroll rounded-l-3xl border-y border-l border-r bg-white lg:flex lg:flex-col"
        >
            <header class="border-b">
                <div class="px-6 pt-4">
                    <div class="flex items-center">
                        <h3 class="flex-1 font-display text-2xl">Map of Biosecurity</h3>

                        <button
                            title="Toggle fullscreen (shortcut: F)"
                            id="toggle-fullscreen"
                            class="-m-2 rounded-full border border-transparent p-2 transition hover:border-gray-200 hover:bg-gray-100 hover:shadow-inner"
                        >
                            <span class="sr-only">Toggle fullscreen</span>
                            <x-heroicon-o-arrows-pointing-out
                                id="not-fullscreen"
                                aria-hidden="true"
                                class="size-5 text-gray-700"
                            />
                            <x-heroicon-o-arrows-pointing-in
                                id="is-fullscreen"
                                aria-hidden="true"
                                class="hidden size-5 text-gray-700"
                            />
                        </button>
                    </div>
                    <p class="mt-& text-gray-700">
                        Last updated on
                        <time
                            datetime="{{ $lastEditedAt->toIso8601String() }}"
                            title="{{ $lastEditedAt->diffForHumans() }}"
                        >
                            {{ $lastEditedAt->format("F j, Y") }}
                        </time>
                        .
                    </p>
                </div>

                <ul class="mt-4 flex space-x-4 px-6 pb-2">
                    <li>
                        <a href="#" class="inline-flex whitespace-nowrap text-sm">
                            <span class="mt-px text-gray-400">&bull;&nbsp;</span>
                            <span class="-mt-px text-gray-700 underline">Inclusion criteria</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="inline-flex whitespace-nowrap text-sm">
                            <span class="mt-px text-gray-400">&bull;&nbsp;</span>
                            <span class="-mt-px text-gray-700 underline">Rejected entries</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://notion.so/{{ config("services.notion.database") }}"
                            class="inline-flex whitespace-nowrap text-sm"
                        >
                            <span class="mt-px text-gray-400">&bull;&nbsp;</span>
                            <span class="-mt-px mr-0.5 text-gray-700 underline">Notion</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                class="-mt-px size-4 text-gray-400 group-hover:text-primary-700"
                                aria-label="External link icon"
                            >
                                <path
                                    d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                                />
                                <path
                                    d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                                />
                            </svg>
                        </a>
                    </li>
                </ul>
            </header>
            <div class="bg-gray-50 px-6 py-4 lg:flex-1">
                <h4 class="flex-1 font-display text-lg">Filters</h4>

                <fieldset class="mt-2">
                    <legend class="font-display leading-6 text-gray-900">Domain</legend>

                    <div class="mt-0.5 rounded-xl bg-white shadow-sm">
                        <div>
                            <input
                                type="checkbox"
                                name="domain_technical"
                                id="domain_technical"
                                value="technical"
                                class="domain-checkbox peer sr-only"
                            />

                            <label
                                for="domain_technical"
                                class="flex cursor-pointer items-center rounded-t-xl border px-4 py-1.5 transition hover:bg-gray-50 peer-checked:border-technical peer-checked:bg-technical peer-focus:border-technical peer-focus:ring-2 peer-focus:ring-technical"
                            >
                                <x-at-technical class="flex-grow" />
                                <x-heroicon-m-check class="check size-5 text-white" />
                            </label>
                        </div>
                        <div>
                            <input
                                type="checkbox"
                                name="domain_governance"
                                id="domain_governance"
                                value="governance"
                                class="domain-checkbox peer sr-only"
                            />
                            <label
                                for="domain_governance"
                                class="domain-checkbox-label flex cursor-pointer items-center rounded-b-xl border !border-t-0 px-4 py-1.5 transition hover:bg-gray-50 peer-checked:border-governance peer-checked:bg-governance peer-focus:border-governance peer-focus:ring-2 peer-focus:ring-governance"
                            >
                                <x-at-governance class="flex-grow" />
                                <x-heroicon-m-check class="check size-5 text-white" />
                            </label>
                        </div>
                    </div>
                </fieldset>
                <div class="mt-6 flex items-center justify-between">
                    <span class="flex flex-grow flex-col">
                        <span class="font-display leading-6 text-gray-900">Focus on GCBRs prevention</span>
                        <span class="text-sm text-gray-500">
                            Include only organizations focused on large-scale pandemics prevention.
                        </span>
                    </span>
                    <x-big-toggle name="has_gcbr_focus" kind="has-gcbr-focus" />
                </div>
                <fieldset class="mt-6">
                    <legend class="inline font-display leading-6 text-gray-900">Activities</legend>

                    <ul class="clear-both mt-1 flex flex-wrap gap-x-2 gap-y-2">
                        @foreach ($tree->activities() as $activity)
                            <li>
                                <x-checkbox-as-pill
                                    name="activity_{{ $activity->id }}"
                                    value="{{ $activity->id }}"
                                    kind="activity-checkbox"
                                    style="background: {{ $activity->color->foreground() }}"
                                    class="border-gray-500/10 text-white"
                                >
                                    <span class="sr-only">Toggle activity</span>
                                    <x-activity-icon
                                        :activity="$activity"
                                        aria-hidden="true"
                                        class="size-[1.125rem] group-hover:opacity-75"
                                    />
                                    <span class="ml-1.5 select-none leading-none group-hover:opacity-75">
                                        {{ $activity->label }}
                                    </span>
                                </x-checkbox-as-pill>
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
                <div class="mt-6">
                    <h4 class="flex-1 font-display leading-6 text-gray-900">Intervention focuses</h4>

                    <div class="mt-4 space-y-8">
                        @foreach ($categorizedFocuses as $category => $focuses)
                            <div id="focuses_wrapper_{{ $category }}">
                                <div class="flex w-full items-center">
                                    <label
                                        title="Toggle all in this group"
                                        class="flex-1 cursor-pointer rounded-full font-bold"
                                        for="focuses_master_checkbox_{{ $category }}"
                                    >
                                        {{ FocusCategory::from($category)->label() }}
                                    </label>
                                    <x-checkbox
                                        name="focuses_master_checkbox_{{ $category }}"
                                        checked
                                        class="focuses-master-checkbox"
                                    />
                                </div>

                                <ul
                                    class="focuses-list -mx-6 mt-0.5 flex cursor-pointer flex-wrap gap-x-2 gap-y-2 rounded-xl border border-transparent bg-white px-4 py-4 shadow-sm transition"
                                >
                                    @foreach ($focuses as $focus)
                                        <li>
                                            <x-checkbox-as-pill
                                                name="focus_{{ $focus->id }}"
                                                value="{{ $focus->id }}"
                                                kind="focus-checkbox"
                                                data-global-offset="{{ $focus->globalSortOrder() }}"
                                                class="bg-primary-50 text-primary-700 hover:border-primary-700"
                                            >
                                                <span class="ml-1.5 select-none leading-none group-hover:opacity-75">
                                                    {{ $focus->label }}
                                                </span>
                                            </x-checkbox-as-pill>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>
        <main
            class="relative h-full w-full rounded-l-3xl rounded-r-3xl border-b border-r bg-gray-100 lg:rounded-l-none"
        >
            <section data-state="error" aria-hidden="true" class="app-state state-inactive">
                <div class="text-center">
                    <h3 class="text-xl">An error has occurred.</h3>
                    <p class="reason mt-1"></p>
                    <p>
                        You can try reloading the page or checking the
                        <a
                            href="{{ $databaseUrl }}"
                            rel="noopener noreferrer nofollow"
                            class="text-primary-700 underline"
                        >
                            Notion table
                        </a>
                        directly.
                    </p>
                    <a
                        href="javascript:window.location.reload();"
                        class="focusable mt-4 inline-flex items-center space-x-2 rounded-md border bg-white px-4 py-1"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path
                                fill-rule="evenodd"
                                d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="-mt-px">Reload</span>
                    </a>
                </div>
            </section>
            <section data-state="loading" aria-hidden="false" class="app-state state-active">
                <div>
                    <svg
                        class="inline h-8 w-8 animate-spin fill-primary-600 text-gray-200"
                        viewBox="0 0 100 101"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"
                        ></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"
                        ></path>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </section>
            <section data-state="empty" class="app-state state-inactive" aria-hidden="true">
                <div class="flex flex-col items-center justify-center text-center">
                    <h3 class="font-display text-xl">There are no entries matching your filters.</h3>
                    <button
                        class="resets-filters focusable mt-4 flex items-center space-x-2 rounded-md border bg-white px-4 py-1 hover:bg-gray-50"
                    >
                        Reset the filters
                    </button>
                </div>
            </section>
            <section data-state="success" class="app-state state-inactive" aria-hidden="true">
                <div class="absolute inset-0 z-20 h-full w-full max-w-md border-y" id="entry-wrapper"></div>
                <div
                    class="pointer-events-none absolute inset-0 z-20 flex h-full w-full max-w-md justify-center rounded-r-3xl border-y border-r bg-gray-50 pt-16 opacity-0 transition-[opacity]"
                    id="entry-loader"
                >
                    <svg
                        class="inline h-8 w-8 animate-spin fill-primary-600 text-gray-200"
                        viewBox="0 0 100 101"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"
                        ></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"
                        ></path>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>

                <div id="above-map"></div>
                <svg id="map" width="100%" height="100%" class="rounded-tr-3xl">
                    <!-- The map will be dynamically inserted here -->
                </svg>
                <div class="absolute bottom-6 right-6">
                    <div class="flex flex-col divide-y rounded-lg bg-white shadow">
                        <button class="focusable rounded-t-lg p-2 hover:bg-gray-50" id="zoom-in">
                            <x-heroicon-s-plus class="size-5 text-gray-700" />
                        </button>
                        <button class="focusable rounded-b-lg p-2 hover:bg-gray-50" id="zoom-out">
                            <x-heroicon-s-minus class="size-5 text-gray-700" />
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <x-footer />
    <a href="/_/entries" class="hidden" aria-hidden="true">entries</a>
    <a href="/_/m" class="hidden" aria-hidden="true">map</a>
</x-layouts.default>
