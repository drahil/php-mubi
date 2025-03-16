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
     * Get movies by genre.
     *
     * @return void
     */
    public function getMoviesByGenre(): void
    {
        echo "Enter genre: ";
        $genre = trim(fgets(STDIN));
        $this->getStatsByOption('genre', $genre);
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
        $lowerValue = mb_strtolower($value);

        $movieByOption = array_filter($this->movieData, function ($movie) use ($option, $lowerValue) {
            $data = $movie['film'][$option];

            if ($option === 'directors') {
                $data = array_column($data, 'name');
            }

            $lowerData = array_map('mb_strtolower', $data);

            return in_array($lowerValue, $lowerData, true);
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
