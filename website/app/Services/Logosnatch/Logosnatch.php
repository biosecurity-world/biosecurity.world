<?php

namespace App\Services\Logosnatch;

use Illuminate\Support\Facades\Cache;
use RuntimeException;

class Logosnatch
{
    public static function retrieve(string $url, int $targetSize = 128): Logo
    {
        $cacheKey = sprintf('logosnatch-download-%s-%d', base64_encode($url), $targetSize);
        if (Cache::has($cacheKey)) {
            return self::createFromLogosnatchResponse(Cache::get($cacheKey));
        }

        $logosnatchBinary = base_path('/../tools/logosnatch/logosnatch');
        if (! is_string($logosnatchBinary) || ! file_exists($logosnatchBinary)) {
            throw new RuntimeException('Could not find logosnatch binary at '.$logosnatchBinary);
        }

        // We can not use symfony/process because it does not let us hook into STDIN.
        $process = proc_open(
            [
                $logosnatchBinary,
                '-o', storage_path('app/public/logos'),
                '-d', storage_path('app/public/missing-logo.svg'),
                '-s', (string) $targetSize,
                '-json',
            ],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            '/tmp',
            [],
        );
        if (! $process) {
            throw new RuntimeException('Failed to start logosnatch process');
        }

        fwrite($pipes[0], $url);
        fclose($pipes[0]);

        $body = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $ret = proc_close($process);

        if ($ret !== 0 || ! $body) {
            throw new RuntimeException('Failed to download logo: '.$error);
        }

        $decoded = json_decode($body, true);
        $logo = self::createFromLogosnatchResponse($decoded);

        Cache::forever($cacheKey, $decoded);

        return $logo;
    }

    private static function createFromLogosnatchResponse(mixed $decoded): Logo
    {
        if (! is_array($decoded) || ! isset($decoded['format'], $decoded['path'], $decoded['filled'], $decoded['size'])) {
            throw new RuntimeException('Unexpected response from logosnatch, expected key format, path, filled, got '.$decoded);
        }

        return new Logo(
            $decoded['format'],
            '/storage/logos/'.$decoded['path'],
            $decoded['size'],
            $decoded['filled']
        );
    }
}
