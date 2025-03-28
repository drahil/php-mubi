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

    public function getStats(): void
    {
        $this->printPossibleStats();
        $statsOption = $this->choseStats();
        $this->executeOption($statsOption);
    }

    private function printPossibleStats(): void
    {
        echo 'Possible stats:' . PHP_EOL;
        echo '1. Top directors' . PHP_EOL;
        echo '2. Top countries' . PHP_EOL;
        echo '3. Top genres' . PHP_EOL;
        echo '4. Rating by movie duration' . PHP_EOL;
    }

    private function choseStats(): string
    {
        $possibleOptions = [1, 2, 3, 4];
        $statsOption = trim(fgets(STDIN));
        while (!in_array($statsOption, $possibleOptions)) {
            $statsOption = trim(fgets(STDIN));
        }

        return $statsOption;
    }

    public function executeOption(mixed $statsOption): void
    {
        switch ($statsOption) {
            case '1':
                $this->getTopDirectors();
                break;
            case '2':
                $this->getTopCountries();
                break;
            case '3':
                $this->getTopGenres();
                break;
            case '4':
                $this->ratingByDuration();
                break;
            default:
                echo 'Invalid option';
        }
    }

    public function getTopDirectors(): void
    {
        $this->printTopItems('directors', 'name', 'Top Directors');
    }

    public function getTopCountries(): void
    {
        $this->printTopItems('historic_countries', null, 'Top Countries');
    }

    public function getTopGenres(): void
    {
        $this->printTopItems('genres', null, 'Top Genres');
    }

    public function printTopItems(string $key, ?string $subKey, string $title): void
    {
        $items = [];

        foreach ($this->movieData as $movie) {
            $data = $movie['film'][$key];
            if ($subKey) {
                $data = array_column($data, $subKey);
            }
            $items = array_merge($items, $data);
        }

        $itemCounts = array_count_values($items);
        arsort($itemCounts);

        echo "$title:\n";
        foreach (array_slice($itemCounts, 0, 10, true) as $item => $count) {
            echo "{$item}: {$count}\n";
        }
    }
}
