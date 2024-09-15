<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\ValidationRule;

class OkStatusRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            throw new \UnexpectedValueException('The value to validate must be a string.');
        }

        if (! str_starts_with($value, 'http')) {
            $value = 'https://'.$value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('The value to validate must be a valid URL.');
        }

        $baseURL = parse_url($value, PHP_URL_SCHEME).'://'.parse_url($value, PHP_URL_HOST);

        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:124.0) Gecko/20100101 Firefox/124.0',
                'Accept' => '*/*',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'en-US,en;q=0.5',
                'DNT' => '1',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache',
                'Referer' => $baseURL,
                'Origin' => $baseURL,
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'cross-site',
            ],
            'timeout' => 5,
            'http_errors' => false,
        ]);

        try {
            $head = $client->head($value);

            if ($head->getStatusCode() >= 200 && $head->getStatusCode() < 300) {
                return;
            }
        } catch (RequestException|ConnectException) {
        }

        try {
            $get = $client->get($value);

            if (($get->getStatusCode() >= 200 && $get->getStatusCode() < 300) || $get->getStatusCode() === 403) {
                return;
            }
        } catch (RequestException|ConnectException) {
        }

        $fail('The URL is not reachable.');
    }
}
