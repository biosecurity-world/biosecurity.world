<g id="zoom-wrapper">
    <g id="center-wrapper">
        <g id="background"></g>
        <foreignObject
            width="100%"
            height="100%"
            class="off-screen pointer-events-none"
            aria-hidden="true"
            data-node="{{ $tree->rootNodeId }}"
        >
            <div class="inline-block px-2 font-display text-2xl text-primary-900">Biosecurity</div>
        </foreignObject>

        <g>
            @foreach ($tree->categories() as $category)
                <foreignObject
                    width="100%"
                    height="100%"
                    class="off-screen pointer-events-none"
                    aria-hidden="true"
                    data-node="{{ $category->id }}"
                >
                    <x-category :category="$category" />
                </foreignObject>
            @endforeach
        </g>

        <g id="entrygroups">
            @foreach ($tree->entrygroups() as $entrygroup)
                <foreignObject
                    width="100%"
                    height="100%"
                    class="off-screen pointer-events-none"
                    aria-hidden="true"
                    data-node="{{ $entrygroup->id }}"
                >
                    <x-entrygroup
                        :entries="array_map(fn (string $id) => $tree->lookup[$id], $entrygroup->entries)"
                        :entrygroup="$entrygroup"
                    />
                </foreignObject>
            @endforeach
        </g>
    </g>
</g>
