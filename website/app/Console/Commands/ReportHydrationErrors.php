<?php

namespace App\Console\Commands;

use App\Services\NotionData\Hydrator;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use App\Support\IdHash;
use Illuminate\Console\Command;
use Notion\Pages\Page;

class ReportHydrationErrors extends Command
{
    protected $signature = 'app:report-hydration-errors';

    /**
     * Execute the console command.
     */
    public function handle(Notion $notion): void
    {
        $pages = $notion->pages();
        $tree = Tree::buildFromPages($pages);

        $markdown = "";

        collect($tree->errors)->groupBy('message')->each(function ($errors, $message) use (&$markdown) {
            $markdown .= "### $message\n";

            foreach ($errors as $error) {
                $page = $error->page;

                $url = "https://www.notion.so/{$page->id}";

                $label = $page instanceof Page ? $page->title()->toString() : $page->label;

                $markdown .= "- [$label]($url)\n";
            }
        });

        if (app()->isProduction()) {
            file_put_contents('/home/runner/report.md', $markdown);
        }  else {
            echo $markdown;
        }
    }
}
