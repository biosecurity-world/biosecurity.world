@if (! $isXHR && ! app()->isProduction())
    {{-- format-ignore-start --}}
    <x-layouts.default class="h-full max-w-lg border-l bg-gray-50">
    {{-- format-ignore-end --}}
@endif

<div class="entry flex h-full flex-col justify-between rounded-r-3xl border-r border-t-0 bg-white">
    <div>
        <div class="flex items-center justify-between rounded-tr-3xl border-b border-r bg-gray-50 py-4 pl-4 pr-6">
            <ol role="list" class="flex items-center space-x-1 overflow-x-scroll">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="flex items-center">
                        <span class="mr-1 whitespace-nowrap text-sm font-bold text-gray-700">{{ $breadcrumb }}</span>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="size-5 flex-shrink-0 text-gray-500"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </li>
                @endforeach

                <li class="flex items-center bg-white">
                    <x-entry-logo :logo="$entry->logo" alt="{{ $entry->label }}'s logo" />
                </li>
            </ol>

            <button
                class="close-entry group -m-2 flex items-center rounded-full border border-transparent p-2 transition duration-300 hover:border-gray-200 hover:bg-white"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    class="size-5 text-gray-700 group-hover:text-primary-600"
                >
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                    />
                </svg>
            </button>
        </div>

        @if ($entry->focusesOnGCBRs)
            <div class="bg-primary-50 px-4 py-1.5">
                <p class="flex items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        class="size-4 text-primary-600"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M8 1.75a.75.75 0 0 1 .692.462l1.41 3.393 3.664.293a.75.75 0 0 1 .428 1.317l-2.791 2.39.853 3.575a.75.75 0 0 1-1.12.814L7.998 12.08l-3.135 1.915a.75.75 0 0 1-1.12-.814l.852-3.574-2.79-2.39a.75.75 0 0 1 .427-1.318l3.663-.293 1.41-3.393A.75.75 0 0 1 8 1.75Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="ml-1 text-xs text-primary-700">
                        This {{ $entry->nounForOrganizationType() }} focuses on
                        <abbr title="Global Catastrophic Biological Risks">GCBRs</abbr>
                        .
                    </span>
                </p>
            </div>
        @endif

        <div class="mt-6 px-4 pb-2">
            <h2 class="font-display text-xl font-bold">
                <a class="text-primary-600 underline" target="_blank" href="{{ $entry->link }}">
                    {{ $entry->label }}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        class="mb-3 inline size-4 text-gray-400 group-hover:text-primary-700"
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
            </h2>

            <ul class="mt-1.5 flex space-x-4 overflow-x-scroll">
                <li class="flex justify-center space-x-1.5">
                    @foreach ($entry->activities as $activity)
                        <span
                            style="background-color: {{ $activity->color->foreground() }}"
                            class="inline-block rounded-full px-2.5 py-1.5"
                        >
                            <x-activity-icon :activity="$activity" class="size-4 text-white" />
                        </span>
                    @endforeach
                </li>
                {{-- <li class="border rounded-full px-2 py-0.5 flex items-center justify-center"> --}}
                {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-gray-400"> --}}
                {{-- <path fill-rule="evenodd" d="m7.539 14.841.003.003.002.002a.755.755 0 0 0 .912 0l.002-.002.003-.003.012-.009a5.57 5.57 0 0 0 .19-.153 15.588 15.588 0 0 0 2.046-2.082c1.101-1.362 2.291-3.342 2.291-5.597A5 5 0 0 0 3 7c0 2.255 1.19 4.235 2.292 5.597a15.591 15.591 0 0 0 2.046 2.082 8.916 8.916 0 0 0 .189.153l.012.01ZM8 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" clip-rule="evenodd" /> --}}
                {{-- </svg> --}}

                {{-- <span class="text-sm text-gray-700 ml-1 truncate">{{ $entry->location }}</span> --}}
                {{-- </li> --}}
            </ul>

            <div class="mt-4 text-justify">
                <x-notion-rich-text :text="$entry->description" />
            </div>

            {{-- @if ($focus->isMetaTechnicalFocus()) --}}
            {{-- <x-at-technical class="underline" /> --}}
            {{-- @elseif($focus->isMetaGovernanceFocus()) --}}
            {{-- <x-at-governance class="underline" /> --}}
            {{-- @endif --}}

            @if ($entry->interventionFocuses->isNotEmpty())
                <p class="mt-4">This {{ $entry->nounForOrganizationType() }} works on</p>

                <ul class="list-inside list-disc">
                    @foreach ($entry->interventionFocuses as $focus)
                        <li>
                            <a href="" class="underline">
                                <span>{{ $focus->label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <div class="space-x-2 rounded-br-3xl border-t bg-gray-50 px-6 py-2">
        <a class="inline-flex text-sm text-gray-700 underline" href="{{ $entry->notionUrl() }}">
            <span class="mr-0.5">Open in Notion</span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="mt-px size-4 text-gray-400 group-hover:text-primary-700"
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
        <a class="inline-flex text-sm text-gray-700 underline">
            <span class="mr-0.5">Report a problem</span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="mt-px size-4 text-gray-400 group-hover:text-primary-700"
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
    </div>
</div>
@if (! $isXHR && ! app()->isProduction())
    {{-- format-ignore-start --}}
    </x-layouts.default>
    {{-- format-ignore-end --}}
@endif
