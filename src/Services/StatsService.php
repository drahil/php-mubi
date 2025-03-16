<?php

namespace drahil\MubiStats\Services;

use drahil\MubiStats\Singletons\MovieDataSingleton;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class StatsService
{
    private MovieDataSingleton $movieDataSingleton;
    private array $movieData;

    public function __construct()
    {
        $this->movieDataSingleton = MovieDataSingleton::getInstance();
        $this->movieData = $this->movieDataSingleton->getMovieData();
    }

    /**
     * Get movies by country.
     *
     * @return void
     */
    public function getMoviesByCountry(): void
    {
        echo "Enter country: ";
        $county = trim(fgets(STDIN));
        $this->getStatsByOption('country', $county);
    }

    /**
     * Get movies by director.
     *
     * @return void
     */
    public function getMoviesByDirector(): void
    {
        echo "Enter director: ";
        $director = trim(fgets(STDIN));
        $this->getStatsByOption('director', $director);
    }

    /**
     * Get movies by option.
     *
     * @param $originalOption
     * @param $value
     * @return void
     */
    public function getStatsByOption($originalOption, $value): void
    {
        $option = $this->mapOptionToMubiArray($originalOption);
        $movieByOption = array_filter($this->movieData, function($movie) use ($option, $value) {
            $data = $movie['film'][$option];

            if ($option === 'directors') {
                $data = array_column($data, 'name');
            }

            return in_array($value, $data);
        });

        echo "Filter: $originalOption. Value: $value\n";
        foreach ($movieByOption as $movie) {
            echo "{$movie['film']['title']}\n";
        }
    }

    /**
     * Map option to Mubi array.
     *
     * @param string $option
     * @return string
     */
    private function mapOptionToMubiArray(string $option): string
    {
        $options = [
            'director' => 'directors',
            'country' => 'historic_countries',
            'genre' => 'genres'
        ];

        return $options[$option];
    }
}
