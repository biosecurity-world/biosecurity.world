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
    <header class="from-primary-600 to-primary-950 w-full bg-linear-to-tl pt-4 pb-24 lg:pt-8">
        <x-navbar class="md:bg-white/20 md:shadow-inner md:shadow-white/30" invert />

        <h1
            class="font-display mx-auto mt-6 max-w-3xl px-6 text-3xl font-bold tracking-tight text-white sm:mt-16 md:text-center lg:mt-24 lg:text-center lg:text-6xl"
        >
            Understand the biosecurity landscape.
        </h1>

        <ul
            class="mx-auto mt-6 max-w-7xl space-y-6 px-6 md:mt-16 md:grid md:grid-cols-3 md:space-y-0 md:gap-x-8 lg:mt-20 xl:gap-x-12 xl:px-0"
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
                    and
                    <a class="underline" href="{{ $databaseUrl }}">open-data</a>
                    scientific project, we keep a record of our inclusion decisions for people to challenge.
                </p>
            </li>
        </ul>
    </header>
    <section class="mx-auto w-full max-w-3xl px-6 pt-6 pb-4 lg:hidden">
        <x-map-info id="map-info" :last-edited-at="$lastEditedAt" />
    </section>
    <!-- Legacy mobile overlay/checkbox removed; controlled via JS now -->
    <div class="mx-auto flex h-screen w-full rounded-3xl shadow-lg duration-1000" id="map-wrapper">
        <aside
            id="mobile-filters-drawer"
            class="fixed inset-x-0 bottom-0 z-30 hidden max-h-14 w-full overflow-y-auto rounded-t-3xl border-t border-gray-200 bg-white transition-[max-height] duration-300 lg:static lg:z-auto lg:flex lg:h-full lg:max-h-none lg:w-full lg:max-w-md lg:flex-col lg:overflow-y-scroll lg:rounded-l-3xl lg:border-y lg:border-r lg:border-l"
        >
            <div class="lg:hidden">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <x-heroicon-o-funnel class="size-5 text-gray-600" />
                        <span>Filters</span>
                    </div>
                    <button
                        id="mobile-filters-expand"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-700"
                    >
                        <x-heroicon-o-chevron-up class="size-5 text-gray-600" />
                        <span>Expand</span>
                    </button>
                </div>
            </div>
            <header class="hidden border-b border-gray-200 lg:block">
                <div class="px-6 pt-4">
                    <div class="flex items-center">
                        <x-map-info id="map-info" :last-edited-at="$lastEditedAt" />
                        <button
                            title="Toggle fullscreen (shortcut: F)"
                            id="toggle-fullscreen"
                            class="-m-2 self-start rounded-full border border-transparent p-2 transition hover:border-gray-200 hover:bg-gray-100 hover:shadow-inner"
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
                </div>
            </header>
            <div class="h-full max-h-[calc(95vh-56px)] overflow-y-auto bg-gray-50 px-6 py-4 lg:max-h-none lg:flex-1">
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
                            Has focus on
                            <abbr title="Global Catastrophic Biological Risks">GCBR</abbr>
                            prevention
                        </span>
                        <span class="">
                            <a
                                class="text-primary-700 hover:text-primary-900 inline underline"
                                href="https://www.nti.org/about/programs-projects/project/global-catastrophic-biological-risks/"
                                rel="noopener noreferrer nofollow"
                            >
                                GCBRs
                            </a>
                            <span class="inline text-sm text-gray-500">
                                are biological risks that could lead to severe and potentially irreversible damage to
                                human civilization on a global scale.
                            </span>
                        </span>
                    </span>
                    <x-big-toggle name="has_gcbr_focus" kind="has-gcbr-focus" />
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
            class="relative h-full w-full rounded-l-3xl rounded-r-3xl border-r border-b border-gray-200 bg-gray-100 lg:rounded-l-none"
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
                    class="pointer-events-none absolute inset-0 z-20 flex h-full w-full max-w-md justify-center rounded-r-3xl border-y border-r border-gray-200 bg-gray-50 pt-16 opacity-0 transition-opacity"
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

                <!-- Mobile: default intro overlay with blur and central "Open the map" button -->
                <div id="mobile-map-intro" class="absolute inset-0 z-30 flex items-center justify-center lg:hidden">
                    <div
                        class="flex h-[40vh] w-11/12 max-w-md items-center justify-center rounded-2xl bg-white/40 shadow-lg backdrop-blur-md"
                    >
                        <button
                            id="open-map-mobile"
                            class="bg-primary-600 hover:bg-primary-700 focus:ring-primary-600 inline-flex items-center rounded-full px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition focus:ring-2 focus:ring-offset-2 focus:outline-hidden"
                        >
                            <span>Open the map</span>
                        </button>
                    </div>
                </div>

                <!-- Mobile: fullscreen UI (close button + bottom filters bar), shown when map is opened -->
                <div id="mobile-map-fullscreen-ui" class="pointer-events-none absolute inset-0 z-50 hidden lg:hidden">
                    <!-- Close button (top-right) -->
                    <button
                        id="close-map-mobile"
                        class="pointer-events-auto absolute top-4 right-4 rounded-full bg-white p-2 shadow-md"
                    >
                        <span class="sr-only">Close the map</span>
                        <x-heroicon-o-x-mark class="size-5 text-gray-700" />
                    </button>
                </div>
                <div class="absolute right-6 bottom-6">
                    <div class="flex flex-col divide-y rounded-lg bg-white shadow-sm">
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
    <section id="faq" class="mx-auto mt-12 w-full max-w-3xl px-6 lg:mt-16 xl:px-0">
        <h2 class="font-display text-3xl font-bold text-gray-900">Frequently Asked Questions</h2>

        <h3 class="font-display mt-10 text-xl font-semibold text-gray-900">Goals</h3>
        <div class="mt-4 space-y-4">
            <x-faq-item title="What is the purpose of this biosecurity landscape map?">
                <p>
                    This map provides a comprehensive overview of organizations working in the biosecurity field
                    according to a specific set of criteria (see
                    <a
                        class="text-primary-700 hover:text-primary-900 underline"
                        href="{{ route("inclusion-criteria") }}"
                        rel="noopener noreferrer"
                    >
                        Inclusion criteria
                    </a>
                    ), allowing users to explore and understand the global biosecurity ecosystem.
                </p>
            </x-faq-item>

            <x-faq-item title="What does GCBR stand for?">
                <p>
                    GCBR stands for
                    <a
                        class="text-primary-700 hover:text-primary-900 underline"
                        href="https://www.nti.org/about/programs-projects/project/global-catastrophic-biological-risks/"
                        rel="noopener noreferrer"
                    >
                        Global Catastrophic Biological Risks
                    </a>
                    . These are biological risks that could lead to severe and potentially irreversible damage to human
                    civilization on a global scale.
                </p>
            </x-faq-item>

            <x-faq-item title="How can I use this information?">
                <p>
                    We want users to use this database to better understand the field, and learn about the different
                    actors.
                </p>
                <p class="mt-2">
                    This map can be used for research, networking, identifying potential collaborations, or simply
                    understanding the scope and diversity of work being done in biosecurity.
                </p>
            </x-faq-item>
        </div>

        <h3 class="font-display mt-10 text-xl font-semibold text-gray-900">Content</h3>
        <div class="mt-4 space-y-4">
            <x-faq-item title="How often is the database updated?">
                <p>
                    We strive to keep the database as current as possible. Updates are made on a regular basis as we
                    receive new information or as organizations change. If you notice something is outdated, please
                    reach out via our
                    <a
                        class="text-primary-700 hover:text-primary-900 underline"
                        href="{{ route("give-feedback") }}"
                        rel="noopener noreferrer"
                    >
                        contact form
                    </a>
                    .
                </p>
            </x-faq-item>

            <x-faq-item title="How can I contribute to the map?">
                <p>
                    You can contribute by using the
                    <a
                        class="text-primary-700 hover:text-primary-900 underline"
                        href="{{ route("give-feedback") }}"
                        rel="noopener noreferrer"
                    >
                        contact form
                    </a>
                    at the top of the page. We welcome feedback, suggestions, and information about organizations that
                    should be included (or excluded).
                </p>
            </x-faq-item>

            <x-faq-item title="What are the inclusion criteria for organizations?">
                <p>
                    We have a specific
                    <a
                        class="text-primary-700 hover:text-primary-900 underline"
                        href="{{ route("inclusion-criteria") }}"
                        rel="noopener noreferrer"
                    >
                        set of criteria
                    </a>
                    that determine whether an organization is included in our database.
                </p>
            </x-faq-item>

            <x-faq-item title="Is this information publicly available?">
                <p>
                    Yes, this database is publicly accessible. Please share with anyone that could find it useful, or
                    help us make it better.
                </p>
            </x-faq-item>
        </div>

        <h3 class="font-display mt-10 text-xl font-semibold text-gray-900">Properties &amp; Filters</h3>
        <div class="mt-4 space-y-4">
            <x-faq-item title="What do the different 'Organization Types' mean?">
                <p>
                    The organization types (such as Research institute, For-profit company, Think tank, etc.) categorize
                    the primary nature of each entity. This helps users understand the diversity of organizations in the
                    biosecurity landscape, and filter if they are looking for a specific type of organization. There is
                    also a specific view where the organizations are sorted via this property.
                </p>
            </x-faq-item>

            <x-faq-item title="How is the 'Activity Type' determined for each organization?">
                <p>
                    The Activity Type is based on the primary functions of each organization. An organization can have
                    multiple activity types, reflecting the diverse nature of their work in biosecurity. Users can
                    filter according to a specific type of activity. There is also a specific view where the
                    organizations are sorted via this property.
                </p>
            </x-faq-item>

            <x-faq-item title="What is the 'Intervention Focus' category?">
                <p>
                    The Intervention Focus category provides more specific information about the areas each organization
                    works on, such as synthetic biology, lab biosafety, or crisis management.
                </p>
                <p class="mt-2">
                    There is a higher-level focus type which is "Technical" or "Governance" which helps differentiate
                    whether the organization is more focused on research ("Technical") or policymaking ("Governance").
                </p>
                <p class="mt-2">
                    Sometimes an organization will not have any intervention focus: it usually means that they might
                    touch on any of the topics, and their mission is more general.
                </p>
            </x-faq-item>
        </div>
    </section>
    <div id="page-footer" class="hidden">
        <x-footer />
    </div>
</x-layouts.default>
