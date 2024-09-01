<?php

namespace App\Console\Commands;

use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
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

        $errors = collect($tree->errors)->flatMap(function ($error) {
            return collect($error->messages)->map(function ($message) use ($error) {
                return [
                    'message' => $message,
                    'page' => $error->page,
                ];
            });
        });

        collect($errors)->groupBy('message')->each(function ($errors, $message) use (&$markdown) {
            $markdown .= "### $message\n";

            foreach ($errors as $error) {
                $page = $error['page'];

                $url = "https://www.notion.so/" . str_replace('-', '', $page->id);

                $label = $page instanceof Page ? $page->title()->toString() : $page->label;

                $markdown .= "- [$label]($url)\n";
            }
        });

        file_put_contents(
            app()->isProduction() ? '/home/runner/report.md' : 'report.md',
            $markdown
        );
    }
}
