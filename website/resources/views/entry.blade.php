{{--<!doctype html>--}}
{{--<html lang="en" class="bg-gray-100 h-full">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport"--}}
{{--          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">--}}
{{--    <meta http-equiv="X-UA-Compatible" content="ie=edge">--}}
{{--    <title>Document</title>--}}

{{--    <link rel="preconnect" href="https://fonts.bunny.net">--}}
{{--    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />--}}

{{--    @vite('resources/css/app.css')--}}
{{--</head>--}}
{{--<body class="bg-gray-50 border-r w-full max-w-md h-full">--}}
<div class="entry flex flex-col h-full justify-between border-r border-t-0 rounded-r-3xl bg-white">
    <div>
        <div class="flex justify-between items-center bg-white pl-4 pr-6 py-4 rounded-tr-3xl border-b border-r">
            <ol role="list" class="flex items-center space-x-2 overflow-x-scroll">
                <li>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19" fill="currentColor"
                             class="size-4 text-gray-500">
                            <path stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M3.3 14v-3.3c0-.5.4-.9.9-.9h11.6c.5 0 .9.4.9.9V14M3.3 14a1.7 1.7 0 1 0 0 3.3 1.7 1.7 0 0 0 0-3.3Zm13.4 0a1.7 1.7 0 1 0 0 3.3 1.7 1.7 0 0 0 0-3.3ZM10 14a1.7 1.7 0 1 0 0 3.3 1.7 1.7 0 0 0 0-3.3Zm0 0V5.7m-1.7 0h3.4c.4 0 .8-.4.8-.9V1.5c0-.5-.4-.8-.8-.8H8.3c-.4 0-.8.3-.8.8v3.3c0 .5.4.9.8.9Z"/>
                            <path d="M7.5.7h5v5h-5z"/>
                        </svg>

                        <span class="sr-only">Root</span>
                    </div>
                </li>
                @foreach($breadcrumbs as $breadcrumb)
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-5 flex-shrink-0 text-gray-500">
                            <path fill-rule="evenodd"
                                  d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z"
                                  clip-rule="evenodd"/>
                        </svg>

                        <span class="ml-2 text-sm font-medium text-gray-700 whitespace-nowrap">{{ $breadcrumb }}</span>
                    </li>
                @endforeach

                <li class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                         class="size-5 flex-shrink-0 text-gray-500">
                        <path fill-rule="evenodd"
                              d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z"
                              clip-rule="evenodd"/>
                    </svg>

                    <x-entry-logo :logo="$entry['logo']" :size="16" class="ml-2"/>
                </li>
            </ol>

            <button class="flex items-center duration-300 hover:bg-gray-50 transition p-2 -m-2 rounded-xl group" onclick="document.getElementById('entry-aside').innerHTML = ''">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     class="size-5 text-gray-700 group-hover:text-emerald-600">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                </svg>
            </button>
        </div>

        @if ($entry['gcbrFocus'])
            <div class="px-4 py-1.5 bg-emerald-50">
                <p class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-emerald-600">
                        <path fill-rule="evenodd" d="M8 1.75a.75.75 0 0 1 .692.462l1.41 3.393 3.664.293a.75.75 0 0 1 .428 1.317l-2.791 2.39.853 3.575a.75.75 0 0 1-1.12.814L7.998 12.08l-3.135 1.915a.75.75 0 0 1-1.12-.814l.852-3.574-2.79-2.39a.75.75 0 0 1 .427-1.318l3.663-.293 1.41-3.393A.75.75 0 0 1 8 1.75Z" clip-rule="evenodd" />
                    </svg>


                    <span class="text-xs ml-1 text-emerald-700">
                 This {{ $organizationTypeNoun }} focuses on <abbr title="Global Catastrophic Biological Risks">GCBRs</abbr>.
                </span>
                </p>
            </div>

        @endif

        <div class="px-4 pb-2 mt-2">
            <span class="text-xs text-gray-500 tracking-tighter font-semibold">{{ ucfirst($organizationTypeNoun) }}</span>

            <h2 class="text-xl font-display font-bold -mt-1">
                <a class="text-emerald-600 underline" target="_blank" href="{{ $entry['link'] }}">
                    {{ $entry['label'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                         class="inline mb-3 size-4 text-gray-400 group-hover:text-emerald-700" aria-label="External link icon">
                        <path
                            d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                        <path
                            d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                    </svg></a>
            </h2>

            @if(!empty($location))
                <ul class="mt-2">
                    <li class="flex items-center space-x-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-gray-400">
                            <path fill-rule="evenodd" d="m7.539 14.841.003.003.002.002a.755.755 0 0 0 .912 0l.002-.002.003-.003.012-.009a5.57 5.57 0 0 0 .19-.153 15.588 15.588 0 0 0 2.046-2.082c1.101-1.362 2.291-3.342 2.291-5.597A5 5 0 0 0 3 7c0 2.255 1.19 4.235 2.292 5.597a15.591 15.591 0 0 0 2.046 2.082 8.916 8.916 0 0 0 .189.153l.012.01ZM8 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" clip-rule="evenodd" />
                        </svg>

                        <span class="text-sm text-gray-700">{{ $location }}</span>
                    </li>
                </ul>
            @endif

            <p class="mt-4 text-justify">{{ $entry['description'] }}</p>

            <p class="mt-2">
                This {{ $organizationTypeNoun  }} works on:
            </p>

            <ul class="list-disc list-inside">
                @foreach($entry['interventionFocuses'] as $interventionFocus)
                    <li>
                        <a href="" class="underline">
                            @if ($interventionFocus->name === '[TECHNICAL]')
                                <x-at-technical class="underline" />
                            @elseif($interventionFocus->name === '[GOVERNANCE]')
                                <x-at-governance class="underline" />
                            @else
                                <span>{{ $interventionFocus->name }}</span>
                            @endif
                        </a>
                    </li>

                @endforeach
            </ul>

        </div>
    </div>
    <div class="py-2 px-6 bg-gray-50 border-t space-x-2">
        <a class="inline-flex text-sm underline text-gray-700" href="{{ $notionUrl }}">
            <span class=mr-0.5">Open in Notion</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                 class="size-4 text-gray-400 group-hover:text-emerald-700 mt-px" aria-label="External link icon">
                <path
                    d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                <path
                    d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
            </svg>
        </a>
        <a class="inline-flex text-sm underline text-gray-700">
            <span class="mr-0.5">Report a problem</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                 class="size-4 text-gray-400 group-hover:text-emerald-700 mt-px" aria-label="External link icon">
                <path
                    d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                <path
                    d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
            </svg>
        </a>
    </div>
</div>
{{--</body>--}}
{{--</html>--}}