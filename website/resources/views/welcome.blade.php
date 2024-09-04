@php use App\Services\NotionData\DataObjects\Activity; @endphp
    <!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Understand the biosecurity landscape. - Biosecurity World</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet"/>

    <script>
        window.nodes = @json($tree->nodes);
        // TODO: This is wasteful and will be removed. We want it to work before optimizing it.
        window.lookup = @json($lookup);
    </script>

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>
<body class="h-full antialiased w-full">
<header class="w-full pb-8  pt-6 px-8 xl:px-0">
    <div class="max-w-7xl mx-auto">
        <nav>
            <ul class="flex justify-between">
                <li class="font-display">
                    <a href="{{ route('welcome') }}" class="hover:underline">
                        biosecurity.world
                    </a>
                </li>
                <li>
                    <span class="sr-only">Menu items</span>

                    <ul class="flex space-x-6">
                        <li><a href="{{ route('how-to-contribute', absolute: false) }}" class="underline">
                                Contribute
                            </a></li>
                        <li><a href="{{ route('give-feedback', absolute: false) }}" class="underline">
                                Give feedback
                            </a></li>
                        <li><a href="{{ route('about', absolute: false) }}" class="underline">
                                About
                            </a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="mt-8">
            <h1 class="text-4xl font-display font-bold">Understand the biosecurity landscape.</h1>
            <p class="mt-2">Explore the organizations, initiatives, and projects shaping the future of biosecurity.</p>
        </div>
    </div>
</header>

<div class="w-full h-full flex duration-500 transition-[padding] " id="map-wrapper">
    <aside class="w-full h-full border-r divide-y border-y border-l max-w-md rounded-l-3xl flex flex-col">
        <section class="p-6 bg-white rounded-l-3xl">
            <h3 class="text-xl font-display flex-grow">
                Explore the map.
            </h3>

            <p class="text-gray-700 mt-1">
                Last updated on
                <time datetime="{{ $lastEditedAt->toIso8601String() }}"
                      title="{{ $lastEditedAt->toIso8601String() }}">
                    {{ $lastEditedAt->format('F j, Y') }}.
                </time>
            </p>

            <div class="mt-4 flex items-center justify-between">
                      <span class="flex flex-grow flex-col">
                        <span class="text-sm font-medium leading-6 text-gray-900">
                            Highlight recently added entries
                        </span>
                        <span class="text-sm text-gray-500">
                            Highlight entries added in the last month.
                        </span>
                      </span>
                <input type="checkbox" class="sr-only peer" aria-hidden="true" id="recent" name="recent">
                <label for="recent" data-toggle="off"
                       class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-600 peer-focus:ring-offset-2">
                <span aria-hidden="true"
                      class="pointer-events-none inline-block h-5 w-5 translate-x-0 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>

                </label>
            </div>
        </section>
        <section class="p-6 bg-gray-50 flex-grow rounded-bl-3xl">
            <h4 class="text-lg font-display">Filters</h4>

            <div class="mt-2">
                <fieldset>
                    <legend class="font-medium leading-6 text-gray-900">Focus on</legend>

                    <div class="shadow-sm rounded-xl mt-1 bg-white">
                        <div>
                            <input type="checkbox" name="lens_technical" id="lens_technical" value="technical"
                                   class="sr-only peer">
                            <label for="lens_technical"
                                   class="block py-1.5 peer-checked:bg-technical hover:bg-gray-50 transition rounded-t-xl px-4 flex items-center border peer-focus:ring-2 peer-focus:ring-technical peer-focus:ring-offset-2">
                                <x-at-technical class="flex-grow"/>
                                <x-heroicon-m-check class="size-5 text-white check"/>
                            </label>
                        </div>
                        <div>
                            <input type="checkbox" name="lens_governance" id="lens_governance" value="governance"
                                   class="sr-only peer">
                            <label for="lens_governance"
                                   class="block py-1.5 peer-checked:bg-governance hover:bg-gray-50 transition rounded-b-xl px-4 flex items-center border border-t-0 peer-focus:ring-2 peer-focus:ring-governance peer-focus:ring-offset-2">
                                <x-at-governance class="flex-grow"/>
                                <x-heroicon-m-check class="size-5 text-white check"/>
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="mt-6">
                <span class="font-medium leading-6 text-gray-900">By activity type</span>
                <p class="text-sm text-gray-700 mt-0.5">Click on an activity type to filter it out of the
                    map.</p>

                <ul class="mt-2 flex flex-wrap gap-x-2 gap-y-2">
                    @foreach($tree->activities() as $activity)
                        <li>
                            <input type="checkbox" checked name="activity_{{ $activity->id }}"
                                   id="activity_{{ $activity->id }}"
                                   data-offset="{{ array_search($activity->id, Activity::$seen) }}"
                                   value="{{ $activity->id }}" class="sr-only peer">
                            <label
                                for="activity_{{ $activity->id }}"
                                class="flex items-center py-1 rounded-full bg-gray-50 text-sm text-white font-medium text-gray-600 border border-gray-500/10 px-2 group whitespace-nowrap shadow-sm peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-emerald-600 peer-focus:ring-opacity-50 transition"
                                style="background-color: {{ $activity->color->foreground() }}"
                                type="button"
                            >
                                <span class="sr-only">Remove filter</span>

                                @if(!empty($activity->iconName()))
                                    <x-activity-icon :activity="$activity"
                                                     aria-hidden="true"
                                                     class="size-[1.125rem] group-hover:opacity-75"/>
                                @endif

                                <span class="ml-1.5 leading-none group-hover:opacity-75">
                                            {{ $activity->label }}
                                        </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </aside>

    <main class="w-full h-full relative border-y border-r rounded-r-3xl">
        <section data-state="error" aria-hidden="true"
                 class="app-state state-inactive">
            <div class="text-center">
                <h3 class="text-xl">An error has occurred.</h3>
                <p class="reason mt-1"></p>
                <pre class="debug my-2 text-left border px-4 py-2 bg-white" hidden></pre>
                <p>
                    You can try reloading the page or checking the <a href="{{ $databaseUrl }}"
                                                                      rel="noopener noreferrer nofollow"
                                                                      class="text-blue-500 underline">Notion
                        table</a> directly.
                </p>
                <a href="javascript:window.location.reload();"
                   class="reload-button border px-4 py-1 rounded-md mt-4 bg-white inline-flex items-center space-x-2">
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
                <svg class="inline h-8 w-8 animate-spin text-gray-200 fill-emerald-600"
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
        <section data-state="success" class="app-state state-inactive !sticky" aria-hidden="true">
            <div class="absolute inset-0 z-20 w-full h-full max-w-md border-y" id="entry-wrapper"></div>
            <div
                class="absolute inset-0 z-20 w-full h-full max-w-md border-y pointer-events-none flex justify-center border-r bg-gray-50 rounded-r-3xl pt-16"
                id="entry-loader">
                <svg class="inline h-8 w-8 animate-spin text-gray-200 fill-emerald-600"
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
            <svg role="main" id="map" width="100%" height="100%">
                <g id="zoom-wrapper">
                    <g id="center-wrapper">
                        <g id="background"></g>


                        {{-- It could be a <rect>, but this way, there's one code path. --}}
                        <foreignObject width="100%" height="100%" class="invisible pointer-events-none"
                                       aria-hidden="true"
                                       data-node="{{ $tree->rootNodeId }}">
                            <div class="size-4 bg-white border"></div>
                        </foreignObject>

                        <g>
                            @foreach($tree->categories() as $category)
                                <foreignObject width="100%" height="100%" class="invisible pointer-events-none"
                                               aria-hidden="true"
                                               data-node="{{ $category->id }}">
                                    <x-category :category="$category"/>
                                </foreignObject>
                            @endforeach
                        </g>

                        <g id="entrygroups">
                            @foreach($tree->entrygroups() as $entrygroup)
                                <foreignObject width="100%"
                                               height="100%"
                                               class="invisible pointer-events-none"
                                               aria-hidden="true"
                                               data-node="{{ $entrygroup->id }}">
                                    <x-entrygroup
                                        :entries="array_map(fn (string $id) => $tree->lookup[$id], $entrygroup->entries)"
                                        :entrygroup="$entrygroup"
                                    />
                                </foreignObject>
                            @endforeach
                        </g>
                    </g>
                </g>
            </svg>
            <div class="absolute bottom-6 right-6">
                <div class="flex flex-col bg-white shadow divide-y rounded-lg">
                    <button class="p-2 hover:bg-gray-50 rounded-t-lg" onclick="zoomIn()">
                        <x-heroicon-s-plus class="size-5 text-gray-700"/>
                    </button>

                    <button class="p-2 hover:bg-gray-50 rounded-b-lg" onclick="zoomOut()">
                        <x-heroicon-s-minus class="size-5 text-gray-700"/>
                    </button>
                </div>
            </div>
        </section>
        <section data-state="empty" class="app-state state-inactive" aria-hidden="true">
            <div class="text-center flex flex-col items-center justify-center">
                <h3 class="text-xl font-display">
                    There are no entries matching your filters.
                </h3>

                <button id="filters-reset"
                        class="border px-4 py-1 rounded-md mt-4 bg-white flex items-center space-x-2">
                    Reset the filters
                </button>
            </div>
        </section>
    </main>

</div>

<footer aria-labelledby="footer-heading" class="px-4 xl:px-36">
    <div class="mt-12 border-t border-gray-900/10 py-4 ">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="flex justify-between">
            <p>
                <a href="{{ route('welcome') }}" class="font-display">biosecurity.world</a>

                <span class="text-gray-700">
                        &mdash; Understand the biosecurity landscape.
                    </span>
            </p>

            <ul class="flex space-x-4">
                <li>
                    <a href="{{ $databaseUrl }}" class="inline-flex text-sm underline text-gray-700">
                        <span class="mr-0.5">Notion Database</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-4 text-gray-400 group-hover:text-emerald-700 mt-px"
                             aria-label="External link icon">
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="https://github.com/biosecurity-world/biosecurity.world"
                       class="inline-flex text-sm underline text-gray-700">
                        <span class="mr-0.5">GitHub</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-4 text-gray-400 group-hover:text-emerald-700 mt-px"
                             aria-label="External link icon">
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</footer>

{{-- Necessary for spatie/laravel-export to find the entries, adding /_/entries to config('export.paths') doesn't crawl the URL on it. --}}
<a href="/_/entries" class="hidden" aria-hidden="true">entries</a>
</body>
</html>
