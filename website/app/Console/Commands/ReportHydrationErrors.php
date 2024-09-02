<?php

namespace App\Console\Commands;

use App\Services\NotionData\HydrationError;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Notion\Pages\Page;

class ReportHydrationErrors extends Command
{
    protected $signature = 'app:report-hydration-errors {path?}';

    /**
     * Execute the console command.
     */
    public function handle(Notion $notion): void
    {
        $pages = $notion->pages();
        $tree = Tree::buildFromPages($pages);

        $report = '';

        collect($tree->errors)
            ->flatMap(
                /** @return Collection<HydrationError> */
                fn ($error) => collect($error->messages)->map(
                    fn ($message): HydrationError => new HydrationError($error->page, [$message])
                )
            )
            ->groupBy(fn (HydrationError $error) => $error->messages[0])
            ->map(fn (Collection $errors) => $errors->map->page)
            ->each(function (Collection $pages, string $errorMessage) use (&$report) {
                $report .= "### $errorMessage\n";

                foreach ($pages as $page) {
                    $url = 'https://www.notion.so/'.str_replace('-', '', $page->id);

                    $label = $page instanceof Page ? $page->title()?->toString() : $page->label;
                    if (is_null($label)) {
                        continue;
                    }

                    $report .= "- [$label]($url)\n";
                }
            });

        $path = $this->argument('path') ?? 'report.md';
        file_put_contents($path, <<<MARKDOWN
# Report: possible problems in Notion
These problems don't stop the site from working, but entries with errors are ignored and will not be displayed.

It can be re-run although the process is a bit tedious, you have to manually "dispatch" a run in the "Actions" tab and provide
a PR/Issue number. I can also run it and share the report.

$report
MARKDOWN
        );
    }
}
