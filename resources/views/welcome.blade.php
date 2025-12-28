@php
    use App\Services\NotionData\Enums\FocusCategory;
    /** @var \App\Services\NotionData\Tree\Tree $tree */
@endphp

<x-layouts.default class="w-full bg-white antialiased" title="Understand the biosecurity landscape.">
    <x-slot:head>
        <script>
            ;{{-- format-ignore-start --}}
            window.nodes = @json($nodes);
            window.filterData = @json($filterData);
            {{-- format-ignore-end --}}
        </script>

        @vite("resources/js/map.ts")
    </x-slot>
    <header class="w-full bg-white pt-4 pb-8 lg:pt-8">
        <x-navbar class="mt-4" />

        <h1
            class="font-display text-primary-900 mx-auto mt-6 max-w-3xl px-6 text-3xl font-bold tracking-tight sm:mt-16 md:text-center lg:mt-24 lg:text-center lg:text-6xl"
        >
            Understand the biosecurity landscape.
        </h1>

        <ul
            class="mx-auto mt-6 mb-8 max-w-7xl space-y-6 px-6 md:mt-16 md:grid md:grid-cols-3 md:space-y-0 md:gap-x-8 lg:mt-20 xl:gap-x-12 xl:px-0"
        >
            <li>
                <h2 class="font-display text-gray-900 lg:text-lg xl:text-2xl">Up-to-date</h2>
                <hr class="mt-1.5 hidden w-8 border-gray-300 lg:block" />

                <p class="mt-1 max-w-[65ch] text-gray-600 lg:mt-2 xl:text-lg">
                    Humans regularly update the map and our team monitors privileged channels for announcements about
                    new organizations.
                </p>
            </li>
            <li>
                <h2 class="font-display text-gray-900 lg:text-lg xl:text-2xl">Crowd-sourced</h2>
                <hr class="mt-1.5 hidden w-8 border-gray-300 lg:block" />

                <p class="mt-1 max-w-[65ch] text-gray-600 lg:mt-2 xl:text-lg">
                    We encourage submissions and corrections, which are individually reviewed by researchers from
                    <a href="https://www.ens.psl.eu" class="text-primary-700 underline hover:text-primary-500">ENS</a>
                    and
                    <a href="https://ox.ac.uk" class="text-primary-700 underline hover:text-primary-500">Oxford University</a>.
                </p>
            </li>
            <li>
                <h2 class="font-display text-gray-900 lg:text-lg xl:text-2xl">Transparent</h2>
                <hr class="mt-1.5 hidden w-8 border-gray-300 lg:block" />

                <p class="mt-1 max-w-[65ch] text-gray-600 lg:mt-2 xl:text-lg">
                    As an
                    <a href="https://github.com/biosecurity-world/biosecurity.world" class="text-primary-700 underline hover:text-primary-500">open-source</a>
                    and
                    <a class="text-primary-700 underline hover:text-primary-500" href="{{ $databaseUrl }}">open-data</a>
                    scientific project, we keep a record of our inclusion decisions for people to challenge.
                </p>
            </li>
        </ul>
    </header>
    <div class="mx-auto flex h-screen w-full duration-1000 lg:w-[80%] lg:rounded-3xl" id="map-wrapper">
        <aside
            id="filters-sidebar"
            class="flex h-full w-full max-w-md flex-col overflow-y-scroll rounded-l-3xl border-y border-r border-l border-gray-200 bg-white"
        >
            <header class="border-b border-gray-200 bg-white rounded-tl-3xl">
                <div class="px-6 py-4">
                    <x-map-info id="map-info" :last-edited-at="$lastEditedAt" />
                </div>
            </header>
            <div class="h-full flex-1 overflow-y-auto bg-gray-50 px-6 py-4">
                <h4 class="font-display flex-1 text-lg">Filters</h4>

                <fieldset class="mt-2">
                    <legend class="font-display leading-6 text-gray-900">High-level focus</legend>

                    <div class="mt-0.5 rounded-xl bg-white shadow-xs">
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
                                class="peer-checked:border-technical peer-checked:bg-technical peer-focus:border-technical peer-focus:ring-technical hover:peer-checked:bg-technical/80 hover:peer-checked:ring-technical/70 flex cursor-pointer items-center rounded-t-xl border border-gray-200 px-4 py-1.5 transition peer-focus:ring-2 hover:peer-not-checked:bg-gray-50"
                            >
                                <x-at-technical class="grow" />
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
                                class="peer-checked:border-governance peer-checked:bg-governance peer-focus:border-governance peer-focus:ring-governance hover:peer-checked:bg-governance/80 hover:peer-checked:ring-governance/70 flex cursor-pointer items-center rounded-b-xl border border-t-0! border-gray-200 px-4 py-1.5 transition peer-focus:ring-2 hover:peer-not-checked:bg-gray-50"
                            >
                                <x-at-governance class="grow" />
                                <x-heroicon-m-check class="check size-5 text-white" />
                            </label>
                        </div>
                    </div>
                </fieldset>
                <div class="mt-6 flex items-center justify-between">
                    <span class="flex grow flex-col">
                        <span class="font-display leading-6 text-gray-900">
                            Has focus on GCBR prevention
                        </span>
                        <span class="text-xs text-gray-500"><a
                                class="text-gray-500 underline hover:text-gray-700"
                                href="https://www.nti.org/about/programs-projects/project/global-catastrophic-biological-risks/"
                                rel="noopener noreferrer nofollow"
                            >GCBRs</a> are biological risks that could lead to severe and potentially irreversible damage to
                            human civilization on a global scale.
                        </span>
                    </span>
                    <x-big-toggle class="ml-4" name="has_gcbr_focus" kind="has-gcbr-focus" />
                </div>
                <fieldset class="mt-6">
                    <legend class="font-display inline leading-6 text-gray-900">Activities</legend>

                    <ul class="clear-both mt-1 flex flex-wrap gap-x-2 gap-y-2">
                        @foreach ($tree->activities() as $activity)
                            @php($fg = $activity->color->foreground()->withAlpha(1)->toHsla())
                            @php($bg = $activity->color->background()->withAlpha(1)->toHsla())
                            @php($border = $activity->color->foreground()->withAlpha(0.2)->toHsla())

                            <li>
                                <x-checkbox-as-pill
                                    name="activity_{{ $activity->id }}"
                                    value="{{ $activity->id }}"
                                    kind="activity-checkbox"
                                    style="--fg: {{ $fg }}; --bg: {{ $bg }}; --border: {{ $border}}"
                                    class="hover:border-primary-700 peer-checked:border-opacity-20 border bg-white text-gray-700 transition peer-checked:border-(--border) peer-checked:bg-(--bg) peer-checked:text-(--fg) hover:bg-white hover:text-(--fg)"
                                >
                                    <span class="sr-only">Toggle activity</span>
                                    <x-activity-icon
                                        :activity="$activity"
                                        aria-hidden="true"
                                        class="size-4.5 opacity-75 transition group-hover:opacity-100"
                                    />
                                    <span class="ml-1.5 font-bold select-none">
                                        {{ $activity->label }}
                                    </span>
                                </x-checkbox-as-pill>
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
                <div class="mt-6">
                    <h4 class="font-display flex-1 leading-6 text-gray-900">Intervention focuses</h4>

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
                                    class="focuses-list -mx-6 mt-0.5 flex cursor-pointer flex-wrap gap-x-2 gap-y-2 rounded-xl border border-transparent bg-white px-4 py-4 shadow-xs transition"
                                >
                                    @foreach ($focuses as $focus)
                                        <li>
                                            <x-checkbox-as-pill
                                                name="focus_{{ $focus->id }}"
                                                value="{{ $focus->id }}"
                                                kind="focus-checkbox"
                                                data-global-offset="{{ $focus->globalSortOrder() }}"
                                                class="hover:border-primary-700 peer-checked:bg-primary-50 peer-checked:border-primary-100 peer-checked:text-primary-800 border border-gray-200 bg-white text-gray-700"
                                            >
                                                <span class="ml-1.5 leading-none select-none group-hover:opacity-75">
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
            class="relative h-full w-full border-b border-gray-200 bg-gray-100 rounded-r-3xl"
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
                        class="focusable mt-4 inline-flex items-center space-x-2 rounded-md border border-gray-200 bg-white px-4 py-1"
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
                        class="fill-primary-600 inline h-8 w-8 animate-spin text-gray-200"
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
                        class="resets-filters focusable mt-4 flex items-center space-x-2 rounded-md border border-gray-200 bg-white px-4 py-1 hover:bg-gray-50"
                    >
                        Reset the filters
                    </button>
                </div>
            </section>
            <section data-state="success" class="app-state state-inactive" aria-hidden="true">
                <div
                    class="absolute inset-0 z-20 h-full w-full max-w-md border-y border-gray-200"
                    id="entry-wrapper"
                ></div>
                <div
                    class="pointer-events-none absolute inset-0 z-20 flex h-full w-full max-w-md justify-center border-y border-r border-gray-200 bg-gray-50 pt-16 opacity-0 transition-opacity rounded-r-3xl"
                    id="entry-loader"
                >
                    <svg
                        class="fill-primary-600 inline h-8 w-8 animate-spin text-gray-200"
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
            </section>
        </main>
    </div>
    <div id="page-footer">
        <x-footer />
    </div>
</x-layouts.default>
