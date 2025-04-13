<?php

namespace drahil\MubiStats\Data;

class MovieData
{
    private static ?MovieData $instance = null;
    private array $movieData;
    private string $profileId;

    private function __construct(string $profileId)
    {
        $this->profileId = $profileId;
        $filePath = "movies_{$profileId}.json";

        if (file_exists($filePath)) {
            var_dump("Loading movie data from file: $filePath");
            $jsonData = file_get_contents($filePath);
            $this->movieData = json_decode($jsonData, true) ?? [];
        } else {
            $this->movieData = [];
        }
    }

    /**
     * Get the singleton instance of MovieData.
     *
     * @param string $profileId
     * @return MovieData
     */
    public static function getInstance(string $profileId): MovieData
    {
        if (self::$instance === null) {
            self::$instance = new MovieData($profileId);
        }

        return self::$instance;
    }

    /**
     * Get the movie data.
     *
     * @return array
     */
    public function getMovieData(): array
    {
        return $this->movieData;
    }

    /**
     * Set the movie data and save it to a file.
     *
     * @param array $movieData
     * @return void
     */
    public function setMovieData(array $movieData): void
    {
        $this->movieData = $movieData;
        $fileName = "movies_{$this->profileId}.json";
        file_put_contents($fileName, json_encode($movieData, JSON_PRETTY_PRINT));
    }
}
