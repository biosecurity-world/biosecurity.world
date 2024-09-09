<?php

namespace App\Services\Logosnatch;

use Illuminate\Support\Facades\Cache;
use RuntimeException;

class Logosnatch
{
    public static function retrieve(string $url, int $targetSize = 128): Logo
    {
        $cacheKey = 'logosnatch-download-'.str_replace(str_split('{}()/\@:'), '_', $url).'-'.$targetSize;
        if (Cache::has($cacheKey)) {
            /** @var array{format: string, path: string, size: int, filled: bool} $decoded */
            $decoded = Cache::get($cacheKey);

            return self::createFromLogosnatchResponse($decoded);
        }

        $logosnatchBinary = base_path('/../tools/logosnatch/logosnatch');
        if (! is_string($logosnatchBinary) || ! file_exists($logosnatchBinary)) {
            throw new RuntimeException('Could not find logosnatch binary at '.$logosnatchBinary);
        }

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

        if (is_resource($process)) {
            fwrite($pipes[0], $url);
            fclose($pipes[0]);

            $body = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $return_value = proc_close($process);

            if ($return_value !== 0) {
                throw new RuntimeException('Failed to download logo: '.$error);
            }
        } else {
            throw new RuntimeException('Failed to start logosnatch process');
        }

        if (! $body) {
            throw new RuntimeException("Failed to download logo: $error");
        }

        $decoded = json_decode($body, true);
        if (! is_array($decoded) || ! array_key_exists('format', $decoded) || ! array_key_exists('path', $decoded) || ! array_key_exists('filled', $decoded) || ! array_key_exists('size', $decoded)) {
            throw new RuntimeException(sprintf('Unexpected response from logosnatch, got %s, expected key format, path, filled', $body));
        }

        Cache::forever($cacheKey, $decoded);

        return self::createFromLogosnatchResponse($decoded);
    }

    /** @param array{format: string, path: string, size: int, filled: bool} $decoded */
    private static function createFromLogosnatchResponse(array $decoded): Logo
    {
        return new Logo(
            $decoded['format'],
            'storage/logos/'.$decoded['path'],
            $decoded['size'],
            $decoded['filled']
        );
    }
}
