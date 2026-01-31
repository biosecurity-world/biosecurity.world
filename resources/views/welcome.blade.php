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

            // Collapsible band toggle functionality
            document.addEventListener('DOMContentLoaded', function () {
                function setupToggle(toggleId, contentId, chevronId, hintId) {
                    const toggle = document.getElementById(toggleId)
                    const content = document.getElementById(contentId)
                    const chevron = document.getElementById(chevronId)
                    const hint = document.getElementById(hintId)

                    if (toggle && content && chevron) {
                        toggle.addEventListener('click', function () {
                            const isExpanded = toggle.getAttribute('aria-expanded') === 'true'
                            toggle.setAttribute('aria-expanded', !isExpanded)
                            content.classList.toggle('hidden')
                            chevron.style.transform = isExpanded ? '' : 'rotate(180deg)'
                            if (hint) {
                                hint.textContent = isExpanded ? '(click to expand)' : '(click to collapse)'
                            }
                        })
                    }
                }

                setupToggle('filters-toggle', 'filters-content', 'filters-chevron', 'filters-hint')
                setupToggle('faq-toggle', 'faq-content', 'faq-chevron', 'faq-hint')
            })
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

                <!-- prettier-ignore -->
                <p class="mt-1 max-w-[65ch] text-gray-600 lg:mt-2 xl:text-lg">
                    We encourage submissions and corrections, which are individually reviewed by researchers from
                    <a href="https://www.ens.psl.eu" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">ENS</a>
                    and
                    <a href="https://ox.ac.uk" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">Oxford University</a>.
                </p>
            </li>
            <li>
                <h2 class="font-display text-gray-900 lg:text-lg xl:text-2xl">Transparent</h2>
                <hr class="mt-1.5 hidden w-8 border-gray-300 lg:block" />

                <!-- prettier-ignore -->
                <p class="mt-1 max-w-[65ch] text-gray-600 lg:mt-2 xl:text-lg">
                    As an
                    <a href="https://github.com/biosecurity-world/biosecurity.world" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">open-source</a>
                    and
                    <a href="{{ $databaseUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">open-data</a>
                    scientific project, we keep a record of our inclusion decisions for people to challenge.
                </p>
            </li>
        </ul>
    </header>

    <div class="mx-auto flex w-full flex-col" id="map-wrapper">
        <!-- Filters section - collapsible, above map -->
        <aside id="filters-sidebar" class="w-full border border-gray-200 bg-white">
            <button
                type="button"
                id="filters-toggle"
                class="bg-primary-900 hover:bg-primary-800 flex w-full cursor-pointer items-center justify-between px-6 py-3 text-left transition"
                aria-expanded="false"
                aria-controls="filters-content"
            >
                <div class="flex items-center gap-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5 text-white/80"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 0 1 .628.74v2.288a2.25 2.25 0 0 1-.659 1.59l-4.682 4.683a2.25 2.25 0 0 0-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 0 1 8 18.25v-5.757a2.25 2.25 0 0 0-.659-1.591L2.659 6.22A2.25 2.25 0 0 1 2 4.629V2.34a.75.75 0 0 1 .628-.74Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="font-display text-base font-semibold text-white">Filter organizations</span>
                    <span id="filters-hint" class="text-sm text-white/70">(click to expand)</span>
                </div>
                <svg
                    id="filters-chevron"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    class="size-5 text-white/70 transition-transform duration-200"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>

            <div id="filters-content" class="hidden overflow-hidden border-t border-gray-200 bg-gray-50 px-6 py-5">
                <div class="mx-auto max-w-7xl space-y-5">
                    <!-- Row 1: High-level focus + GCBR toggle + Activities -->
                    <div class="flex flex-wrap items-center gap-x-10 gap-y-4">
                        <!-- High-level focus -->
                        <fieldset class="flex items-center gap-3">
                            <legend class="sr-only">High-level focus</legend>
                            <span class="text-sm font-semibold text-gray-700">Focus</span>
                            <div class="flex rounded-xl bg-white shadow-sm">
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
                                        class="peer-checked:border-technical peer-checked:bg-technical peer-focus:border-technical peer-focus:ring-technical hover:peer-checked:bg-technical/80 flex cursor-pointer items-center rounded-l-xl border border-gray-200 px-4 py-1.5 transition peer-focus:ring-2 hover:peer-not-checked:bg-gray-50"
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
                                        class="peer-checked:border-governance peer-checked:bg-governance peer-focus:border-governance peer-focus:ring-governance hover:peer-checked:bg-governance/80 flex cursor-pointer items-center rounded-r-xl border border-l-0! border-gray-200 px-4 py-1.5 transition peer-focus:ring-2 hover:peer-not-checked:bg-gray-50"
                                    >
                                        <x-at-governance class="grow" />
                                        <x-heroicon-m-check class="check size-5 text-white" />
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <!-- GCBR toggle -->
                        <div class="flex items-center gap-3">
                            <!-- prettier-ignore -->
                            <span class="text-sm font-semibold text-gray-700"><a class="underline decoration-gray-400 underline-offset-2 hover:text-gray-900 hover:decoration-gray-600" href="https://www.nti.org/about/programs-projects/project/global-catastrophic-biological-risks/" rel="noopener noreferrer nofollow" title="Global Catastrophic Biological Risks">GCBR</a> focus</span>
                            <x-big-toggle name="has_gcbr_focus" kind="has-gcbr-focus" />
                        </div>

                        <!-- Activities -->
                        <fieldset class="flex items-center gap-3">
                            <legend class="sr-only">Activities</legend>
                            <span class="text-sm font-semibold text-gray-700">Activities</span>
                            <ul class="flex flex-wrap gap-2">
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
                                            class="hover:border-primary-700 peer-checked:border-opacity-20 border bg-white text-gray-700 shadow-sm transition peer-checked:border-(--border) peer-checked:bg-(--bg) peer-checked:text-(--fg) hover:bg-white hover:text-(--fg)"
                                        >
                                            <span class="sr-only">Toggle activity</span>
                                            <x-activity-icon
                                                :activity="$activity"
                                                aria-hidden="true"
                                                class="size-4.5 opacity-75 transition group-hover:opacity-100"
                                            />
                                            <span class="ml-1.5 font-semibold select-none">
                                                {{ $activity->label }}
                                            </span>
                                        </x-checkbox-as-pill>
                                    </li>
                                @endforeach
                            </ul>
                        </fieldset>
                    </div>

                    <!-- Row 2: Intervention focuses -->
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="mb-3 flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-700">Intervention focuses</h4>
                            <button
                                class="resets-filters focusable flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>Reset all</span>
                            </button>
                        </div>
                        <div class="grid gap-4 md:grid-cols-3">
                            @foreach ($categorizedFocuses as $category => $focuses)
                                <div id="focuses_wrapper_{{ $category }}">
                                    <div class="mb-2 flex items-center gap-2">
                                        <x-checkbox
                                            name="focuses_master_checkbox_{{ $category }}"
                                            checked
                                            class="focuses-master-checkbox"
                                        />
                                        <label
                                            title="Toggle all in {{ FocusCategory::from($category)->label() }}"
                                            class="cursor-pointer text-sm font-semibold text-gray-800"
                                            for="focuses_master_checkbox_{{ $category }}"
                                        >
                                            {{ FocusCategory::from($category)->label() }}
                                        </label>
                                    </div>
                                    <ul class="focuses-list flex cursor-pointer flex-wrap gap-1.5">
                                        @foreach ($focuses as $focus)
                                            <li>
                                                <x-checkbox-as-pill
                                                    name="focus_{{ $focus->id }}"
                                                    value="{{ $focus->id }}"
                                                    kind="focus-checkbox"
                                                    data-global-offset="{{ $focus->globalSortOrder() }}"
                                                    class="hover:border-primary-700 peer-checked:bg-primary-50 peer-checked:border-primary-200 peer-checked:text-primary-800 border border-gray-200 bg-gray-50 text-sm text-gray-600"
                                                >
                                                    <span class="leading-none select-none">
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
            </div>
        </aside>

        <!-- Map section - full width -->
        <main
            class="relative w-full overflow-hidden border-x border-b border-gray-200 bg-gray-100"
            style="min-height: 300px"
        >
            <section data-state="error" aria-hidden="true" class="app-state state-inactive">
                <div class="text-center">
                    <h3 class="text-xl">An error has occurred.</h3>
                    <p class="reason mt-1"></p>
                    <!-- prettier-ignore -->
                    <p>
                        You can try reloading the page or checking the
                        <a href="{{ $databaseUrl }}" rel="noopener noreferrer nofollow" class="text-primary-700 underline">Notion table</a>
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
                    class="absolute inset-y-0 left-0 z-20 h-full w-full max-w-md border-r border-gray-200 bg-white"
                    id="entry-wrapper"
                ></div>
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 z-20 flex h-full w-full max-w-md justify-center border-r border-gray-200 bg-gray-50 pt-16 opacity-0 transition-opacity"
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

                <svg id="map" width="100%" height="100%">
                    <!-- The map will be dynamically inserted here -->
                </svg>
            </section>
        </main>
    </div>

    <!-- FAQ collapsible band -->
    <div class="w-full">
        <button
            type="button"
            id="faq-toggle"
            class="bg-primary-900 hover:bg-primary-800 flex w-full cursor-pointer items-center justify-between px-6 py-3 text-left transition"
            aria-expanded="false"
            aria-controls="faq-content"
        >
            <div class="flex items-center gap-3">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    class="size-5 text-white/80"
                >
                    <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0ZM8.94 6.94a.75.75 0 1 1-1.061-1.061 3 3 0 1 1 2.871 5.026v.345a.75.75 0 0 1-1.5 0v-.5c0-.72.57-1.172 1.081-1.287A1.5 1.5 0 1 0 8.94 6.94ZM10 15a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                        clip-rule="evenodd"
                    />
                </svg>
                <span class="font-display text-base font-semibold text-white">Frequently Asked Questions</span>
                <span id="faq-hint" class="text-sm text-white/70">(click to expand)</span>
            </div>
            <svg
                id="faq-chevron"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="size-5 text-white/80 transition-transform duration-200"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>
        <div id="faq-content" class="hidden overflow-hidden border-t border-gray-200 bg-gray-50 px-6 py-8">
            <div class="mx-auto max-w-3xl space-y-8">
                <div>
                    <h3 class="font-display mb-4 text-lg font-semibold text-gray-900">Goals</h3>
                    <div class="space-y-3">
                        <x-faq-item title="What is the purpose of this biosecurity landscape map?">
                            <!-- prettier-ignore -->
                            <p>
                                This map provides a comprehensive overview of organizations working in the biosecurity
                                field according to a specific set of criteria (see
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="{{ route("inclusion-criteria") }}" rel="noopener noreferrer">Inclusion criteria</a>),
                                allowing users to explore and understand the global biosecurity ecosystem.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="What does GCBR stand for?">
                            <!-- prettier-ignore -->
                            <p>
                                GCBR stands for
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://www.nti.org/about/programs-projects/project/global-catastrophic-biological-risks/" rel="noopener noreferrer">Global Catastrophic Biological Risks</a>.
                                These are biological risks that could lead to severe and potentially irreversible
                                damage to human civilization on a global scale.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="How can I use this information?">
                            <p>
                                We want users to use this database to better understand the field, and learn about the
                                different actors.
                            </p>
                            <p class="mt-2">
                                This map can be used for research, networking, identifying potential collaborations, or
                                simply understanding the scope and diversity of work being done in biosecurity.
                            </p>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="font-display mb-4 text-lg font-semibold text-gray-900">Content</h3>
                    <div class="space-y-3">
                        <x-faq-item title="How often is the database updated?">
                            <!-- prettier-ignore -->
                            <p>
                                We strive to keep the database as current as possible. Updates are made on a regular
                                basis as we receive new information or as organizations change. If you notice something
                                is outdated, please reach out via our
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="{{ route("give-feedback") }}" rel="noopener noreferrer">contact form</a>.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="How can I contribute to the map?">
                            <!-- prettier-ignore -->
                            <p>
                                You can contribute by using the
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="{{ route("give-feedback") }}" rel="noopener noreferrer">contact form</a>
                                at the top of the page. We welcome feedback, suggestions, and information about
                                organizations that should be included (or excluded).
                            </p>
                        </x-faq-item>

                        <x-faq-item title="What are the inclusion criteria for organizations?">
                            <!-- prettier-ignore -->
                            <p>
                                We have a specific
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="{{ route("inclusion-criteria") }}" rel="noopener noreferrer">set of criteria</a>
                                that determine whether an organization is included in our database.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="Is this information publicly available?">
                            <p>
                                Yes, this database is publicly accessible. Please share with anyone that could find it
                                useful, or help us make it better.
                            </p>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="font-display mb-4 text-lg font-semibold text-gray-900">Properties & Filters</h3>
                    <div class="space-y-3">
                        <x-faq-item title="What do the different 'Organization Types' mean?">
                            <p>
                                The organization types (such as Research institute, For-profit company, Think tank,
                                etc.) categorize the primary nature of each entity. This helps users understand the
                                diversity of organizations in the biosecurity landscape, and filter if they are looking
                                for a specific type of organization. There is also a specific view where the
                                organizations are sorted via this property.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="How is the 'Activity Type' determined for each organization?">
                            <p>
                                The Activity Type is based on the primary functions of each organization. An
                                organization can have multiple activity types, reflecting the diverse nature of their
                                work in biosecurity. Users can filter according to a specific type of activity. There is
                                also a specific view where the organizations are sorted via this property.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="What is the 'Intervention Focus' category?">
                            <p>
                                The Intervention Focus category provides more specific information about the areas each
                                organization works on, such as synthetic biology, lab biosafety, or crisis management.
                            </p>
                            <p class="mt-2">
                                There is a higher-level focus type which is "Technical" or "Governance" which helps
                                differentiate whether the organization is more focused on research ("Technical") or
                                policymaking ("Governance").
                            </p>
                            <p class="mt-2">
                                Sometimes an organization will not have any intervention focus: it usually means that
                                they might touch on any of the topics, and their mission is more general.
                            </p>
                        </x-faq-item>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="page-footer">
        <x-footer />
    </div>
</x-layouts.default>
