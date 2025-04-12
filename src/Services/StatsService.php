<?php

namespace drahil\MubiStats\Services;

class StatsService
{
    private array $movieData;

    public function __construct()
    {
        $this->movieData = [];
    }

    /**
     * Set movies data.
     *
     * @param array $movieData
     * @return void
     */
    public function setMoviesData(array $movieData): void
    {
        $this->movieData = $movieData;
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

    /**
     * Get stats.
     *
     * @return void
     */
    public function getStats(): void
    {
        $this->printPossibleStats();
        $statsOption = $this->choseStats();
        $this->executeOption($statsOption);
    }

    /**
     * Print possible stats.
     *
     * @return void
     */
    private function printPossibleStats(): void
    {
        echo 'Possible stats:' . PHP_EOL;
        echo '1. Top directors' . PHP_EOL;
        echo '2. Top countries' . PHP_EOL;
        echo '3. Top genres' . PHP_EOL;
        echo '4. Rating by movie duration' . PHP_EOL;
    }

    /**
     * Chose stats.
     *
     * @return string
     */
    private function choseStats(): string
    {
        $possibleOptions = [1, 2, 3, 4];

        do {
            echo "Choose option: ";
            $statsOption = trim(fgets(STDIN));

        } while (!in_array($statsOption, $possibleOptions));

        return $statsOption;
    }

    /**
     * Execute option.
     *
     * @param mixed $statsOption
     * @return void
     */
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

    /**
     * Get top directors.
     *
     * @return void
     */
    public function getTopDirectors(): void
    {
        $this->printTopItems('directors', 'name', 'Top Directors');
    }

    /**
     * Get top countries.
     *
     * @return void
     */
    public function getTopCountries(): void
    {
        $this->printTopItems('historic_countries', null, 'Top Countries');
    }

    /**
     * Get top genres.
     *
     * @return void
     */
    public function getTopGenres(): void
    {
        $this->printTopItems('genres', null, 'Top Genres');
    }

    /**
     * Print top items.
     *
     * @param string $key
     * @param string|null $subKey
     * @param string $title
     * @return void
     */
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
        foreach (array_slice($itemCounts, 0, 30, true) as $item => $count) {
            echo "{$item}: {$count}\n";
        }
    }

    /**
     * Rating by duration.
     *
     * @return void
     */
    public function ratingByDuration(): void
    {
        $ratings = [];

        foreach ($this->movieData as $movie) {
            $duration = $this->floorToNearestTen($movie['film']['duration']);
            $rating = $movie['overall'];

            if ($duration > 180) {
                $duration = 180;
            }

            if (!array_key_exists($duration, $ratings)) {
                $ratings[$duration] = [];
            }

            $ratings[$duration][] = $rating;
        }

        foreach ($ratings as $duration => $rating) {
            $averageRating = array_sum($rating) / count($rating);
            echo "Duration: $duration. Average rating: $averageRating\n";
        }
    }

    /**
     * Floor to nearest ten.
     *
     * @param $number
     * @return float|int
     */
    private function floorToNearestTen($number): float|int
    {
        return floor($number / 10) * 10;
    }
}
