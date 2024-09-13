<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class NewLayoutTestUrlCommand extends Command
{
    protected $signature = 'app:new-layout-test-url {delta} {theta} {length?} {width?}';

    public function handle(): int
    {
        if (! app()->isLocal()) {
            $this->error('This command can only be run in local environment');
            return 1;
        }

        $delta = $this->evalExpr($this->argument('delta'));
        $theta = $this->evalExpr($this->argument('theta'));
        $length = $this->argument('length') ?? 100;
        $width = $this->argument('width') ?? 20;

        $id = Str::uuid();
        cache()->forever('tree-'.$id, [[
            'sector' => [$delta, $theta],
            'width' => (int) $width,
            'length' => (int) $length,
        ]]);

        echo route('tree-rendering', ['caseId' => $id]) . PHP_EOL;

        return 0;
    }

    private function evalExpr(string $argument)
    {
        $argument = trim(strtolower($argument));

        $argument = str_replace('pi', 'pi()', $argument);

        return eval("return $argument;");
    }
}
