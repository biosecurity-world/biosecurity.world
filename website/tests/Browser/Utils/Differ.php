<?php

namespace Tests\Browser\Utils;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;

class Differ
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected int $browserWidth,
        protected int $browserHeight,
        protected string $nodeBinary,
        protected string $chromePath,
        public bool $antialias = false,
        public float $threshold = 0.1,
        public float $errorPercentage = 0.1,

    ) {}

    public function runTest(): void
    {
        $hasExistingScreenshot = file_exists($this->getImagePath('cmp'));
        $imagePath = $this->getImagePath($hasExistingScreenshot ? 'new' : 'cmp');

        Browsershot::url($this->url)
            ->noSandbox()
            ->setNodeBinary('node')
            ->setChromePath($this->chromePath)
            ->windowSize($this->browserWidth, $this->browserHeight)
            ->waitForFunction('window.visualDiffReady === true')
            ->save($imagePath);

        if (! $hasExistingScreenshot) {
            Assert::assertTrue(true);
            return;
        }

        $process = new Process([
            "node",
            __DIR__.'/diff.mjs',
            json_encode([
                'image_1' => $this->getImagePath('cmp'),
                'image_2' => $this->getImagePath('new'),
                'output' => $this->getImagePath('diff'),
                'threshold' => $this->threshold,
                'antialias' => $this->antialias,
            ]),
        ]);

        $process->setEnv(['NODE_PATH' => base_path('node_modules/')])->run();

        if (! $process->isSuccessful()) {
            throw new Exception($process->getOutput()."\n\n".$process->getErrorOutput());
        }

        $result = json_decode($process->getOutput(), false, flags: JSON_THROW_ON_ERROR);

        try {
            Assert::assertLessThanOrEqual(
                $this->errorPercentage,
                $result->error_percentage,
                sprintf(
                    "Image for %s has changed considerably.\n\tSee: %s\n\tReplicate: %s\n",
                    $this->name,
                    $this->getImagePath('diff'),
                    $this->url,
                )
            );
        } catch (ExpectationFailedException $e) {
            if (! in_array('--update-screenshots', $_SERVER['argv'])) {
                throw $e;
            }
        }

        // Rename new image for next comparison
        rename($this->getImagePath('new'), $this->getImagePath('cmp'));
    }

    protected function getImagePath(string $type): string
    {
        @mkdir(base_path('tests/Browser/regressions'), 0755, true);

        if (! in_array($type, ['new', 'cmp', 'diff'])) {
            throw new InvalidArgumentException('Invalid type, got '.$type.', expected one of "new", "cmp", "diff"');
        }

        return base_path(sprintf(
            'tests/Browser/regressions/%dx%d_%s_%s.png',
            $this->browserWidth,
            $this->browserHeight,
            $type,
            $this->name)
        );

    }
}
