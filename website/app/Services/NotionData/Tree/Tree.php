<?php

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\DataObjects\Activity;
use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\Entrygroup;
use App\Services\NotionData\DataObjects\InterventionFocus;
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

    /** @return Collection<int, Category> */
    public function categories(): Collection
    {
        return collect($this->lookup)->filter(fn ($el) => $el instanceof Category);
    }

    /** @return Collection<int, Entry> */
    public function entries(): Collection
    {
        return collect($this->lookup)->filter(fn ($el) => $el instanceof Entry);
    }

    /** @return Collection<int, Entrygroup> */
    public function entrygroups(): Collection
    {
        return collect($this->lookup)->filter(fn ($el) => $el instanceof Entrygroup);
    }

    /** @return Collection<int, Activity> */
    public function activities(): Collection
    {
        return $this->entries()->flatMap(fn (Entry $e) => $e->activities)->unique('id')->values();
    }

    /** @return Collection<int, InterventionFocus> */
    public function interventionFocuses(): Collection
    {
        return $this->entries()->flatMap(fn (Entry $e) => $e->interventionFocuses)->unique('id')->values();
    }

    public function getNodeById(int $id): ?Node
    {
        return Arr::first($this->nodes, fn (Node $node) => $node->id === $id);
    }

    public static function buildFromPages(HydratedPages $pages): Tree
    {
        $tree = new Tree([], [], $pages->errors, IdHash::hash('root'));

        foreach ($pages->data as $page) {
            $tree->lookup[$page->id] = $page;

            $node = new Node($page->id, $page->parentId !== null ? $page->parentId : $tree->rootNodeId);

            $tree->nodes[] = $node;
        }

        $rootNodes = collect($tree->nodes)->filter(fn (Node $node) => $node->parentId === $tree->rootNodeId);
        if (count($rootNodes) >= 1) {
            foreach ($rootNodes as $root) {
                $root->parentId = $tree->rootNodeId;
            }

            $tree->lookup[$tree->rootNodeId] = new Root($tree->rootNodeId);
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

                    /** @var array<int> $entryIds */
                    $entryIds = Arr::pluck($entries->toArray(), 'id');

                    // fixme: see below
                    /** @phpstan-ignore-next-line */
                    usort($entryIds, fn (int $a, int $b) => $tree->lookup[$a]->createdAt <=> $tree->lookup[$b]->createdAt);

                    $id = sha1($parentId.'-'.implode('-', $entryIds));
                    $reducedId = IdHash::hash($id);

                    $rest->push(new Node($reducedId, $parentId));
                    $tree->lookup[$reducedId] = new Entrygroup(IdHash::hash($id), $entryIds);

                    return $rest;
                })
            ->toArray();

        /** @var Collection<int, Collection<int, Node>> $parentToChildrenMap */
        $parentToChildrenMap = collect($tree->nodes)->groupBy('parentId');

        $buildTree = function (int $id, int $parentId, array $trail = [], int $depth = 0) use (&$buildTree, $tree, $parentToChildrenMap): \Generator {
            $node = self::getNode($tree, $id, $parentId);

            if (! $parentToChildrenMap->has($id) && $tree->lookup[$id] instanceof Category) {
                $tree->errors[] = HydrationError::fromString('Category contains 0 entries.', $tree->lookup[$id]);

                return [];
            }

            $node->trail = $trail;
            $node->depth = $depth;

            if ($node->id !== $tree->rootNodeId) {
                $trail[] = $node->id;
            }

            $od = 0;
            foreach ($parentToChildrenMap->get($id, []) as $child) {
                foreach ($buildTree($child->id, $node->id, $trail, $node->depth + 1) as $childNode) {
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

        $matches = array_filter($tree->nodes, fn (Node $node) => $node->parentId === $parentId && $node->id === $id);
        if (count($matches) !== 1) {
            throw new Exception(sprintf('Should not happen: The node represented by the couple (id: `%s`, parentId: `%s`) has %s matches while it should have exactly one.', $id, $parentId, count($matches)));
        }

        return $matches[array_key_first($matches)];
    }
}
