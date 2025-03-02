<?php

namespace drahil\MubiStats\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MovieService
{
    private int $perPage = 100;

    /**
     * Get movies from Mubi API.
     *
     * @param string $profileId
     * @return array
     * @throws GuzzleException
     */
    public function getMovies(string $profileId): array
    {
        if (file_exists($profileId . '.json')) {
            return json_decode(file_get_contents($profileId . '.json'), true);
        }

        $client = new Client();

        $response = $client->get("https://api.mubi.com/v3/users/{$profileId}/ratings", [
            'headers' => [
                'client' => 'web',
                'client-country' => 'ME'
            ],
            'query' => [
                'per_page' => $this->perPage
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
                    'per_page' => $this->perPage
                ]
            ]);

            $newData = json_decode($response->getBody(), true);

            $nextCursor = $newData['meta']['next_cursor'];

            $data = array_merge($data, $newData['ratings']);
        } while ($nextCursor !== null);

        return $data;
    }

    /**
     * Save movies to a JSON file.
     *
     * @param array $movies
     * @param string $profileId
     * @return true
     * @throws Exception
     */
    public function saveMovies(array $movies, string $profileId): true
    {
        try {
            $jsonData = json_encode($movies, JSON_PRETTY_PRINT);
            file_put_contents($profileId . '.json', $jsonData);

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to save movies to a file.');
        }
    }
}