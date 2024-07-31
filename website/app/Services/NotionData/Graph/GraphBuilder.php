<?php

namespace App\Services\NotionData\Graph;

use App\Services\NotionData\Page;
use App\Services\NotionData\Enums\PageType;
use Illuminate\Support\Collection;

class GraphBuilder
{
    protected array $lookup = [];
    public function __construct(
        /** @var iterable<Page> */
        protected iterable $elements
    )
    {

    }

    public function build(): array
    {
        /** @var Collection<Vertex> $elements */
        $elements = collect();
        foreach ($this->elements as $element) {
            if ($element->parents === []) {
                $element->parents = ['root'];
                $elements[] = $element;
                continue;
            }

            foreach ($element->parents as $parentId) {
                $element->parents = [$parentId];

                $elements[] = $element;
            }
        }

        $elements = $elements
            ->groupBy(function (Page $element) {
                // We've 'expanded' all vertices so they each have one parent.
                return $element->parents[0];
            })
            ->map(function (/** @var Collection<Page[]> $pages */ $pages, string $parentId) {
                $groupedPages = $pages->groupBy('type');
                /** @var Collection<Page> $categories */
                $categories = $groupedPages['category'] ?? collect();
                /** @var Collection<Page> $entries */
                $entries = $groupedPages["entry"] ?? collect();

                foreach ($categories as $category) {
                    $this->lookup[$category->id] = $category->data;
                }

                if ($entries->isNotEmpty()) {
                    $entryIds = $entries->pluck('id');

                    foreach ($entries as $entry) {
                        $this->lookup[$entry->id] = $entry->data;
                    }

                    $categories[] = new Page(
                        PageType::EntryGroup,
                        sha1($entryIds->join('')),
                        [$parentId],
                        ['entries' => $entryIds, 'label' => 'Entry group']
                    );
                }

                return $categories;
            });


        $elements = $elements
            ->flatten(1)
            ->map(function (Page $page) use ($elements) {
                return new Vertex(
                    $page->id,
                    $page->data['label'],
                    $page->parents[0],
                    $elements->has($page->id) ? $elements[$page->id]->pluck('id')->toArray() : []
                );
            })
            ->groupBy('parentId')
            ->map(fn(Collection $vertices) => $vertices
                ->map(function (Vertex $vertex, int $index) use ($vertices) {
                    $vertex->siblingsCount  = count($vertices);
                    $vertex->index = $index;

                    return $vertex;
                })
            );


        $sorted = $this->dfsSort(
            'root',
            $elements->map->pluck('id')->map->toArray()->toArray()
        );

        // remove the fake root
        array_shift($sorted);

        $lookup = $elements->flatten(1)->groupBy('id')->map->first();

        return [
            'vertices' => array_map(fn(string $id) => $lookup[$id], $sorted),
            'lookup' => $this->lookup,
        ];
    }


    /*
     * We want to sort specifically with depth-first-search, not any topological sort.
     * All DFS sorts are valid topological sorts but not all topological sorts are valid DFS sorts.
     */
    public function dfsSort(string $startVertexRef, array $childrenLookup): array
    {
        $stack = new \SplStack();
        $stack->push($startVertexRef);
        $visited = [];
        $sorted = [];

        while (count($stack) > 0) {
            $vertex = $stack->pop();
            if (!array_key_exists($vertex, $visited)) {
                $visited[$vertex] = true;
                $sorted[] = $vertex;

                if (array_key_exists($vertex, $childrenLookup)) {
                    foreach (array_reverse($childrenLookup[$vertex]) as $nextVertex) {
                        $stack->push($nextVertex);
                    }
                }
            }
        }

        return $sorted;
    }
}
