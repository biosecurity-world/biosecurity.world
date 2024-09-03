<?php

namespace App\Services\Logosnatch;

use Illuminate\Support\Facades\Cache;

class IconSnatch
{
    public static function retrieve(string $url): Logo
    {
        $cacheKey = 'iconsnatch-download-'.str_replace(str_split('{}()/\@:'), '_', $url);
        if (Cache::has($cacheKey)) {
            /** @phpstan-ignore-next-line  */
            return Cache::get($cacheKey);
        }

        $iconsnatchBinary = base_path('tools/logosnatch/logosnatch');
        if (! is_string($iconsnatchBinary) || ! file_exists($iconsnatchBinary)) {
            throw new \RuntimeException('IconSnatch binary not configured');
        }

        $process = proc_open(
            [
                $iconsnatchBinary,
                '-o', storage_path('app/public/logos'),
                '-d', storage_path('app/public/missing-logo.svg'),
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
                throw new \RuntimeException('Failed to download logo: '.$error);
            }
        } else {
            throw new \RuntimeException('Failed to start iconsnatch process');
        }

        if (! $body) {
            throw new \RuntimeException("Failed to download logo: $error");
        }

        $decoded = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        if (! is_array($decoded) || ! array_key_exists('format', $decoded) || ! array_key_exists('path', $decoded) || ! array_key_exists('filled', $decoded)) {
            throw new \RuntimeException(sprintf('Unexpected response from iconsnatch, got %s, expected key format, path, filled', $body));
        }

        $logo = new Logo(
            $decoded['format'],
            'storage/logos/'.$decoded['path'],
            $decoded['filled']
        );

        Cache::forever($cacheKey, $logo);

        return $logo;
    }
}
