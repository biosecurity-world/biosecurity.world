<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\NotionData\HydrationError;
use App\Services\NotionData\Hydrator;
use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Notion\Pages\Page;

class CheckNotionPublication extends Command
{
    protected $signature = 'app:check-notion-publication';

    protected $description = 'Report Notion pages that are not publishable on the site';

    public function handle(NotionClient $notion): int
    {
        $rawPages = collect($notion->rawPages());
        $publishedPages = $this->publishedPages($rawPages);
        $hydratedPages = $notion->pages();
        $tree = Tree::buildFromPages($hydratedPages);

        $this->line(sprintf(
            'Notion publication audit: %d raw pages, %d publishable pages, %d displayed entries.',
            $rawPages->count(),
            $publishedPages->count(),
            $tree->entries()->count()
        ));

        $this->reportPendingEntries($rawPages);
        $this->reportArchivedEntries($rawPages);
        $this->reportEntriesWithUnreadableStatus($rawPages);

        if ($tree->errors === []) {
            $this->info('No hydration/tree errors found.');

            return self::SUCCESS;
        }

        $this->reportHydrationErrors($tree->errors);
        $this->error('Some publishable Notion pages are not displayed. Fix the issues above before deploying.');

        return self::FAILURE;
    }

    /**
     * @param  Collection<int, Page>  $rawPages
     * @return Collection<int, Page>
     */
    private function publishedPages(Collection $rawPages): Collection
    {
        return $rawPages->filter(function (Page $page): bool {
            if ($page->archived) {
                return false;
            }

            try {
                if ($page->properties()->getCheckboxById(Hydrator::SCHEMA['isCategory'])->checked) {
                    return true;
                }

                return $page->properties()->getStatus('Status')->option?->name !== 'Pending';
            } catch (\Throwable) {
                return true;
            }
        });
    }

    /** @param  Collection<int, Page>  $rawPages */
    private function reportPendingEntries(Collection $rawPages): void
    {
        $pending = $rawPages->filter(function (Page $page): bool {
            if ($page->archived || $this->isCategory($page)) {
                return false;
            }

            try {
                return $page->properties()->getStatus('Status')->option?->name === 'Pending';
            } catch (\Throwable) {
                return false;
            }
        });

        if ($pending->isEmpty()) {
            return;
        }

        $this->warn(sprintf('%d non-archived entries are intentionally hidden because Status is Pending:', $pending->count()));
        $pending->each(fn (Page $page) => $this->line('- '.$this->pageLabel($page).' '.$this->pageUrl($page)));
    }

    /** @param  Collection<int, Page>  $rawPages */
    private function reportArchivedEntries(Collection $rawPages): void
    {
        $archivedEntries = $rawPages->filter(fn (Page $page): bool => $page->archived && ! $this->isCategory($page));

        if ($archivedEntries->isEmpty()) {
            return;
        }

        $this->warn(sprintf('%d entries are hidden because they are archived in Notion:', $archivedEntries->count()));
        $archivedEntries->each(fn (Page $page) => $this->line('- '.$this->pageLabel($page).' '.$this->pageUrl($page)));
    }

    /** @param  Collection<int, Page>  $rawPages */
    private function reportEntriesWithUnreadableStatus(Collection $rawPages): void
    {
        $entriesWithUnreadableStatus = $rawPages->filter(function (Page $page): bool {
            if ($page->archived || $this->isCategory($page)) {
                return false;
            }

            try {
                $page->properties()->getStatus('Status');

                return false;
            } catch (\Throwable) {
                return true;
            }
        });

        if ($entriesWithUnreadableStatus->isEmpty()) {
            return;
        }

        $this->warn(sprintf(
            '%d entries have an unreadable Status property and are treated as publishable:',
            $entriesWithUnreadableStatus->count()
        ));
        $entriesWithUnreadableStatus->each(fn (Page $page) => $this->line('- '.$this->pageLabel($page).' '.$this->pageUrl($page)));
    }

    /** @param  HydrationError[]  $errors */
    private function reportHydrationErrors(array $errors): void
    {
        collect($errors)
            ->groupBy('message')
            ->each(function (Collection $errors, string $message): void {
                $details = $errors
                    ->map(fn (HydrationError $error): string => $this->errorSubject($error))
                    ->unique()
                    ->sort()
                    ->values();

                $githubMessage = $message.' - '.$details->implode('; ');
                if (getenv('GITHUB_ACTIONS') === 'true') {
                    $this->line('::warning title=Notion publication issue::'.$githubMessage);
                }

                $this->warn($message);
                $details->each(fn (string $detail) => $this->line('- '.$detail));
            });
    }

    private function isCategory(Page $page): bool
    {
        try {
            return $page->properties()->getCheckboxById(Hydrator::SCHEMA['isCategory'])->checked;
        } catch (\Throwable) {
            return false;
        }
    }

    private function errorSubject(HydrationError $error): string
    {
        if ($error->page instanceof Page) {
            return $this->pageLabel($error->page).' '.$this->pageUrl($error->page);
        }

        if ($error->page instanceof Entry) {
            return $error->page->label.' '.$error->page->notionUrl();
        }

        return $error->page->label;
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
