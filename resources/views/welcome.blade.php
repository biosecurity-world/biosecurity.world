@php
    use App\Services\NotionData\Enums\FocusCategory;
    use App\Services\NotionData\Enums\LocationRegion;
    /** @var \App\Services\NotionData\Tree\Tree $tree */
@endphp

<x-layouts.default class="bg-paper text-ink w-full antialiased" title="Understand the biosecurity landscape.">
    <x-slot:head>
        <script>
            {{-- format-ignore-start --}}
            window.nodes = @json($nodes);
            window.filterData = @json($filterData);
            {{-- format-ignore-end --}}// Collapsible band toggle functionality
            document.addEventListener("DOMContentLoaded", function () {
                function setupToggle(toggleId, contentId, chevronId, hintId) {
                    const toggle = document.getElementById(toggleId)
                    const content = document.getElementById(contentId)
                    const chevron = document.getElementById(chevronId)
                    const hint = document.getElementById(hintId)

                    if (toggle && content && chevron) {
                        toggle.addEventListener("click", function () {
                            const isExpanded = toggle.getAttribute("aria-expanded") === "true"
                            toggle.setAttribute("aria-expanded", !isExpanded)
                            content.classList.toggle("hidden")
                            chevron.style.transform = isExpanded ? "" : "rotate(180deg)"
                            if (hint) {
                                hint.textContent = isExpanded ? "(click to expand)" : "(click to collapse)"
                            }
                        })
                    }
                }

                setupToggle("filters-toggle", "filters-content", "filters-chevron", "filters-hint")
                setupToggle("faq-toggle", "faq-content", "faq-chevron", "faq-hint")

                // Collapsed location pills: "+ N…" reveals the rest of a region's locations
                document.querySelectorAll(".location-more-toggle").forEach(function (btn) {
                    btn.addEventListener("click", function () {
                        const expanded = btn.getAttribute("aria-expanded") === "true"
                        btn.closest("ul")
                            .querySelectorAll(".location-overflow")
                            .forEach(function (li) {
                                li.classList.toggle("hidden", expanded)
                            })
                        btn.setAttribute("aria-expanded", String(!expanded))
                        btn.textContent = expanded ? btn.dataset.moreLabel : "− less"
                    })
                })
            })
        </script>

        @vite ("resources/js/map.ts")
    </x-slot:head>
    <header class="bg-paper w-full pt-4 pb-8 lg:pt-8">
        <x-navbar class="mt-4" />

        <h1
            class="font-display text-hero mx-auto mt-6 max-w-3xl px-6 text-3xl font-bold tracking-tight text-balance sm:mt-16 md:text-center lg:mt-24 lg:text-center lg:text-6xl"
        >
            Understand the biosecurity landscape.
        </h1>

        <ul
            class="mx-auto mt-6 mb-8 max-w-7xl space-y-6 px-6 md:mt-16 md:grid md:grid-cols-3 md:space-y-0 md:gap-x-8 lg:mt-20 xl:gap-x-12 xl:px-0"
        >
            <li>
                <div class="border-sand-400 flex flex-wrap items-center gap-x-3 gap-y-1.5 border-b pb-2 lg:pb-2.5">
                    <h2 class="font-display text-ink font-semibold lg:text-lg xl:text-2xl">Up-to-date</h2>
                    <span
                        class="border-sand-300 text-ink-muted inline-flex items-center gap-1.5 rounded-full border bg-white px-2.5 py-1 text-xs font-medium"
                        title="The map is rebuilt and redeployed from the live Notion database on every update."
                    >
                        <span class="relative flex h-2 w-2" aria-hidden="true">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"
                            ></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                        </span>
                        <time
                            datetime="{{ $deployedAt->toIso8601String() }}"
                            title="{{ $deployedAt->diffForHumans() }}"
                        >
                            Updated {{ $deployedAt->format("M j, Y") }}
                        </time>
                    </span>
                </div>

                <p class="text-ink-muted mt-2 max-w-[65ch] lg:mt-3 xl:text-lg">We regularly update the map and monitor announcements about new organizations.</p>
            </li>
            <li>
                <div class="border-sand-400 border-b pb-2 lg:pb-2.5">
                    <h2 class="font-display text-ink font-semibold lg:text-lg xl:text-2xl">Crowd-sourced</h2>
                </div>

                <!-- prettier-ignore -->
                <p class="text-ink-muted mt-2 max-w-[65ch] lg:mt-3 xl:text-lg">
                    We encourage
                    <a href="https://biosecurityworld.notion.site/33a4061a75b7806fad1dee0fcd2e921a" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">submissions</a>
                    and
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSfJrpJ9o3xpIXOHgdOdkj_yrUt5LadIVbnzwKQk6tKWMuU5xw/viewform?usp=send_form" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">corrections</a>.
                </p>
            </li>
            <li>
                <div class="border-sand-400 border-b pb-2 lg:pb-2.5">
                    <h2 class="font-display text-ink font-semibold lg:text-lg xl:text-2xl">Transparent</h2>
                </div>

                <!-- prettier-ignore -->
                <p class="text-ink-muted mt-2 max-w-[65ch] lg:mt-3 xl:text-lg">
                    This is an
                    <a href="https://github.com/biosecurity-world/biosecurity.world" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">open-source</a>
                    and
                    <a href="{{ $databaseUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary-700 hover:text-primary-500 underline">open-data</a>&nbsp;project.
                </p>
            </li>
        </ul>
    </header>

    <div class="mx-auto flex w-full flex-col" id="map-wrapper">
        <!-- Filters section - collapsible, above map -->
        <aside
            id="filters-sidebar"
            class="border-sand-300 mx-4 overflow-hidden rounded-2xl border bg-white shadow-sm lg:mx-6"
        >
            <button
                type="button"
                id="filters-toggle"
                class="bg-band hover:bg-band-light flex w-full cursor-pointer items-center justify-between px-6 py-3 text-left transition"
                aria-expanded="true"
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
                    <span id="filters-hint" class="text-sm text-white/70">(click to collapse)</span>
                </div>
                <div class="flex items-center gap-4">
                    <!-- prettier-ignore -->
                    <span class="text-primary-200 hidden text-sm sm:inline">
                        <span id="filter-count-current" class="font-semibold text-white">{{ count($filterData) }}</span> / {{ count($filterData) }} organizations
                    </span>
                    <svg
                        id="filters-chevron"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="size-5 text-white/70 transition-transform duration-200"
                        style="transform: rotate(180deg)"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </button>

            <div id="filters-content" class="border-sand-300 overflow-hidden border-t bg-white px-6 py-4">
                <div class="mx-auto max-w-7xl space-y-3.5">
                    <!-- Row 1: High-level focus + GCBR toggle + Activities -->
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                        <!-- High-level focus -->
                        <fieldset class="flex items-center gap-3">
                            <legend class="sr-only">High-level focus</legend>
                            <span class="text-sm font-semibold text-gray-700">Focus</span>
                            <div class="bg-sand-100 border-sand-300 flex gap-1 rounded-xl border p-1">
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
                                        class="peer-focus:ring-technical flex cursor-pointer items-center rounded-lg px-4 py-1.5 transition peer-checked:bg-white peer-checked:shadow-sm peer-focus:ring-2 hover:peer-not-checked:bg-white/60"
                                    >
                                        <x-at-technical class="grow" />
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
                                        class="peer-focus:ring-governance flex cursor-pointer items-center rounded-lg px-4 py-1.5 transition peer-checked:bg-white peer-checked:shadow-sm peer-focus:ring-2 hover:peer-not-checked:bg-white/60"
                                    >
                                        <x-at-governance class="grow" />
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
                                    @php ($fg = $activity->color->foreground()->withAlpha(1)->toHsla())
                                    @php ($bg = $activity->color->background()->withAlpha(1)->toHsla())
                                    @php ($border = $activity->color->foreground()->withAlpha(0.2)->toHsla())
                                    <li>
                                        <x-checkbox-as-pill
                                            name="activity_{{ $activity->id }}"
                                            value="{{ $activity->id }}"
                                            kind="activity-checkbox"
                                            style="--fg: {{ $fg }}; --bg: {{ $bg }}; --border: {{ $border}}"
                                            class="border-sand-300 hover:border-primary-700 border bg-white text-gray-500 transition peer-checked:border-(--border) peer-checked:bg-(--bg) peer-checked:text-(--fg) hover:bg-white hover:text-(--fg)"
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

                    <!-- Row 2: Location hints -->
                    @if ($categorizedLocations->isNotEmpty())
                        <div class="bg-card border-sand-300 rounded-xl border p-3.5">
                            <div class="mb-2.5 flex items-center gap-3">
                                <h4 class="font-display text-ink text-base font-semibold">Locations</h4>
                                @foreach ($topLevelLocations as $location)
                                    <div>
                                        <x-checkbox-as-pill
                                            name="location_{{ $location->id }}"
                                            value="{{ $location->id }}"
                                            kind="location-checkbox"
                                            data-global-offset="{{ $location->globalSortOrder() }}"
                                            data-top-level="true"
                                            class="peer-checked:bg-primary-50 peer-checked:border-mint-border peer-checked:text-primary-700 bg-sand-100 border-sand-300 border text-sm text-gray-400"
                                        >
                                            <span class="leading-none select-none"> {{ $location->label }} </span>
                                        </x-checkbox-as-pill>
                                    </div>
                                @endforeach
                            </div>
                            <div class="columns-1 gap-5 space-y-3 sm:columns-2 md:columns-3">
                                @foreach ($categorizedLocations as $regionValue => $locations)
                                    @php ($region = LocationRegion::tryFrom($regionValue))
                                    @continue (! $region)
                                    @php ($headerLocation = $locations->first(fn ($l) => $l->isRegionHeader()))
                                    @php ($children = $locations->filter(fn ($l) => ! $l->isRegionHeader()))
                                    <div
                                        class="break-inside-avoid"
                                        id="locations_wrapper_{{ Str::slug($region->value) }}"
                                    >
                                        <div class="mb-1.5 flex w-full items-center gap-2">
                                            <x-checkbox
                                                name="locations_master_checkbox_{{ Str::slug($region->value) }}"
                                                checked
                                                class="locations-master-checkbox"
                                            />
                                            <label
                                                title="Toggle all in {{ $region->label() }}"
                                                class="cursor-pointer text-sm font-semibold text-gray-800"
                                                for="locations_master_checkbox_{{ Str::slug($region->value) }}"
                                            >
                                                {{ $region->label() }}
                                            </label>
                                            @if ($headerLocation)
                                                <input
                                                    type="checkbox"
                                                    checked
                                                    name="location_{{ $headerLocation->id }}"
                                                    id="location_{{ $headerLocation->id }}"
                                                    value="{{ $headerLocation->id }}"
                                                    class="location-checkbox sr-only"
                                                    data-global-offset="{{ $headerLocation->globalSortOrder() }}"
                                                    data-is-region-header="true"
                                                />
                                            @endif
                                        </div>
                                        @if ($children->isNotEmpty())
                                            @php ($visibleLocationCount = 6)
                                            @php ($collapsible = $children->count() > $visibleLocationCount + 2)
                                            <ul class="flex cursor-pointer flex-wrap gap-1.5">
                                                @foreach ($children as $location)
                                                    <li
                                                        @class (["location-overflow hidden" => $collapsible && $loop->index >= $visibleLocationCount])
                                                    >
                                                        <x-checkbox-as-pill
                                                            name="location_{{ $location->id }}"
                                                            value="{{ $location->id }}"
                                                            kind="location-checkbox"
                                                            data-global-offset="{{ $location->globalSortOrder() }}"
                                                            class="{{ $location->isCountry() ? 'hover:border-primary-700 peer-checked:bg-primary-50 peer-checked:border-mint-border peer-checked:text-primary-700 bg-sand-100 border-sand-300 border text-sm text-gray-500 rounded-md! uppercase' : 'hover:border-primary-700 peer-checked:bg-primary-50 peer-checked:border-mint-border peer-checked:text-primary-700 bg-sand-100 border-sand-300 border text-sm text-gray-500' }}"
                                                        >
                                                            <span class="leading-none select-none">
                                                                {{ $location->label }}
                                                            </span>
                                                        </x-checkbox-as-pill>
                                                    </li>
                                                @endforeach

                                                @if ($collapsible)
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="location-more-toggle bg-sand-100 border-sand-300 cursor-pointer rounded-full border px-2.5 py-1 text-sm font-semibold text-gray-500 hover:border-gray-400 hover:text-gray-700"
                                                            aria-expanded="false"
                                                            data-more-label="+ {{ $children->count() - $visibleLocationCount }}…"
                                                        >
                                                            + {{ $children->count() - $visibleLocationCount }}…
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Row 3: Intervention focuses -->
                    <div class="bg-card border-sand-300 rounded-xl border p-3.5">
                        <div class="mb-2.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <h4 class="font-display text-ink text-base font-semibold">Intervention focuses</h4>
                                <x-checkbox-as-pill
                                    name="show_no_focus"
                                    value="1"
                                    kind="no-focus-checkbox"
                                    class="peer-checked:bg-primary-50 peer-checked:border-mint-border peer-checked:text-primary-700 bg-sand-100 border-sand-300 border text-sm text-gray-400"
                                >
                                    <span class="leading-none select-none">No specific focus</span>
                                </x-checkbox-as-pill>
                            </div>
                            <button
                                class="resets-filters focusable border-sand-300 hover:bg-sand-100 flex items-center gap-1.5 rounded-lg border bg-white px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900"
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
                        <div
                            class="grid gap-x-6 gap-y-3 md:grid-cols-(--focus-columns)"
                            style="--focus-columns: {{ $focusColumns }}"
                        >
                            @foreach ($categorizedFocuses as $category => $focuses)
                                <div id="focuses_wrapper_{{ $category }}">
                                    <div class="mb-1.5 flex items-center gap-2">
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
                                                    class="hover:border-primary-700 peer-checked:bg-primary-50 peer-checked:border-mint-border bg-sand-100 border-sand-300 border text-sm text-gray-500 peer-checked:text-[#3f6152]"
                                                >
                                                    <span class="leading-none select-none"> {{ $focus->label }} </span>
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
            class="border-sand-300 bg-paper relative mx-4 mt-4 mb-6 overflow-hidden rounded-2xl border lg:mx-6"
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
                        class="focusable border-sand-300 hover:bg-sand-100 mt-4 inline-flex items-center space-x-2 rounded-md border bg-white px-4 py-1"
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
                        class="resets-filters focusable border-sand-300 hover:bg-sand-100 mt-4 flex items-center space-x-2 rounded-md border bg-white px-4 py-1"
                    >
                        Reset the filters
                    </button>
                </div>
            </section>
            <section data-state="success" class="app-state state-inactive" aria-hidden="true">
                <div class="border-sand-300 left-0 z-20 w-full max-w-md border-r bg-white" id="entry-wrapper"></div>
                <div
                    class="border-sand-300 bg-card pointer-events-none left-0 z-20 flex w-full max-w-md justify-center border-r pt-16 opacity-0 transition-opacity"
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

                <div id="zoom-controls" class="absolute top-3 right-3 z-10 flex flex-col gap-1">
                    <button
                        id="zoom-in"
                        class="border-sand-300 text-ink-muted hover:bg-sand-100 flex h-8 w-8 items-center justify-center rounded-md border bg-white shadow-sm"
                        title="Zoom in"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"
                            />
                        </svg>
                    </button>
                    <button
                        id="zoom-out"
                        class="border-sand-300 text-ink-muted hover:bg-sand-100 flex h-8 w-8 items-center justify-center rounded-md border bg-white shadow-sm"
                        title="Zoom out"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                            <path
                                fill-rule="evenodd"
                                d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>

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
            class="bg-band hover:bg-band-light flex w-full cursor-pointer items-center justify-between px-6 py-3 text-left transition"
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
        <div id="faq-content" class="border-sand-300 bg-paper hidden overflow-hidden border-t px-6 py-8">
            <div class="mx-auto max-w-3xl space-y-8">
                <div>
                    <h3 class="font-display text-ink mb-4 text-lg font-semibold">Goals</h3>
                    <div class="space-y-3">
                        <x-faq-item title="What is the purpose of this biosecurity landscape map?">
                            <!-- prettier-ignore -->
                            <p>
                                This map provides a comprehensive overview of organizations working in the biosecurity
                                field according to a specific set of criteria (see
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://docs.google.com/document/d/12JhGqx5PaA_jD0UKPDfWX4dfDp1gBoxdVM5tDTykPCA/edit?tab=t.0" rel="noopener noreferrer">Inclusion criteria</a>),
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
                            <p>We want users to use this database to better understand the field, and learn about the different actors.</p>
                            <p class="mt-2">This map can be used for research, networking, identifying potential collaborations, or simply understanding the scope and diversity of work being done in biosecurity.</p>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="font-display text-ink mb-4 text-lg font-semibold">Content</h3>
                    <div class="space-y-3">
                        <x-faq-item title="How often is the database updated?">
                            <!-- prettier-ignore -->
                            <p>
                                We strive to keep the database as current as possible. Updates are made on a regular
                                basis as we receive new information or as organizations change. If you notice something
                                is outdated or missing, you can suggest an addition via our
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://biosecurityworld.notion.site/33a4061a75b7806fad1dee0fcd2e921a" rel="noopener noreferrer">suggestion form</a>,
                                or reach out via our
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://docs.google.com/forms/d/e/1FAIpQLSfJrpJ9o3xpIXOHgdOdkj_yrUt5LadIVbnzwKQk6tKWMuU5xw/viewform?usp=send_form" rel="noopener noreferrer">contact form</a>.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="How can I contribute to the map?">
                            <!-- prettier-ignore -->
                            <p>
                                You can suggest an organization via the
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://biosecurityworld.notion.site/33a4061a75b7806fad1dee0fcd2e921a" rel="noopener noreferrer">suggestion form</a>,
                                or reach out via our
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://docs.google.com/forms/d/e/1FAIpQLSfJrpJ9o3xpIXOHgdOdkj_yrUt5LadIVbnzwKQk6tKWMuU5xw/viewform?usp=send_form" rel="noopener noreferrer">contact form</a>.
                                We welcome feedback, suggestions, and information about
                                organizations that should be included (or excluded).
                            </p>
                        </x-faq-item>

                        <x-faq-item title="What are the inclusion criteria for organizations?">
                            <!-- prettier-ignore -->
                            <p>
                                We have a specific
                                <a class="text-primary-700 hover:text-primary-900 underline" target="_blank" href="https://docs.google.com/document/d/12JhGqx5PaA_jD0UKPDfWX4dfDp1gBoxdVM5tDTykPCA/edit?tab=t.0" rel="noopener noreferrer">set of criteria</a>
                                that determine whether an organization is included in our database.
                            </p>
                        </x-faq-item>

                        <x-faq-item title="Is this information publicly available?">
                            <p>Yes, this database is publicly accessible. Please share with anyone that could find it useful, or help us make it better.</p>
                        </x-faq-item>
                    </div>
                </div>

                <div>
                    <h3 class="font-display text-ink mb-4 text-lg font-semibold">Properties & Filters</h3>
                    <div class="space-y-3">
                        <x-faq-item title="What do the different 'Organization Types' mean?">
                            <p>The organization types (such as Research institute, For-profit company, Think tank, etc.) categorize the primary nature of each entity. This helps users understand the diversity of organizations in the biosecurity landscape, and filter if they are looking for a specific type of organization. There is also a specific view where the organizations are sorted via this property.</p>
                        </x-faq-item>

                        <x-faq-item title="How is the 'Activity Type' determined for each organization?">
                            <p>The Activity Type is based on the primary functions of each organization. An organization can have multiple activity types, reflecting the diverse nature of their work in biosecurity. Users can filter according to a specific type of activity. There is also a specific view where the organizations are sorted via this property.</p>
                        </x-faq-item>

                        <x-faq-item title="What is the 'Intervention Focus' category?">
                            <p>The Intervention Focus category provides more specific information about the areas each organization works on, such as synthetic biology, lab biosafety, or crisis management.</p>
                            <p class="mt-2">There is a higher-level focus type which is "Technical" or "Governance" which helps differentiate whether the organization is more focused on research ("Technical") or policymaking ("Governance").</p>
                            <p class="mt-2">Sometimes an organization will not have any intervention focus: it usually means that they might touch on any of the topics, and their mission is more general.</p>
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
