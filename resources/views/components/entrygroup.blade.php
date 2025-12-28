<div
    class="entrygroup pointer-events-auto relative w-fit cursor-pointer divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white px-3"
>
    @foreach ($entries as $orgType => $collection)
        <div class="org-type py-2">
            <div class="grid grid-cols-6 gap-2">
                @foreach ($collection as $k => $entry)
                    <a
                        href="{{ route("entries.show", ["id" => $entry->id, "slug" => $entry->slug()]) }}"
                        title="{{ $entry->label }}"
                        data-entry="{{ $entry->id }}"
                        data-entrygroup="{{ $entrygroup->id }}"
                    >
                        <x-entry-logo
                            :logo="$entry->logo"
                            :organizationType="$orgType"
                            alt="Open {{ $entry->label }}'s entry"
                        />
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
