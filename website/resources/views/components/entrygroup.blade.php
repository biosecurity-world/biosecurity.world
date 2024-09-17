<div class="relative w-fit cursor-pointer rounded-xl border shadow-sm bg-white pointer-events-auto entrygroup pb-2 px-3">
    @foreach($entries as $name => $collection)
        <div>
            <h4 class="org-type text-xs tracking-tight text-gray-500 font-semibold -mb-1.5">{{ $name }}</h4>
            <div class="grid grid-cols-6 gap-x-2">
                @foreach($collection as $k => $entry)
                    <button title="{{ $entry->label }}" data-entry="{{ $entry->id }}" data-entrygroup="{{ $entrygroup->id }}" class="mt-1.5">
                        <x-entry-logo :logo="$entry->logo" class="hover:border-primary-600" alt="Open {{ $entry->label }}'s entry" />
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
