<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\Entrygroup;
use App\Services\NotionData\Models\Logo;
use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Node;
use App\Services\NotionData\Tree\Tree;
use App\Support\IdMap;
use Illuminate\Support\Collection;
use Notion\Pages\Properties\RichTextProperty;
use Tests\TestCase;

final class EntryRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        IdMap::restore(['notion-entry' => 10]);
    }

    protected function tearDown(): void
    {
        IdMap::restore([]);
        parent::tearDown();
    }

    public function test_entry_without_slug_redirects_to_its_canonical_url(): void
    {
        $this->bindTree($this->tree());

        $this->get('/entry/10')
            ->assertRedirectToRoute('entries.show', ['id' => 10, 'slug' => 'alpha-org']);
    }

    public function test_entry_with_wrong_slug_redirects_to_its_canonical_url(): void
    {
        $this->bindTree($this->tree());

        $this->get('/entry/10/wrong-slug')
            ->assertRedirectToRoute('entries.show', ['id' => 10, 'slug' => 'alpha-org']);
    }

    public function test_entry_page_renders_for_a_valid_entry_and_slug(): void
    {
        $this->bindTree($this->tree());

        $this->get('/entry/10/alpha-org')
            ->assertOk()
            ->assertSee('Alpha Org');
    }

    public function test_entry_route_rejects_a_category_id(): void
    {
        $this->bindTree($this->tree());

        $this->get('/entry/1/category')->assertNotFound();
        $this->get('/entry/1')->assertNotFound();
    }

    public function test_entry_partial_rejects_an_entry_outside_the_requested_group(): void
    {
        $tree = $this->tree();
        $tree->lookup[11] = $this->entry(11, 'Other Org');
        $this->bindTree($tree);

        $this->get('/partials/entries/20/11')->assertNotFound();
    }

    public function test_entry_partial_renders_for_an_entry_in_the_requested_group(): void
    {
        $this->bindTree($this->tree());

        $this->get('/partials/entries/20/10')
            ->assertOk()
            ->assertSee('Alpha Org')
            ->assertSee('Category');
    }

    private function bindTree(Tree $tree): void
    {
        $this->app->instance(NotionClient::class, new StubNotionClient($tree));
    }

    private function tree(): Tree
    {
        $category = new Category(1, null, 'Category', new \DateTimeImmutable);
        $entry = $this->entry(10, 'Alpha Org');
        $group = new Entrygroup(20, [10]);
        $groupNode = new Node(20, 1);
        $groupNode->trail = [1];

        return new Tree(
            nodes: [$groupNode],
            lookup: [1 => $category, 10 => $entry, 20 => $group],
            errors: [],
        );
    }

    private function entry(int $id, string $label): Entry
    {
        return new Entry(
            id: $id,
            parentId: 1,
            label: $label,
            createdAt: new \DateTimeImmutable,
            link: 'https://example.com',
            description: RichTextProperty::createEmpty(),
            organizationType: 'National non-profit organization',
            domains: new Collection,
            interventionFocuses: new Collection,
            activities: new Collection,
            locationHints: new Collection,
            focusesOnGCBRs: false,
            logo: new Logo('https://example.com/logo.png'),
        );
    }
}

final class StubNotionClient extends NotionClient
{
    public function __construct(private readonly Tree $fakeTree) {}

    public function tree(): Tree
    {
        return $this->fakeTree;
    }
}
