<?php

namespace Tests\Browser;

use Tests\TestCase;

class PageResponseTest extends TestCase
{
    public function test_the_homepage_is_ok()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_hidden_entries_page_is_ok()
    {
        $response = $this->get('/_/entries');

        $response->assertStatus(200);
    }

    public function test_the_marketing_pages_are_ok() {
        $this->get('/about')->assertStatus(200);
        $this->get('/give-feedback')->assertStatus(200);
        $this->get('/how-to-contribute')->assertStatus(200);
    }

    public function test_the_entry_page_is_ok()
    {
        $notion = $this->app->make(\App\Services\NotionData\Notion::class);
        $tree = \App\Services\NotionData\Tree\Tree::buildFromPages($notion->pages());

        $entrygroup = $tree->entrygroups()->first();
        $entryId = $entrygroup->entries[0];

        $response = $this->get(route('entries.show', ['id' => $entrygroup->id, 'entryId' => $entryId]));

        $response->assertStatus(200);
    }
}
