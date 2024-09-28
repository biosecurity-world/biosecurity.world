<div
    class="entrygroup pointer-events-auto relative w-fit cursor-pointer rounded-xl border bg-white px-3 pb-2 shadow-sm"
>
    @foreach ($entries as $name => $collection)
        <div>
            <h4 class="org-type -mb-1.5 text-xs font-semibold tracking-tight text-gray-500">{{ $name }}</h4>
            <div class="grid grid-cols-6 gap-x-2">
                @foreach ($collection as $k => $entry)
                    <button
                        title="{{ $entry->label }}"
                        data-entry="{{ $entry->id }}"
                        data-entrygroup="{{ $entrygroup->id }}"
                        class="mt-1.5"
                    >
                        <x-entry-logo
                            :logo="$entry->logo"
                            class="hover:border-primary-600"
                            alt="Open {{ $entry->label }}'s entry"
                        />
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
