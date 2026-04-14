<div
    class="entrygroup pointer-events-auto relative w-fit cursor-pointer divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white px-3"
>
    @foreach ($entries as $orgType => $collection)
        <div class="org-type py-2">
            <p class="mb-1 text-xs font-medium text-gray-500">{{ $orgType }}</p>
            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                @foreach ($collection as $k => $entry)
                    <a
                        href="{{ route("entries.show", ["id" => $entry->id, "slug" => $entry->slug()]) }}"
                        title="{{ $entry->label }}"
                        data-entry="{{ $entry->id }}"
                        data-entrygroup="{{ $entrygroup->id }}"
                        class="flex items-center gap-2"
                    >
                        <x-entry-logo
                            :logo="$entry->logo"
                            :organizationType="$orgType"
                            alt="Open {{ $entry->label }}'s entry"
                        />
                        @php
                            $displayLabel = $entry->label;
                            // Remove everything after @ (e.g., "Lab @ MIT" → "Lab")
                            $displayLabel = preg_replace('/@.*$/', "", $displayLabel);
                            $displayLabel = trim($displayLabel);
                            // Extract short name from parenthesis anywhere in the name
                            // Matches acronyms and short names: (PRIF), (NTI.bio), (ARPA-H), (Esvelt Lab), etc.
                            // Limited to 20 chars to avoid matching long descriptions
                            if (preg_match("/\(([A-Za-z][A-Za-z0-9.\- ]{1,18})\)/", $displayLabel, $matches)) {
                                $displayLabel = trim($matches[1]);
                            }
                        @endphp

                        <span class="max-w-40 truncate text-sm text-gray-700">{{ $displayLabel }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
