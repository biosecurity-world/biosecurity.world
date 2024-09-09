<div class="relative w-fit cursor-pointer rounded-xl border shadow-sm bg-white pointer-events-auto entrygroup">
    @foreach($entries as $name => $collection)
        <div>
            <h4 class="intervention-type mb-0.5 mt-1">{{ $name }}</h4>
            <div class="grid grid-cols-6 gap-x-2 gap-y-1 px-3">
                @foreach($collection as $k => $entry)
                    <button data-entry="{{ $entry->id }}" data-entrygroup="{{ $entrygroup->id }}">
                        <x-entry-logo :logo="$entry->logo" class="hover:border-primary-600" alt="Open {{ $entry->label }}'s entry" />
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
