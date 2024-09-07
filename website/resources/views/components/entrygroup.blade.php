<div class="relative w-fit cursor-pointer rounded-xl border shadow-sm bg-white pointer-events-auto entrygroup">
    @foreach($entries as $name => $collection)
        <div>
            <h4 class="intervention-type">{{ $name }}</h4>
            <div class="grid grid-cols-6 gap-x-2 px-3 mt-0.5">
                @foreach($collection as $k => $entry)
                    <button data-entry="{{ $entry->id }}" data-entrygroup="{{ $entrygroup->id }}">
                        <x-entry-logo :logo="$entry->logo" class="hover:border-primary-600" />
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
