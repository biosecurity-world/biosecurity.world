<?php

namespace BeyondCode\VisualDiff;

use Symfony\Component\Process\Process;

class VisualDiff
{
    protected $nodeBinary = null;

    protected $npmBinary = null;

    protected $binPath = null;

    protected $newImage;

    protected $comparisonImage;

    protected $threshold = 0.1;

    protected $antialias = false;

    public function __construct(string $newImage, string $comparisonImage)
    {
        $this->newImage = $newImage;
        $this->comparisonImage = $comparisonImage;
    }

    public static function diff(string $newImage, string $comparisonImage)
    {
        return new static($newImage, $comparisonImage);
    }

    public function setNodeBinary(string $nodeBinary)
    {
        $this->nodeBinary = $nodeBinary;

        return $this;
    }

    public function setNpmBinary(string $npmBinary)
    {
        $this->npmBinary = $npmBinary;

        return $this;
    }


    public function setAntialias(bool $antialias)
    {
        $this->antialias = $antialias;

        return $this;
    }

    public function setThreshold(float $threshold)
    {
        $this->threshold = $threshold;

        return $this;
    }

    public function save($filename)
    {
        $output = $this->callDiff($this->buildSaveCommand($filename));

        return json_decode($output);
    }

    protected function callDiff(array $command)
    {
        $nodeBinary = $this->nodeBinary ?: 'node';
        $binPath = $this->binPath ?: __DIR__ . '/../bin/diff.mjs';

        $process = new Process([
            $nodeBinary,
            $binPath,
            json_encode($command)
        ]);

        $process->setEnv(['NODE_PATH' => base_path('node_modules')]);

        $process->run();

        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        } else {
            throw new \Exception($process->getErrorOutput());
        }
    }

    public function buildSaveCommand($filename): array
    {
        return [
            'image_1' => $this->newImage,
            'image_2' => $this->comparisonImage,
            'output' => $filename,
            'threshold' => $this->threshold,
            'antialias' => $this->antialias,
        ];
    }
}
