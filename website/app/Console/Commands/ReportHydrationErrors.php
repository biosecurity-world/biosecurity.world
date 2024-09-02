<?php

namespace App\Console\Commands;

use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Console\Command;
use Notion\Pages\Page;

class ReportHydrationErrors extends Command
{
    protected $signature = 'app:report-hydration-errors {path}';

    /**
     * Execute the console command.
     */
    public function handle(Notion $notion): void
    {
        $pages = $notion->pages();
        $tree = Tree::buildFromPages($pages);

        $markdown = '';

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
                $url = 'https://www.notion.so/'.str_replace('-', '', $page->id);

                $label = $page instanceof Page ? $page->title()->toString() : $page->label;

                $markdown .= "- [$label]($url)\n";
            }
        });

        $markdown = <<<MARKDOWN
# Report: possible problems with the data
These problems don't stop the site from working, but entries with errors are ignored and will not be displayed.

It can be re-run although the process is a bit tedious, you have to manually "dispatch" a run in the "Actions" tab and provide
a PR/Issue number. I can also run it and share the report.

$markdown
MARKDOWN;

        $path = $this->argument('path') ?? 'report.md';
        file_put_contents($path, $markdown);
    }
}
