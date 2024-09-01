@php use App\Support\IdHash; @endphp
<div class="relative w-fit cursor-pointer rounded-md border bg-white p-2 space-y-2 pointer-events-auto">
    @foreach($entries as $name => $collection)
        <div>
            <h4 class="intervention-type text-xs uppercase tracking-tight text-gray-500  font-display">{{ $name }}</h4>
            <div class="grid grid-cols-6 gap-2">
                @foreach($collection as $k => $entry)
                    <button
                        data-sum="{{ crc32($entry->id) }}"
                        data-entry="{{ IdHash::hash($entry->id) }}"
                        data-entrygroup="{{ IdHash::hash($entrygroup->id) }}"
                        data-entry-url="{{ route('entries.show', ['id' => IdHash::hash($entrygroup->id), 'entryId' => IdHash::hash($entry->id)], absolute: false) }}/"
                    >
                        <x-entry-logo :logo="$entry->logo" class="hover:border-emerald-600" />
                    </button>
                @endforeach
            </div>

        </div>
    @endforeach
</div>
