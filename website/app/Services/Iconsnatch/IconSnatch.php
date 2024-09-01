<?php

namespace App\Services\Iconsnatch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use stdClass;

class IconSnatch
{
    public static function client(): Client
    {
        return new Client([
            'base_uri' => config('services.iconsnatch.endpoint').'/api/v1/resolve/',
            'timeout' => 5,
            'headers' => [
                'User-Agent' => config('services.iconsnatch.useragent'),
            ],
        ]);
    }

    public static function downloadFrom(string $url): Logo
    {
        $cacheKey = 'iconsnatch-download-'.str_replace(str_split('{}()/\@:'), '_', $url);
        if (Cache::has($cacheKey)) {
            /** @phpstan-ignore-next-line  */
            return Cache::get($cacheKey) ?? Logo::zero();
        }

        try {
            $body = static::client()
                ->get(urlencode($url))
                ->getBody()->getContents();
        } catch (GuzzleException $e) {
            report($e);

            return Logo::zero();
        }

        /** @var stdClass $data */
        $data = json_decode($body, false, flags: JSON_THROW_ON_ERROR);

        $logo = Logo::fromResponse($data);
        Cache::forever($cacheKey, $logo);

        return $logo ?? Logo::zero();
    }
}
