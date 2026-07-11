<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\NotionData\Hydrator;
use App\Services\NotionData\NotionClient;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Notion\Pages\Page;

class ReportPendingEntries extends Command
{
    protected $signature = 'app:report-pending-entries';

    protected $description = 'List non-archived entries whose Notion Status is Pending, as a Markdown bullet list (empty output when none).';

    public function handle(NotionClient $notion): int
    {
        $pending = collect($notion->rawPages())->filter(function (Page $page): bool {
            if ($page->archived || $this->isCategory($page)) {
                return false;
            }

            try {
                return $page->properties()->getStatus('Status')->option->name === 'Pending';
            } catch (\Throwable) {
                return false;
            }
        });

        $this->printPendingList($pending);

        return self::SUCCESS;
    }

    /** @param  Collection<int, Page>  $pending */
    private function printPendingList(Collection $pending): void
    {
        $pending
            ->map(fn (Page $page): string => sprintf('- [%s](%s)', $this->pageLabel($page), $this->pageUrl($page)))
            ->sort()
            ->each(fn (string $line) => $this->line($line));
    }

    private function isCategory(Page $page): bool
    {
        try {
            return $page->properties()->getCheckboxById(Hydrator::SCHEMA['isCategory'])->checked;
        } catch (\Throwable) {
            return false;
        }
    }

    private function pageLabel(Page $page): string
    {
        return $page->title()?->toString() ?: '(untitled)';
    }

    private function pageUrl(Page $page): string
    {
        return 'https://www.notion.so/'.str_replace('-', '', (string) $page->id);
    }
}
