<?php

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\Entrygroup;
use App\Services\NotionData\DataObjects\Root;
use App\Services\NotionData\HydratedPages;
use App\Services\NotionData\HydrationError;
use App\Support\IdHash;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Tree
{
    public function __construct(
        /** @var Node[] */
        public array $nodes,
        /** @var array<int, Category|Entrygroup|Root|Entry> */
        public array $lookup,
        /** @var HydrationError[] */
        public array $errors,
        public int $rootNodeId = 0,
    ) {}

    public function root(): Root
    {
        return $this->lookup[$this->rootNodeId];
    }

    public function categories(): Collection
    {
        return collect($this->lookup)->filter(fn ($el) => $el instanceof Category);
    }

    public function entries(): Collection
    {
        return collect($this->lookup)->filter(fn ($el) => $el instanceof Entry);
    }

    public function entrygroups(): Collection
    {
        return collect($this->lookup)->filter(fn ($el) => $el instanceof Entrygroup);
    }

    public function activities(): array
    {
        return $this->entries()
            ->flatMap(fn (Entry $e) => $e->activities)
            ->groupBy('id')
            ->map(function ($group) {
                $activity = $group->first();
                $activity->total = $group->count();

                return $activity;
            })
            ->values();
    }

    public function interventionFocuses(): array
    {
        return $this->entries()
            ->flatMap(fn (Entry $e) => $e->interventionFocuses)
            ->groupBy('id')
            ->map(function ($group) {
                $focus = $group->first();
                $focus->total = $group->count();

                return $focus;
            })
            ->values();
    }

    public function getNodeById(int $id): ?Node
    {
        return Arr::first($this->nodes, fn (Node $node) => $node->id === $id);
    }

    public static function buildFromPages(HydratedPages $pages): Tree
    {
        $tree = new Tree([], [], $pages->errors, IdHash::hash('root'));

        foreach ($pages->data as $page) {
            $reducedId = IdHash::hash($page->id);

            $tree->lookup[$reducedId] = $page;
            $tree->nodes[] = new Node($reducedId, $page->parentId !== null ? IdHash::hash($page->parentId) : $tree->rootNodeId);
        }

        $rootNodes = collect($tree->nodes)->filter(fn (Node $node) => $node->parentId === $tree->rootNodeId);
        if (count($rootNodes) >= 1) {
            foreach ($rootNodes as $root) {
                $root->parentId = $tree->rootNodeId;
            }

            $tree->lookup[$tree->rootNodeId] = new Root('root');
        } else {
            return $tree;
        }

        /** @phpstan-ignore-next-line  */
        $tree->nodes = collect($tree->nodes)
            ->groupBy('parentId')
            ->flatMap(
                /** @return Collection<Node> */
                function (Collection $children, int $parentId) use ($tree): Collection {
                    [$entries, $rest] = $children->partition(fn (Node $node) => $tree->lookup[$node->id] instanceof Entry);

                    if ($entries->isEmpty()) {
                        return $rest;
                    }

                    /** @var Collection<int,string> $entryIds */
                    $entryIds = $entries->pluck('id');

                    $id = sha1($parentId.'-'.$entryIds->sort()->join('-'));
                    $reducedId = IdHash::hash($id);

                    $rest->push(new Node($reducedId, $parentId));
                    $tree->lookup[$reducedId] = new Entrygroup($id, $entryIds->toArray());

                    return $rest;
                })
            ->toArray();

        $parentToChildrenMap = collect($tree->nodes)->groupBy('parentId');
        $buildTree = function (int $id, int $parentId, array $trail = [], int $depth = 0) use (&$buildTree, $tree, $parentToChildrenMap): \Generator {
            $node = self::getNode($tree, $id, $parentId);

            if (! $parentToChildrenMap->has($id) && $tree->lookup[$id] instanceof Category) {
                $tree->errors[] = HydrationError::fromString('Category contains 0 entries.', $tree->lookup[$id]);

                return [];
            }

            $node->trail = $trail;
            $node->depth = $depth;

            if ($id !== $tree->rootNodeId) {
                $trail[] = $id;
            }

            $od = 0;
            foreach ($parentToChildrenMap->get($id, []) as $child) {
                foreach ($buildTree($child->id, $node->id, $node->trail, $node->depth + 1) as $childNode) {
                    if ($childNode->depth === $node->depth + 1) {
                        $od++;
                    }

                    yield $childNode;
                }

            }

            $node->od = $od;
            yield $node;
        };

        $nodes = [];
        foreach (
            $buildTree(id: $tree->rootNodeId, parentId: $tree->rootNodeId) as $node
        ) {
            $nodes[] = $node;
        }

        $tree->nodes = $nodes;

        return $tree;
    }

    protected static function getNode(Tree $tree, int $id, int $parentId): Node
    {
        if ($id === $tree->rootNodeId) {
            return new Node($id, $parentId);
        }

        $matches = collect($tree->nodes)
            ->where('id', $id)
            ->where(fn (Node $node) => $node->parentId === $parentId);
        if (count($matches) !== 1) {
            throw new Exception("Should not happen: The node represented by the couple (id: `$id`, parentId: `$parentId`) has {$matches->count()} matches while it should have exactly one.");
        }

        /** @var Node $node */
        return $matches->first();
    }
}
