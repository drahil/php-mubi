<?php

namespace drahil\PhpMubi\Services;

use GuzzleHttp\Client;

class MovieService
{
    public function getMovies(string $profileId): array
    {
        $perPage = 100;
        $client = new Client();
        
        $response = $client->get("https://api.mubi.com/v3/users/{$profileId}/ratings", [
            'headers' => [
                'client' => 'web',
                'client-country' => 'ME'
            ],
            'query' => [
                'per_page' => $perPage
            ]
        ]);

        $initialData = json_decode($response->getBody(), true);

        $nextCursor = $initialData['meta']['next_cursor'];
        $data = $initialData['ratings'];

        do {
            $response = $client->get(
                "https://api.mubi.com/v4/users/{$profileId}/ratings", [
                'headers' => [
                    'client' => 'web',
                    'client-country' => 'ME'
                ],
                'query' => [
                    'before' => $nextCursor,
                    'per_page' => $perPage
                ]
            ]);

            $newData = json_decode($response->getBody(), true);

            $nextCursor = $newData['meta']['next_cursor'];

            $data = array_merge($data, $newData['ratings']);
        } while ($nextCursor !== null);

        return $data;
    }
}