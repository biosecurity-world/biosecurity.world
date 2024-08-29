<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Understand the biosecurity landscape. - Biosecurity World</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <script>
        window.rawMap = @json($tree);
    </script>

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>
<body class="h-full antialiased w-full">
    <div class="flex flex-col w-full h-full items-start">
        <header class="w-full">
            <div class="max-w-7xl mx-auto py-6">
                <nav>
                    <ul class="flex justify-between">
                        <li class="font-display">biosecurity.world</li>
                        <li>
                            <span class="sr-only">Menu items</span>

                            <ul class="flex space-x-6">
                                <li><a href="{{ route('give-feedback', absolute: false) }}" class="underline">
                                        Give feedback
                                    </a></li>
                                <li><a href="{{ route('how-to-contribute', absolute: false) }}" class="underline">
                                        How to contribute?
                                    </a></li>
                                <li><a href="{{ route('about', absolute: false) }}" class="underline">
                                        About
                                    </a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <div class="mt-6">
                    <h1 class="text-4xl font-display font-bold">Understand the biosecurity landscape.</h1>
                    <p class="mt-2">Explore the organizations, initiatives, and projects shaping the future of biosecurity.</p>
                </div>
            </div>
        </header>

        <div class="w-full h-full flex border-t">
            <aside class="order-1 bg-gray-100 w-full max-w-md" id="entry-aside">
            </aside>
            <aside class="order-0 h-full max-w-[calc((100%-80rem)/2+22rem)] filters-sidebar bg-gray-100 w-full">
                <div class="w-full h-full rounded-r-3xl border-r bg-gray-50 flex justify-end">
                    <div class="flex flex-col max-w-sm">
                        <div class="flex-grow p-6">
                            <h3 class="text-xl font-display">Filters</h3>
                            <div class="relative mt-2">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <x-heroicon-s-magnifying-glass class="size-5 text-gray-500"/>
                                </div>
                                <input type="text" autocomplete="off"
                                       class="bg-white rounded-xl py-2.5 pl-11 pr-2.5 w-full shadow-sm border focus:outline-none focus:ring transition focus:ring-emerald-600"
                                       placeholder="Search '1Day Sooner' or 'Research labs'">
                            </div>

                            <fieldset class="mt-6">
                                <legend class="font-medium">Focus on</legend>
                                <div class="mt-1">
                                    <input type="radio" name="focus" id="focus_technical" class="sr-only peer">
                                    <label for="focus_technical" class="block py-1.5 peer-checked:bg-technical transition rounded-t-xl px-4 flex items-center border">
                                        <x-at-technical class="flex-grow"  />
                                        <x-heroicon-m-check class="size-4 text-white check"/>
                                    </label>
                                </div>

                                <div>
                                    <input type="radio" name="focus" id="focus_governance" class="sr-only peer">
                                    <label for="focus_governance" class="block py-1.5 peer-checked:bg-governance transition px-4 flex items-center border border-t-0">
                                        <x-at-governance class="flex-grow" />
                                        <x-heroicon-m-check class="size-4 text-white check"/>
                                    </label>
                                </div>

                                <div>
                                    <input checked type="radio" name="focus" id="focus_neither" class="sr-only peer">
                                    <label for="focus_neither" class="block py-1.5 peer-checked:bg-white transition rounded-b-xl px-4 flex items-center border border-t-0">
                                        <span class="flex-grow">Neither</span>
                                        <x-heroicon-m-check class="size-4 text-gray-700 check"/>
                                    </label>
                                </div>
                            </fieldset>

                            <div class="mt-6">
                                <span class="font-medium leading-6 text-gray-900 whitespace-nowrap">By activity type</span>
                                <p class="text-sm text-gray-700 mt-0.5">Click on an activity type to filter it out of the map.</p>

                                <ul class="mt-2 flex flex-wrap gap-x-2 gap-y-2">
                                    @foreach($activityTypes as $activityType)
                                        <li>
                                            <button
                                                class="flex items-center py-1 rounded-full bg-gray-50 text-sm font-medium text-gray-600 border border-gray-500/10 px-2 group whitespace-nowrap"
                                                style="color: {{ $activityType['fg'] }}; background-color: {{ $activityType['bg'] }}"
                                                type="button"
                                            >
                                                <span class="sr-only">Remove filter</span>

                                                @unless(empty($activityType['icon']))
                                                    <x-activity-type-icon :icon="$activityType['icon']"
                                                                          aria-hidden="true"
                                                                          class="size-[1.125rem] group-hover:opacity-75"/>
                                                @endunless
                                                <span class="ml-1 leading-none group-hover:opacity-75">
                                                                                        {{ Str::limit($activityType['name'], 35) }}
                                                                                    </span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <main class="order-2 w-full h-full relative">
                <div id="app" class="w-full">
                    <section data-state="error" aria-hidden="true"
                             class="app-state state-inactive flex items-center justify-center z-50">
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
                    <section data-state="loading" aria-hidden="false"
                             class="app-state state-active flex items-center justify-center">
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
                    <section data-state="success" class="app-state state-inactive flex" aria-hidden="true">
                        <svg role="main" id="map" width="100%" height="100%">
                            <g id="zoom-wrapper">
                                <g id="center-wrapper">
                                    <g id="cartesian-flip">
                                            <g id="background"></g>
                                            <rect width="8" height="8" rx="2" ry="2" data-vertex="{{ $tree->id }}"
                                                  class="invisible" aria-hidden="true"
                                                  stroke="#e5e7eb" fill="white"></rect>
                                            <g>
                                                @foreach($categories as $category)
                                                    <x-category :category="$category"/>
                                                @endforeach
                                            </g>
                                            <g>
                                                @foreach($entrygroups as $entrygroup)
                                                    <foreignObject width="100%" height="100%" class="invisible" aria-hidden="true"
                                                                   data-vertex="{{ $entrygroup['@id']}}">
                                                        <x-entrygroup :entries="$entrygroup['entries']" :id="$entrygroup['@id']"/>
                                                    </foreignObject>
                                                @endforeach
                                            </g>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </section>
                </div>
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/htmx.org@2.0.2"></script>
</body>
</html>
