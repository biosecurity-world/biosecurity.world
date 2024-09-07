@if (!$isXHR && !app()->isProduction())
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>
<body class="h-full max-w-lg border-l bg-gray-50">
@endif
<div class="entry flex flex-col h-full justify-between border-r border-t-0 rounded-r-3xl bg-white">
    <div>
        <div class="flex justify-between items-center bg-gray-50 pl-4 pr-6 py-4 rounded-tr-3xl border-b border-r">
            <ol role="list" class="flex items-center space-x-1 overflow-x-scroll">
                @foreach($breadcrumbs as $breadcrumb)
                    <li class="flex items-center">
                        <span class="mr-1 text-sm font-medium text-gray-700 whitespace-nowrap">{{ $breadcrumb }}</span>

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-5 flex-shrink-0 text-gray-500">
                            <path fill-rule="evenodd"
                                  d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </li>
                @endforeach

                <li class="flex items-center">
                    <x-entry-logo class="ml-2" :logo="$entry->logo" />
                </li>
            </ol>

            <button class="flex items-center duration-300 hover:bg-white hover:border-gray-200 border border-transparent transition p-2 -m-2 rounded-full group close-entry">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     class="size-5 text-gray-700 group-hover:text-primary-600">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                </svg>
            </button>
        </div>

        @if ($entry->gcbrFocus)
            <div class="px-4 py-1.5 bg-primary-50">
                <p class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-primary-600">
                        <path fill-rule="evenodd" d="M8 1.75a.75.75 0 0 1 .692.462l1.41 3.393 3.664.293a.75.75 0 0 1 .428 1.317l-2.791 2.39.853 3.575a.75.75 0 0 1-1.12.814L7.998 12.08l-3.135 1.915a.75.75 0 0 1-1.12-.814l.852-3.574-2.79-2.39a.75.75 0 0 1 .427-1.318l3.663-.293 1.41-3.393A.75.75 0 0 1 8 1.75Z" clip-rule="evenodd" />
                    </svg>


                    <span class="text-xs ml-1 text-primary-700">
                 This {{ $entry->nounForOrganizationType() }} focuses on <abbr title="Global Catastrophic Biological Risks">GCBRs</abbr>.
                </span>
                </p>
            </div>
        @endif

        <div class="px-4 pb-2 mt-6">
            <h2 class="text-xl font-display font-bold">
                <a class="text-primary-600 underline" target="_blank" href="{{ $entry->link }}">
                    {{ $entry->label }}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                         class="inline mb-3 size-4 text-gray-400 group-hover:text-primary-700" aria-label="External link icon">
                        <path
                            d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                        <path
                            d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                    </svg>
                </a>
            </h2>

            @if(!empty($entry->location))
                <ul class="mt-1.5 flex space-x-4 overflow-x-scroll">
                    <li class="flex justify-center space-x-1.5">
                        @foreach($entry->activities as $activity)
                            <span style="background-color: {{ $activity->color->foreground() }}" class="inline-block py-1.5 px-2.5 rounded-full">
                            <x-activity-icon :activity="$activity" class="size-4 text-white" />
                        </span>
                        @endforeach
                    </li>
                    <li class="border rounded-full px-2 py-0.5 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-gray-400">
                            <path fill-rule="evenodd" d="m7.539 14.841.003.003.002.002a.755.755 0 0 0 .912 0l.002-.002.003-.003.012-.009a5.57 5.57 0 0 0 .19-.153 15.588 15.588 0 0 0 2.046-2.082c1.101-1.362 2.291-3.342 2.291-5.597A5 5 0 0 0 3 7c0 2.255 1.19 4.235 2.292 5.597a15.591 15.591 0 0 0 2.046 2.082 8.916 8.916 0 0 0 .189.153l.012.01ZM8 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" clip-rule="evenodd" />
                        </svg>

                        <span class="text-sm text-gray-700 ml-1 truncate">{{ $entry->location }}</span>
                    </li>
                </ul>
            @endif

            <div class="mt-6 text-justify">
                <x-notion-rich-text :text="$entry->description" />
            </div>

            <p class="mt-4">
                This {{ $entry->nounForOrganizationType()  }} works on:
            </p>

            <ul class="list-disc list-inside">
                @foreach($entry->interventionFocuses as $focus)
                    <li>
                        <a href="" class="underline">
                            @if ($focus->isMetaTechnicalFocus())
                                <x-at-technical class="underline" />
                            @elseif($focus->isMetaGovernanceFocus())
                                <x-at-governance class="underline" />
                            @else
                                <span>{{ $focus->label }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="py-2 px-6 bg-gray-50 border-t space-x-2 rounded-br-3xl">
        <a class="inline-flex text-sm underline text-gray-700" href="{{ $entry->notionUrl() }}">
            <span class=mr-0.5">Open in Notion</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                 class="size-4 text-gray-400 group-hover:text-primary-700 mt-px" aria-label="External link icon">
                <path
                    d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                <path
                    d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
            </svg>
        </a>
        <a class="inline-flex text-sm underline text-gray-700">
            <span class="mr-0.5">Report a problem</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                 class="size-4 text-gray-400 group-hover:text-primary-700 mt-px" aria-label="External link icon">
                <path
                    d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                <path
                    d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
            </svg>
        </a>
    </div>
</div>
@if (!$isXHR && !app()->isProduction())
</body>
</html>
@endif
