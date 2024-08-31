<?php

declare(strict_types=1);

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\Category;
use App\Services\NotionData\Entry;
use App\Services\NotionData\Entrygroup;
use App\Services\NotionData\Root;
use App\Support\IdHash;
use Exception;
use Illuminate\Support\Collection;

class TreeBuilder
{
    /** @var array<int, Category|Entrygroup|Entry|Root> */
    protected array $nodeToPage = [];

    /** @var array<int, Node[]> A map from a parent ID to its children's IDs */
    protected array $parentToChildrenMap = [];

    /** @var Node[] */
    protected array $nodes = [];

    protected int $rootNodeId;

    /**
     * @param  array<Category|Entry>  $pages
     * @return array{nodes: Node[], lookup: array<int, Category|Entrygroup|Root|Entry>}
     */
    public function build(array $pages): array
    {
        $this->rootNodeId = IdHash::hash('root');
        $this->nodes = [];

        foreach ($pages as $page) {
            $reducedId = IdHash::hash($page->id);

            $this->nodeToPage[$reducedId] = $page;
            $this->nodes[] = new Node($reducedId, $page->parentId !== null ? IdHash::hash($page->parentId) : $this->rootNodeId);
        }

        $rootNodes = collect($this->nodes)->filter(fn (Node $node) => $node->parentId === $this->rootNodeId);
        if (count($rootNodes) >= 1) {
            foreach ($rootNodes as $root) {
                $root->parentId = $this->rootNodeId;
            }

            $this->nodeToPage[$this->rootNodeId] = new Root('root');
        } else {
            return ['nodes' => [], 'lookup' => []];
        }

        /** @phpstan-ignore-next-line  */
        $this->nodes = collect($this->nodes)
            ->groupBy('parentId')
            ->flatMap(
                /** @return Collection<Node> */
                function (Collection $children, int $parentId): Collection {
                    [$entries, $rest] = $children->partition(fn (Node $node) => $this->nodeToPage[$node->id] instanceof Entry);

                    if ($entries->isEmpty()) {
                        return $rest;
                    }

                    /** @var Collection<int,string> $entryIds */
                    $entryIds = $entries->pluck('id');

                    $id = sha1($parentId.'-'.$entryIds->sort()->join('-'));
                    $reducedId = IdHash::hash($id);

                    $rest->push(new Node($reducedId, $parentId));
                    $this->nodeToPage[$reducedId] = new Entrygroup(
                        id: $id,
                        entries: $entryIds->toArray(),
                    );

                    return $rest;
                })->toArray();

        $this->parentToChildrenMap = collect($this->nodes)->groupBy('parentId')->toArray();

        $nodes = [];

        foreach (
            $this->buildTree(
                id: $this->rootNodeId,
                parentId: $this->rootNodeId
            ) as $node
        ) {
            $nodes[] = $node;
        }

        return ['nodes' => $nodes, 'lookup' => $this->nodeToPage];
    }

    /**
     * @param  array<int>  $trail
     * @return \Generator<Node>
     */
    public function buildTree(int $id, int $parentId, array $trail = [], int $depth = 0): \Generator
    {
        if ($id === $this->rootNodeId) {
            $node = new Node($id, $parentId);
        } else {
            $matches = collect($this->nodes)->where('id', $id)->where(fn (Node $node) => $node->parentId === $parentId);
            if (count($matches) !== 1) {
                throw new Exception("Should not happen: The node represented by the couple (id: `$id`, parentId: `$parentId`) has {$matches->count()} matches while it should have exactly one.");
            }

            /** @var Node $node */
            $node = $matches->first();
        }

        if (! array_key_exists($id, $this->parentToChildrenMap) && $this->nodeToPage[$id] instanceof Category) {
            return;
        }

        $node->trail = $trail;
        $node->depth = $depth;

        if ($id !== $this->rootNodeId) {
            $trail[] = $id;
        }

        $od = 0;
        foreach (($this->parentToChildrenMap[$id] ?? []) as $child) {
            foreach (
                $this->buildTree($child->id, $id, $trail, $depth + 1) as $c
            ) {
                if ($c->depth === $depth + 1) {
                    $od++;
                }

                yield $c;
            }

        }

        $node->od = $od;

        yield $node;
    }
}
