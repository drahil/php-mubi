<?php

namespace drahil\MubiStats\Singletons;

class MovieDataSingleton
{
    private static ?MovieDataSingleton $instance = null;
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

    public static function getInstance(string $profileId): MovieDataSingleton
    {
        if (self::$instance === null) {
            self::$instance = new MovieDataSingleton($profileId);
        }

        return self::$instance;
    }

    public function getMovieData(): array
    {
        return $this->movieData;
    }

    public function setMovieData(array $movieData , string $profileId): void
    {
        $this->movieData = $movieData;
        $fileName = "movies_{$profileId}.json";
        file_put_contents($fileName, json_encode($movieData, JSON_PRETTY_PRINT));
    }
}
