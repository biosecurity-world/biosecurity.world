<?php

namespace App\Services\Iconsnatch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;

class IconSnatch
{
    public static function client() {
        return new Client([
            'base_uri' => rtrim(
                    env("ICONSNATCH_ENDPOINT", "https://iconsnatch.forevue.org"),
                    '/'
                ) . '/api/v1/resolve/',
            'timeout' => 5,
            'headers' => [
                'User-Agent' => env("ICONSNATCH_USERAGENT", "Unknown")
            ]
        ]);
    }

    public static function downloadFrom(string $url): ?Icon {
        try {
            $body = static::client()
                ->get(urlencode($url))
                ->getBody()->getContents();
        } catch (GuzzleException $e) {
            report($e);

            return null;
        }

        $data = json_decode($body, false, flags: JSON_THROW_ON_ERROR);

        if (!$data->success || $data->value === "") {
            return null;
        }

        return Icon::fromResponse($data);
    }
}
