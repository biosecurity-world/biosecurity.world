<x-layouts.default
    :title="$entry->label"
    :description="$entry->description->toString()"
    :canonical="route('entries.show', ['id' => $entry->id, 'slug' => $entry->slug()], absolute: false)"
    :image="$entry->logo->url"
    :json-ld="$jsonLd"
    class="bg-paper text-ink"
>
    <header class="bg-paper/80 sticky top-0 z-40 backdrop-blur">
        <x-navbar class="mt-4" />
    </header>

    <main class="mx-auto mt-8 w-full max-w-5xl px-6 xl:px-0">
        <section class="mx-auto mt-8 max-w-5xl">
            <div class="border-sand-300 rounded-2xl border bg-white px-6 py-5">
                <div class="items-center justify-between gap-6 md:flex">
                    <div>
                        <h2 class="font-display text-ink text-lg font-bold">Explore the biosecurity landscape</h2>
                        <p class="text-ink-muted mt-1 text-sm">See where this organization fits within the broader map of biosecurity work around the world.</p>
                    </div>
                    <a
                        href="{{ route("welcome", absolute: false) }}"
                        class="bg-primary-600 hover:bg-primary-700 focus:ring-primary-600 mt-3 inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold text-white shadow-sm transition focus:ring-2 focus:ring-offset-2 focus:outline-hidden md:mt-0"
                    >
                        <span>View on the map</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="ml-2 size-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3.5 10a.75.75 0 0 1 .75-.75h9.638L10.22 5.78a.75.75 0 0 1 1.06-1.06l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 0 1-1.06-1.06l3.668-3.47H4.25A.75.75 0 0 1 3.5 10Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
        <article class="border-sand-300 mt-8 rounded-3xl border bg-white shadow-xs">
            <header
                class="border-sand-300 bg-card flex flex-col items-start justify-between gap-4 rounded-t-3xl border-b px-6 py-5 md:flex-row md:items-center"
            >
                <div class="flex items-center gap-3">
                    <x-entry-logo
                        :logo="$entry->logo"
                        :organizationType="$entry->organizationType"
                        class="border-sand-300 size-12 rounded-lg border bg-white"
                        alt="{{ $entry->label }}'s logo"
                    />
                    <div>
                        <h1 class="font-display text-ink text-2xl leading-tight font-bold">
                            <a
                                class="text-primary-700 hover:text-primary-800 inline-flex items-center underline"
                                target="_blank"
                                rel="noopener"
                                href="{{ $entry->link }}"
                            >
                                <span>{{ $entry->label }}</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 16 16"
                                    fill="currentColor"
                                    class="ml-1 size-4 text-gray-400"
                                    aria-label="External link"
                                >
                                    <path
                                        d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                                    />
                                    <path
                                        d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                                    />
                                </svg>
                            </a>
                        </h1>
                        <p class="text-ink-muted mt-0.5 text-sm">
                            {{ ucfirst($entry->nounForOrganizationType()) }} • {{ $entry->host() }}
                        </p>
                    </div>
                </div>
            </header>

            @if ($entry->focusesOnGCBRs)
                <div class="bg-primary-50 px-6 py-2">
                    <p class="text-primary-700 flex items-center text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                            <path
                                fill-rule="evenodd"
                                d="M8 1.75a.75.75 0 0 1 .692.462l1.41 3.393 3.664.293a.75.75 0 0 1 .428 1.317l-2.791 2.39.853 3.575a.75.75 0 0 1-1.12.814L7.998 12.08l-3.135 1.915a.75.75 0 0 1-1.12-.814l.852-3.574-2.79-2.39a.75.75 0 0 1 .427-1.318l3.663-.293 1.41-3.393A.75.75 0 0 1 8 1.75Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="ml-2">
                            This {{ $entry->nounForOrganizationType() }} focuses on
                            <abbr title="Global Catastrophic Biological Risks">GCBRs</abbr>
                            .
                        </span>
                    </p>
                </div>
            @endif

            <section class="px-6 py-6">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($entry->activities as $activity)
                        <span
                            style="background-color: {{ $activity->color->foreground() }}"
                            class="inline-flex items-center rounded-full px-2.5 py-1.5"
                        >
                            <x-activity-icon :activity="$activity" class="size-4 text-white" />
                        </span>
                    @endforeach
                </div>

                <div class="text-ink-muted mt-5 text-justify">
                    <x-notion-rich-text :text="$entry->description" />
                </div>

                @if ($entry->interventionFocuses->isNotEmpty())
                    <div class="mt-6">
                        <p class="text-ink">This {{ $entry->nounForOrganizationType() }} works on</p>
                        <ul class="text-ink-muted mt-2 list-inside list-disc">
                            @foreach ($entry->interventionFocuses as $focus)
                                <li>
                                    <span>{{ $focus->label }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>

            <footer class="border-sand-300 bg-card flex flex-wrap items-center gap-4 border-t px-6 py-3">
                <a
                    class="text-ink-muted inline-flex items-center text-sm underline"
                    href="{{ $entry->notionUrl() }}"
                    target="_blank"
                    rel="noopener"
                >
                    <span class="mr-0.5">Open in Notion</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        class="mt-px size-4 text-gray-400"
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

                <span aria-hidden="true" class="bg-sand-400 hidden h-4 w-px md:inline-block"></span>

                <a
                    class="text-ink-muted inline-flex items-center text-sm underline"
                    href="https://docs.google.com/forms/d/e/1FAIpQLSfJrpJ9o3xpIXOHgdOdkj_yrUt5LadIVbnzwKQk6tKWMuU5xw/viewform?usp=send_form"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <span class="mr-0.5">Report a problem</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        class="mt-px size-4 text-gray-400"
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

                <div class="ml-auto">
                    <a
                        href="{{ route("welcome", absolute: false) }}"
                        class="bg-primary-600 hover:bg-primary-700 focus:ring-primary-600 inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold text-white shadow-sm transition focus:ring-2 focus:ring-offset-2 focus:outline-hidden"
                    >
                        <span>Back to the map</span>
                    </a>
                </div>
            </footer>
        </article>
    </main>

    <div class="mt-12">
        <x-footer />
    </div>
</x-layouts.default>
