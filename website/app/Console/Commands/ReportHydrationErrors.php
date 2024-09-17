<?php

namespace App\Console\Commands;

use App\Services\NotionData\Hydrator;
use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Notion\Pages\Page;

class ReportHydrationErrors extends Command
{
    protected $signature = 'app:report-hydration-errors {path?} {--strict}';

    /**
     * Execute the console command.
     */
    public function handle(NotionClient $notion): void
    {
        if ($this->option('strict')) {
            Hydrator::setStrictMode(true);
        }

        $pages = $notion->pages();
        $tree = Tree::buildFromPages($pages);

        $report = '';
        $reported = 0;

        collect($tree->errors)
            ->groupBy('message')
            ->map(fn (Collection $errors) => $errors->map->page)
            ->each(function (Collection $pages, string $errorMessage) use (&$report, &$reported) {
                $report .= "### $errorMessage\n";

                foreach ($pages as $page) {
                    $url = 'https://www.notion.so/'.str_replace('-', '', (string) $page->id);

                    $label = $page instanceof Page ? $page->title()?->toString() : $page->label;
                    if (is_null($label)) {
                        continue;
                    }

                    $report .= "- [$label]($url)\n";
                    $reported++;
                }

                $report .= "\n";
            });

        $path = $this->argument('path') ?? 'report.md';
        file_put_contents($path, <<<MD
# Report: possible problems in Notion

These problems need to be fixed before the PR can be merged.

$report
MD
        );

        $this->outputComponents()->info("Reported $reported errors in $path.");
    }
}
