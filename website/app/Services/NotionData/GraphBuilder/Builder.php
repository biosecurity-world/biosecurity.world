<?php

namespace App\Services\NotionData\GraphBuilder;

use Generator;
use App\Services\NotionData\Enums\NodeType;
use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;

class Builder
{
    /** @var array<string, Node[]> */
    protected array $parentToChildrenMap = [];
    // Each entry can have multiple parents, and will be present
    // as many times in the graph as it has parents.  To reduce the size of the JSON,
    // we only store a reference to the nodes in the graph, and provide a lookup table.
    protected array $lookup = [];
    protected array $warnings = [];

    private array $circularReferenceTracker = [];

    public function __construct(protected readonly iterable $pages)
    {

    }

    public function build()
    {
        $this->prepareLookupTables();

        $nodes = [];

        foreach (
            $this->mergeNode(
            // We create a fake root node that contains all the real root nodes,
            // that way, we have only one code path for all the nodes.
                new Node(
                    NodeType::Category,
                    'root',
                    '???',
                )
            ) as $node
        ) {
            $nodes[] = $node;
        }

        array_shift($nodes);

        return [
            'nodes' => $nodes,
            'lookup' => $this->lookup,
            'warnings' => $this->warnings,
        ];
    }


    private function prepareLookupTables(): void
    {
        $nodes = collect();

        /** @var Category|Entry $page */
        foreach ($this->pages as $page) {
            $this->lookup[$page->id] = $page;

            if ($page->parents === []) {
                if ($page instanceof Entry) {
                    $this->warnings[] = [
                        'content' => "The link entry \"{$page->label}\" has no parent, it must have at least one parent.",
                        "id" => $page->id
                    ];
                    continue;
                }

                $page->parents = ['root'];
            }

            // Create a node per parent.
            foreach ($page->parents as $parentId) {
                $node = new Node(
                    $page instanceof Category ? NodeType::Category : NodeType::Entry,
                    id: $page->id,
                    label: $page->label, // For debugging
                );

                if (!array_key_exists($parentId, $this->parentToChildrenMap)) {
                    $this->parentToChildrenMap[$parentId] = [];
                }

                $this->parentToChildrenMap[$parentId][] = $node;

                $nodes->push($node);
            }
        }

        $nodes = $nodes->groupBy('parentId');

        foreach ($nodes as $parent => $children) {
            $entries = collect();

            $children = $children->filter(function (Node $node) use (&$entries) {
                if ($node->type === NodeType::Entry) {
                    $entries[] = $node;
                }

                return $node->type !== NodeType::Entry;
            });

            if ($entries->isEmpty()) {
                continue;
            }

            $entryGroup = new Node(
                NodeType::EntryGroup,
                sha1($entries->pluck('id')->join(',')),
                $parent,
            );

            $children->push($entryGroup);

            $this->lookup[$entryGroup->id] = $entries->pluck('id')->toArray();

            $this->parentToChildrenMap[$parent] = $children->values()->toArray();
        }
    }

    /** @return Generator<Node> */
    public function mergeNode(Node $node): Generator
    {
        if (isset($this->circularReferenceTracker[$node->id])) {
            $this->warnings[] = [
                'content' => "Circular reference detected for node \"{$node->label}\".",
                "id" => $node->id
            ];

            return;
        }

        $this->circularReferenceTracker[$node->id] = true;

        $children = $this->parentToChildrenMap[$node->id] ?? [];
        if ($node->type === NodeType::Category && count($children) === 0) {
            $this->warnings[] = [
                'content' => "The category \"{$node->label}\" is empty.",
                "id" => $node->id
            ];

            return;
        }

        $node->children = collect($children)->pluck('id')->toArray();

        yield $node;

        if ($node->type !== NodeType::Category && count($this->parentToChildrenMap[$node->id] ?? []) > 0) {
            $this->warnings[] = [
                'content' => "The entry \"{$node->label}\" of type \"{$node->type->value}\" has children. Only categories can have children.",
                "id" => $node->id
            ];
            return;
        }

        foreach ($children as $child) {
            if ($child->type === NodeType::Entry) {
                continue;
            }

            yield from $this->mergeNode($child);
        }

        unset($this->circularReferenceTracker[$node->id]);
    }

}
