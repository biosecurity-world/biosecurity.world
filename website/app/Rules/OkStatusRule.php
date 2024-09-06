<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\ValidationRule;

class OkStatusRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:124.0) Gecko/20100101 Firefox/124.0',
            'DNT' => '1',
            'Accept' => 'image/avif,image/webp,*/*',
            'Cache-Control' => 'no-cache',
            'Referer' => 'baseURL',
            'Origin' => 'baseURL',
            'Sec-Fetch-Dest' => 'image',
            'Sec-Fetch-Mode' => 'no-cors',
            'Sec-Fetch-Site' => 'same-origin',
        ];

        try {
            $client = new Client([
                'headers' => $headers,
                'timeout' => 5,
                'http_errors' => false,
            ]);

            try {
                $head = $client->head($value);

                if ($head->getStatusCode() >= 200 && $head->getStatusCode() < 300) {
                    return;
                }
            } catch (RequestException|ConnectException) {
                // Continue to GET request
            }

            $get = $client->get($value);

            if ($get->getStatusCode() >= 200 || $get->getStatusCode() === 403) {
                return;
            }

            $fail('The URL is not reachable.');
        } catch (RequestException $e) {
            $fail('The URL is not reachable.');
        }
    }
}
