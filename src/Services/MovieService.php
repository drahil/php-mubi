<?php

namespace drahil\MubiStats\Services;

use drahil\MubiStats\Data\MovieData;
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
        if (file_exists("movies_{$profileId}.json")) {
            $movieData = json_decode(file_get_contents("movies_{$profileId}.json"), true);
            MovieData::getInstance($profileId)->setMovieData($movieData);
            return $movieData;
        }

        $client = new Client();

        $response = $client->get("https://api.mubi.com/v3/users/{$profileId}/ratings", [
            'headers' => [
                'client' => 'web',
                'client-country' => 'US'
            ],
            'query' => [
                'per_page' => $this->perPage
            ]
        ]);

        $initialData = json_decode($response->getBody(), true);

        $nextCursor = $initialData['meta']['next_cursor'];
        $movieData = $initialData['ratings'];

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

            $movieData = array_merge($movieData, $newData['ratings']);
        } while ($nextCursor !== null);

        MovieData::getInstance($profileId)->setMovieData($movieData);
        return $movieData;
    }

    /**
     * Save movies to a JSON file.
     *
     * @param array $movies
     * @param string $profileId
     * @return true
     * @throws Exception
     */
    public function saveMovies(array $movies, string $profileId): bool
    {
        try {
            $jsonData = json_encode($movies, JSON_PRETTY_PRINT);
            $fileName = "movies_{$profileId}.json";
            file_put_contents($fileName, $jsonData);

            MovieData::getInstance($profileId)->setMovieData($movies);

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to save movies to a file.');
        }
    }
}