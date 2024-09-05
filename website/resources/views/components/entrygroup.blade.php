<div class="relative w-fit cursor-pointer rounded-md border bg-white p-2 pointer-events-auto entrygroup">
    @foreach($entries as $name => $collection)
        <div class="">
            <h4 class="intervention-type mt-2 text-xs uppercase tracking-tight text-gray-500  font-display">{{ $name }}</h4>
            <div class="grid grid-cols-6 gap-2">
                @foreach($collection as $k => $entry)
                    <button
                        data-sum="{{ crc32($entry->id) }}"
                        data-entry="{{ $entry->id }}"
                        data-entrygroup="{{ $entrygroup->id }}"
                        data-entry-url="{{ route('entries.show', ['id' => $entrygroup->id, 'entryId' => $entry->id], absolute: false) }}/"
                    >
                        <x-entry-logo :logo="$entry->logo" class="hover:border-emerald-600" />
                    </button>
                @endforeach
            </div>

        </div>
    @endforeach
</div>
