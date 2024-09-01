<?php

namespace App\Console\Commands;

use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Console\Command;

class ReportHydrationErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:report-hydration-errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Notion $notion)
    {
        $pages = $notion->pages();
        $errors = Tree::buildFromPages($pages)->errors;

        dd($errors);
    }
}
