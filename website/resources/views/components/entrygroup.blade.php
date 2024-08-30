<div class="relative w-fit cursor-pointer rounded-md border bg-white p-2 space-y-2 pointer-events-auto">
    @foreach($groups as $name => $entries)
        <div>
            <h4 class="text-xs uppercase tracking-tight text-gray-500  font-display">{{ match ($name) {
    "Research institute / lab / network" => "Research institute",
    "International non-profit organization" => "International NGO",
    "National non-profit organization" => "National NGO",
    default => $name,
} }}</h4>
            <div class="grid grid-cols-6 gap-2 mt-0.5">
                @foreach($entries as $k => $entry)
                    <button hx-get="{{ route('entries.show', ['id' => $id, 'entryId' => $entry['id']], absolute: false) }}"
                            hx-trigger="click"
                            hx-target="#entry-aside"
                    >
                        <x-entry-logo :logo="$entry['logo']" :size="24" />
                    </button>
                @endforeach
            </div>

        </div>
    @endforeach

</div>
