<?php

namespace Tests\Browser\Utils;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;

class Differ
{
    /** @var callable|null */
    private $browsershotConfigurationCallback = null;

    public function __construct(
        protected string $name,
        protected string $url,
        protected string $nodeBinary,
        protected string $chromePath,
        public float $threshold = 0.1,
        public float $errorPercentage = 0,
        protected int $windowWidth = 1920,
        protected int $windowHeight = 1080,

    ) {}

    public function configureBrowsershot(callable $callback): self
    {
        $this->browsershotConfigurationCallback = $callback;

        return $this;
    }

    public function runTest(): void
    {
        $hasExistingScreenshot = file_exists($this->getImagePath('cmp'));
        $shouldUpdateScreenshots = in_array('--update-screenshots', $_SERVER['argv']);
        $imagePath = $this->getImagePath($hasExistingScreenshot ? 'new' : 'cmp');

        $browsershot = Browsershot::url($this->url)
            ->noSandbox()
            ->setNodeBinary('node')
            ->setChromePath($this->chromePath)
            ->windowSize($this->windowWidth, $this->windowHeight);

        if ($this->browsershotConfigurationCallback) {
            call_user_func($this->browsershotConfigurationCallback, $browsershot);
        }

        $browsershot->save($imagePath);

        if (! $hasExistingScreenshot) {
            return;
        }

        $process = new Process([
            'node',
            __DIR__.'/diff.mjs',
            json_encode([
                'image_1' => $this->getImagePath('cmp'),
                'image_2' => $this->getImagePath('new'),
                'output' => $this->getImagePath('diff'),
                'threshold' => $this->threshold,
            ]),
        ]);

        $process->setEnv(['NODE_PATH' => base_path('node_modules/')])->run();

        if (! $process->isSuccessful()) {
            throw new Exception($process->getOutput()."\n\n".$process->getErrorOutput());
        }

        $result = json_decode($process->getOutput(), false, flags: JSON_THROW_ON_ERROR);
        if (isset($result->error)) {
            if ($result->error_type === 'image_dimensions_mismatch' && $shouldUpdateScreenshots) {
                rename($this->getImagePath('new'), $this->getImagePath('cmp'));

                return;
            }

            throw new Exception($result->error);
        }

        if ($shouldUpdateScreenshots) {
            rename($this->getImagePath('new'), $this->getImagePath('cmp'));

            return;
        }

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
    }

    protected function getImagePath(string $type): string
    {
        @mkdir(base_path('tests/Browser/regressions'), 0755, true);

        if (! in_array($type, ['new', 'cmp', 'diff'])) {
            throw new InvalidArgumentException('Invalid type, got '.$type.', expected one of "new", "cmp", "diff"');
        }

        return base_path(sprintf(
            'tests/Browser/regressions/%dx%d_%s_%s.png',
            $this->windowWidth,
            $this->windowHeight,
            $type,
            $this->name)
        );

    }
}
