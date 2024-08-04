<?php

namespace App\Services\NotionData\Tree;

use App\Services\NotionData\Enums\NodeType;
use App\Services\NotionData\Page;
use App\Services\NotionData\Utils\IdPool;
use Illuminate\Support\Collection;

class TreeBuilder
{
    protected array $vertexData = [];
    protected array $parentToChildrenMap = [];
    protected string $rootNodeId;
    protected array $nodes = [];


    /**
     * @param Collection<Page> $pages
     * @return array
     * @throws \Exception
     */
    public function build(Collection $pages): array
    {
        // Normalize the map, so it has a single root node.
        $this->rootNodeId = 'root';
        /** @var Collection<Page> $rootNodes */
        $rootNodes = collect($pages)->filter(fn(Page $page) => $page->parent === null);
        if (count($rootNodes) === 1) {
            $rootNodes->first()->parents = [$this->rootNodeId];
        } else if (count($rootNodes) > 1) {
            // We want to connect the forest to a single root node.
            foreach ($rootNodes as $root) {
                $root->parent = $this->rootNodeId;
            }

            $this->vertexData[$this->rootNodeId] = [
                '@type' => NodeType::Root,
            ];
        } else {
            throw new \Exception("Should not happen: the map is empty.");
        }

        $this->nodes = [];
        foreach ($pages as $page) {
            // Pages have a lot of data associated with them (title, link...) and we are possibly
            // duplicating them 10 or 15 times. We don't want to send this data more than once,
            // so we separate the relational data (id, parent) from the page data (title...)
            $page->data['@type'] = $page->type;
            $page->data['@id'] = $page->id;
            $this->vertexData[$page->id] = $page->data;

            // This is true only if the raw pages already had a single root.
            if ($page->id === $this->rootNodeId) {
                $this->nodes[] = new Node($page->id, $page->id);
                continue;
            }
            $this->nodes[] = new Node($page->id, $page->parent);
        }

        // Squash all entries into entrygroups
        $this->nodes = collect($this->nodes)->groupBy('parentId')
            ->map(function (Collection $children, string $parentId) {
                [$entries, $rest] = $children->partition(function (Node $vertex) {
                    return $this->vertexData[$vertex->id]['@type'] === NodeType::Entry;
                });

                if ($entries->isNotEmpty()) {
                    /** @var Collection<string> $entryIds */
                    $entryIds = $entries->pluck('id');

                    $entryGroupId = sha1($parentId . '-' . $entryIds->sort()->join(''));
                    $rest->push(new Node($entryGroupId, $parentId));
                    $this->vertexData[$entryGroupId] = [
                        '@type' => NodeType::EntryGroup,
                        '@id' => $entryGroupId,
                        'entries' => $entryIds->toArray()
                    ];
                }

                return $rest;
            })
            ->flatten(1)
            ->toArray();


        $this->parentToChildrenMap = collect($this->nodes)->groupBy('parentId')->toArray();

        $tree = $this->buildTree(id: $this->rootNodeId, parentId: $this->rootNodeId);


        return [
            'tree' => $tree,
            'lookup' => $this->vertexData
        ];
    }

    public function buildTree(string $id, string $parentId, array $trail = []): ?Node
    {
        if ($id === $this->rootNodeId) {
            $node = new Node($id, $parentId, []);
        } else {
            $matches = collect($this->nodes)->where('id', $id)->where(fn(Node $node) => $node->parentId === $parentId);
            if ($matches->count() !== 1) {
                throw new \Exception("Should not happen: The vertex represented by the couple (id: `$id`, parentId: `$parentId`) has {$matches->count()} matches while it should have exactly one.");
            }

            $node = $matches->first();
        }

        if (!array_key_exists($id, $this->parentToChildrenMap) && $this->vertexData[$id]['@type'] === NodeType::Category) {
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

//        if ($id === "4ff9bf16-3eff-48cd-9f75-6c646d334fcb") {
//            dd($this->vertexData[$id], array_map(fn ($c) => $this->vertexData[$c->id], $node->children));
//        }



        return $node;
    }
}
