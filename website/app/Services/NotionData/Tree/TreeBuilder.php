<?php

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\Category;
use App\Services\NotionData\Entry;
use App\Services\NotionData\Enums\NodeType;
use Exception;
use Illuminate\Support\Collection;

class TreeBuilder
{
    /** @var array<string, array<string, mixed>>  */
    protected array $lookup = [];

    /** @var array<string, Node[]> A map from a parent ID to its children's IDs */
    protected array $parentToChildrenMap = [];

    /** @var Node[] */
    protected array $nodes = [];
    protected string $rootNodeId;


    /**
     * @param array<Category|Entry> $pages
     * @return array{tree: Node|null, lookup: array<string, array<string, mixed>>}
     */
    public function build(array $pages): array
    {
        // Normalize the map, so it has a single root node.
        $this->rootNodeId = 'root';
        $rootNodes = collect($pages)->filter(fn(Category|Entry $page) => $page->parentId === null);
        if (count($rootNodes) === 1) {
            $rootNodes->first()->parent = $this->rootNodeId;
        } else if (count($rootNodes) > 1) {
            // We want to connect the forest to a single root node.
            foreach ($rootNodes as $root) {
                $root->parent = $this->rootNodeId;
            }

            $this->lookup[$this->rootNodeId] = [
                '@type' => NodeType::Root,
            ];
        } else {
            return ['tree' => null, 'lookup' => []];
        }

        $this->nodes = [];
        foreach ($pages as $page) {
            $data = $page->toArray();
            $data['@type'] = $page instanceof Category ? NodeType::Category : NodeType::Entry;

            // Pages have a lot of data associated with them (title, link...) and we are possibly
            // duplicating them 10 or 15 times. We don't want to send this data more than once,
            // so we separate the relational data (id, parent) from the page data (title...)
            $this->lookup[$page->id] = $data;

            // This is true only if the raw pages already had a single root.
            if ($page->id === $this->rootNodeId) {
                $this->nodes[] = new Node($page->id, $page->id);
                continue;
            }
            $this->nodes[] = new Node($page->id, $page->parentId ?? $this->rootNodeId);
        }

        // Squash all entries into entrygroups
        $this->nodes = collect($this->nodes)->groupBy('parentId')
            ->map(function (Collection $children, string $parentId) {
                [$entries, $rest] = $children->partition(function (Node $vertex) {
                    return $this->lookup[$vertex->id]['@type'] === NodeType::Entry;
                });

                if ($entries->isNotEmpty()) {
                    /** @var Collection<string> $entryIds */
                    $entryIds = $entries->pluck('id');

                    $entryGroupId = sha1($parentId . '-' . $entryIds->sort()->join(''));
                    $rest->push(new Node($entryGroupId, $parentId));
                    $this->lookup[$entryGroupId] = [
                        '@type' => NodeType::EntryGroup,
                        'id' => $entryGroupId,
                        'entries' => $entryIds->toArray()
                    ];
                }

                return $rest;
            })
            ->flatten(1)
            ->toArray();


        $this->parentToChildrenMap = collect($this->nodes)->groupBy('parentId')->toArray();

        return [
            'tree' => $this->buildTree(id: $this->rootNodeId, parentId: $this->rootNodeId),
            'lookup' => $this->lookup
        ];
    }

    /**
     * @param array<string> $trail
     */
    public function buildTree(string $id, string $parentId, array $trail = []): ?Node
    {
        if ($id === $this->rootNodeId) {
            $node = new Node($id, $parentId, []);
        } else {
            $matches = collect($this->nodes)->where('id', $id)->where(fn(Node $node) => $node->parentId === $parentId);
            if (count($matches) !== 1) {
                throw new Exception("Should not happen: The vertex represented by the couple (id: `$id`, parentId: `$parentId`) has {$matches->count()} matches while it should have exactly one.");
            }

            /** @var Node $node */
            $node = $matches->first();
        }

        if (!array_key_exists($id, $this->parentToChildrenMap) && $this->lookup[$id]['@type'] === NodeType::Category) {
            return null;
        }

        $node->trail = $trail;

        if ($id !== $this->rootNodeId) {
            $trail[] = $id;
        }

        $children = [];
        foreach (($this->parentToChildrenMap[$id] ?? []) as $child) {
            $subtree = $this->buildTree($child->id, $id, $trail);
            if (!$subtree) {
                continue;
            }

            $children[] = $subtree;
        }

        $node->children = $children;

        return $node;
    }
}
