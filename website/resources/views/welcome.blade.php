<x-layouts.default class="h-full antialiased w-full bg-gray-100" title="Understand the biosecurity landscape.">
<x-slot:head>
    <script>
        window.nodes = @json($nodes);
        window.filterData = @json($filterData);
    </script>

    @vite('resources/js/map.ts')
</x-slot:head>
<header class="w-full pb-52 lg:pb-[14.25rem] bg-gradient-to-tl from-primary-600 to-primary-950 pt-4 lg:pt-8">
    <x-navbar class="md:bg-white/20 md:shadow-inner md:shadow-white/30" invert/>

    <h1 class="px-6 mt-6 sm:mt-16  lg:mt-24 max-w-3xl lg:text-center text-3xl font-bold tracking-tight lg:text-6xl font-display text-white mx-auto md:text-center">
        Understand the biosecurity landscape.
    </h1>

    <ul class="max-w-7xl space-y-6 md:space-y-0 mx-auto px-6 xl:px-0  md:grid md:grid-cols-3 md:gap-x-8 xl:gap-x-12 mt-6 md:mt-16 lg:mt-20">
        <li>
            <h2 class="font-display lg:text-lg xl:text-2xl text-white">Up-to-date</h2>
            <hr class="w-8 mt-1.5 border-white/40 hidden lg:block">

            <p class="max-w-[65ch] mt-1 lg:mt-2 text-white/95 xl:text-lg">
                Humans update regularly the map and our team monitors privileged channels for announcements about new
                organizations.
            </p>
        </li>
        <li>
            <h2 class="font-display lg:text-lg xl:text-2xl text-white">Crowd-sourced</h2>
            <hr class="w-8 mt-1.5 border-white/40 hidden lg:block">

            <p class="max-w-[65ch] mt-1 lg:mt-2 text-white/95 xl:text-lg">
                We encourage submissions and corrections, which are individually reviewed by
                researchers from
                <a href="https://www.ens.psl.eu" class="underline text-white hover:text-white/70">ENS</a>
                and <a href="https://ox.ac.uk" class="underline text-white hover:text-white/70">Oxford University</a>.
            </p>
        </li>
        <li>
            <h2 class="font-display lg:text-lg xl:text-2xl text-white">Transparent</h2>
            <hr class="w-8 mt-1.5 border-white/40 hidden lg:block">

            <p class="max-w-[65ch] mt-1 lg:mt-2 text-white/95 xl:text-lg">
                As an <a href="https://github.com/biosecurity-world/biosecurity.world" class="underline">open-source</a>, <a
                    class="underline" href="{{ $databaseUrl }}">open-data</a>, scientific non-profit, we keep a record of
                our inclusion decisions for people to challenge.
            </p>
        </li>
    </ul>
</header>
<h2 class="sr-only">Map</h2>
<div class="w-full h-full flex duration-300 motion-safe:transition-[padding,border-radius] mx-auto" id="map-wrapper">
    <aside
        class="w-full h-full overflow-y-scroll border-r border-y border-l max-w-md rounded-l-3xl hidden lg:flex lg:flex-col bg-white">
        <header class="border-b">
            <div class="px-6 pt-4">
                <div class="flex items-center">
                    <h3 class="flex-1 font-display text-2xl">
                        Map of Biosecurity
                    </h3>

                    <button title="Toggle fullscreen (shortcut: F)" id="toggle-fullscreen" class="-m-2 p-2 transition hover:bg-gray-100 border-transparent hover:border-gray-200 border rounded-full hover:shadow-inner">
                        <span class="sr-only">Toggle fullscreen</span>
                        <x-heroicon-o-arrows-pointing-out id="not-fullscreen" aria-hidden="true" class="size-5 text-gray-700"/>
                        <x-heroicon-o-arrows-pointing-in id="is-fullscreen" aria-hidden="true" class="hidden size-5 text-gray-700"/>
                    </button>
                </div>
                <p class="text-gray-700 mt-&">
                    Last updated on <time datetime="{{ $lastEditedAt->toIso8601String() }}"
                                          title="{{ $lastEditedAt->diffForHumans() }}">{{ $lastEditedAt->format('F j, Y') }}</time>.
                </p>

                <ul class="list-disc list-inside mt-4">
                    <li>
                        <a href="#"
                           class="inline-flex underline whitespace-nowrap">
                            Suggest an entry
                        </a>
                    </li>
                </ul>
            </div>

            <ul class="flex space-x-4 px-6 mt-4 py-2">
                <li>
                    <a href="#"
                       class="inline-flex text-sm whitespace-nowrap">
                        <span class="text-gray-400">&bull;&nbsp;</span>
                        <span class="underline -mt-px text-gray-700">Inclusion criteria</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="inline-flex text-sm whitespace-nowrap">
                        <span class="text-gray-400">&bull;&nbsp;</span>
                        <span class="underline -mt-px text-gray-700">Rejected entries</span>
                    </a>
                </li>
                <li>
                    <a href="https://notion.so/{{ config('services.notion.database') }}"
                       class="inline-flex text-sm whitespace-nowrap">
                        <span class="text-gray-400">&bull;&nbsp;</span>
                        <span class="mr-0.5 -mt-px underline text-gray-700">Notion</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-4 text-gray-400 group-hover:text-primary-700 -mt-px"
                             aria-label="External link icon">
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </header>
        <div class="bg-gray-50 px-6 py-4 lg:flex-1">
            <h4 class="flex-1 text-lg font-display">Filters</h4>

            <fieldset class="mt-2">
                <legend class="font-bold leading-6 text-gray-900">
                    Domain
                </legend>

                <div class="shadow-sm rounded-xl mt-0.5 bg-white">
                    <div>
                        <input type="checkbox" name="domain_technical" id="domain_technical" value="technical"
                               class="sr-only peer">
                        <label for="domain_technical"
                               class="py-1.5 peer-checked:bg-technical hover:bg-gray-50 transition rounded-t-xl px-4 flex items-center border peer-focus:ring-2 peer-focus:ring-technical peer-focus:ring-offset-2">
                            <x-at-technical class="flex-grow"/>
                            <x-heroicon-m-check class="size-5 text-white check"/>
                        </label>
                    </div>
                    <div>
                        <input type="checkbox" name="domain_governance" id="domain_governance" value="governance"
                               class="sr-only peer">
                        <label for="domain_governance"
                               class="py-1.5 peer-checked:bg-governance hover:bg-gray-50 transition rounded-b-xl px-4 flex items-center border border-t-0 peer-focus:ring-2 peer-focus:ring-governance peer-focus:ring-offset-2">
                            <x-at-governance class="flex-grow"/>
                            <x-heroicon-m-check class="size-5 text-white check"/>
                        </label>
                    </div>
                </div>
            </fieldset>
            <div class="mt-6 flex items-center justify-between">
                      <span class="flex flex-grow flex-col">
                        <span class="font-bold leading-6 text-gray-900">
                            Focus on GCBRs prevention
                        </span>
                        <span class="text-sm text-gray-500">
                            Include only organizations focused on large-scale pandemics prevention.
                        </span>
                      </span>
                <input type="checkbox" class="sr-only peer" aria-hidden="true" id="gcbr_focus" name="gcbr_focus">
                <label for="gcbr_focus" data-toggle="off"
                       class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-600 peer-focus:ring-offset-2">
                <span aria-hidden="true"
                      class="pointer-events-none inline-block h-5 w-5 translate-x-0 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>

                </label>
            </div>
            <fieldset class="mt-6">
                <legend class="font-bold leading-6 text-gray-900 inline">Activities</legend>

                <ul class="mt-2.5 clear-both flex flex-wrap gap-x-2 gap-y-2">
                    @foreach($tree->activities() as $activity)
                        <li>
                            <input type="checkbox"  checked name="activity_{{ $activity->id }}"
                                   id="activity_{{ $activity->id }}"
                                   value="{{ $activity->id }}" class="sr-only peer">
                            <label
                                for="activity_{{ $activity->id }}"
                                class="flex items-center py-1 rounded-full bg-gray-50 text-sm text-white font-bold border border-gray-500/10 px-2 group whitespace-nowrap shadow-sm peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-primary-600 peer-focus:ring-opacity-50 transition"
                                style="background-color: {{ $activity->color->foreground() }}"
                                type="button"
                            >
                                <span class="sr-only">Remove filter</span>

                                @if(!empty($activity->iconName()))
                                    <x-activity-icon :activity="$activity"
                                                     aria-hidden="true"
                                                     class="size-[1.125rem] group-hover:opacity-75"/>
                                @endif

                                <span class="ml-1.5 leading-none group-hover:opacity-75 select-none">
                                    {{ $activity->label }}
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </fieldset>
            <fieldset class="mt-6">
                <legend class="font-bold leading-6 text-gray-900 mr-4 flex-1">Focuses</legend>

                <ul class="mt-2.5">
                    @foreach($tree->interventionFocuses() as $focus)
                        <li>
                            <input type="checkbox"  checked name="focus_{{ $focus->id }}"
                                   id="focus_{{ $focus->id }}"
                                   value="{{ $focus->id }}" class="sr-only peer">
                            <label
                                for="focus_{{ $focus->id }}"
                                type="button"
                            >
                                <span class="sr-only">Toggle filter</span>
                                <span class="leading-none group-hover:opacity-75 select-none">
                                    {{ $focus->label }}
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </fieldset>
        </div>
    </aside>
    <main class="w-full h-full relative border-b border-r bg-gray-100 rounded-r-3xl rounded-l-3xl lg:rounded-l-none">
        <section data-state="error" aria-hidden="true" class="app-state state-inactive">
            <div class="text-center">
                <h3 class="text-xl">An error has occurred.</h3>
                <p class="reason mt-1"></p>
                <p>
                    You can try reloading the page or checking the <a href="{{ $databaseUrl }}"
                                                                      rel="noopener noreferrer nofollow"
                                                                      class="text-emerald-700 underline">Notion
                        table</a> directly.
                </p>
                <a href="javascript:window.location.reload();"
                   class="focusable border px-4 py-1 rounded-md mt-4 bg-white inline-flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                        <path fill-rule="evenodd"
                              d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                              clip-rule="evenodd"/>
                    </svg>
                    <span class="-mt-px">Reload</span>
                </a>
            </div>
        </section>
        <section data-state="loading" aria-hidden="false" class="app-state state-active">
            <div>
                <svg class="inline h-8 w-8 animate-spin text-gray-200 fill-primary-600"
                     viewBox="0 0 100 101" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"></path>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"></path>
                </svg>
                <span class="sr-only">Loading...</span>
            </div>
        </section>
        <section data-state="empty" class="app-state state-inactive" aria-hidden="true">
            <div class="text-center flex flex-col items-center justify-center">
                <h3 class="text-xl font-display">
                    There are no entries matching your filters.
                </h3>
                <button
                        class="resets-filters focusable border px-4 py-1 rounded-md mt-4 bg-white flex items-center space-x-2 hover:bg-gray-50">
                Reset the filters
                </button>
            </div>
        </section>
        <section data-state="success" class="app-state state-inactive" aria-hidden="true">
            <div class="absolute inset-0 z-20 w-full h-full max-w-md border-y" id="entry-wrapper"></div>
            <div
                class="absolute inset-0 z-20 w-full h-full max-w-md border-y pointer-events-none flex justify-center border-r bg-gray-50 rounded-r-3xl pt-16"
                id="entry-loader">
                <svg class="inline h-8 w-8 animate-spin text-gray-200 fill-primary-600"
                     viewBox="0 0 100 101" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"></path>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"></path>
                </svg>
                <span class="sr-only">Loading...</span>
            </div>

            <div id="above-map"></div>
            <svg id="map" width="100%" height="100%" class="rounded-tr-3xl">
                <!-- The map will be dynamically inserted here -->
            </svg>
            <div class="absolute bottom-6 right-6">
                <div class="flex flex-col bg-white shadow divide-y rounded-lg">
                    <button class="p-2 hover:bg-gray-50 rounded-t-lg focusable" id="zoom-in">
                        <x-heroicon-s-plus class="size-5 text-gray-700"/>
                    </button>
                    <button class="p-2 hover:bg-gray-50 rounded-b-lg focusable" id="zoom-out">
                        <x-heroicon-s-minus class="size-5 text-gray-700"/>
                    </button>
                </div>
            </div>
        </section>
    </main>
</div>
<x-footer />
 Necessary for spatie/laravel-export to find the entries, adding /_/entries to config('export.paths') doesn't crawl the URL on it.
<a href="/_/entries" class="hidden" aria-hidden="true">entries</a>
<a href="/_/m" class="hidden" aria-hidden="true">map</a>
</x-layouts.default>
